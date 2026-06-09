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
            } elseif ($this->ehEventoSistema($payload)) {
                $this->tratarEventoSistema($payload, $conta);
            } else {
                $this->tratarMensagemRecebida($payload, $conta);
            }

        } catch (\Throwable $e) {
            // Violação de unicidade em ds_message_id_wmm: duas entregas simultâneas
            // do mesmo webhook (race condition). A mensagem já foi gravada pela
            // requisição concorrente — não é um erro real, apenas ignoramos.
            if ($this->ehDuplicateKeyError($e)) {
                Log::debug('[CHATPRO-WEBHOOK] Entrega duplicada ignorada (race condition).', [
                    'message_id' => $payload['Body']['Info']['Id'] ?? '?',
                ]);
                return response()->json(['ok' => true]);
            }

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

    /**
     * Eventos administrativos da instância (charge_status, instance_status,
     * presence, etc.) — não são mensagens nem acks. Apenas registramos
     * como rastro, sem tentar correlacionar.
     */
    private function ehEventoSistema(array $p)
    {
        $tipoLower = strtolower((string) ($p['type'] ?? ''));
        if (in_array($tipoLower, ['charge_status', 'instance_status', 'presence', 'status'], true)) {
            return true;
        }
        // Sem Body de mensagem e sem ack → não há o que processar como inbound.
        if (empty($p['Body']) && empty($p['body']) && empty($p['message'])) {
            return true;
        }
        return false;
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

        // ACK sem outbound correlacionada: ignora silenciosamente.
        Log::debug('[CHATPRO-WEBHOOK] ACK sem outbound correlacionada.', ['msgId' => $msgId]);
    }

    /**
     * Eventos do sistema (charge_status etc.): registra como rastro 'A'
     * sem tentar relacionar a uma mensagem específica.
     */
    private function tratarEventoSistema(array $p, $conta)
    {
        // Evento de sistema (charge_status, instance_status etc.): ignora silenciosamente.
        Log::debug('[CHATPRO-WEBHOOK] Evento de sistema ignorado.', ['type' => $p['type'] ?? 'system']);
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

        // Sem texto nem origem nem id: payload desconhecido — ignora.
        if (empty($texto) && empty($origem) && empty($msgId)) {
            return;
        }

        // Idempotência: não duplica mesmo id.
        if ($msgId && WhatsappMensagem::where('ds_message_id_wmm', $msgId)->exists()) {
            return;
        }

        $cdCorrespondente = $origem
            ? $this->resolverCorrespondentePorTelefone($origem, $conta->cd_conta_con)
            : null;

        // [VÍNCULO COM PROCESSO]
        // 1) Reply citado: usa o QuotedMessageID e pega o processo
        //    diretamente da outbound original.
        // 2) Fallback: última outbound para o mesmo telefone com
        //    cd_processo_pro preenchido, dentro da janela configurada.
        $cdProcesso       = null;
        $cdProcessoCkPck  = null;
        $quotedMsgId      = $this->extrairQuotedMsgId($p);
        $janelaDias       = (int) config('chatpro.inbound_link_window_days', 30);

        if ($quotedMsgId || ($origem && $janelaDias > 0)) {
            $vinculo = $this->resolverProcessoParaInbound(
                $origem,
                $conta->cd_conta_con,
                $quotedMsgId,
                $janelaDias
            );
            if ($vinculo) {
                $cdProcesso      = $vinculo['cd_processo_pro']         ?? null;
                $cdProcessoCkPck = $vinculo['cd_processo_checkin_pck'] ?? null;
            }
        }

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
            'cd_processo_pro'        => $cdProcesso,
            'cd_processo_checkin_pck'=> $cdProcessoCkPck,
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
     * Retorna true se a exceção for uma violação de chave única no PostgreSQL
     * (SQLSTATE 23505) — usado para tratar entregas duplicadas do webhook.
     */
    private function ehDuplicateKeyError(\Throwable $e): bool
    {
        $msg = $e->getMessage();
        return str_contains($msg, '23505')
            || str_contains($msg, 'Unique violation')
            || str_contains($msg, 'duplicate key value');
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

    /**
     * Extrai o ID da mensagem citada (reply) de um payload de inbound.
     *
     * O ChatPro v5 não documenta um nome único para esse campo; aqui
     * tentamos as variações observadas em produção. Devolve string ou
     * null.
     */
    private function extrairQuotedMsgId(array $p)
    {
        $candidatos = [
            $p['Body']['Info']['QuotedMessageID']         ?? null,
            $p['Body']['Info']['QuotedMessageId']         ?? null,
            $p['Body']['QuotedMessageID']                 ?? null,
            $p['Body']['QuotedMessageId']                 ?? null,
            $p['Body']['ContextInfo']['QuotedMessageId']  ?? null,
            $p['Body']['ContextInfo']['stanzaId']         ?? null,
            $p['quotedMsgId']                             ?? null,
        ];
        foreach ($candidatos as $c) {
            if (is_string($c) && $c !== '') {
                return $c;
            }
        }
        return null;
    }

    /**
     * Resolve o processo (e check-in) ao qual uma mensagem INBOUND
     * pertence.
     *
     *  1) Se o WhatsApp marcou a inbound como reply de uma outbound
     *     nossa (QuotedMessageID), herda o cd_processo_pro daquela
     *     outbound — vínculo mais preciso possível.
     *  2) Caso contrário, procura a última outbound enviada para o
     *     mesmo telefone (sufixo de 10 dígitos), com cd_processo_pro
     *     preenchido, dentro da janela configurada.
     *
     * Retorna ['cd_processo_pro' => ..., 'cd_processo_checkin_pck' => ...]
     * ou null se nada for encontrado.
     */
    private function resolverProcessoParaInbound($telefone, $cdContaEscritorio, $quotedMsgId, $janelaDias)
    {
        // 1) Reply citado.
        if ($quotedMsgId) {
            $q = WhatsappMensagem::where('ds_message_id_wmm', $quotedMsgId)
                ->whereNotNull('cd_processo_pro')
                ->first();
            if ($q) {
                return [
                    'cd_processo_pro'         => $q->cd_processo_pro,
                    'cd_processo_checkin_pck' => $q->cd_processo_checkin_pck,
                ];
            }
        }

        // 2) Última outbound para o mesmo telefone.
        if (!$telefone || $janelaDias <= 0) {
            return null;
        }
        $tel = preg_replace('/\D+/', '', (string) $telefone);
        if (strlen($tel) < 10) {
            return null;
        }
        $sufixo = substr($tel, -10);

        $row = WhatsappMensagem::where('cd_conta_con', $cdContaEscritorio)
            ->where('tp_direcao_wmm', 'O')
            ->whereNotNull('cd_processo_pro')
            ->whereRaw("regexp_replace(nu_telefone_destino_wmm, '\\D', '', 'g') LIKE ?", ['%' . $sufixo])
            ->where('created_at', '>=', Carbon::now()->subDays($janelaDias))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$row) {
            return null;
        }

        return [
            'cd_processo_pro'         => $row->cd_processo_pro,
            'cd_processo_checkin_pck' => $row->cd_processo_checkin_pck,
        ];
    }
}
