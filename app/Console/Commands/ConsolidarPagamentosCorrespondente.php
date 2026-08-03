<?php

namespace App\Console\Commands;

use App\PagamentoCorrespondente;
use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\StatusProcesso;
use App\Services\Pagamento\PagamentoCorrespondenteRefreshService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ConsolidarPagamentosCorrespondente
 *
 * Consolida os pagamentos devidos aos correspondentes na competência
 * (honorários + despesas reembolsáveis) e reconcilia os já existentes
 * (remove cancelados, atualiza valores — equivalente ao Atualizar Valores em lote).
 *
 * Agendamento: todo dia à meia-noite via Kernel.php
 *
 * Uso manual:
 *   php artisan pagamentos:consolidar
 *   php artisan pagamentos:consolidar --mes=5 --ano=2026
 *   php artisan pagamentos:consolidar --conta=64
 *   php artisan pagamentos:consolidar --dry-run
 */
class ConsolidarPagamentosCorrespondente extends Command
{
    protected $signature = 'pagamentos:consolidar
                            {--mes= : Mês de referência (1–12). Default: mês atual.}
                            {--ano= : Ano de referência. Default: ano atual.}
                            {--conta= : Restringe a um cd_conta_con específico.}
                            {--dry-run : Não persiste, apenas lista o que consolidaria.}';

    protected $description = 'Consolida e reconcilia os pagamentos de correspondentes na competência.';

    public function handle(): int
    {
        $hoje   = Carbon::now();
        $mes    = (int) ($this->option('mes')  ?: $hoje->month);
        $ano    = (int) ($this->option('ano')  ?: $hoje->year);
        $conta  = $this->option('conta');
        $dryRun = (bool) $this->option('dry-run');

        if ($mes < 1 || $mes > 12 || $ano < 2000) {
            $this->error('[consolidar] Competência inválida. Informe mês entre 1 e 12 e ano a partir de 2000.');
            return 1;
        }

        $competencia = Carbon::createFromDate($ano, $mes, 1)->startOfMonth();

        if ($competencia->gt($hoje->copy()->startOfMonth())) {
            $this->error('[consolidar] Não é permitido criar pagamentos para competências futuras.');
            return 1;
        }

        $dtInicio = Carbon::createFromDate($ano, $mes, 1)->startOfMonth()->toDateString();
        $dtFim    = Carbon::createFromDate($ano, $mes, 1)->endOfMonth()->toDateString();

        $this->info("[consolidar] mes={$mes}/{$ano}  dtInicio={$dtInicio}  dtFim={$dtFim}" . ($dryRun ? '  DRY-RUN' : ''));

        $processos = $this->buscarProcessosElegiveis($dtInicio, $dtFim, $conta);

        $criados       = 0;
        $cabecalhos    = 0;
        $reconciliados = 0;
        $ignorados     = 0;
        $pulados       = 0;
        $descartados   = 0;
        $removidos     = 0;
        $excluidos     = 0;

        // 1) Garante cabeçalhos para grupos com processos elegíveis
        $agrupado = $processos->groupBy(function ($row) {
            return $row->cd_conta_con . '_' . $row->cd_correspondente_cor;
        });

        if ($processos->isEmpty()) {
            $this->info('[consolidar] Nenhum processo elegível no período — seguirá reconciliando pagamentos existentes.');
        }

        foreach ($agrupado as $itens) {
            $itens            = $itens->unique('cd_processo_pro')->values();
            $primeiro         = $itens->first();
            $cdConta          = $primeiro->cd_conta_con;
            $cdCorrespondente = $primeiro->cd_correspondente_cor;

            $valorTotal = $itens->sum(function ($i) {
                return (float) $i->vl_taxa_honorario_correspondente_pth + (float) $i->vl_despesa;
            });

            if ($valorTotal <= 0) {
                $ignorados++;
                if ($dryRun) {
                    $this->line("  DRY-RUN (ignorado: total zero)  conta={$cdConta}  cor={$cdCorrespondente}");
                }
                continue;
            }

            if ($dryRun) {
                $this->line("  DRY-RUN  conta={$cdConta}  cor={$cdCorrespondente}  processos={$itens->count()}  total=R$ " . number_format($valorTotal, 2, ',', '.'));
                continue;
            }

            DB::transaction(function () use (
                $cdConta, $cdCorrespondente, $mes, $ano, &$criados, &$cabecalhos
            ) {
                $pagamento = PagamentoCorrespondente::withTrashed()->firstOrNew([
                    'cd_conta_con'          => $cdConta,
                    'cd_correspondente_cor' => $cdCorrespondente,
                    'nu_mes_pag'            => $mes,
                    'nu_ano_pag'            => $ano,
                ]);

                $isNovo = ! $pagamento->exists;

                // Cabeçalhos já avançados (enviado/aprovado/etc.) não são recriados aqui;
                // a reconciliação abaixo cuida dos itens quando permitido.
                if (! $isNovo && (int) $pagamento->cd_status_pag !== StatusPagamentoCorrespondente::GERADO) {
                    return;
                }

                if (! $isNovo && $pagamento->trashed()) {
                    $pagamento->restore();
                }

                if ($isNovo) {
                    $pagamento->cd_status_pag = StatusPagamentoCorrespondente::GERADO;
                    $pagamento->vl_total_pag  = 0;
                    $pagamento->save();
                    $criados++;
                } else {
                    $cabecalhos++;
                }
            });
        }

        // 2) Reconcilia todos os pagamentos da competência (remove cancelados, atualiza valores)
        if (! $dryRun) {
            $refresh = app(PagamentoCorrespondenteRefreshService::class);

            $pagamentosQuery = PagamentoCorrespondente::with('baixas')
                ->where('nu_mes_pag', $mes)
                ->where('nu_ano_pag', $ano);

            if ($conta) {
                $pagamentosQuery->where('cd_conta_con', $conta);
            }

            foreach ($pagamentosQuery->get() as $pagamento) {
                if (! $pagamento->podeAtualizarValores()) {
                    $pulados++;
                    continue;
                }

                $stats = $refresh->refreshPagamento($pagamento);

                if (isset($stats['erro'])) {
                    $pulados++;
                    continue;
                }

                $reconciliados++;
                $removidos   += (int) ($stats['removidos'] ?? 0);
                $excluidos   += (int) ($stats['excluidos'] ?? 0);

                if (! empty($stats['descartado'])) {
                    $descartados++;
                }
            }
        }

        $resumo = "Criados={$criados}  Cabeçalhos={$cabecalhos}  Reconciliados={$reconciliados}"
            . "  Removidos(itens)={$removidos}  Excluidos={$excluidos}"
            . "  Descartados={$descartados}  Pulados={$pulados}  Ignorados(total zero)={$ignorados}";

        $this->info("[consolidar] Concluído.  {$resumo}");
        Log::info("[pagamentos:consolidar] mes={$mes}/{$ano}  {$resumo}");

        return 0;
    }

