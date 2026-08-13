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
        return view('correspondente/financeiro/comprovantes-pagamento/index', [
            'comprovantes' => [],
            'meses'        => $this->meses(),
        ]);
    }

    public function buscar(Request $request)
    {
        $cliente = $request->cd_conta_con;
        $mes     = $request->mes ? (int) $request->mes : null;
        $pro     = trim((string) $request->processo);

        $baixas = PagamentoCorrespondenteBaixa::query()
            ->with([
                'pagamento.conta',
                'item.processo',
            ])
            ->whereNotNull('dc_comprovante_pcb')
            ->where('dc_comprovante_pcb', '!=', '')
            ->whereHas('pagamento', function ($query) use ($cliente, $mes) {
                $query->where('cd_correspondente_cor', $this->conta);

                if ($cliente) {
                    $query->where('cd_conta_con', $cliente);
                }

                if ($mes >= 1 && $mes <= 12) {
                    $query->where('nu_mes_pag', $mes);
                }
            })
            ->when($pro !== '', function ($query) use ($pro) {
                $query->whereHas('item.processo', function ($q) use ($pro) {
                    $q->where('nu_processo_pro', 'ilike', '%' . $pro . '%');
                });
            })
            ->orderByDesc('dt_baixa_pcb')
            ->orderByDesc('cd_pagamento_correspondente_baixa_pcb')
            ->get();

        $comprovantes = [];

        foreach ($baixas as $baixa) {
            $path = $baixa->dc_comprovante_pcb;
            $arquivoExiste = Storage::disk('public')->exists($path);

            $pagamento = $baixa->pagamento;
            $processo  = optional(optional($baixa->item)->processo);
            $nomeArquivo = basename($path);
            $competencia = $pagamento
                ? str_pad((string) $pagamento->nu_mes_pag, 2, '0', STR_PAD_LEFT) . '/' . $pagamento->nu_ano_pag
                : '—';

            $comprovantes[] = [
                'cliente'         => optional($pagamento->conta)->nm_razao_social_con
                    ?? optional($pagamento->conta)->nm_fantasia_con
                    ?? '—',
                'processo'        => $processo->nu_processo_pro ?? '—',
                'tipo'            => $baixa->nm_tipo,
                'valor'           => (float) $baixa->vl_baixa_pcb,
                'data'            => $baixa->dt_baixa_pcb ? $baixa->dt_baixa_pcb->format('d/m/Y') : '—',
                'competencia'     => $competencia,
                'nome'            => $nomeArquivo,
                'baixa_id'        => $baixa->cd_pagamento_correspondente_baixa_pcb,
                'arquivo_existe'  => $arquivoExiste,
            ];
        }

        return view('correspondente/financeiro/comprovantes-pagamento/index', [
            'comprovantes' => $comprovantes,
            'meses'        => $this->meses(),
            'mesParam'     => $mes,
            'processo'     => $pro,
        ]);
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
