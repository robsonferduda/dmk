<?php

namespace App\Console\Commands;

use App\Conta;
use App\Processo;
use App\ContaCorrespondente;
use App\WhatsappMensagem;
use App\Services\ChatPro\ChatProClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * [WHATSAPP-LEMBRETE PRÉ-DILIGÊNCIA]
 * Comando diário que envia ao CORRESPONDENTE uma mensagem WhatsApp para
 * cada diligência cuja audiência é AMANHÃ, lembrando o correspondente.
 *
 * Agendamento padrão: todas as manhãs, via app/Console/Kernel.php.
 * Execução manual:
 *     php artisan whatsapp:lembrete-prediligencias
 *     php artisan whatsapp:lembrete-prediligencias --data=2026-05-16
 *     php artisan whatsapp:lembrete-prediligencias --dry-run
 *     php artisan whatsapp:lembrete-prediligencias --processo=97561
 */
class EnviarLembretesPreDiligencia extends Command
{
    protected $signature = 'whatsapp:lembrete-prediligencias
                            {--data= : Data alvo (Y-m-d). Default: amanhã.}
                            {--processo= : Limita a um cd_processo_pro específico (ignora filtro de data).}
                            {--force : Reenvia mesmo que já tenha sido enviado (útil em testes).}
                            {--dry-run : Não envia; apenas lista o que enviaria.}';

    protected $description = 'Envia lembretes de PRÉ-diligência via WhatsApp aos correspondentes (prazo fatal = amanhã).';

    public function handle()
    {
        $data       = $this->option('data') ? Carbon::parse($this->option('data'))->toDateString() : Carbon::tomorrow()->toDateString();
        $cdProcesso = $this->option('processo');
        $dryRun     = (bool) $this->option('dry-run');
        $force      = (bool) $this->option('force');

        if ($cdProcesso) {
            $this->info("[lembrete-pré] MODO TESTE: processo={$cdProcesso} (filtro de data ignorado)" . ($dryRun ? '  (DRY-RUN)' : ''));
        } else {
            $this->info("[lembrete-pré] Data alvo: {$data}" . ($dryRun ? '  (DRY-RUN)' : ''));
        }

        $q = Processo::whereNotNull('cd_correspondente_cor');
        if ($cdProcesso) {
            $q->where('cd_processo_pro', $cdProcesso);
        } else {
            $q->whereDate('dt_prazo_fatal_pro', $data);
        }

        $processos = $q->with('cliente', 'vara', 'cidade.estado')->get();

        $this->info('[lembrete-pré] Processos encontrados: ' . $processos->count());

        $enviados = 0; $ignorados = 0; $falhas = 0;

        foreach ($processos as $proc) {
            try {
                $conta = Conta::where('cd_conta_con', $proc->cd_conta_con)->first();
                if (!$conta) { $ignorados++; continue; }

                $client = ChatProClient::forConta($conta);
                if (!$client) {
                    $this->warn("  processo {$proc->cd_processo_pro}: conta {$conta->cd_conta_con} sem ChatPro ativo.");
                    $ignorados++; continue;
                }

                $contaCorrespondente = Conta::where('cd_conta_con', $proc->cd_correspondente_cor)->first();
                if (!$contaCorrespondente) { $ignorados++; continue; }

                $destino = $contaCorrespondente->nu_telefone_whatsapp_con ?? null;
                if (empty($destino)) {
                    $this->warn("  processo {$proc->cd_processo_pro}: correspondente {$proc->cd_correspondente_cor} sem WhatsApp.");
                    $ignorados++; continue;
                }

                if (!$force) {
                    $jaEnviado = WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                        ->where('cd_processo_pro', $proc->cd_processo_pro)
                        ->where('ds_tipo_wmm', 'lembrete_prediligencia')
                        ->whereDate('created_at', $data)
                        ->exists();
                    if ($jaEnviado) {
                        $this->line("  processo {$proc->cd_processo_pro}: lembrete pré já enviado, pulando (use --force para reenviar).");
                        $ignorados++; continue;
                    }
                }

                $mensagem = $this->montarMensagem($proc, $contaCorrespondente, $conta);

                $this->line("  -> {$proc->cd_processo_pro}  destino={$destino}");

                if ($dryRun) {
                    $this->line(str_repeat('-', 60));
                    $this->line($mensagem);
                    $this->line(str_repeat('-', 60));
                    $enviados++;
                    continue;
                }

                $res = $client->sendText($destino, $mensagem);

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
                    'ds_tipo_wmm'             => 'lembrete_prediligencia',
                    'ds_status_wmm'           => $res['success'] ? 'sent' : 'failed',
                    'cd_processo_pro'         => $proc->cd_processo_pro,
                    'cd_correspondente_cor'   => $proc->cd_correspondente_cor,
                    'dt_evento_wmm'           => now(),
                ];

                if ($msgId) {
                    $row = WhatsappMensagem::firstOrNew(['ds_message_id_wmm' => $msgId]);
                    foreach ($valores as $k => $v) {
                        if ($k === 'ds_status_wmm') {
                            $atual = $row->{$k};
                            if (in_array($atual, [null, '', 'pending', 'sent'], true)) {
                                $row->{$k} = $v;
                            }
                            continue;
                        }
                        if ($row->{$k} === null || $row->{$k} === '') {
                            $row->{$k} = $v;
                        }
                    }
                    if (empty($row->ds_payload_raw_wmm)) {
                        $row->ds_payload_raw_wmm = ['response' => $res, 'context' => 'lembrete_prediligencia'];
                    }
                    $row->save();
                } else {
                    $valores['ds_payload_raw_wmm'] = ['response' => $res, 'context' => 'lembrete_prediligencia'];
                    WhatsappMensagem::create($valores);
                }

                if ($res['success']) { $enviados++; } else { $falhas++; }
            } catch (\Throwable $e) {
                $falhas++;
                Log::error('[WHATSAPP-LEMBRETE-PRÉ] processo ' . $proc->cd_processo_pro . ': ' . $e->getMessage());
                $this->error('  processo ' . $proc->cd_processo_pro . ' falhou: ' . $e->getMessage());
            }
        }

