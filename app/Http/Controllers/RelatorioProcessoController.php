<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use App\RelatorioJasper;
use Illuminate\Http\Request;
use App\Conta;
use App\Cliente;
use App\Processo;
use App\TipoDespesa;
use App\Exports\ProcessoParaClienteExport;

class RelatorioProcessoController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function relatorios(){   

        return view('processo/relatorios',['arquivos' => $this->getFiles()]);
    }

    public function buscar(Request $request){

        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

            $cliente = $request->cd_cliente_cli;
            $conta   = $this->conta;

            if($request->relatorio == 'para-cliente'){

                $processos = Processo::with('advogadoSolicitante')
                                ->with('cliente')
                                ->with('vara')
                                ->with('cidade')                                
                                ->with('honorario')
                                ->with(['tiposDespesa' => function($query){
                                    $query->wherePivot('cd_tipo_entidade_tpe',\TipoEntidade::CLIENTE);
                                }])
                                ->when(!empty($cliente), function($query) use($cliente){
                                    $query->where('cd_cliente_cli',$cliente);
                                 })
                                ->where('cd_conta_con',$this->conta)
                                ->get();

                $despesas = TipoDespesa::whereHas('ReembolsoTipoDespesa', function ($query) use ($cliente,$conta) {
                    $query->whereHas('cliente', function ($query) use ($cliente,$conta) {
                           
                        $query->where('cd_cliente_cli',$cliente)->where('cd_conta_con',$conta);
                            
                    });
                 })->get()->sortBy('nm_tipo_despesa_tds');

                //dd($processos[2]);
                dd($processos[2]->tiposDespesa->withPivot('sum'));

                $dados = array('processos' => $processos, 'dtInicio' => $request->dtInicio, 'dtFim' => $request->dtFim, 'despesas' => $despesas);
                return \Excel::download(new ProcessoParaClienteExport($dados), 'teste.xlsx');

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

        \File::makeDirectory(storage_path().'/reports/processo/'.$this->conta, $mode = 0744, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path()."/reports/processo/".$this->conta))
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