    private function buscarProcessosElegiveis(string $dtInicio, string $dtFim, $conta)
    {
        $query = DB::table('processo_pro as t3')
            ->join('processo_taxa_honorario_pth as t5', function ($j) {
                $j->on('t3.cd_processo_pro', '=', 't5.cd_processo_pro')
                  ->whereNull('t5.deleted_at')
                  ->whereRaw('t5.cd_processo_taxa_honorario_pth = (
                      SELECT MAX(t5b.cd_processo_taxa_honorario_pth)
                      FROM processo_taxa_honorario_pth t5b
                      WHERE t5b.cd_processo_pro = t3.cd_processo_pro
                        AND t5b.deleted_at IS NULL
                  )');
            })
            ->join('conta_correspondente_ccr as t8', 't3.cd_correspondente_cor', '=', 't8.cd_correspondente_cor')
            ->leftJoin(
                DB::raw('(SELECT cd_processo_pro, COALESCE(SUM(vl_processo_despesa_pde),0) as vl_despesa
                          FROM processo_despesa_pde
                          WHERE fl_despesa_reembolsavel_pde = \'S\' AND cd_tipo_entidade_tpe = 6
                          GROUP BY cd_processo_pro) as desp'),
                'desp.cd_processo_pro', '=', 't3.cd_processo_pro'
            )
            ->whereNull('t3.deleted_at')
            ->whereBetween('t3.dt_prazo_fatal_pro', [$dtInicio, $dtFim])
            ->whereNotNull('t3.cd_correspondente_cor')
            ->whereNotIn('t3.cd_status_processo_stp', [
                StatusProcesso::CANCELADO,
                StatusProcesso::CANCELADO_PELO_ESCRITORIO,
            ])
            ->select(
                't3.cd_processo_pro',
                't3.nu_processo_pro',
                't3.cd_conta_con',
                't8.cd_correspondente_cor',
                't5.cd_processo_taxa_honorario_pth',
                't5.vl_taxa_honorario_correspondente_pth',
                DB::raw('COALESCE(desp.vl_despesa, 0) as vl_despesa'),
                't3.dt_prazo_fatal_pro',
                't3.nm_reu_pro'
            )
            ->orderBy('t8.cd_correspondente_cor')
            ->orderBy('t3.cd_processo_pro');

        if ($conta) {
            $query->where('t3.cd_conta_con', $conta);
        }

        return $query->get();
    }
}
