<?php

namespace App\Services\Pagamento;

use App\Enums\StatusPagamentoCorrespondente;
use App\Enums\TipoEntidade;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteItem;
use App\ProcessoDespesa;
use App\ProcessoTaxaHonorario;

class PagamentoCorrespondenteSyncService
{
    /**
     * Status em que os valores consolidados ainda podem ser atualizados
     * a partir da edição do processo (pagamentos já efetuados são preservados).
     */
    private const STATUS_EDITAVEIS = [
        StatusPagamentoCorrespondente::GERADO,
        StatusPagamentoCorrespondente::ENVIADO_APROVACAO,
        StatusPagamentoCorrespondente::APROVADO,
        StatusPagamentoCorrespondente::RECUSADO,
    ];

    /**
     * Sincroniza honorário e despesas do processo nos itens de pagamento
     * consolidados do correspondente, recalculando o total do faturamento.
     */
    public function syncProcesso(int $cdProcessoPro): void
    {
        $honorario = ProcessoTaxaHonorario::where('cd_processo_pro', $cdProcessoPro)->first();

        $vlHonorario = (float) ($honorario->vl_taxa_honorario_correspondente_pth ?? 0);
        $vlDespesa   = $this->sumDespesasCorrespondente($cdProcessoPro);
        $cdPth       = $honorario->cd_processo_taxa_honorario_pth ?? null;

        $itens = PagamentoCorrespondenteItem::where('cd_processo_pro', $cdProcessoPro)->get();

        if ($itens->isEmpty()) {
            return;
        }

        $pagamentosAfetados = [];

        foreach ($itens as $item) {
            $pagamento = PagamentoCorrespondente::find($item->cd_pagamento_correspondente_pag);

            if (! $pagamento || ! in_array($pagamento->cd_status_pag, self::STATUS_EDITAVEIS, true)) {
                continue;
            }

            $item->vl_honorario_pai = $vlHonorario;
            $item->vl_despesa_pai   = $vlDespesa;

            if ($cdPth) {
                $item->cd_processo_taxa_honorario_pth = $cdPth;
            }

            $item->save();

            $pagamentosAfetados[$pagamento->cd_pagamento_correspondente_pag] = $pagamento;
        }

        foreach ($pagamentosAfetados as $pagamento) {
            $this->recalcularTotal($pagamento);
        }
    }

    private function sumDespesasCorrespondente(int $cdProcessoPro): float
    {
        return (float) ProcessoDespesa::where('cd_processo_pro', $cdProcessoPro)
            ->where('fl_despesa_reembolsavel_pde', 'S')
            ->where('cd_tipo_entidade_tpe', TipoEntidade::CORRESPONDENTE)
            ->sum('vl_processo_despesa_pde');
    }

    private function recalcularTotal(PagamentoCorrespondente $pagamento): void
    {
        $total = PagamentoCorrespondenteItem::where('cd_pagamento_correspondente_pag', $pagamento->cd_pagamento_correspondente_pag)
            ->get()
            ->sum(function ($item) {
                return (float) $item->vl_honorario_pai + (float) $item->vl_despesa_pai;
            });

        $pagamento->vl_total_pag = $total;
        $pagamento->save();
    }
}
