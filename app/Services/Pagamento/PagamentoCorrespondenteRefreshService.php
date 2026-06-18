<?php

namespace App\Services\Pagamento;

use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\StatusProcesso;
use App\Enums\TipoEntidade;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\Processo;
use App\ProcessoDespesa;
use App\ProcessoTaxaHonorario;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PagamentoCorrespondenteRefreshService
{
    private const STATUS_PAGAMENTO_EDITAVEL = [
        StatusPagamentoCorrespondente::GERADO,
        StatusPagamentoCorrespondente::ENVIADO_APROVACAO,
        StatusPagamentoCorrespondente::APROVADO,
        StatusPagamentoCorrespondente::RECUSADO,
    ];

    private const STATUS_PROCESSO_CANCELADO = [
        StatusProcesso::CANCELADO,
        StatusProcesso::CANCELADO_PELO_ESCRITORIO,
    ];

    /**
     * Reconcilia um pagamento com os processos elegíveis do mês/competência.
     */
    public function refreshPagamento(PagamentoCorrespondente $pagamento): array
    {
        if ($pagamento->cd_status_pag === StatusPagamentoCorrespondente::PAGO) {
            return ['erro' => 'Pagamento já efetuado e não pode ser atualizado.'];
        }

        $stats = ['adicionados' => 0, 'atualizados' => 0, 'removidos' => 0, 'excluidos' => 0, 'duplicados' => 0];

        DB::transaction(function () use ($pagamento, &$stats) {
            $stats['duplicados'] = $this->deduplicarItensPagamento($pagamento);

            $elegiveis = $this->buscarProcessosElegiveis(
                (int) $pagamento->cd_conta_con,
                (int) $pagamento->cd_correspondente_cor,
                (int) $pagamento->nu_mes_pag,
                (int) $pagamento->nu_ano_pag
            );

            $elegiveisPorId = $elegiveis->keyBy('cd_processo_pro');
            $itens = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)->get();

            foreach ($itens as $item) {
                if (! $item->cd_processo_pro) {
                    continue;
                }

                $dados = $elegiveisPorId->get($item->cd_processo_pro);

                if (! $dados) {
                    $item->delete();
                    $stats['removidos']++;
                    continue;
                }

                $cancelado = $this->processoCancelado((int) $dados->cd_status_processo_stp);
                $eraExcluido = $this->itemExcluido($item);

                if ($this->aplicarValoresItem($item, $dados)) {
                    $stats['atualizados']++;
                }

                if ($cancelado && ! $eraExcluido) {
                    $item->fl_excluido_pai = 'S';
                    $item->save();
                    $stats['excluidos']++;
                }
            }

            $idsComItem = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)
                ->whereNotNull('cd_processo_pro')
                ->pluck('cd_processo_pro')
                ->unique()
                ->values()
                ->all();

            foreach ($elegiveis as $dados) {
                if (in_array($dados->cd_processo_pro, $idsComItem, true)) {
                    continue;
                }

                $cancelado = $this->processoCancelado((int) $dados->cd_status_processo_stp);

                PagamentoCorrespondenteItem::create([
                    'cd_pagamento_correspondente_pag'  => $pagamento->cd_pagamento_correspondente_pag,
                    'cd_processo_pro'                  => $dados->cd_processo_pro,
                    'cd_processo_taxa_honorario_pth'   => $dados->cd_processo_taxa_honorario_pth,
                    'ds_descricao_pai'                 => $this->montarDescricao($dados),
                    'vl_honorario_pai'                 => (float) $dados->vl_taxa_honorario_correspondente_pth,
                    'vl_despesa_pai'                   => (float) $dados->vl_despesa,
                    'fl_excluido_pai'                  => $cancelado ? 'S' : 'N',
                ]);

                $idsComItem[] = $dados->cd_processo_pro;
                $stats['adicionados']++;
                if ($cancelado) {
                    $stats['excluidos']++;
                }
            }

            $stats['duplicados'] += $this->deduplicarItensPagamento($pagamento);

            $this->recalcularTotal($pagamento);
        });

        return $stats;
    }

    /**
     * Sincroniza um processo em todos os pagamentos relacionados (honorário, despesa, correspondente).
     */
    public function syncProcesso(int $cdProcessoPro): void
    {
        $processo = Processo::where('cd_processo_pro', $cdProcessoPro)->whereNull('deleted_at')->first();

        if (! $processo) {
            $this->removerItensProcesso($cdProcessoPro);
            return;
        }

        $itens = PagamentoCorrespondenteItem::where('cd_processo_pro', $cdProcessoPro)->get();

        foreach ($itens as $item) {
            $pagamento = PagamentoCorrespondente::find($item->cd_pagamento_correspondente_pag);

            if (! $pagamento || ! $this->pagamentoEditavel($pagamento)) {
                continue;
            }

            if (! $this->itemPertenceAoPagamento($processo, $pagamento)) {
                $item->delete();
                $this->recalcularTotal($pagamento);
            }
        }

        if (! $this->processoTemHonorario($cdProcessoPro)) {
            $this->removerItensProcesso($cdProcessoPro);
            return;
        }

        if ($this->processoCancelado((int) $processo->cd_status_processo_stp)) {
            $this->marcarItensCancelados($processo);
            return;
        }

        if (! $processo->cd_correspondente_cor || ! $processo->dt_prazo_fatal_pro) {
            $this->removerItensProcesso($cdProcessoPro);
            return;
        }

        $pagamento = $this->resolverPagamento($processo);

        if (! $pagamento) {
            return;
        }

        $item = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)
            ->where('cd_processo_pro', $cdProcessoPro)
            ->orderBy('cd_pagamento_correspondente_item_pai')
            ->first();

        $valores = $this->getValoresProcesso($cdProcessoPro);

        if (! $item) {
            PagamentoCorrespondenteItem::create([
                'cd_pagamento_correspondente_pag'  => $pagamento->cd_pagamento_correspondente_pag,
                'cd_processo_pro'                  => $cdProcessoPro,
                'cd_processo_taxa_honorario_pth'   => $valores['cd_pth'],
                'ds_descricao_pai'                 => $this->montarDescricaoProcesso($processo),
                'vl_honorario_pai'                 => $valores['honorario'],
                'vl_despesa_pai'                   => $valores['despesa'],
                'fl_excluido_pai'                  => 'N',
            ]);
        } else {
            $item->vl_honorario_pai = $valores['honorario'];
            $item->vl_despesa_pai   = $valores['despesa'];
            $item->ds_descricao_pai = $this->montarDescricaoProcesso($processo);

            if ($valores['cd_pth']) {
                $item->cd_processo_taxa_honorario_pth = $valores['cd_pth'];
            }

            $item->save();
        }

        $this->deduplicarItensPagamento($pagamento);
        $this->recalcularTotal($pagamento);
    }

    /**
     * Query base de processos elegíveis para compor pagamentos (prazo fatal na competência).
     */
    public function buscarProcessosElegiveis(int $cdConta, int $cdCorrespondente, int $mes, int $ano): Collection
    {
        $dtInicio = Carbon::createFromDate($ano, $mes, 1)->startOfMonth()->toDateString();
        $dtFim    = Carbon::createFromDate($ano, $mes, 1)->endOfMonth()->toDateString();

        return DB::table('processo_pro as t3')
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
                          WHERE fl_despesa_reembolsavel_pde = \'S\' AND cd_tipo_entidade_tpe = ' . TipoEntidade::CORRESPONDENTE . '
                          GROUP BY cd_processo_pro) as desp'),
                'desp.cd_processo_pro', '=', 't3.cd_processo_pro'
            )
            ->whereNull('t3.deleted_at')
            ->whereBetween('t3.dt_prazo_fatal_pro', [$dtInicio, $dtFim])
            ->whereNotNull('t3.cd_correspondente_cor')
            ->where('t3.cd_conta_con', $cdConta)
            ->where('t8.cd_correspondente_cor', $cdCorrespondente)
            ->whereNotIn('t3.cd_status_processo_stp', self::STATUS_PROCESSO_CANCELADO)
            ->select(
                't3.cd_processo_pro',
                't3.nu_processo_pro',
                't3.cd_status_processo_stp',
                't3.cd_conta_con',
                't8.cd_correspondente_cor',
                't5.cd_processo_taxa_honorario_pth',
                't5.vl_taxa_honorario_correspondente_pth',
                DB::raw('COALESCE(desp.vl_despesa, 0) as vl_despesa'),
                't3.dt_prazo_fatal_pro',
                't3.nm_reu_pro'
            )
            ->orderBy('t3.cd_processo_pro')
            ->get()
            ->unique('cd_processo_pro')
            ->values();
    }

    /**
     * Remove itens duplicados do mesmo processo no pagamento (mantém um registro).
     */
    public function deduplicarItensPagamento(PagamentoCorrespondente $pagamento): int
    {
        $removidos = 0;

        $itens = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)
            ->whereNotNull('cd_processo_pro')
            ->orderBy('cd_pagamento_correspondente_item_pai')
            ->get()
            ->groupBy('cd_processo_pro');

        foreach ($itens as $grupo) {
            if ($grupo->count() <= 1) {
                continue;
            }

            $manter = $grupo->first(function ($item) {
                return ! $this->itemExcluido($item);
            }) ?? $grupo->first();

            foreach ($grupo as $item) {
                if ((int) $item->cd_pagamento_correspondente_item_pai === (int) $manter->cd_pagamento_correspondente_item_pai) {
                    continue;
                }

                $item->delete();
                $removidos++;
            }
        }

        return $removidos;
    }

    public function recalcularTotal(PagamentoCorrespondente $pagamento): void
    {
        $total = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)
            ->get()
            ->sum(function ($item) {
                if ($this->itemExcluido($item)) {
                    return 0;
                }

                return (float) $item->vl_honorario_pai + (float) $item->vl_despesa_pai;
            });

        $pagamento->vl_total_pag = $total;
        $pagamento->save();
    }

    public function itemExcluido(PagamentoCorrespondenteItem $item): bool
    {
        return strtoupper((string) ($item->fl_excluido_pai ?? 'N')) === 'S';
    }

    private function pagamentoEditavel(PagamentoCorrespondente $pagamento): bool
    {
        return in_array($pagamento->cd_status_pag, self::STATUS_PAGAMENTO_EDITAVEL, true);
    }

    private function processoCancelado(int $status): bool
    {
        return in_array($status, self::STATUS_PROCESSO_CANCELADO, true);
    }

    private function processoTemHonorario(int $cdProcessoPro): bool
    {
        return ProcessoTaxaHonorario::where('cd_processo_pro', $cdProcessoPro)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function itemPertenceAoPagamento(Processo $processo, PagamentoCorrespondente $pagamento): bool
    {
        if ((int) $processo->cd_conta_con !== (int) $pagamento->cd_conta_con) {
            return false;
        }

        if ((int) $processo->cd_correspondente_cor !== (int) $pagamento->cd_correspondente_cor) {
            return false;
        }

        if (! $processo->dt_prazo_fatal_pro) {
            return false;
        }

        $dt = Carbon::parse($processo->dt_prazo_fatal_pro);

        return (int) $dt->month === (int) $pagamento->nu_mes_pag
            && (int) $dt->year === (int) $pagamento->nu_ano_pag;
    }

    private function resolverPagamento(Processo $processo): ?PagamentoCorrespondente
    {
        $dt = Carbon::parse($processo->dt_prazo_fatal_pro);

        $pagamento = PagamentoCorrespondente::firstOrCreate(
            [
                'cd_conta_con'          => $processo->cd_conta_con,
                'cd_correspondente_cor' => $processo->cd_correspondente_cor,
                'nu_mes_pag'            => $dt->month,
                'nu_ano_pag'            => $dt->year,
            ],
            [
                'vl_total_pag'  => 0,
                'cd_status_pag' => StatusPagamentoCorrespondente::GERADO,
            ]
        );

        if ($pagamento->cd_status_pag === StatusPagamentoCorrespondente::PAGO) {
            return null;
        }

        return $pagamento;
    }

    private function getValoresProcesso(int $cdProcessoPro): array
    {
        $honorario = ProcessoTaxaHonorario::where('cd_processo_pro', $cdProcessoPro)->whereNull('deleted_at')->first();

        return [
            'honorario' => (float) ($honorario->vl_taxa_honorario_correspondente_pth ?? 0),
            'despesa'   => $this->sumDespesasCorrespondente($cdProcessoPro),
            'cd_pth'    => $honorario->cd_processo_taxa_honorario_pth ?? null,
        ];
    }

    private function sumDespesasCorrespondente(int $cdProcessoPro): float
    {
        return (float) ProcessoDespesa::where('cd_processo_pro', $cdProcessoPro)
            ->where('fl_despesa_reembolsavel_pde', 'S')
            ->where('cd_tipo_entidade_tpe', TipoEntidade::CORRESPONDENTE)
            ->sum('vl_processo_despesa_pde');
    }

    private function aplicarValoresItem(PagamentoCorrespondenteItem $item, $dados): bool
    {
        $honorario = (float) $dados->vl_taxa_honorario_correspondente_pth;
        $despesa   = (float) $dados->vl_despesa;
        $descricao = $this->montarDescricao($dados);

        $alterado = (float) $item->vl_honorario_pai !== $honorario
            || (float) $item->vl_despesa_pai !== $despesa
            || (string) $item->ds_descricao_pai !== $descricao;

        $item->vl_honorario_pai = $honorario;
        $item->vl_despesa_pai   = $despesa;
        $item->ds_descricao_pai = $descricao;

        if ($dados->cd_processo_taxa_honorario_pth) {
            $item->cd_processo_taxa_honorario_pth = $dados->cd_processo_taxa_honorario_pth;
        }

        $item->save();

        return $alterado;
    }

    private function montarDescricao($dados): string
    {
        return $this->montarDescricaoProcesso(
            (object) ['nu_processo_pro' => $dados->nu_processo_pro, 'nm_reu_pro' => $dados->nm_reu_pro ?? null]
        );
    }

    private function montarDescricaoProcesso($processo): string
    {
        $desc = $processo->nu_processo_pro ?? '';

        if (! empty($processo->nm_reu_pro)) {
            $desc .= ' - ' . $processo->nm_reu_pro;
        }

        return $desc;
    }

    private function removerItensProcesso(int $cdProcessoPro): void
    {
        $itens = PagamentoCorrespondenteItem::where('cd_processo_pro', $cdProcessoPro)->get();

        foreach ($itens as $item) {
            $pagamento = PagamentoCorrespondente::find($item->cd_pagamento_correspondente_pag);

            if ($pagamento && $this->pagamentoEditavel($pagamento)) {
                $item->delete();
                $this->recalcularTotal($pagamento);
            }
        }
    }

    private function marcarItensCancelados(Processo $processo): void
    {
        $valores = $this->getValoresProcesso((int) $processo->cd_processo_pro);
        $itens   = PagamentoCorrespondenteItem::where('cd_processo_pro', $processo->cd_processo_pro)->get();

        foreach ($itens as $item) {
            $pagamento = PagamentoCorrespondente::find($item->cd_pagamento_correspondente_pag);

            if (! $pagamento || ! $this->pagamentoEditavel($pagamento)) {
                continue;
            }

            if (! $this->itemPertenceAoPagamento($processo, $pagamento)) {
                $item->delete();
                $this->recalcularTotal($pagamento);
                continue;
            }

            $item->vl_honorario_pai = $valores['honorario'];
            $item->vl_despesa_pai   = $valores['despesa'];
            $item->ds_descricao_pai = $this->montarDescricaoProcesso($processo);
            $item->fl_excluido_pai  = 'S';
            $item->save();

            $this->recalcularTotal($pagamento);
        }
    }
}
