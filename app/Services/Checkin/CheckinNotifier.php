<?php

namespace App\Services\Checkin;

use App\Conta;
use App\ProcessoCheckin;
use App\Services\ChatPro\ChatProClient;
use Illuminate\Support\Facades\Log;

/**
 * [CHECK-IN]
 * Notificações disparadas quando um check-in é registrado.
 *
 * Mantido fire-and-forget e tolerante a falhas: nunca propaga exceções
 * para que o fluxo do correspondente não quebre por problemas no
 * WhatsApp/ChatPro.
 *
 * Hoje: envia 1 mensagem WhatsApp para o telefone da conta.
 * Futuro: ponto único para acrescentar outros efeitos (e-mail, push,
 * atualização de status, log de auditoria etc.).
 */
class CheckinNotifier
{
    /**
     * Notifica todos os destinos configurados a partir do ID do check-in.
     * Recebe ID (e não objeto) porque é tipicamente chamado em
     * app()->terminating(), depois da resposta HTTP — é mais seguro
     * recarregar do banco do que carregar um objeto stale.
     */
    public static function notificar($cdProcessoCheckinPck)
    {
        try {
            $ck = ProcessoCheckin::with('processo.cliente', 'processo.vara', 'processo.cidade.estado', 'processo.correspondente')
                ->where('cd_processo_checkin_pck', $cdProcessoCheckinPck)
                ->first();

            if (!$ck || !$ck->processo) {
                return;
            }

            $conta = Conta::where('cd_conta_con', $ck->cd_conta_con)->first();
            if (!$conta) {
                return;
            }

            $client = ChatProClient::forConta($conta);
            if (!$client) {
                // Integração não habilitada/configurada para a conta — silencioso.
                return;
            }

            // [CHECK-IN] Confirmação do check-in vai para o ESCRITÓRIO
            // (a notificação ao correspondente é feita ANTES, pelo lembrete
            // diário de diligência).
            $destino = $conta->nu_telefone_whatsapp_con ?? null;
            if (empty($destino)) {
                Log::info('[CHECKIN-NOTIFY] Escritório (conta ' . $conta->cd_conta_con . ') sem telefone WhatsApp configurado.');
                return;
            }

            $mensagem = self::montarMensagem($ck);

            $res = $client->sendText($destino, $mensagem);
            if (!$res['success']) {
                Log::warning('[CHECKIN-NOTIFY] Falha ao enviar WhatsApp do check-in ' . $ck->cd_processo_checkin_pck . ': ' . ($res['message'] ?? '?'));
            }

            // [WHATSAPP-LOG] Persiste o envio no histórico unificado. Usamos
            // updateOrCreate keyed pelo message_id porque o webhook do
            // ChatPro (eco do envio) costuma chegar ANTES desta linha
            // ser gravada — quando isso acontece, ele já criou uma linha
            // outbound com o mesmo id, e nós aqui apenas enriquecemos
            // (preenchemos cd_processo_pro / cd_correspondente_cor / etc.).
            try {
                // ID retornado pela API (chave "resposeMessage.id" — sic, sem 'n').
                $msgId = null;
                if (!empty($res['body']) && is_array($res['body'])) {
                    $msgId = $res['body']['resposeMessage']['id']
                          ?? $res['body']['responseMessage']['id']
                          ?? $res['body']['message_id']
                          ?? null;
                }

                $valores = [
                    'cd_conta_con'            => $conta->cd_conta_con,
                    'tp_direcao_wmm'          => 'O',
                    'nu_telefone_origem_wmm'  => null,
                    'nu_telefone_destino_wmm' => preg_replace('/\D+/', '', (string) $destino),
                    'ds_mensagem_wmm'         => $mensagem,
                    'ds_tipo_wmm'             => 'text',
                    'ds_status_wmm'           => $res['success'] ? 'sent' : 'failed',
                    'cd_processo_pro'         => $ck->cd_processo_pro,
                    'cd_processo_checkin_pck' => $ck->cd_processo_checkin_pck,
                    'cd_correspondente_cor'   => $ck->processo ? $ck->processo->cd_correspondente_cor : null,
                    'dt_evento_wmm'           => now(),
                ];

                if ($msgId) {
                    // Existe → atualiza enriquecendo. Não existe → cria.
                    $row = \App\WhatsappMensagem::firstOrNew(['ds_message_id_wmm' => $msgId]);
                    foreach ($valores as $k => $v) {
                        // Para status, só sobrescreve se a coluna ainda está
                        // sem nada ou em "sent"/"pending" — não regredimos
                        // de "delivered/read" para "sent".
                        if ($k === 'ds_status_wmm') {
                            $atual = $row->{$k};
                            if (in_array($atual, [null, '', 'pending', 'sent'], true)) {
                                $row->{$k} = $v;
                            }
                            continue;
                        }
                        // Para os demais campos, preenche se ainda não tem;
                        // não sobrescreve dado já gravado pelo webhook.
                        if ($row->{$k} === null || $row->{$k} === '') {
                            $row->{$k} = $v;
                        }
                    }
                    // Payload só grava se não houver (não sobrescreve o do eco).
                    if (empty($row->ds_payload_raw_wmm)) {
                        $row->ds_payload_raw_wmm = ['response' => $res, 'context' => 'checkin'];
                    }
                    $row->save();
                } else {
                    // Sem id ainda? Insere e o eco/ack vai casar pelo
                    // fallback (destino + texto + recent + null id).
                    $valores['ds_payload_raw_wmm'] = ['response' => $res, 'context' => 'checkin'];
                    \App\WhatsappMensagem::create($valores);
                }
            } catch (\Throwable $e) {
                Log::warning('[CHECKIN-NOTIFY] Falha ao logar mensagem WhatsApp: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            Log::error('[CHECKIN-NOTIFY] Exceção: ' . $e->getMessage());
        }
    }

    /**
     * Monta a mensagem a partir do template em config/chatpro.php.
     */
    private static function montarMensagem(ProcessoCheckin $ck)
    {
        $template = config('chatpro.templates.checkin', '');

        $proc = $ck->processo;

        // O nome do correspondente vem de conta_correspondente_ccr (vínculo
        // correspondente↔escritório). NÃO usamos $proc->correspondente->contaCorrespondente
        // porque essa relação é filtrada por \Session::get('SESSION_CD_CONTA') e
        // aqui rodamos pós-resposta, com o correspondente logado (sessão != escritório).
        $correspondente = '—';
        if ($proc && $proc->cd_correspondente_cor && $ck->cd_conta_con) {
            $cc = \App\ContaCorrespondente::where('cd_correspondente_cor', $proc->cd_correspondente_cor)
                ->where('cd_conta_con', $ck->cd_conta_con)
                ->first();
            if ($cc && !empty($cc->nm_conta_correspondente_ccr)) {
                $correspondente = $cc->nm_conta_correspondente_ccr;
            }
        }

        $cliente = ($proc && $proc->cliente) ? $proc->cliente->nm_razao_social_cli : '—';

        $coords = '—';
        $maps   = '—';
        if ($ck->nu_latitude_pck && $ck->nu_longitude_pck) {
            $coords = number_format($ck->nu_latitude_pck, 6, '.', '')
                    . ', ' . number_format($ck->nu_longitude_pck, 6, '.', '');
            if ($ck->nu_precisao_metros_pck) {
                $coords .= ' (±' . (int) $ck->nu_precisao_metros_pck . 'm)';
            }
            $maps = 'https://maps.google.com/?q=' . $ck->nu_latitude_pck . ',' . $ck->nu_longitude_pck;
        }

        $datahora = \Carbon\Carbon::parse($ck->dt_checkin_pck)->format('d/m/Y H:i');

        $vars = [
            '{processo}'       => $proc->nu_processo_pro ?? '—',
            '{correspondente}' => $correspondente,
            '{cliente}'        => $cliente,
            '{coordenadas}'    => $coords,
            '{maps_url}'       => $maps,
            '{datahora}'       => $datahora,
            '{vara}'           => optional($proc->vara)->nm_vara_var ?: '—',
            '{cidade}'         => optional(optional($proc->cidade))->nm_cidade_cde ?: '—',
        ];

        return strtr($template, $vars);
    }
}
