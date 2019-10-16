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
use App\Exports\ProcessoPautaDiariaExportPDF;
use App\Exports\ProcessoPautaDiariaExportExcel;

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

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dtFim)));


            $cliente = $request->cd_cliente_cli;
            $conta   = $this->conta;

            if($request->relatorio == 'para-cliente'){

                if(!empty($cliente)){
                    $processos = Processo::with('advogadoSolicitante')
                                    ->with('cliente')
                                    ->with('vara')
                                    ->with('cidade')                                
                                    ->with('honorario')
                                    ->with(['tiposDespesa' => function($query){
                                        $query->wherePivot('cd_tipo_entidade_tpe',\TipoEntidade::CLIENTE);
                                        $query->wherePivot('fl_despesa_reembolsavel_pde','S');
                                    }])
                                    ->when(!empty($cliente), function($query) use($cliente){
                                        $query->where('cd_cliente_cli',$cliente);
                                     })
                                    ->where('cd_conta_con',$this->conta)
                                    ->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim])
                                    ->when(!empty($request->finalizado), function($query){
                                        $query->where('cd_status_processo_stp',\StatusProcesso::FINALIZADO);
                                    })
                                    ->get();

                    $despesas = TipoDespesa::whereHas('ReembolsoTipoDespesa', function ($query) use ($cliente,$conta) {
                        $query->whereHas('cliente', function ($query) use ($cliente,$conta) {
                               
                            $query->where('cd_cliente_cli',$cliente)->where('cd_conta_con',$conta);
                                
                        });
                     })->get()->sortBy('nm_tipo_despesa_tds');

                    $dados = array('processos' => $processos, 'dtInicio' => $request->dtInicio, 'dtFim' => $request->dtFim, 'despesas' => $despesas);
                   
                    if(!$processos->isEmpty()){
                        \Excel::store(new ProcessoParaClienteExport($dados),"/processo/{$this->conta}/".time() . "_".$request->nm_cliente_cli.'.xlsx','reports',\Maatwebsite\Excel\Excel::XLSX);
                    }else{
                        Flash::error('Não há dados para os parâmetros informados!');

                    }

                }else{
                    Flash::error('Campo cliente obrigatório para o tipo de relatório informado!');
                }

            }            
        
        }else{

            Flash::error('Data(s) inválida(s) !');

        }

        return \Redirect::back()->with('dtInicio',str_replace('/','',$request->dtInicio))
                                ->with('dtFim' ,str_replace('/','',$request->dtFim))
                                ->with('relatorio',$request->relatorio)
                                ->with('finalizado',$request->finalizado)
                                ->with('cliente',$request->cd_cliente_cli)
                                ->with('nmCliente',$request->nm_cliente_cli); //view('correspondente/relatorios',['arquivos' => $this->getFiles()]);
    }

    public function pautaDiaria(Request $request){

        $processos = Processo::with('cidade')->where('cd_conta_con',$this->conta)->whereNotIn('cd_status_processo_stp',[\StatusProcesso::FINALIZADO,\StatusProcesso::CANCELADO]);

        $responsavel = $request->responsavel;
        $tipoProcesso = $request->tipoProcesso;
        $correspondente = $request->cdCorrespondente;

        if(!empty($responsavel)){

            $processos = $processos->where('cd_responsavel_pro',$responsavel);
        }
        
        if(!empty($request->dt_inicio) && !empty($request->dt_fim) && \Helper::validaData($request->dt_inicio) && \Helper::validaData($request->dt_fim)){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dt_inicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dt_fim)));

            $processos = $processos->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
        }else{

            if(!empty($request->dt_inicio) && \Helper::validaData($request->dt_inicio)){

                $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$request->dt_inicio)));
               
                $processos = $processos->where('dt_prazo_fatal_pro',$dtInicio);
            
            }else{
                if(!empty($request->dt_fim) && \Helper::validaData($request->dt_fim)){

                    $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$request->dt_fim)));
               
                    $processos = $processos->where('dt_prazo_fatal_pro',$dtFim);
            
                }

            }
        }

        if(!empty($tipoProcesso)){
            $processos = $processos->where('cd_tipo_processo_tpo',$tipoProcesso);
        }

        if(!empty($correspondente)){
            $processos = $processos->where('cd_correspondente_cor',$correspondente);
        }

        $processos = $processos->orderBy('dt_prazo_fatal_pro')->get();

        if($request->tipo == 'pdf'){
            return \Excel::download(new ProcessoPautaDiariaExportPDF(['processos' => $processos]),'Pauta.pdf',\Maatwebsite\Excel\Excel::DOMPDF);
        }else{
            return \Excel::download(new ProcessoPautaDiariaExportExcel(['processos' => $processos]),'Pauta.xls',\Maatwebsite\Excel\Excel::XLS);    
        }

        
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
        
        \Storage::disk('reports')->delete("/processo/$this->conta/".$nome);

        return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);    

    }

    public function arquivo($nome){
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('reports/processo/'."$this->conta/".$nome));

    }

}