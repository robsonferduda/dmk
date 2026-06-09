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
 * [Z-API WEBHOOK]
 * Endpoint público chamado pela Z-API a cada evento da instância.
 *
 * Tipos de evento tratados:
 *
 *  A) DeliveryCallback — confirmação de envio de mensagem:
 *     { "type": "DeliveryCallback",
 *       "phone": "554891...", "messageId": "...", "zaapId": "...",
 *       "instanceId": "...", "momment": 1234567890000,
 *       "error": "..." }  ← presente só em falhas
 *
 *  B) ReceivedCallback — mensagem recebida (incluindo ecos fromMe):
 *     { "type": "ReceivedCallback",
 *       "instanceId": "...", "messageId": "...", "phone": "554891...",
 *       "fromMe": true|false, "senderName": "...",
 *       "text": { "message": "..." }, "momment": ... }
 *
 *  C) StatusCallback — atualização de status (entregue/lida):
 *     { "type": "StatusCallback",
 *       "instanceId": "...", "messageId": "...", "phone": "...",
 *       "status": "RECEIVED|READ|PLAYED", "momment": ... }
 *
 * Estratégia:
 *   - DeliveryCallback: atualiza a outbound com o status de envio.
 *   - ReceivedCallback com fromMe=true: eco de envio — correlaciona
 *     com a outbound gravada pelo serviço e preenche o messageId.
 *   - ReceivedCallback com fromMe=false: mensagem inbound — cria 'I'.
 *   - StatusCallback: atualiza ds_status_wmm da outbound.
 *   - Tudo que não casar: linha 'A' de rastro para debug.
 *   - Sempre responde 200 para evitar retentativas em loop.
 */
class ZApiWebhookController extends Controller
{
    /** Mapeamento de status Z-API → nome legível persistido. */
    private static $STATUS_MAP = [
        'RECEIVED' => 'delivered',
        'READ'     => 'read',
        'PLAYED'   => 'played',
        'PENDING'  => 'pending',
        'SENT'     => 'sent',
    ];

