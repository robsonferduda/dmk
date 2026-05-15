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
            $ck = ProcessoCheckin::with('processo.cliente', 'processo.vara', 'processo.cidade.estado', 'processo.correspondente.contaCorrespondente')
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

            $destino = $conta->nu_telefone_whatsapp_con ?? null;
            if (empty($destino)) {
                Log::info('[CHECKIN-NOTIFY] Conta ' . $conta->cd_conta_con . ' sem telefone WhatsApp configurado.');
                return;
            }

            $mensagem = self::montarMensagem($ck);

            $res = $client->sendText($destino, $mensagem);
            if (!$res['success']) {
                Log::warning('[CHECKIN-NOTIFY] Falha ao enviar WhatsApp do check-in ' . $ck->cd_processo_checkin_pck . ': ' . ($res['message'] ?? '?'));
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

        $correspondente = '—';
        if ($proc && $proc->correspondente && $proc->correspondente->contaCorrespondente) {
            $correspondente = $proc->correspondente->contaCorrespondente->nm_conta_correspondente_ccr ?: '—';
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
