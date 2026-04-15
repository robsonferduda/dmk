<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Auth;
use App\Conta;
use App\Cliente;
use App\Processo;
use App\TipoDespesa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Exports\RelatorioCobrancaClienteExport;

class RelatorioClienteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        Session::put('menu_pai', 'clientes');
        Session::put('item_pai', 'cliente.relatorio-cobranca');

        return view('cliente/relatorio-cobranca', [
            'processos' => null,
            'despesas'  => collect(),
            'dados'     => null,
        ]);
    }

    public function buscar(Request $request)
    {
        Session::put('menu_pai', 'clientes');

        [$processos, $despesas, $erro] = $this->executarBusca($request);

        if ($erro) {
            Flash::error($erro);
        }

        return view('cliente/relatorio-cobranca', [
            'processos' => $processos,
            'despesas'  => $despesas,
            'dados'     => $request->all(),
        ]);
    }

    public function exportarExcel(Request $request)
    {
        [$processos, $despesas, $erro] = $this->executarBusca($request);

        if ($erro || !$processos || $processos->isEmpty()) {
            Flash::error($erro ?? 'Não há dados para exportar.');
            return redirect('cliente/relatorio-cobranca');
        }

        $dados = [
            'processos' => $processos,
            'despesas'  => $despesas,
            'dtInicio'  => $request->dtInicio,
            'dtFim'     => $request->dtFim,
        ];

        $nomeCliente = str_replace('/', '', $request->nm_cliente_cli ?? 'cliente');
        $nomeArquivo = 'cobranca_' . $nomeCliente . '_' . now()->format('Y-m-d') . '.xlsx';

        return \Excel::download(new RelatorioCobrancaClienteExport($dados), $nomeArquivo, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportarPdf(Request $request)
    {
        [$processos, $despesas, $erro] = $this->executarBusca($request);

        if ($erro || !$processos || $processos->isEmpty()) {
            Flash::error($erro ?? 'Não há dados para exportar.');
            return redirect('cliente/relatorio-cobranca');
        }

        $eventoParametros = \App\EventoParametros::where('cd_conta_con', $this->conta)->first();

        $dados = [
            'processos' => $processos,
            'despesas'  => $despesas,
            'dtInicio'  => $request->dtInicio,
            'dtFim'     => $request->dtFim,
            'conta'     => Conta::where('cd_conta_con', $this->conta)->first(),
        ];

        $nomeCliente = str_replace('/', '', $request->nm_cliente_cli ?? 'cliente');
        $nomeArquivo = 'cobranca_' . $nomeCliente . '_' . now()->format('Y-m-d') . '.pdf';

        return PDF::loadView('relatorios.pdf.cobranca-cliente', $dados, [], [
            'title'  => 'Relatório de Cobrança - ' . ($request->nm_cliente_cli ?? ''),
            'format' => 'A4-L',
        ])->download($nomeArquivo);
    }

    protected function executarBusca(Request $request): array
    {
        if (empty($request->cd_cliente_cli)) {
            return [null, collect(), 'Campo cliente é obrigatório.'];
        }

        if (!\Helper::validaData($request->dtInicio) || !\Helper::validaData($request->dtFim)) {
            return [null, collect(), 'Data(s) inválida(s).'];
        }

        if (strtotime(str_replace('/', '-', $request->dtInicio)) > strtotime(str_replace('/', '-', $request->dtFim))) {
            return [null, collect(), 'A data inicial não pode ser maior que a data final.'];
        }

        $dtInicio = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtInicio)));
        $dtFim    = date('Y-m-d', strtotime(str_replace('/', '-', $request->dtFim)));
        $cliente  = $request->cd_cliente_cli;

        $processos = Processo::with('advogadoSolicitante')
            ->with('cliente')
            ->with('vara')
            ->with('cidade.estado')
            ->with('honorario.tipoServico')
            ->with(['tiposDespesa' => function ($query) {
                $query->wherePivot('cd_tipo_entidade_tpe', \TipoEntidade::CLIENTE);
                $query->wherePivot('fl_despesa_reembolsavel_pde', 'S');
            }])
            ->where('cd_conta_con', $this->conta)
            ->where('cd_cliente_cli', $cliente)
            ->whereBetween('dt_prazo_fatal_pro', [$dtInicio, $dtFim])
            ->when(!empty($request->finalizado), function ($query) {
                $query->where('cd_status_processo_stp', \StatusProcesso::FINALIZADO);
            })
            ->get();

        $despesas = TipoDespesa::whereHas('ReembolsoTipoDespesa')
            ->where('cd_conta_con', $this->conta)
            ->where('fl_reembolso_tds', 'S')
            ->get()
            ->sortBy('nm_tipo_despesa_tds');

        return [$processos, $despesas, null];
    }
}
