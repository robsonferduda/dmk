<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;
use App\Conta;
use App\Processo;
use App\Exports\Correspondente\RelacaoProcessosExport;

class RelatorioPainelCorrespondenteController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index(){   

        return view('correspondente/painel/index',['arquivos' => $this->getFiles()]);

    }

    public function buscar(Request $request){
        
        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dtFim)));

            $cliente = $request->cd_conta_con;

            $processos = Processo::where('cd_correspondente_cor', $this->conta)
                                    ->when(!empty($request->cd_conta_con), function ($query) use ($cliente) {
                                        return $query->where('cd_conta_con',$cliente);
                                    })
                                    ->get();

            if(!empty($cliente)){

                $conta = Conta::where('cd_conta_con',$cliente)->first();
                $fileName = '_Relação Processos_'.$conta->nm_razao_social_con;
            }else{
                $fileName = '_Relação Processos_Todos';
            }

             $dados = array('processos' => $processos, 'cliente' => $cliente);    

            return \Excel::download(new RelacaoProcessosExport($dados),time().$fileName.'.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            
        
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