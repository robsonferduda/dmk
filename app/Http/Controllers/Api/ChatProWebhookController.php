<?php

namespace App\Http\Controllers\Api;

use App\Conta;
use App\Http\Controllers\Controller;
use App\WhatsappMensagem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * [CHATPRO WEBHOOK]
 * Endpoint público chamado pelo ChatPro a cada evento da instância.
 *
 * Formatos vistos em produção (ChatPro v5):
 *
 *  A) Eco de envio (FromMe=true) — vem logo após NÓS enviarmos:
 *     { "Type": "send_message",
 *       "Body": { "Info": { "Id": "...", "FromMe": true,
 *                           "RemoteJid": "554891...@s.whatsapp.net",
 *                           "SenderJid": "554891...@s.whatsapp.net",
 *                           "Timestamp": 1778933416,
 *                           "Status": 1 },
 *                 "Text": "..." },
 *       "token": "..." }
 *
 *  B) ACK / status update (entregue, lida):
 *     { "0": "Msg",
 *       "1": { "id": "...", "ack": 1|2|3, "cmd": "ack", "t": 1778933417 },
 *       "token": "..." }
 *
 *  C) Mensagem recebida real (alguém respondendo nossa msg):
 *     Formato similar ao (A), mas com FromMe=false e SenderJid = nº do remetente.
 *     (Estrutura confirmada conforme aparecer; parser é tolerante.)
 *
 * Estratégia:
 *   - Eco (A) e ACK (B) NÃO criam linha nova: atualizam a outbound já gravada
 *     com mesmo message_id (preenchendo o id se ainda não tinha, e o status).
 *   - Mensagem recebida (C) cria linha nova com tp_direcao='I'.
 *   - Tudo que não casar: linha nova com payload bruto, pra debug.
 *   - Sempre responde 200, para não disparar reentrega em loop.
 */
class ChatProWebhookController extends Controller
{
    /** Tradução dos códigos de ack do WhatsApp para nomes legíveis. */
    private static $ACK_NOMES = [
        0  => 'pending',
        1  => 'sent',
        2  => 'delivered',
        3  => 'read',
        4  => 'played',
        -1 => 'failed',
    ];

