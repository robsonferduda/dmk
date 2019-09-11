<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\ProcessoTaxaHonorario;

class FinanceiroController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function entradaIndex(){   

        if(!session('flBuscar')){
            \Session::put('dtInicio',null);
            \Session::put('dtFim',null);
            \Session::put('cliente',null);
            \Session::put('nmCliente',null);
            \Session::put('todas',null);

            $dtInicio = '';
            $dtFim = '';
            $cliente = '';
            $nmCliente = '';
            $todas = '';

        }else{
            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');
            $cliente = session('cliente');
            $nmCliente = session('nmCliente');
            $todas = session('todas');
        }

        $entradas = ProcessoTaxaHonorario::with(array('tipoServico' => function($query){
            $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
        }))->with(array('processo' => function($query){
            $query->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_correspondente_cor','dt_prazo_fatal_pro');
            $query->with(array('correspondente' => function($query){
                $query->select('cd_conta_con');
                $query->with(array('contaCorrespondente' => function($query){
                    $query->select('nm_conta_correspondente_ccr','cd_correspondente_cor');
                }));
            }));
            $query->with(array('cliente' => function($query){
                $query->select('cd_cliente_cli','nm_razao_social_cli');
                $query->where('cd_conta_con', $this->conta);
            }));
            $query->with(array('tiposDespesa' => function($query){
                $query->wherePivot('cd_tipo_entidade_tpe',\TipoEntidade::CLIENTE);
                $query->wherePivot('fl_despesa_reembolsavel_pde','S');

            }));
        }))->has('processo');

        if(!empty($dtInicio) && !empty($dtFim)){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));

            $entradas = $entradas->whereHas('processo', function($query) use ($dtInicio, $dtFim) {
                    $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
            });
        }

        if(!empty($cliente)){
            $entradas = $entradas->whereHas('processo', function($query) use ($cliente) {
                    $query->where('cd_cliente_cli',$cliente);
            });
        }

        if(empty($todas)){
            $entradas = $entradas->where('fl_pago_cliente_pth','N');
        }

        $entradas = $entradas->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_tse','fl_pago_cliente_pth','vl_taxa_cliente_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');

        //dd($entradas);

        \Session::put('flBuscar',false);

        return view('financeiro/entrada',['entradas' => $entradas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'cliente' => $cliente, 'nmCliente' => $nmCliente]);
    }

    public function saidaIndex(){   

        if(!session('flBuscar')){
            \Session::put('dtInicio',null);
            \Session::put('dtFim',null);
            \Session::put('correspondente',null);
            \Session::put('nmCorrespondente',null);
            \Session::put('todas',null);

            $dtInicio = '';
            $dtFim = '';
            $correspondente = '';
            $nmCorrespondente = '';
            $todas = '';

        }else{
            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');
            $correspondente = session('correspondente');
            $nmCorrespondente = session('nmCorrespondente');
            $todas = session('todas');
        }

        $saidas = ProcessoTaxaHonorario::with(array('tipoServico' => function($query){
            $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
        }))->whereHas('processo' , function($query){
            $query->has('correspondente');
            $query->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_correspondente_cor','dt_prazo_fatal_pro');            
        });

        if(!empty($dtInicio) && !empty($dtFim)){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));

            $saidas = $saidas->whereHas('processo', function($query) use ($dtInicio, $dtFim) {
                    $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
            });
        }

        if(!empty($correspondente)){
            $saidas = $saidas->whereHas('processo', function($query) use ($correspondente) {
                    $query->where('cd_correspondente_cor',$correspondente);
            });
        }

        if(empty($todas)){
            $saidas = $saidas->where('fl_pago_correspondente_pth','N');
        }

        $saidas = $saidas->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_tse','fl_pago_correspondente_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');
        //dd($saidas);
        \Session::put('flBuscar',false);
        //dd($saidas[0]->processo->processoDespesa);
        return view('financeiro/saida',['saidas' => $saidas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'correspondente' => $correspondente, 'nmCorrespondente' => $nmCorrespondente]);
    }

    public function entradaBuscar(Request $request){

        
        \Session::put('flBuscar',true);                        
        \Session::put('cliente',$request->cd_cliente_cli);
        \Session::put('nmCliente',$request->nm_cliente_cli);

        if(!empty($request->todas)){
            \Session::put('todas','S');
        }else{
            \Session::put('todas',null);
        }    

        if((!empty($request->dtInicio) && empty($request->dtFim)) || (empty($request->dtInicio) && !empty($request->dtFim))){
        
            Flash::error('É preciso preencher a data de início e fim.');
        
        }else{

            if((!empty($request->dtInicio) && !empty($request->dtFim))){
                
                if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

                   \Session::put('dtInicio',$request->dtInicio);
                   \Session::put('dtFim',$request->dtFim);
                   

                }else{

                    Flash::error('Data(s) inválida(s) !');
                }
            }
        }


        return redirect('financeiro/entradas');

    }

    public function saidaBuscar(Request $request){

        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){
           
            \Session::put('dtInicio',$request->dtInicio);
            \Session::put('dtFim',$request->dtFim);
            \Session::put('flBuscar',true);                        
            \Session::put('correspondente',$request->cd_correspondente_cor);
            \Session::put('nmCorrespondente',$request->nm_correspondente_cor);

            if(!empty($request->todas)){
                \Session::put('todas','S');
            }else{
                \Session::put('todas',null);
            }

        }else{

            Flash::error('Data(s) inválida(s) !');
        }

        return redirect('financeiro/saidas');

    }

    public function baixaCliente(Request $request){

        $processosTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->whereIn('cd_processo_taxa_honorario_pth',$request->ids)->get();
            
        $response = false;
        foreach($processosTaxaHonorario as $processoTaxaHonorario){
            
            $processoTaxaHonorario->fl_pago_cliente_pth = $request->checked;
            
            if(!empty($request->data)){
                $processoTaxaHonorario->dt_baixa_cliente_pth = date('Y-m-d',strtotime(str_replace('/','-',$request->data)));
            }else{
                $processoTaxaHonorario->dt_baixa_cliente_pth = null;
            }

            if(!empty($request->nota)){
                $processoTaxaHonorario->nu_cliente_nota_fiscal_pth = $request->nota;
            }else{
                $processoTaxaHonorario->nu_cliente_nota_fiscal_pth = null;   
            }


            $response = $processoTaxaHonorario->saveOrFail();

            if(!$response){
                echo json_encode(false);
                break;
            }

        }

        echo json_encode(true);
        
    }

    public function baixaCorrespondente(Request $request){

        $processosTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->whereIn('cd_processo_taxa_honorario_pth',$request->ids)->get();
            
        $response = false;
        foreach($processosTaxaHonorario as $processoTaxaHonorario){
            
            $processoTaxaHonorario->fl_pago_correspondente_pth = $request->checked;

             if(!empty($request->data)){
                $processoTaxaHonorario->dt_baixa_correspondente_pth = date('Y-m-d',strtotime(str_replace('/','-',$request->data)));
            }else{
                $processoTaxaHonorario->dt_baixa_correspondente_pth = null;
            }

            $response = $processoTaxaHonorario->saveOrFail();

            if(!$response){
                echo json_encode(false);
                break;
            }

        }

        echo json_encode(true);
    }

}