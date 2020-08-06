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

        return view('correspondente/relatorios',['arquivos' => $this->getFiles()]);

        // $fileName = "correspondente";
        // $sourceName = "extrato-correspondentes.jrxml";

        // $bancoQuery = '';

        // if(!empty($bancoQuery)) $bancoQuery = " AND t2.cd_banco_ban = '002' ";

        // $parametros = array('bancoQuery' => $bancoQuery);

        // $jasper = new RelatorioJasper();
        // return $jasper->processar($parametros,$sourceName,$fileName);
    }

    public function buscar(Request $request){


        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

            $conta = Conta::where('cd_conta_con',$this->conta)->select('nm_razao_social_con')->first();

            $empresa = $conta->nm_razao_social_con;

            $bancoQuery = '';
            $correspondenteQuery = '';
            $statusQuery = '';

            if($request->relatorio == 'pagamento-correspondentes-por-processo'){

                if($request->extensao == 'pdf'){
                    $sourceName = 'extrato-correspondentes-por-processo.jrxml';
                }else{
                    $sourceName = 'extrato-correspondentes-por-processo-xls.jrxml';
                }

                $fileName   = 'Pagamento de Correspondentes (Por Processo)';     

                if(!empty($request->cd_correspondente_cor))
                    $correspondenteQuery = "  AND t8.cd_correspondente_cor =  {$request->cd_correspondente_cor} ";

                if(!empty($request->cd_banco_ban)) 
                    $bancoQuery = " AND t2.cd_banco_ban = '".str_pad($request->cd_banco_ban,3, '0', STR_PAD_LEFT)."' ";       
            }

            if($request->relatorio == 'pagamento-correspondentes-sumarizado'){

                if($request->extensao == 'pdf'){
                    $sourceName = 'extrato-correspondentes.jrxml';
                }else{
                    $sourceName = 'extrato-correspondentes-xls.jrxml';
                }
                $fileName   = 'Pagamento de Correspondentes (Sumarizado)';

                if(!empty($request->cd_correspondente_cor))
                    $correspondenteQuery = "  AND t8.cd_correspondente_cor =  {$request->cd_correspondente_cor} ";

                if(!empty($request->cd_banco_ban)) 
                    $bancoQuery = " AND t4.cd_banco_ban = '".str_pad($request->cd_banco_ban,3, '0', STR_PAD_LEFT)."' ";  
            }

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dtFim)));

            $dataQuery = " AND dt_prazo_fatal_pro between '$dtInicio' and '$dtFim' ";

            if(!empty($request->finalizado))
                $statusQuery = ' AND t3.cd_status_processo_stp = '.\StatusProcesso::FINALIZADO;
            
            $parametros = array('bancoQuery'          => $bancoQuery,
                                'dataQuery'           => $dataQuery,
                                'conta'               => $this->conta, 
                                'dataInicio'          => $request->dtInicio,
                                'dataFim'             => $request->dtFim,
                                'empresa'             => $empresa,
                                'correspondenteQuery' => $correspondenteQuery,
                                'statusQuery'         => $statusQuery);

            $jasper = new RelatorioJasper();

            $jasper->processar($parametros,$sourceName,$fileName,false,$request->extensao);
        
        }else{

            Flash::error('Data(s) inválida(s) !');

        }

        return \Redirect::back()->with('dtInicio',str_replace('/','',$request->dtInicio))
                                ->with('dtFim' ,str_replace('/','',$request->dtFim))
                                ->with('relatorio',$request->relatorio)
                                ->with('extensao',$request->extensao)
                                ->with('finalizado',$request->finalizado)
                                ->with('banco',$request->cd_banco_ban)
                                ->with('correspondente',$request->cd_correspondente_cor)
                                ->with('nmCorrespondente',$request->nm_correspondente_cor); //view('correspondente/relatorios',['arquivos' => $this->getFiles()]);
    }

    private function getFiles(){

        \File::makeDirectory(storage_path().'/reports/'.$this->conta, $mode = 0744, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path()."/reports/".$this->conta))
                 ->sortByDesc(function ($file) {
                    return $file->getCTime();
                });
        
        foreach($files as $file){
            
            $arquivos[] = array('nome' => $file->getFilename(), 'data' => date('d/m/Y H:i:s',$file->getCTime()),'tamanho' => round($file->getSize()/1024,2) );           
        }

        return $arquivos;
    }

    public function excluir($nome){
        
        \Storage::disk('reports')->delete("$this->conta/".$nome);

        return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);    

    }

    public function arquivo($nome){
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('reports/'."$this->conta/".$nome));

    }

}