    public function handle(Request $request)
    {
        // 1) Autenticação por token (query string OU campo "token" no body)
        $tokenEsperado = (string) config('chatpro.webhook_token', '');
        if ($tokenEsperado !== '') {
            $tokenRecebido = (string) ($request->query('token', '') ?: $request->input('token', ''));
            if (!hash_equals($tokenEsperado, $tokenRecebido)) {
                Log::warning('[CHATPRO-WEBHOOK] Token inválido. IP=' . $request->ip());
                return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
            }
        }

        $payload = $request->all();

        try {
            // Single-tenant: única conta com ChatPro ativo.
            $conta = Conta::where('fl_chatpro_ativo_con', true)->orderBy('cd_conta_con')->first();
            if (!$conta) {
                Log::warning('[CHATPRO-WEBHOOK] Nenhuma conta com ChatPro ativo. Payload ignorado.');
                return response()->json(['ok' => true, 'note' => 'no active account']);
            }

            if ($this->ehAck($payload)) {
                $this->tratarAck($payload, $conta);
            } elseif ($this->ehEcoDeEnvio($payload)) {
                $this->tratarEcoEnvio($payload, $conta);
            } else {
                $this->tratarMensagemRecebida($payload, $conta);
            }

        } catch (\Throwable $e) {
            Log::error('[CHATPRO-WEBHOOK] Exceção ao processar payload: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'payload' => $payload,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------
    // Identificação de tipos
    // -----------------------------------------------------------------

    /** Formato B: { "0":"Msg", "1": { "cmd":"ack", ... } } */
    private function ehAck(array $p)
    {
        return isset($p[0]) && $p[0] === 'Msg'
            && isset($p[1]) && is_array($p[1])
            && (($p[1]['cmd'] ?? null) === 'ack' || isset($p[1]['ack']));
    }

    /** Formato A: Type=send_message com FromMe=true */
    private function ehEcoDeEnvio(array $p)
    {
        $type   = $p['Type'] ?? null;
        $fromMe = $p['Body']['Info']['FromMe'] ?? null;
        return $type === 'send_message'
            && ($fromMe === true || $fromMe === 'true' || $fromMe === 1);
    }

    // -----------------------------------------------------------------
    // Tratadores
    // -----------------------------------------------------------------

    /**
     * ACK: atualiza ds_status_wmm da outbound correspondente.
     * Se não houver outbound (raro), grava uma linha 'A' como rastro.
     */
    private function tratarAck(array $p, $conta)
    {
        $a          = $p[1] ?? [];
        $msgId      = $a['id']  ?? null;
        $ackCod     = $a['ack'] ?? null;
        $statusNome = self::$ACK_NOMES[(int) $ackCod] ?? ('ack:' . $ackCod);
        $ts         = $a['t']   ?? null;
        $dtEvt      = $ts ? Carbon::createFromTimestamp((int) $ts) : null;

        if ($msgId) {
            $row = WhatsappMensagem::where('ds_message_id_wmm', $msgId)
                ->where('tp_direcao_wmm', 'O')
                ->first();
            if ($row) {
                $row->ds_status_wmm = $statusNome;
                if ($dtEvt && !$row->dt_evento_wmm) {
                    $row->dt_evento_wmm = $dtEvt;
                }
                $row->save();
                return;
            }
        }

        WhatsappMensagem::create([
            'cd_conta_con'       => $conta->cd_conta_con,
            'tp_direcao_wmm'     => 'A',
            'ds_tipo_wmm'        => 'ack',
            'ds_status_wmm'      => $statusNome,
            'ds_payload_raw_wmm' => $p,
            'dt_evento_wmm'      => $dtEvt,
        ]);
    }

    /**
     * ECO de envio: atualiza a outbound (preenche id se faltava, atualiza
     * status). Se não conseguir correlacionar, grava como rastro.
     */
    private function tratarEcoEnvio(array $p, $conta)
    {
        $info        = $p['Body']['Info'] ?? [];
        $texto       = $p['Body']['Text'] ?? null;
        $msgId       = $info['Id'] ?? null;
        $destinoJid  = $info['RemoteJid'] ?? null;
        $destino     = $this->jidParaTelefone($destinoJid);
        $ts          = $info['Timestamp'] ?? null;
        $dtEvt       = $ts ? Carbon::createFromTimestamp((int) $ts) : null;
        $statusInfo  = isset($info['Status']) ? ('whatsapp:' . $info['Status']) : null;

        // 1) Procura outbound pelo message_id.
        $row = null;
        if ($msgId) {
            $row = WhatsappMensagem::where('ds_message_id_wmm', $msgId)
                ->where('tp_direcao_wmm', 'O')
                ->first();
        }

        // 2) Se não achou, tenta casar pela última outbound recente
        //    com mesmo destino + mesmo texto sem id atribuído ainda.
        if (!$row && $destino && $texto) {
            $row = WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                ->where('tp_direcao_wmm', 'O')
                ->where('nu_telefone_destino_wmm', $destino)
                ->where('ds_mensagem_wmm', $texto)
                ->where('created_at', '>=', Carbon::now()->subMinutes(10))
                ->whereNull('ds_message_id_wmm')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if ($row) {
            if ($msgId && !$row->ds_message_id_wmm) {
                $row->ds_message_id_wmm = $msgId;
            }
            if ($statusInfo) {
                $row->ds_status_wmm = $statusInfo;
            }
            if ($dtEvt && !$row->dt_evento_wmm) {
                $row->dt_evento_wmm = $dtEvt;
            }
            $row->save();
            return;
        }

        // Sem correlação possível: registra como rastro.
        WhatsappMensagem::create([
            'cd_conta_con'            => $conta->cd_conta_con,
            'tp_direcao_wmm'          => 'O',
            'nu_telefone_destino_wmm' => $destino,
            'ds_mensagem_wmm'         => $texto,
            'ds_tipo_wmm'             => 'text',
            'ds_message_id_wmm'       => $msgId,
            'ds_status_wmm'           => $statusInfo,
            'ds_payload_raw_wmm'      => $p,
            'dt_evento_wmm'           => $dtEvt,
        ]);
    }

    /**
     * MENSAGEM RECEBIDA: cria linha 'I' e tenta resolver o correspondente
     * pelo telefone de origem.
     */
    private function tratarMensagemRecebida(array $p, $conta)
    {
        $info  = $p['Body']['Info'] ?? [];
        $texto = $p['Body']['Text']
              ?? ($p['Body']['Body'] ?? null)
              ?? ($p['Body']['text'] ?? null);

        // Em mensagens recebidas, o remetente é o SenderJid (em grupos) ou
        // RemoteJid (em 1-a-1). Se FromMe=false, ambos costumam ser o remetente.
        $origemJid = $info['SenderJid'] ?? $info['RemoteJid'] ?? null;
        $origem    = $this->jidParaTelefone($origemJid);

        $msgId = $info['Id']        ?? null;
        $ts    = $info['Timestamp'] ?? null;
        $dtEvt = $ts ? Carbon::createFromTimestamp((int) $ts) : null;

        // Idempotência: não duplica mesmo id.
        if ($msgId && WhatsappMensagem::where('ds_message_id_wmm', $msgId)->exists()) {
            return;
        }

        $cdCorrespondente = $origem
            ? $this->resolverCorrespondentePorTelefone($origem, $conta->cd_conta_con)
            : null;

        WhatsappMensagem::create([
            'cd_conta_con'           => $conta->cd_conta_con,
            'tp_direcao_wmm'         => 'I',
            'nu_telefone_origem_wmm' => $origem,
            'ds_mensagem_wmm'        => $texto,
            'ds_tipo_wmm'            => $p['Type'] ?? 'message',
            'ds_message_id_wmm'      => $msgId,
            'ds_status_wmm'          => 'received',
            'ds_payload_raw_wmm'     => $p,
            'cd_correspondente_cor'  => $cdCorrespondente,
            'dt_evento_wmm'          => $dtEvt,
        ]);
    }

    // -----------------------------------------------------------------
    // Utilitários
    // -----------------------------------------------------------------

    /** "554891030204@s.whatsapp.net" → "554891030204" */
    private function jidParaTelefone($jid)
    {
        if (!$jid) {
            return null;
        }
        $num = preg_replace('/@.*$/', '', (string) $jid);
        $num = preg_replace('/\D+/', '', $num);
        return $num !== '' ? $num : null;
    }

    /**
     * Acha o cd_conta_con cujo nu_telefone_whatsapp_con bate com os
     * últimos 10 dígitos do telefone informado.
     */
    private function resolverCorrespondentePorTelefone($telefone, $cdContaEscritorio)
    {
        $tel = preg_replace('/\D+/', '', (string) $telefone);
        if (strlen($tel) < 10) {
            return null;
        }
        $sufixo = substr($tel, -10);

        $row = DB::table('conta_con')
            ->where('cd_conta_con', '!=', $cdContaEscritorio)
            ->whereNotNull('nu_telefone_whatsapp_con')
            ->whereRaw("regexp_replace(nu_telefone_whatsapp_con, '\\D', '', 'g') LIKE ?", ['%' . $sufixo])
            ->select('cd_conta_con')
            ->first();

        return $row ? $row->cd_conta_con : null;
    }
}
