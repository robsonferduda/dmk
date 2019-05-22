<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;

class RelatorioCorrespondenteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function relatorios(){
        
        return view('correspondente/relatorios');

        // $fileName = "correspondente";
        // $sourceName = "extrato-correspondentes.jrxml";

        // $bancoQuery = '';

        // if(!empty($bancoQuery)) $bancoQuery = " AND t2.cd_banco_ban = '002' ";

        // $parametros = array('bancoQuery' => $bancoQuery);

        // $jasper = new RelatorioJasper();
        // return $jasper->processar($parametros,$sourceName,$fileName);
    }

    public function buscar(Request $request){

        if($request->relatorio == 'pagamento-correspondentes-por-processo'){
            $sourceName = 'extrato-correspondentes-por-processo.jrxml';
            $fileName   = 'Pagamento de Correspondentes (Por Processo)';
        }

        if($request->relatorio == 'pagamento-correspondentes-sumarizado'){
            $sourceName = 'extrato-correspondentes.jrxml';
            $fileName   = 'Pagamento de Correspondentes (Sumarizado)';
        }

        $bancoQuery = '';

        if(!empty($bancoQuery)) $bancoQuery = " AND t2.cd_banco_ban = '002' ";
        $parametros = array('bancoQuery' => $bancoQuery);

        $jasper = new RelatorioJasper();
        return $jasper->processar($parametros,$sourceName,$fileName);
    }

}