    public function handle(Request $request)
    {
        // 1) Autenticação por token (query string OU campo "token" no body).
        $tokenEsperado = (string) config('zapi.webhook_token', '');
        if ($tokenEsperado !== '') {
            $tokenRecebido = (string) ($request->query('token', '') ?: $request->input('token', ''));
            if (!hash_equals($tokenEsperado, $tokenRecebido)) {
                Log::warning('[ZAPI-WEBHOOK] Token inválido. IP=' . $request->ip());
                return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
            }
        }

        $payload = $request->all();

        try {
            // Identifica a conta pela instanceId presente no payload.
            $instanceId = $payload['instanceId'] ?? null;
            $conta      = $this->resolverConta($instanceId);

            if (!$conta) {
                Log::warning('[ZAPI-WEBHOOK] Nenhuma conta encontrada para instanceId: ' . $instanceId . '. Payload ignorado.');
                return response()->json(['ok' => true, 'note' => 'no matching account']);
            }

            $tipo = (string) ($payload['type'] ?? '');

            if ($tipo === 'DeliveryCallback') {
                $this->tratarDeliveryCallback($payload, $conta);
            } elseif ($tipo === 'StatusCallback' || $tipo === 'MessageStatusCallback') {
                $this->tratarStatusCallback($payload, $conta);
            } elseif ($tipo === 'ReceivedCallback') {
                $fromMe = $payload['fromMe'] ?? false;
                if ($fromMe) {
                    $this->tratarEcoDeEnvio($payload, $conta);
                } else {
                    $this->tratarMensagemRecebida($payload, $conta);
                }
            } else {
                // Evento desconhecido: ignora silenciosamente.
                Log::debug('[ZAPI-WEBHOOK] Evento ignorado.', ['type' => $tipo]);
            }

        } catch (\Throwable $e) {
            // Violação de unicidade: entrega duplicada simultânea do webhook.
            // A mensagem já foi gravada pela requisição concorrente — ignoramos.
            if ($this->ehDuplicateKeyError($e)) {
                Log::debug('[ZAPI-WEBHOOK] Entrega duplicada ignorada (race condition).', [
                    'message_id' => $payload['messageId'] ?? '?',
                ]);
                return response()->json(['ok' => true]);
            }

            Log::error('[ZAPI-WEBHOOK] Exceção ao processar payload: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'payload' => $payload,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------
    // Tratadores
    // -----------------------------------------------------------------

    /**
     * DeliveryCallback: confirma entrega (ou falha) do envio.
     * Atualiza a outbound já gravada com o status resultante.
     */
    private function tratarDeliveryCallback(array $p, $conta)
    {
        $msgId  = $p['messageId'] ?? null;
        $zaapId = $p['zaapId']    ?? null;
        $erro   = $p['error']     ?? null;
        // Sem error = WhatsApp aceitou na fila (queued).
        // Com error  = envio falhou definitivamente.
        $status = $erro ? 'failed' : 'queued';
        $ts     = isset($p['momment']) ? (int) ($p['momment'] / 1000) : null;
        $dtEvt  = $ts ? Carbon::createFromTimestamp($ts) : null;

        if ($msgId) {
            $row = WhatsappMensagem::where('ds_message_id_wmm', $msgId)
                ->where('tp_direcao_wmm', 'O')
                ->first();

            if (!$row) {
                // Tenta correlacionar pelo zaapId (id interno da Z-API).
                if ($zaapId) {
                    $row = WhatsappMensagem::where('ds_message_id_wmm', $zaapId)
                        ->where('tp_direcao_wmm', 'O')
                        ->first();
                }
            }

            // Último recurso: outbound recente sem messageId para o mesmo telefone.
            if (!$row && $erro) {
                $destino = $this->normalizarTelefone($p['phone'] ?? null);
                if ($destino) {
                    $sufixo = substr($destino, -10);
                    $row = WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                        ->where('tp_direcao_wmm', 'O')
                        ->where('ds_status_wmm', 'sent')
                        ->whereNull('ds_message_id_wmm')
                        ->whereRaw("regexp_replace(nu_telefone_destino_wmm, '\\D', '', 'g') LIKE ?", ['%' . $sufixo])
                        ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
            }

            if ($row) {
                if (!$row->ds_message_id_wmm) {
                    $row->ds_message_id_wmm = $msgId;
                }
                $row->ds_status_wmm = $status;
                if ($dtEvt && !$row->dt_evento_wmm) {
                    $row->dt_evento_wmm = $dtEvt;
                }
                if ($erro) {
                    $atual = (array) ($row->ds_payload_raw_wmm ?? []);
                    $atual['delivery_error'] = $erro;
                    $row->ds_payload_raw_wmm = $atual;
                    Log::warning('[ZAPI-WEBHOOK] Falha de entrega para ' . ($p['phone'] ?? '?') . ': ' . $erro);
                }
                $row->save();
                return;
            }
        }

        // Sem outbound correlacionada: ignora silenciosamente (exceto em erros).
        if ($erro) {
            Log::warning('[ZAPI-WEBHOOK] DeliveryCallback de falha sem outbound correlacionada.', [
                'messageId' => $msgId,
                'phone'     => $p['phone'] ?? '?',
                'error'     => $erro,
            ]);
        } else {
            Log::debug('[ZAPI-WEBHOOK] DeliveryCallback sem outbound correlacionada.', ['messageId' => $msgId]);
        }
    }

    /**
     * StatusCallback: atualiza ds_status_wmm (entregue / lida / reproduzida).
     */
    private function tratarStatusCallback(array $p, $conta)
    {
        $msgId      = $p['messageId'] ?? null;
        $statusRaw  = strtoupper((string) ($p['status'] ?? ''));
        $statusNome = self::$STATUS_MAP[$statusRaw] ?? strtolower($statusRaw);
        $ts    = isset($p['momment']) ? (int) ($p['momment'] / 1000) : null;
        $dtEvt = $ts ? Carbon::createFromTimestamp($ts) : null;

        Log::debug('[ZAPI-WEBHOOK] StatusCallback recebido.', [
            'messageId'  => $msgId,
            'statusRaw'  => $statusRaw,
            'statusNome' => $statusNome,
        ]);

        if ($msgId) {
            $row = WhatsappMensagem::where('ds_message_id_wmm', $msgId)->first();
            if ($row) {
                // Não regride um status mais avançado para um menos avançado.
                // 'queued' e 'sent' são tratados no mesmo nível (1).
                $ordem = ['pending' => 0, 'queued' => 1, 'sent' => 1, 'delivered' => 2, 'read' => 3, 'played' => 4];
                $ordemAtual = $ordem[$row->ds_status_wmm] ?? -1;
                $ordemNovo  = $ordem[$statusNome]         ?? -1;

                Log::debug('[ZAPI-WEBHOOK] StatusCallback correlacionado.', [
                    'messageId'   => $msgId,
                    'statusAtual' => $row->ds_status_wmm,
                    'statusNovo'  => $statusNome,
                    'ordemAtual'  => $ordemAtual,
                    'ordemNovo'   => $ordemNovo,
                ]);

                if ($ordemNovo > $ordemAtual) {
                    $row->ds_status_wmm = $statusNome;
                    $row->save();
                }
                if ($dtEvt && !$row->dt_evento_wmm) {
                    $row->dt_evento_wmm = $dtEvt;
                    $row->save();
                }
                return;
            }
        }

        Log::debug('[ZAPI-WEBHOOK] StatusCallback sem outbound correlacionada.', ['messageId' => $msgId]);
    }

    /**
     * Eco de envio (ReceivedCallback com fromMe=true):
     * correlaciona com a outbound e preenche messageId se faltava.
     */
    private function tratarEcoDeEnvio(array $p, $conta)
    {
        $msgId   = $p['messageId'] ?? null;
        $destino = $this->normalizarTelefone($p['phone'] ?? null);
        $texto   = $p['text']['message'] ?? null;
        $ts      = isset($p['momment']) ? (int) ($p['momment'] / 1000) : null;
        $dtEvt   = $ts ? Carbon::createFromTimestamp($ts) : null;

        $row = null;

        // 1) Busca pelo messageId.
        if ($msgId) {
            $row = WhatsappMensagem::where('ds_message_id_wmm', $msgId)
                ->where('tp_direcao_wmm', 'O')
                ->first();
        }

        // 2) Busca pela última outbound recente sem id, mesmo destino e texto.
        if (!$row && $destino && $texto) {
            $sufixo = substr($destino, -10);
            $row = WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                ->where('tp_direcao_wmm', 'O')
                ->whereRaw("regexp_replace(nu_telefone_destino_wmm, '\\D', '', 'g') LIKE ?", ['%' . $sufixo])
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
            if ($dtEvt && !$row->dt_evento_wmm) {
                $row->dt_evento_wmm = $dtEvt;
            }
            $row->save();
            return;
        }

        // Sem correlação: rastro.
        WhatsappMensagem::create([
            'cd_conta_con'            => $conta->cd_conta_con,
            'tp_direcao_wmm'          => 'O',
            'nu_telefone_destino_wmm' => $destino,
            'ds_mensagem_wmm'         => $texto,
            'ds_tipo_wmm'             => 'text',
            'ds_message_id_wmm'       => $msgId,
            'ds_status_wmm'           => 'sent',
            'ds_payload_raw_wmm'      => $p,
            'dt_evento_wmm'           => $dtEvt,
        ]);
    }

    /**
     * Mensagem recebida (ReceivedCallback com fromMe=false):
     * cria linha 'I' e resolve correspondente/processo pelo telefone de origem.
     */
    private function tratarMensagemRecebida(array $p, $conta)
    {
        $msgId   = $p['messageId'] ?? null;
        $isGroup = (bool) ($p['isGroup'] ?? false);

        // Em grupos, phone = ID do grupo (ex: 5513991077083-1442944292).
        // O remetente real é participantPhone.
        // Em conversas 1-a-1, phone é o número do remetente.
        $origem = $isGroup
            ? $this->normalizarTelefone($p['participantPhone'] ?? null)
            : $this->normalizarTelefone($p['phone'] ?? null);

        $texto  = $p['text']['message']
               ?? ($p['image']['caption']    ?? null)
               ?? ($p['document']['caption'] ?? null)
               ?? null;
        $ts     = isset($p['momment']) ? (int) ($p['momment'] / 1000) : null;
        $dtEvt  = $ts ? Carbon::createFromTimestamp($ts) : null;

        // Sem nenhuma informação útil: ignora.
        if (empty($texto) && empty($origem) && empty($msgId)) {
            return;
        }

        // Idempotência: não duplica mesmo messageId.
        if ($msgId && WhatsappMensagem::where('ds_message_id_wmm', $msgId)->exists()) {
            return;
        }

        $cdCorrespondente = $origem
            ? $this->resolverCorrespondentePorTelefone($origem, $conta->cd_conta_con)
            : null;

        // Vínculo com processo: reply citado ou última outbound para o mesmo telefone.
        $cdProcesso      = null;
        $cdProcessoCkPck = null;
        $quotedMsgId     = $p['contextInfo']['quotedMessageId']
                        ?? ($p['contextInfo']['stanzaId'] ?? null);
        $janelaDias      = (int) config('zapi.inbound_link_window_days', 30);

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
            'cd_conta_con'            => $conta->cd_conta_con,
            'tp_direcao_wmm'          => 'I',
            'nu_telefone_origem_wmm'  => $origem,
            'ds_mensagem_wmm'         => $texto,
            'ds_tipo_wmm'             => $this->resolverTipoMensagem($p),
            'ds_message_id_wmm'       => $msgId,
            'ds_status_wmm'           => 'received',
            'ds_payload_raw_wmm'      => $p,
            'cd_correspondente_cor'   => $cdCorrespondente,
            'cd_processo_pro'         => $cdProcesso,
            'cd_processo_checkin_pck' => $cdProcessoCkPck,
            'dt_evento_wmm'           => $dtEvt,
        ]);
    }

    // -----------------------------------------------------------------
    // Utilitários
    // -----------------------------------------------------------------

    /**
     * Localiza a Conta pelo instanceId da Z-API.
     * Cai no modo single-tenant (primeira conta ativa) se não houver
     * correspondência direta.
     */
    private function resolverConta($instanceId)
    {
        if ($instanceId) {
            $conta = Conta::where('ds_zapi_instance_id_con', $instanceId)
                ->where('fl_zapi_ativo_con', true)
                ->first();
            if ($conta) {
                return $conta;
            }
        }
        // Fallback single-tenant.
        return Conta::where('fl_zapi_ativo_con', true)->orderBy('cd_conta_con')->first();
    }

    private function normalizarTelefone($numero)
    {
        if (!$numero) {
            return null;
        }
        // Remove tudo que não for dígito e limita a 20 chars (tamanho da coluna).
        // IDs de grupo (ex: 5513991077083-1442944292) ficam muito longos — são
        // descartados aqui pois o remetente real já vem em participantPhone.
        $n = preg_replace('/\D+/', '', (string) $numero);
        if (strlen($n) < 10 || strlen($n) > 20) {
            return null;
        }
        return $n;
    }

    private function ehDuplicateKeyError(\Throwable $e): bool
    {
        $msg = $e->getMessage();
        return str_contains($msg, '23505')
            || str_contains($msg, 'Unique violation')
            || str_contains($msg, 'duplicate key value');
    }

    /**
     * Determina o tipo de mensagem a partir das chaves presentes no payload.
     */
    private function resolverTipoMensagem(array $p)
    {
        foreach (['text', 'image', 'video', 'audio', 'document', 'sticker', 'contact', 'location'] as $tipo) {
            if (isset($p[$tipo])) {
                return $tipo;
            }
        }
        return 'message';
    }

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

        return $row ? [
            'cd_processo_pro'         => $row->cd_processo_pro,
            'cd_processo_checkin_pck' => $row->cd_processo_checkin_pck,
        ] : null;
    }
}
