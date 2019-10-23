<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;
use App\Conta;

class RelatorioPainelCorrespondenteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth',['except' => ['cadastro','aceitarFiliacao','aceitarConvite','acompanhamento']]);
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index(){   

        return view('correspondente/painel/index',['arquivos' => $this->getFiles()]);

    }

    public function buscar(Request $request){
        
        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dtFim)));




            
            $dados = array('entradas' => $entradas,'conta' => $conta,'saidas' => $saidas, 'despesas' => $despesas);    

            return \Excel::download(new BalancoDetalhadoExport($dados),'teste.xlsx');
            
        
        }else{

            Flash::error('Data(s) inválida(s) !');

        }

        return \Redirect::back()->with('dtInicio',str_replace('/','',$request->dtInicio))
                                ->with('dtFim' ,str_replace('/','',$request->dtFim))
                                ->with('relatorio',$request->relatorio)                                
                                ->with('finalizado',$request->finalizado)

                                ->with('conta',$request->cd_conta_con)
                                ->with('nmConta',$request->nm_conta_con); 
    }

    private function getFiles(){
        return [];
    }

    public function excluir($nome){ 

    }

    public function arquivo($nome){

    }

}