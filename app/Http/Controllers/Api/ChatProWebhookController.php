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
 * Endpoint público chamado pelo ChatPro a cada evento da instância:
 *   - mensagem recebida (received_message / text-message-evento)
 *   - confirmação de entrega/leitura (ack_update)
 *   - eventos de sessão (opened_session, closed_session, etc.)
 *
 * Estratégia desta primeira versão (Fase 1):
 *   1. Validar token de segurança.
 *   2. Persistir o evento bruto em whatsapp_mensagem_wmm.
 *   3. Tentar resolver vínculos (correspondente pelo telefone) — best-effort.
 *   4. Sempre responder 200, para o ChatPro não ficar reenviando.
 *      (Erros internos viram log; idempotência garante reentrega segura.)
 *
 * Roteamento de respostas (interpretar SIM/NÃO, etc.) virá nas próximas fases.
 */
class ChatProWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Autenticação simples por token na query string
        $tokenEsperado = (string) config('chatpro.webhook_token', '');
        if ($tokenEsperado !== '') {
            $tokenRecebido = (string) $request->query('token', '');
            if (!hash_equals($tokenEsperado, $tokenRecebido)) {
                Log::warning('[CHATPRO-WEBHOOK] Token inválido. IP=' . $request->ip());
                return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
            }
        }

        $payload = $request->all();

        try {
            // Em instalação single-tenant: pega a única conta com ChatPro ativo.
            // (Se um dia for multi-tenant, dá pra resolver pelo instance_id no payload.)
            $conta = Conta::where('fl_chatpro_ativo_con', true)
                ->orderBy('cd_conta_con')
                ->first();

            if (!$conta) {
                Log::warning('[CHATPRO-WEBHOOK] Nenhuma conta com ChatPro ativo. Payload ignorado.');
                return response()->json(['ok' => true, 'note' => 'no active account']);
            }

            $info = $this->extrairCampos($payload);

            // Idempotência: se já temos esse message_id, devolve OK sem regravar.
            if (!empty($info['message_id'])) {
                $existe = WhatsappMensagem::where('ds_message_id_wmm', $info['message_id'])->first();
                if ($existe) {
                    return response()->json(['ok' => true, 'duplicate' => true]);
                }
            }

            // Tenta achar o correspondente pelo telefone de origem.
            $cdCorrespondente = null;
            if (!empty($info['from'])) {
                $cdCorrespondente = $this->resolverCorrespondentePorTelefone($info['from'], $conta->cd_conta_con);
            }

            WhatsappMensagem::create([
                'cd_conta_con'            => $conta->cd_conta_con,
                'tp_direcao_wmm'          => $info['direcao'],
                'nu_telefone_origem_wmm'  => $info['from'],
                'nu_telefone_destino_wmm' => $info['to'],
                'ds_mensagem_wmm'         => $info['body'],
                'ds_tipo_wmm'             => $info['tipo'],
                'ds_message_id_wmm'       => $info['message_id'],
                'ds_status_wmm'           => $info['status'],
                'ds_payload_raw_wmm'      => $payload,
                'cd_correspondente_cor'   => $cdCorrespondente,
                'dt_evento_wmm'           => $info['dt_evento'],
            ]);

        } catch (\Throwable $e) {
            // Nunca devolve 5xx: ChatPro reenviaria em loop. Log + 200.
            Log::error('[CHATPRO-WEBHOOK] Exceção ao processar payload: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'payload' => $payload,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Extrai os campos comuns do payload (que tem N formatos diferentes
     * dependendo do tipo de evento). Best-effort: o que não tiver fica null
     * — o payload bruto fica salvo de qualquer jeito em ds_payload_raw_wmm.
     */
    private function extrairCampos(array $p)
    {
        $out = [
            'direcao'    => 'I',  // default: inbound
            'from'       => null,
            'to'         => null,
            'body'       => null,
            'tipo'       => null,
            'message_id' => null,
            'status'     => null,
            'dt_evento'  => null,
        ];

        // Formato comum ChatPro v5 (received_message / text-message-evento):
        //   { "Body": { "From": "...", "To": "...", "Body": "...", "Type": "...", "Id": "...", "Timestamp": ... } }
        // Mas as chaves variam de "Body" para "data", "message", "messages[0]", etc.
        // Fazemos lookup tolerante.
        $b = $p['Body'] ?? $p['data'] ?? $p['message'] ?? $p;

        // Algumas integrações mandam array de mensagens
        if (isset($b['messages']) && is_array($b['messages']) && !empty($b['messages'])) {
            $b = $b['messages'][0];
        }

        $out['from']       = $this->normalizarTelefone($b['From']     ?? $b['from']     ?? $b['Author'] ?? null);
        $out['to']         = $this->normalizarTelefone($b['To']       ?? $b['to']       ?? null);
        $out['body']       = $b['Body']                               ?? $b['body']     ?? $b['text']   ?? null;
        $out['tipo']       = $b['Type']                               ?? $b['type']     ?? null;
        $out['message_id'] = $b['Id']                                 ?? $b['id']       ?? $b['message_id'] ?? $b['MessageId'] ?? null;
        $out['status']     = $b['Status']                             ?? $b['status']   ?? $b['ack']    ?? null;

        // Timestamp pode vir em segundos (unix), ms, ou ISO.
        $ts = $b['Timestamp'] ?? $b['timestamp'] ?? $b['t'] ?? null;
        if (!empty($ts)) {
            try {
                if (is_numeric($ts)) {
                    $ts = (int) $ts;
                    if ($ts > 9999999999) {        // ms
                        $out['dt_evento'] = Carbon::createFromTimestampMs($ts);
                    } else {                        // s
                        $out['dt_evento'] = Carbon::createFromTimestamp($ts);
                    }
                } else {
                    $out['dt_evento'] = Carbon::parse($ts);
                }
            } catch (\Throwable $e) {
                // Ignora — fica null.
            }
        }

        // Direção: se vier um campo claro, usa; senão, inferimos.
        // ChatPro normalmente marca FromMe = true em mensagens enviadas
        // pela própria instância (eco).
        $fromMe = $b['FromMe'] ?? $b['fromMe'] ?? $b['from_me'] ?? null;
        if ($fromMe === true || $fromMe === 'true' || $fromMe === 1 || $fromMe === '1') {
            $out['direcao'] = 'O';
        }

        // Eventos de ack vêm com tipo "ack" ou só com status sem body
        if (in_array(strtolower((string) $out['tipo']), ['ack', 'status'], true)
            || (!empty($out['status']) && empty($out['body']))) {
            $out['direcao'] = 'A';
        }

        return $out;
    }

    /**
     * Tenta achar o cd_correspondente_cor (que é um cd_conta_con) cujo
     * nu_telefone_whatsapp_con bate com o telefone informado.
     * Compara só os últimos dígitos para tolerar variações de máscara
     * (com/sem '55', com/sem 9 extra etc.).
     */
    private function resolverCorrespondentePorTelefone($telefone, $cdContaEscritorio)
    {
        $tel = preg_replace('/\D+/', '', (string) $telefone);
        if (strlen($tel) < 10) {
            return null;
        }
        $sufixo = substr($tel, -10);    // últimos 10 dígitos (DDD + número)

        $row = DB::table('conta_con')
            ->where('cd_conta_con', '!=', $cdContaEscritorio)
            ->whereNotNull('nu_telefone_whatsapp_con')
            ->whereRaw("regexp_replace(nu_telefone_whatsapp_con, '\\D', '', 'g') LIKE ?", ['%' . $sufixo])
            ->select('cd_conta_con')
            ->first();

        return $row ? $row->cd_conta_con : null;
    }

    private function normalizarTelefone($num)
    {
        if ($num === null || $num === '') {
            return null;
        }
        // ChatPro às vezes manda "5548999999999@c.us" — tira o sufixo.
        $num = preg_replace('/@.*$/', '', (string) $num);
        $num = preg_replace('/\D+/', '', $num);
        return $num !== '' ? $num : null;
    }
}
