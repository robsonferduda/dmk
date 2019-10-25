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
                                    ->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim])
                                    ->orderBy('dt_prazo_fatal_pro','asc')
                                    ->orderBy('hr_audiencia_pro')
                                    ->get();

            if(!empty($cliente)){

                $conta = Conta::where('cd_conta_con',$cliente)->first();
                $fileName = '_Relação Processos_'.$conta->nm_razao_social_con;
            }else{
                $fileName = '_Relação Processos_Todos';
            }

            $dados = array('processos' => $processos, 'cliente' => $conta);    

            if(!$processos->isEmpty()){
                \Excel::store(new RelacaoProcessosExport($dados),"/correspondente/{$this->conta}/".time().$fileName.'.xlsx','reports',\Maatwebsite\Excel\Excel::XLSX);
            }else{
                Flash::error('Não há dados para os parâmetros informados!');

            }
        
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

        \File::makeDirectory(storage_path().'/reports/correspondente/'.$this->conta, $mode = 0744, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path()."/reports/correspondente/".$this->conta))
                 ->sortByDesc(function ($file) {
                    return $file->getCTime();
                });
        
        foreach($files as $file){
            
            $arquivos[] = array('nome' => $file->getFilename(), 'data' => date('d/m/Y H:i:s',$file->getCTime()),'tamanho' => round($file->getSize()/1024,2) );           
        }

        return $arquivos;
    }

    public function excluir($nome){
        dd("/correspondente/$this->conta/".$nome);
        $teste = \Storage::disk('reports')->delete("/correspondente/$this->conta/".$nome);
        
        //return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);    

    }

    public function arquivo($nome){
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('reports/correspondente/'."$this->conta/".$nome));

    }

}