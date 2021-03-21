<?php

namespace App\Http\Controllers;

use Excel;
use App\Cliente;
use App\Contato;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Exports\Layout\LayoutProcesso;

class LayoutController extends Controller
{
    private $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function processo(Request $request)
    {
        $cliente = Cliente::where('cd_conta_con', $this->conta)
                    ->where('cd_cliente_cli', $request->cliente)
                    ->select('cd_cliente_cli', 'nu_cliente_cli', 'nm_razao_social_cli')
                    ->first();

        $contato = Contato::where('cd_conta_con', $this->conta)
                    ->where('cd_contato_cot', $request->advogado)
                    ->select('cd_contato_cot', 'nu_contato_cot', 'nm_contato_cot')
                    ->first();

        $nomeCliente = $cliente->nm_razao_social_cli.' ---'.$cliente->nu_cliente_cli.'---';

        $nomeContato = !empty($contato) ? $contato->nm_contato_cot.' ---'.$contato->nu_contato_cot.'---' : '';

        $dados = [
            'cliente' => $nomeCliente,
            'contato' => $nomeContato
        ];


        return \Excel::download(new LayoutProcesso($dados), 'layout-importacao-processos.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
