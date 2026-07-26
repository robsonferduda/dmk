<?php

namespace App\Console\Commands;

use App\Conta;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\StatusProcesso;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ConsolidarPagamentosCorrespondente
 *
 * Consolida diariamente os pagamentos devidos aos correspondentes
 * no mês corrente (honorários + despesas reembolsáveis).
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

    protected $description = 'Consolida diariamente os pagamentos devidos aos correspondentes no mês corrente.';

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

        // Busca processos elegíveis (prazo fatal no mês, exceto cancelados)
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

        $processos = $query->get();

        if ($processos->isEmpty()) {
            $this->info('[consolidar] Nenhum processo encontrado para o período.');
            return 0;
        }

        // Agrupa por conta + correspondente
        $agrupado = $processos->groupBy(function ($row) {
            return $row->cd_conta_con . '_' . $row->cd_correspondente_cor;
        });

        $criados     = 0;
        $atualizados = 0;
        $ignorados   = 0;
        $descartados = 0;

        foreach ($agrupado as $chave => $itens) {
            $itens           = $itens->unique('cd_processo_pro')->values();
            $primeiro        = $itens->first();
            $cdConta         = $primeiro->cd_conta_con;
            $cdCorrespondente = $primeiro->cd_correspondente_cor;

            $valorTotal = $itens->sum(function ($i) {
                return (float) $i->vl_taxa_honorario_correspondente_pth + (float) $i->vl_despesa;
            });

            if ($dryRun) {
                $rotulo = $valorTotal > 0 ? 'DRY-RUN' : 'DRY-RUN (ignorado: total zero)';
                $this->line("  {$rotulo}  conta={$cdConta}  cor={$cdCorrespondente}  processos={$itens->count()}  total=R$ " . number_format($valorTotal, 2, ',', '.'));
                continue;
            }

            DB::transaction(function () use (
                $cdConta, $cdCorrespondente, $mes, $ano, $valorTotal, $itens,
                &$criados, &$atualizados, &$ignorados, &$descartados
            ) {
                // withTrashed: o índice único da competência não considera deleted_at
                $pagamento = PagamentoCorrespondente::withTrashed()->firstOrNew([
                    'cd_conta_con'          => $cdConta,
                    'cd_correspondente_cor' => $cdCorrespondente,
                    'nu_mes_pag'            => $mes,
                    'nu_ano_pag'            => $ano,
                ]);

                $isNovo = ! $pagamento->exists;

                // Sem valor a pagar: não cria cabeçalho e limpa o que estiver zerado em Gerado
                if ($valorTotal <= 0) {
                    if ($isNovo) {
                        $ignorados++;
                        return;
                    }

                    if ((int) $pagamento->cd_status_pag === StatusPagamentoCorrespondente::GERADO) {
                        PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)->delete();
                        $pagamento->forceDelete();
                        $descartados++;
                        return;
                    }

                    $ignorados++;
                    return;
                }

                // Só atualiza valor e itens se ainda estiver no status GERADO
                if ($isNovo || $pagamento->cd_status_pag === StatusPagamentoCorrespondente::GERADO) {
                    if (! $isNovo && $pagamento->trashed()) {
                        $pagamento->restore();
                    }

                    $pagamento->vl_total_pag   = $valorTotal;
                    $pagamento->cd_status_pag  = $pagamento->cd_status_pag ?? StatusPagamentoCorrespondente::GERADO;
                    $pagamento->save();

                    // Remove e recria os itens para refletir o estado atual do mês
                    PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)->delete();

                    foreach ($itens as $item) {
                        PagamentoCorrespondenteItem::create([
                            'cd_pagamento_correspondente_pag'  => $pagamento->cd_pagamento_correspondente_pag,
                            'cd_processo_pro'                  => $item->cd_processo_pro,
                            'cd_processo_taxa_honorario_pth'   => $item->cd_processo_taxa_honorario_pth,
                            'ds_descricao_pai'                 => $item->nu_processo_pro . ($item->nm_reu_pro ? ' - ' . $item->nm_reu_pro : ''),
                            'vl_honorario_pai'                 => (float) $item->vl_taxa_honorario_correspondente_pth,
                            'vl_despesa_pai'                   => (float) $item->vl_despesa,
                        ]);
                    }

                    $isNovo ? $criados++ : $atualizados++;
                }
            });
        }

        $resumo = "Criados={$criados}  Atualizados={$atualizados}  Ignorados(total zero)={$ignorados}  Descartados={$descartados}";

        $this->info("[consolidar] Concluído.  {$resumo}");
        Log::info("[pagamentos:consolidar] mes={$mes}/{$ano}  {$resumo}");

        return 0;
    }
}
