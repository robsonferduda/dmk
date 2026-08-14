<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
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
     * @param  array{cliente?:mixed,mes?:?int,processo?:string}  $filtros
     */
    private function listarComprovantes(array $filtros = [], ?int $limit = 10): array
    {
        $cliente  = $filtros['cliente'] ?? null;
        $mes      = isset($filtros['mes']) ? (int) $filtros['mes'] : null;
        $processo = trim((string) ($filtros['processo'] ?? ''));

        $query = PagamentoCorrespondenteBaixa::query()
            ->with([
                'pagamento.conta',
                'item.processo',
            ])
            ->whereNotNull('dc_comprovante_pcb')
            ->where('dc_comprovante_pcb', '!=', '')
            ->whereHas('pagamento', function ($q) use ($cliente, $mes) {
                $q->where('cd_correspondente_cor', $this->conta);

                if ($cliente) {
                    $q->where('cd_conta_con', $cliente);
                }

                if ($mes >= 1 && $mes <= 12) {
                    $q->where('nu_mes_pag', $mes);
                }
            })
            ->when($processo !== '', function ($q) use ($processo) {
                $q->whereHas('item.processo', function ($qp) use ($processo) {
                    $qp->where('nu_processo_pro', 'ilike', '%' . $processo . '%');
                });
            })
            ->orderByDesc('dt_baixa_pcb')
            ->orderByDesc('cd_pagamento_correspondente_baixa_pcb');

        if ($limit) {
            $query->limit($limit);
        }

        $comprovantes = [];

        foreach ($query->get() as $baixa) {
            $path = $baixa->dc_comprovante_pcb;
            $pagamento = $baixa->pagamento;
            $processoModel = optional(optional($baixa->item)->processo);

            $comprovantes[] = [
                'cliente'        => optional($pagamento->conta)->nm_razao_social_con
                    ?? optional($pagamento->conta)->nm_fantasia_con
                    ?? '—',
                'processo'       => $processoModel->nu_processo_pro ?? '—',
                'tipo'           => $baixa->nm_tipo,
                'valor'          => (float) $baixa->vl_baixa_pcb,
                'data'           => $baixa->dt_baixa_pcb ? $baixa->dt_baixa_pcb->format('d/m/Y') : '—',
                'competencia'    => $pagamento
                    ? str_pad((string) $pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag
                    : '—',
                'nome'           => basename($path),
                'baixa_id'       => $baixa->cd_pagamento_correspondente_baixa_pcb,
                'arquivo_existe' => Storage::disk('public')->exists($path),
            ];
        }

        return $comprovantes;
    }

    /**
     * Download do comprovante (nova estrutura) restrito ao correspondente logado.
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
