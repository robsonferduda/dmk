<?php

namespace App\Console\Commands;

use App\Conta;
use App\Processo;
use App\ContaCorrespondente;
use App\WhatsappMensagem;
use App\Services\WhatsappDispatcher;
use App\Support\DiasUteis;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
                            {--data= : Data alvo (Y-m-d). Default: de amanhã até o próximo dia útil.}
                            {--processo= : Limita a um cd_processo_pro específico (ignora filtro de data).}
                            {--conta= : Limita a um cd_conta_con específico (útil em testes por escritório).}
                            {--force : Reenvia mesmo que já tenha sido enviado (útil em testes).}
                            {--dry-run : Não envia; apenas lista o que enviaria com o corpo da mensagem.}
                            {--list : Exibe tabela compacta dos processos que seriam notificados (sem enviar).}';

    protected $description = 'Envia lembretes de PRÉ-diligência via WhatsApp aos correspondentes (audiências até o próximo dia útil, um aviso por audiência).';

    public function handle()
    {
        // Sem --data: de amanhã até o próximo dia útil (sex → sáb, dom e seg; véspera de feriado inclui o feriado).
        // Quem já foi avisado para a mesma audiência não recebe de novo, então rodar todo dia não repete mensagem.
        if ($this->option('data')) {
            $inicio = $fim = Carbon::parse($this->option('data'))->toDateString();
        } else {
            [$inicio, $fim] = array_map(function (Carbon $d) { return $d->toDateString(); }, self::periodoAlvo());
        }
        $data = $inicio === $fim ? $inicio : "{$inicio}..{$fim}";
        $cdProcesso = $this->option('processo');
        $cdConta    = $this->option('conta');
        $dryRun     = (bool) $this->option('dry-run');
        $list       = (bool) $this->option('list');
        $force      = (bool) $this->option('force');

        $contexto = [];
        if ($cdProcesso) { $contexto[] = "processo={$cdProcesso} (filtro de data ignorado)"; }
        else             { $contexto[] = "data={$data}"; }
        if ($cdConta)    { $contexto[] = "conta={$cdConta}"; }
        if ($dryRun)     { $contexto[] = 'DRY-RUN'; }
        if ($list)       { $contexto[] = 'LIST'; }
        $this->info('[lembrete-pré] ' . implode('  ', $contexto));

        $q = Processo::whereNotNull('cd_correspondente_cor');
        if ($cdProcesso) {
            $q->where('cd_processo_pro', $cdProcesso);
        } else {
            $q->whereDate('dt_prazo_fatal_pro', '>=', $inicio)->whereDate('dt_prazo_fatal_pro', '<=', $fim);
        }
        if ($cdConta) {
            $q->where('cd_conta_con', $cdConta);
        }

        $processos = $q->with('cliente', 'vara', 'cidade.estado', 'status', 'honorario.tipoServicoCorrespondente')->get();

        $this->info('[lembrete-pré] Processos encontrados: ' . $processos->count());

        // --list: tabela compacta sem enviar nada
        if ($list) {
            $linhas = [];
            foreach ($processos as $proc) {
                $escritorio        = Conta::find($proc->cd_conta_con);
                $correspondente    = Conta::find($proc->cd_correspondente_cor);
                $whatsappOk        = $escritorio && WhatsappDispatcher::forConta($escritorio);
                $whatsapp          = $correspondente->nu_telefone_whatsapp_con ?? null;
                $jaEnviado         = self::avisosDaAudiencia($proc->cd_conta_con, [$proc])->has($proc->cd_processo_pro);

                if (!$whatsappOk)       { $situacao = 'SEM WHATSAPP'; }
                elseif (!$correspondente) { $situacao = 'SEM CORRESPONDENTE'; }
                elseif (empty($whatsapp)) { $situacao = 'SEM WHATSAPP'; }
                elseif ($jaEnviado)     { $situacao = 'JA ENVIADO'; }
                else                    { $situacao = 'ENVIARA'; }

                $linhas[] = [
                    $proc->cd_processo_pro,
                    $proc->nu_processo_pro ?: '-',
                    $proc->status->nm_status_processo_conta_stp ?? '-',
                    $proc->dt_prazo_fatal_pro ?? '-',
                    $proc->hr_audiencia_pro  ?? '-',
                    $correspondente->nm_razao_social_con ?? $correspondente->nm_conta_con ?? '-',
                    $whatsapp ?: '-',
                    $situacao,
                ];
            }
            usort($linhas, fn($a, $b) => strcmp($a[5], $b[5]));
            $this->table(
                ['ID', 'Nº Processo', 'Status', 'Data', 'Hora', 'Correspondente', 'WhatsApp', 'Situação'],
                $linhas
            );
            return 0;
        }

        $enviados = 0; $ignorados = 0; $falhas = 0;

        foreach ($processos as $proc) {
            try {
                $conta = Conta::where('cd_conta_con', $proc->cd_conta_con)->first();
                if (!$conta) { $ignorados++; continue; }

                $client = WhatsappDispatcher::forConta($conta);
                if (!$client) {
                    $this->warn("  processo {$proc->cd_processo_pro}: conta {$conta->cd_conta_con} sem integração WhatsApp ativa (Z-API ou ChatPro).");
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
                    $jaEnviado = self::avisosDaAudiencia($conta->cd_conta_con, [$proc])->has($proc->cd_processo_pro);
                    if ($jaEnviado) {
                        $this->line("  processo {$proc->cd_processo_pro}: lembrete pré já enviado, pulando (use --force para reenviar).");
                        $ignorados++; continue;
                    }
                }


                $mensagem = $this->montarMensagem($proc, $contaCorrespondente, $conta);

                // Log de depuração após montar a mensagem
                \Log::info('[LEMBRETE-PRÉ] Enviando mensagem', [
                    'processo' => $proc->cd_processo_pro,
                    'destino' => $destino,
                    'mensagem' => $mensagem
                ]);

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
                    // Z-API retorna messageId/id; ChatPro usava resposeMessage.id
                    $msgId = $res['body']['messageId']
                          ?? $res['body']['id']
                          ?? $res['body']['resposeMessage']['id']
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

                // Remove registros anteriores do mesmo processo/dia (reenvio gera novo messageId).
                WhatsappMensagem::where('cd_conta_con', $conta->cd_conta_con)
                    ->where('cd_processo_pro', $proc->cd_processo_pro)
                    ->where('ds_tipo_wmm', 'lembrete_prediligencia')
                    ->whereDate('created_at', Carbon::today())
                    ->delete();

                $valores['ds_message_id_wmm']  = $msgId;
                $valores['ds_payload_raw_wmm'] = ['response' => $res, 'context' => 'lembrete_prediligencia'];
                WhatsappMensagem::create($valores);

                if ($res['success']) { $enviados++; } else { $falhas++; }

                // Pausa entre envios para evitar sobrecarga na sessão ChatPro ("database is locked").
                if (!$dryRun) {
                    usleep(1_500_000); // 1,5 s
                }
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
     * Audiências cobertas por uma execução sem --data: de amanhã até o próximo dia útil.
     *
     * @return Carbon[] [início, fim]
     */
    public static function periodoAlvo(?Carbon $hoje = null): array
    {
        $hoje = ($hoje ?: Carbon::today())->copy()->startOfDay();

        return [$hoje->copy()->addDay(), DiasUteis::proximo($hoje)];
    }

    /**
     * Último lembrete pré de cada processo enviado para a audiência atual, por cd_processo_pro.
     * Só contam envios a partir do dia útil anterior à audiência: se ela for remarcada, o aviso antigo não vale.
     * Falhas não contam (por padrão), para serem tentadas de novo na próxima execução.
     */
    public static function avisosDaAudiencia($cdConta, iterable $processos, bool $incluirFalhas = false): Collection
    {
        $avisos = collect();
        foreach ($processos as $proc) {
            $desde = $proc->dt_prazo_fatal_pro
                ? DiasUteis::anterior(Carbon::parse($proc->dt_prazo_fatal_pro))
                : Carbon::today();

            $consulta = WhatsappMensagem::where('cd_conta_con', $cdConta)
                ->where('cd_processo_pro', $proc->cd_processo_pro)
                ->where('ds_tipo_wmm', 'lembrete_prediligencia')
                ->where('created_at', '>=', $desde);
            if (!$incluirFalhas) {
                $consulta->where(function ($q) {
                    $q->whereNull('ds_status_wmm')->orWhere('ds_status_wmm', '<>', 'failed');
                });
            }

            $aviso = $consulta->latest()->first();
            if ($aviso) {
                $avisos->put($proc->cd_processo_pro, $aviso);
            }
        }

        return $avisos;
    }

    /**
     * Monta a mensagem a partir do template lembrete_prediligencia.
     */
    private function montarMensagem(Processo $proc, Conta $contaCorrespondente, Conta $contaEscritorio)
    {
        $template = config('chatpro.templates.lembrete_prediligencia', '');
        \Log::info('[LEMBRETE-PRÉ] Template carregado em montarMensagem', [
            'processo' => $proc->cd_processo_pro,
            'template' => $template
        ]);
        // Gera token de confirmação se não existir
        $token = $proc->getOrCreateConfirmacaoAudienciaToken();
        $linkConfirmacao = url('/processo/confirmar-audiencia/' . $token);

        $resp = [];
        if (!empty($proc->nm_preposto_pro))  $resp[] = "👷 Preposto: {$proc->nm_preposto_pro}";
        if (!empty($proc->nm_advogado_pro))  $resp[] = "⚖️ Advogado: {$proc->nm_advogado_pro}";
        $responsaveis = $resp ? implode("\n", $resp) . "\n" : '';

        return strtr($template, [
            '{correspondente}'  => $contaCorrespondente->nm_conta_con ?? $contaCorrespondente->nm_razao_social_con ?? 'Correspondente',
            '{processo}'        => $proc->nu_processo_pro ?: ('#' . $proc->cd_processo_pro),
            '{autor}'           => $proc->nm_autor_pro ?: '—',
            '{reu}'             => $proc->nm_reu_pro ?: '—',
            '{vara}'            => ($proc->vara) ? ($proc->vara->nm_vara_var ?? '—') : '—',
            '{cidade}'          => ($proc->cidade) ? $proc->cidade->nm_cidade_cde . (isset($proc->cidade->estado) ? '/' . $proc->cidade->estado->sg_estado_est : '') : '—',
            '{data}'            => $proc->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($proc->dt_prazo_fatal_pro)) : '—',
            '{hora_audiencia}'  => $proc->hr_audiencia_pro ? date('H:i', strtotime($proc->hr_audiencia_pro)) : '—',
            '{tipo_servico}'    => optional(optional($proc->honorario)->tipoServicoCorrespondente)->nm_tipo_servico_tse ?? '—',
            '{responsaveis}'    => $responsaveis,
            '{link_confirmacao_audiencia}' => $linkConfirmacao,
        ]);
    }
}