        $this->info("[lembrete-pré] Concluído. Enviados={$enviados}  Ignorados={$ignorados}  Falhas={$falhas}");
        return 0;
    }

    /**
     * Monta a mensagem a partir do template lembrete_prediligencia.
     */
    private function montarMensagem(Processo $proc, Conta $contaCorrespondente, Conta $contaEscritorio)
    {
        $template = config('chatpro.templates.lembrete_prediligencia', '');
        // Gera token de confirmação se não existir
        $token = $proc->getOrCreateConfirmacaoAudienciaToken();
        $linkConfirmacao = url('/processo/confirmar-audiencia/' . $token);
        return strtr($template, [
            '{correspondente}'  => $contaCorrespondente->nm_conta_con ?? $contaCorrespondente->nm_razao_social_con ?? 'Correspondente',
            '{processo}'        => $proc->nu_processo_pro ?: ('#' . $proc->cd_processo_pro),
            '{reu}'             => $proc->nm_reu_pro ?: '—',
            '{vara}'            => ($proc->vara) ? ($proc->vara->nm_vara_var ?? '—') : '—',
            '{cidade}'          => ($proc->cidade) ? $proc->cidade->nm_cidade_cde . (isset($proc->cidade->estado) ? '/' . $proc->cidade->estado->sg_estado_est : '') : '—',
            '{data}'            => $proc->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($proc->dt_prazo_fatal_pro)) : '—',
            '{hora_audiencia}'  => $proc->hr_audiencia_pro ? date('H:i', strtotime($proc->hr_audiencia_pro)) : '—',
            '{link_confirmacao_audiencia}' => $linkConfirmacao,
        ]);
    }
}
