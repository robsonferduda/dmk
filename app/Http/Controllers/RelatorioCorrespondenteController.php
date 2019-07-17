<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;
use App\Conta;

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

        $conta = Conta::where('cd_conta_con',$this->conta)->select('nm_razao_social_con')->first();

        $empresa = $conta->nm_razao_social_con;

        if($request->relatorio == 'pagamento-correspondentes-por-processo'){
            $sourceName = 'extrato-correspondentes-por-processo.jrxml';
            $fileName   = 'Pagamento de Correspondentes (Por Processo)';            
        }

        if($request->relatorio == 'pagamento-correspondentes-sumarizado'){
            $sourceName = 'extrato-correspondentes.jrxml';
            $fileName   = 'Pagamento de Correspondentes (Sumarizado)';
        }

        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dtInicio)));
        $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dtFim)));
       
        $dataQuery = " AND dt_prazo_fatal_pro between '$dtInicio' and '$dtFim' ";

        $bancoQuery = '';
        $correspondenteQuery = '';

        if(!empty($request->cd_correspondente_cor))
            $correspondenteQuery = "  AND t8.cd_correspondente_cor =  {$request->cd_correspondente_cor} ";

        if(!empty($request->cd_banco_ban)) $bancoQuery = " AND t2.cd_banco_ban = '".str_pad($request->cd_banco_ban,3, '0', STR_PAD_LEFT)."' ";
        $parametros = array('bancoQuery'          => $bancoQuery,
                            'dataQuery'           => $dataQuery,
                            'conta'               => $this->conta, 
                            'dataInicio'          => $request->dtInicio,
                            'dataFim'             => $request->dtFim,
                            'empresa'             => $empresa,
                            'correspondenteQuery' => $correspondenteQuery);

        $jasper = new RelatorioJasper();
        return $jasper->processar($parametros,$sourceName,$fileName);
    }

}