<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use App\PagamentoCorrespondente;
use App\PagamentoCorrespondenteBaixa;

class CorrespondenteFinanceiroController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    private function meses()
    {
        return [
            '1'  => 'Janeiro',
            '2'  => 'Fevereiro',
            '3'  => 'Março',
            '4'  => 'Abril',
            '5'  => 'Maio',
            '6'  => 'Junho',
            '7'  => 'Julho',
            '8'  => 'Agosto',
            '9'  => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        ];
    }

    public function comprovantes()
    {
        return view('correspondente/financeiro/comprovantes-pagamento/index', array_merge(
            $this->montarViewData(),
            [
                'comprovantes' => $this->listarComprovantes([], 10),
                'tituloLista'  => 'Últimos comprovantes',
                'listaLimitada'=> true,
            ]
        ));
    }

    public function buscar(Request $request)
    {
        $filtros = [
            'cliente'  => $request->cd_conta_con,
            'mes'      => $request->mes ? (int) $request->mes : null,
            'processo' => trim((string) $request->processo),
        ];

        $temFiltro = ! empty($filtros['cliente'])
            || ($filtros['mes'] >= 1 && $filtros['mes'] <= 12)
            || $filtros['processo'] !== '';

        return view('correspondente/financeiro/comprovantes-pagamento/index', array_merge(
            $this->montarViewData($request),
            [
                'comprovantes' => $this->listarComprovantes($filtros, $temFiltro ? null : 10),
                'tituloLista'  => $temFiltro ? 'Resultados da busca' : 'Últimos comprovantes',
                'listaLimitada'=> ! $temFiltro,
                'mesParam'     => $filtros['mes'],
                'processo'     => $filtros['processo'],
                'cdContaCon'   => $filtros['cliente'],
                'nmContaCon'   => $request->nm_conta_con,
            ]
        ));
    }

    private function montarViewData(Request $request = null): array
    {
        return [
            'meses'      => $this->meses(),
            'mesParam'   => $request ? ($request->mes ? (int) $request->mes : null) : null,
            'processo'   => $request ? trim((string) $request->processo) : '',
            'cdContaCon' => $request ? $request->cd_conta_con : '',
            'nmContaCon' => $request ? $request->nm_conta_con : '',
        ];
    }

    /**
     * Um comprovante por pagamento (não por processo).
     *
     * @param  array{cliente?:mixed,mes?:?int,processo?:string}  $filtros
     */
    private function listarComprovantes(array $filtros = [], ?int $limit = 10): array
    {
        $cliente  = $filtros['cliente'] ?? null;
        $mes      = isset($filtros['mes']) ? (int) $filtros['mes'] : null;
        $processo = trim((string) ($filtros['processo'] ?? ''));

        $query = PagamentoCorrespondente::query()
            ->with(['conta', 'itens.processo', 'baixas'])
            ->where('cd_correspondente_cor', $this->conta)
            ->whereNotNull('dc_comprovante_pag')
            ->where('dc_comprovante_pag', '!=', '')
            ->when($cliente, function ($q) use ($cliente) {
                $q->where('cd_conta_con', $cliente);
            })
            ->when($mes >= 1 && $mes <= 12, function ($q) use ($mes) {
                $q->where('nu_mes_pag', $mes);
            })
            ->when($processo !== '', function ($q) use ($processo) {
                $q->whereHas('itens.processo', function ($qp) use ($processo) {
                    $qp->where('nu_processo_pro', 'ilike', '%' . $processo . '%');
                });
            })
            ->orderByDesc('dt_pagamento_pag')
            ->orderByDesc('cd_pagamento_correspondente_pag');

        if ($limit) {
            $query->limit($limit);
        }

        $comprovantes = [];

        foreach ($query->get() as $pagamento) {
            $path = $pagamento->dc_comprovante_pag;

            $itensAtivos = $pagamento->itens
                ->filter(function ($item) {
                    return strtoupper((string) ($item->fl_excluido_pai ?? 'N')) !== 'S';
                })
                ->values();

            $itensDetalhe = [];
            foreach ($itensAtivos as $item) {
                $nuProcesso = optional($item->processo)->nu_processo_pro ?? '—';
                $destaque = $processo !== '' && $nuProcesso !== '—'
                    && stripos($nuProcesso, $processo) !== false;

                $itensDetalhe[] = [
                    'processo'  => $nuProcesso,
                    'descricao' => $item->ds_descricao_pai ?: null,
                    'honorario' => (float) $item->vl_honorario_pai,
                    'despesa'   => (float) $item->vl_despesa_pai,
                    'total'     => (float) $item->vl_honorario_pai + (float) $item->vl_despesa_pai,
                    'destaque'  => $destaque,
                ];
            }

            $dataPag = $pagamento->dt_pagamento_pag
                ? $pagamento->dt_pagamento_pag->format('d/m/Y')
                : optional($pagamento->baixas->sortByDesc('dt_baixa_pcb')->first())->dt_baixa_pcb;

            if ($dataPag && ! is_string($dataPag)) {
                $dataPag = $dataPag->format('d/m/Y');
            }

            $comprovantes[] = [
                'origem'         => 'pagamento',
                'pagamento_id'   => $pagamento->cd_pagamento_correspondente_pag,
                'cliente'        => optional($pagamento->conta)->nm_razao_social_con
                    ?? optional($pagamento->conta)->nm_fantasia_con
                    ?? '—',
                'valor'          => (float) ($pagamento->vl_pago_total ?: $pagamento->vl_total_pag),
                'data'           => $dataPag ?: '—',
                'competencia'    => str_pad((string) $pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag,
                'nome'           => basename($path),
                'arquivo_existe' => Storage::disk('public')->exists($path),
                'qtd_processos'  => count($itensDetalhe),
                'itens'          => $itensDetalhe,
            ];
        }

        return $comprovantes;
    }

    /**
     * Download do comprovante do pagamento (nível agrupamento).
     */
    public function baixarComprovantePagamento($id)
    {
        $pagamento = PagamentoCorrespondente::where('cd_correspondente_cor', $this->conta)
            ->where('cd_pagamento_correspondente_pag', $id)
            ->firstOrFail();

        if (! $pagamento->dc_comprovante_pag) {
            Flash::error('Este pagamento não possui comprovante.');
            return redirect()->back();
        }

        if (! Storage::disk('public')->exists($pagamento->dc_comprovante_pag)) {
            Flash::error('Arquivo do comprovante não encontrado no servidor.');
            return redirect()->back();
        }

        return Storage::disk('public')->response($pagamento->dc_comprovante_pag);
    }

    /**
     * Download legado de comprovante vinculado a uma baixa individual.
     */
    public function baixarComprovante($baixaId)
    {
        $baixa = PagamentoCorrespondenteBaixa::with('pagamento')
            ->where('cd_pagamento_correspondente_baixa_pcb', $baixaId)
            ->firstOrFail();

        $pagamento = $baixa->pagamento;

        if (! $pagamento || (int) $pagamento->cd_correspondente_cor !== (int) $this->conta) {
            abort(403, 'Comprovante não disponível para este correspondente.');
        }

        if (! $baixa->dc_comprovante_pcb) {
            Flash::error('Este lançamento não possui comprovante.');
            return redirect()->back();
        }

        if (! Storage::disk('public')->exists($baixa->dc_comprovante_pcb)) {
            Flash::error('Arquivo do comprovante não encontrado no servidor.');
            return redirect()->back();
        }

        return Storage::disk('public')->response($baixa->dc_comprovante_pcb);
    }
}
