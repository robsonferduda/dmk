<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\ProcessoTaxaHonorario;
use App\Balanco;
use App\CategoriaDespesa;
use App\Conta;
use App\TipoDespesa;
use App\Processo;
use App\Exports\BalancoDetalhadoExport;
use App\Exports\BalancoSumarizadoExport;
use App\Despesa;

class FinanceiroController extends Controller
{
    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function balancoIndex(){

         if(!session('flBuscar')){
            \Session::put('dtInicio',date('d/m/Y',strtotime(date("Y-m-01"))));
            \Session::put('dtFim',date('d/m/Y',strtotime(date("Y-m-t"))));
            \Session::put('tipoDespesa',null);
            \Session::put('categoria',null);

            $dtInicio = date('d/m/Y',strtotime(date("Y-m-01")));
            $dtFim = date('d/m/Y',strtotime(date("Y-m-t")));
            $tipoDespesa = '';
            $categoria = '';

        }else{

            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');   
            $categoria = session('categoria');
            $tipoDespesa = session('tipoDespesa');

        }

        
        $entradas = Balanco::where('cd_conta_con', $this->conta)->where('cod_tipo_despesa', -1);
        $saidas = Balanco::where('cd_conta_con', $this->conta)->where('cod_tipo_despesa', -2);
        $despesas = Balanco::where('cd_conta_con', $this->conta)->whereNotIn('cod_tipo_despesa',[-1,-2]);

        if(!empty($dtInicio) && !empty($dtFim)){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));

            $entradas = $entradas->whereBetween('date',[$dtInicio,$dtFim]);
            $saidas   = $saidas->whereBetween('date',[$dtInicio,$dtFim]);
            $despesas = $despesas->whereBetween('date',[$dtInicio,$dtFim]);
            
        }

        if(empty($tipoDespesa) && !empty($categoria)){
            $retorno = TipoDespesa::where('cd_conta_con',$this->conta)->whereIn('cd_categoria_despesa_cad',$categoria)->get();

            $tipoDespesa = array();

            foreach ($retorno as $ret) {
                $tipoDespesa[] = $ret->cd_tipo_despesa_tds;
            }
        }

        if(!empty($tipoDespesa)){
            $despesas = $despesas->whereIn('cod_tipo_despesa',$tipoDespesa);
        }

        $entradas = $entradas->sum('valor_total');
        $saidas   = $saidas->sum('valor_total');
        $despesas = $despesas->sum('valor_total');

        $saldo = $entradas - $saidas - $despesas;

        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();

        $tiposDespesa = TipoDespesa::when(!empty($categoria), function ($q) use ($categoria) { 
                        return $q->whereIn('cd_categoria_despesa_cad',$categoria);
                   })->where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();
        

        \Session::put('flBuscar',false);

        return view('financeiro/balanco',['tiposDespesa' => $tiposDespesa,'categorias' => $categorias,'despesas' => $despesas,'saidas' => $saidas,'entradas' => $entradas,'saldo' => $saldo])->with(['dtFim' => $dtFim,'tipoDespesa' => $tipoDespesa,'categoria' => $categoria]);
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

        $entradas = $entradas->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_tse','fl_pago_cliente_pth','vl_taxa_cliente_pth','dt_baixa_cliente_pth','nu_cliente_nota_fiscal_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');

        \Session::put('flBuscar',false);

        return view('financeiro/entrada',['entradas' => $entradas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'cliente' => $cliente, 'nmCliente' => $nmCliente]);
    }

    public function saidaIndex(){   

        if(!session('flBuscar')){
            \Session::put('dtInicio',date('d/m/Y',strtotime(date("Y-m-01"))));
            \Session::put('dtFim',date('d/m/Y',strtotime(date("Y-m-t"))));
            \Session::put('correspondente',null);
            \Session::put('nmCorrespondente',null);
            \Session::put('todas',null);

            $dtInicio = date('d/m/Y',strtotime(date("Y-m-01")));
            $dtFim = date('d/m/Y',strtotime(date("Y-m-t")));
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

        $saidas = ProcessoTaxaHonorario::with(array('tipoServicoCorrespondente' => function($query){
            $query->select('cd_tipo_servico_tse','nm_tipo_servico_tse');
        }))->whereHas('processo' , function($query){
            $query->has('correspondente');
            $query->select('cd_processo_pro','nu_processo_pro','cd_cliente_cli','cd_correspondente_cor','dt_prazo_fatal_pro');            
        })->whereNotNull('cd_tipo_servico_correspondente_tse');

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

        $saidas = $saidas->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_correspondente_tse','fl_pago_correspondente_pth','dt_baixa_correspondente_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');
        //dd($saidas);
        \Session::put('flBuscar',false);
        //dd($saidas[0]->processo->processoDespesa);

        return view('financeiro/saida',['saidas' => $saidas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim,'correspondente' => $correspondente, 'nmCorrespondente' => $nmCorrespondente]);
    }

    public function balancoBuscar(Request $request){
        
        \Session::put('flBuscar',true);                        
        \Session::put('tipoDespesa',$request->cd_tipo_despesa_tds);
        \Session::put('categoria',$request->cd_categoria_despesa_cad);
       
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


        return redirect('financeiro/balanco');

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

    public function relatorioBalancoSumarizado($request){

        $conta = Conta::where('cd_conta_con',$this->conta)->select('nm_razao_social_con')->first();

        $entradasVetor = [];

        if(!empty($request->entradas)){
            $entradas = Processo::whereHas('honorario', function($query) use ($dtInicioBaixa,$dtFimBaixa){
                                    $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_baixa_cliente_pth',[$dtInicioBaixa,$dtFimBaixa]);
                                    }); 

                                    $query->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_baixa_cliente_pth',$dtInicioBaixa);
                                    });

                                    $query->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_baixa_cliente_pth',$dtFimBaixa);
                                    });

                                 })
                                 ->with('cliente')                            
                                 ->with('tiposDespesa')
                                 ->where('cd_conta_con',$this->conta)
                                  ->when(!empty($cliente), function ($query) use ($cliente) {
                                        return $query->where('cd_cliente_cli',$cliente);
                                 }) 
                                 ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                        return $query->where('cd_correspondente_cor',$correspondente);
                                 })   
                                 ->when(!empty($finalizado), function ($query){
                                        return $query->where('cd_status_processo_stp',\StatusProcesso::FINALIZADO);
                                 })    
                                 ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
                                        $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));
                                        return $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
                                 })    
                                 ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {                                  
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtInicio);
                                 })    
                                 ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                        
                                        $dtFim = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtFim);
                                 })    
                                 ->get()->sortBy('cliente.nm_razao_social_cli');
        }else{
            $entradas = array();
        }

        foreach ($entradas as $entrada) {

            $totalDespesas = 0;
            $total = 0;

            foreach($entrada->tiposDespesa as $despesa){
                    if($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S'){
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
            }

            $entrada->honorario->vl_taxa_honorario_cliente_pth = $entrada->honorario->vl_taxa_honorario_cliente_pth - (($entrada->honorario->vl_taxa_honorario_cliente_pth * $entrada->honorario->vl_taxa_cliente_pth)/100);

            if(array_key_exists($entrada->cliente->cd_cliente_cli, $entradasVetor)){
                   $entrada->honorario->vl_taxa_honorario_cliente_pth += round($entradasVetor[$entrada->cliente->cd_cliente_cli]['valor'],2);
                   $totalDespesas += $entradasVetor[$entrada->cliente->cd_cliente_cli]['despesa'];
            }

            $total = $entrada->honorario->vl_taxa_honorario_cliente_pth + $totalDespesas;

            $entradasVetor[$entrada->cliente->cd_cliente_cli] = array('cliente' => $entrada->cliente->nm_razao_social_cli, 'valor' => $entrada->honorario->vl_taxa_honorario_cliente_pth, 'despesa' => $totalDespesas, 'total' => $total);
        }

        $saidasVetor = [];

        if(!empty($request->saidas)){
            $saidas = Processo::whereHas('honorario.tipoServicoCorrespondente')
                                ->whereHas('honorario', function($query) use ($dtInicioBaixa,$dtFimBaixa){
                                    $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_baixa_correspondente_pth',[$dtInicioBaixa,$dtFimBaixa]);
                                    }); 

                                    $query->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_baixa_correspondente_pth',$dtInicioBaixa);
                                    });

                                    $query->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_baixa_correspondente_pth',$dtFimBaixa);
                                    });
                                })
                                ->whereHas('correspondente')                            
                                ->with('tiposDespesa')
                                ->where('cd_conta_con',$this->conta)
                                ->when(!empty($cliente), function ($query) use ($cliente) {
                                        return $query->where('cd_cliente_cli',$cliente);
                                }) 
                                ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                        return $query->where('cd_correspondente_cor',$correspondente);
                                })   
                                ->when(!empty($finalizado), function ($query){
                                        return $query->where('cd_status_processo_stp',\StatusProcesso::FINALIZADO);
                                })  
                                ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
                                        $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));
                                        return $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
                                })    
                                ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {                                  
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtInicio);
                                })    
                                ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                        
                                        $dtFim = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtFim);
                                })   
                                ->get()
                                ->sort(function($a, $b){
                                    $lengthA = strlen($a->correspondente->contaCorrespondente->nm_conta_correspondente_ccr);
                                    $lengthB = strlen($b->correspondente->contaCorrespondente->nm_conta_correspondente_ccr);
                                    $valueA = $a->correspondente->contaCorrespondente->nm_conta_correspondente_ccr;
                                    $valueB = $b->correspondente->contaCorrespondente->nm_conta_correspondente_ccr;

                                    if($lengthA == $lengthB){
                                        if($valueA == $valueB) return 0;
                                        return $valueA > $valueB ? 1 : -1;
                                    }
                                    return $lengthA > $lengthB ? 1 : -1;
                                });
        }else{
            $saidas = array();
        }

        foreach ($saidas as $saida) {

            $totalDespesas = 0;
            $total = 0;

            foreach($saida->tiposDespesa as $despesa){
                    if($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S'){
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
            }

            if(array_key_exists($saida->correspondente->cd_conta_con, $saidasVetor)){
                   $saida->honorario->vl_taxa_honorario_correspondente_pth += round($saidasVetor[$saida->correspondente->cd_conta_con]['valor'],2);
                   $totalDespesas += $saidasVetor[$saida->correspondente->cd_conta_con]['despesa'];
            }

            $total = $saida->honorario->vl_taxa_honorario_correspondente_pth + $totalDespesas;

            $saidasVetor[$saida->correspondente->cd_conta_con] = array('correspondente' => $saida->correspondente->contaCorrespondente->nm_conta_correspondente_ccr, 'valor' => $saida->honorario->vl_taxa_honorario_correspondente_pth, 'despesa' => $totalDespesas, 'total' => $total);
        }

        if(!empty($request->despesas)){
            $despesas = Despesa::where('cd_conta_con',$this->conta)
                                ->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_pagamento_des',[$dtInicioBaixa,$dtFimBaixa]);
                                 }) 
                                 ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_pagamento_des',$dtInicioBaixa);
                                 })
                                 ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_pagamento_des',$dtFimBaixa);
                                 })
                                 ->get()
                                 ->sortBy('tipo.categoriaDespesa.nm_categoria_despesa_cad');
        }else{
            $despesas = array();
        }

        $despesasVetor = [];

        foreach ($despesas as $despesa) {
            
            if(array_key_exists($despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad, $despesasVetor)){

                $despesa->vl_valor_des += $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad]['valor'];

            }

            $despesasVetor[$despesa->tipo->categoriaDespesa->cd_categoria_despesa_cad] = array('despesa' => $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad, 'valor' => $despesa->vl_valor_des);

        }

        $dados = array('entradas' => $entradasVetor,'conta' => $conta,'saidas' => $saidasVetor, 'despesas' => $despesasVetor,'flagEntradas' => $request->entradas,'flagSaidas' => $request->saidas, 'flagDespesas' => $request->despesas);    

        
        \Excel::store(new BalancoSumarizadoExport($dados),"/financeiro/balanco/{$this->conta}/".time().'_Relatório_Sumarizado.xlsx','reports',\Maatwebsite\Excel\Excel::XLSX);
        
    }

    public function relatorioBalancoDetalhado($request){

        $dtInicio       = $request->dtInicio;
        $dtFim          = $request->dtFim;
        $dtInicioBaixa  = $request->dtInicioBaixa;
        $dtFimBaixa     = $request->dtFimBaixa;
        $finalizado     = $request->finalizado;
        $cliente        = $request->cd_cliente_cli;
        $correspondente = $request->cd_correspondente_cor;

        $conta = Conta::where('cd_conta_con',$this->conta)->select('nm_razao_social_con')->first();

        if(!empty($request->entradas)){
            $entradas = Processo::whereHas('honorario', function($query) use ($dtInicioBaixa,$dtFimBaixa){
                                    $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_baixa_cliente_pth',[$dtInicioBaixa,$dtFimBaixa]);
                                    }); 

                                    $query->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_baixa_cliente_pth',$dtInicioBaixa);
                                    });

                                    $query->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_baixa_cliente_pth',$dtFimBaixa);
                                    });

                                 })
                                 ->with('cliente')                            
                                 ->with('tiposDespesa')
                                 ->where('cd_conta_con',$this->conta)        
                                 ->when(!empty($cliente), function ($query) use ($cliente) {
                                        return $query->where('cd_cliente_cli',$cliente);
                                 }) 
                                 ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                        return $query->where('cd_correspondente_cor',$correspondente);
                                 })   
                                 ->when(!empty($finalizado), function ($query){
                                        return $query->where('cd_status_processo_stp',\StatusProcesso::FINALIZADO);
                                 })    
                                 ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
                                        $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));
                                        return $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
                                 })    
                                 ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {                                  
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtInicio);
                                 })    
                                 ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                        
                                        $dtFim = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtFim);
                                 })    
                                 ->orderBy('dt_prazo_fatal_pro')
                                 ->get();
        }else{
            $entradas = array();
        }

        if(!empty($request->saidas)){
            $saidas = Processo::whereHas('honorario.tipoServicoCorrespondente')
                                ->whereHas('honorario', function($query) use ($dtInicioBaixa,$dtFimBaixa){
                                    $query->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_baixa_correspondente_pth',[$dtInicioBaixa,$dtFimBaixa]);
                                    }); 

                                    $query->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_baixa_correspondente_pth',$dtInicioBaixa);
                                    });

                                    $query->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_baixa_correspondente_pth',$dtFimBaixa);
                                    });
                                })
                                ->whereHas('correspondente')                            
                                ->with('tiposDespesa')
                                ->where('cd_conta_con',$this->conta)
                                ->when(!empty($cliente), function ($query) use ($cliente) {
                                        return $query->where('cd_cliente_cli',$cliente);
                                }) 
                                ->when(!empty($correspondente), function ($query) use ($correspondente) {
                                        return $query->where('cd_correspondente_cor',$correspondente);
                                })   
                                ->when(!empty($finalizado), function ($query){
                                        return $query->where('cd_status_processo_stp',\StatusProcesso::FINALIZADO);
                                })  
                                ->when(!empty($dtInicio) && !empty($dtFim), function ($query) use ($dtInicio,$dtFim) {
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
                                        $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));
                                        return $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
                                })    
                                ->when(!empty($dtInicio) && empty($dtFim), function ($query) use ($dtInicio) {                                  
                                        $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtInicio);
                                })    
                                ->when(empty($dtInicio) && !empty($dtFim), function ($query) use ($dtFim) {
                                        
                                        $dtFim = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));                 
                                        return $query->where('dt_prazo_fatal_pro',$dtFim);
                                })   
                                ->orderBy('dt_prazo_fatal_pro') 
                                ->get();
        }else{
            $saidas = array();
        }

        if(!empty($request->despesas)){
            $despesas = Despesa::where('cd_conta_con',$this->conta)
                                 ->when(!empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtInicioBaixa,$dtFimBaixa) {
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));
                                        $dtFimBaixa    = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));
                                        return $query->whereBetween('dt_pagamento_des',[$dtInicioBaixa,$dtFimBaixa]);
                                 }) 
                                 ->when(!empty($dtInicioBaixa) && empty($dtFimBaixa), function ($query) use ($dtInicioBaixa) {
                                        
                                        $dtInicioBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtInicioBaixa)));                 
                                        return $query->where('dt_pagamento_des',$dtInicioBaixa);
                                 })
                                 ->when(empty($dtInicioBaixa) && !empty($dtFimBaixa), function ($query) use ($dtFimBaixa) {
                                        
                                        $dtFimBaixa = date('Y-m-d', strtotime(str_replace('/','-',$dtFimBaixa)));                 
                                        return $query->where('dt_pagamento_des',$dtFimBaixa);
                                 })
                                 ->orderBy('dt_vencimento_des')
                                 ->get();
        }else{
            $despesas = array();
        }

        $dados = array('entradas' => $entradas,'conta' => $conta,'saidas' => $saidas, 'despesas' => $despesas,'flagEntradas' => $request->entradas,'flagSaidas' => $request->saidas, 'flagDespesas' => $request->despesas);    

        \Excel::store(new BalancoDetalhadoExport($dados),"/financeiro/balanco/{$this->conta}/".time().'_Relatório_Detalhado.xlsx','reports',\Maatwebsite\Excel\Excel::XLSX);

    }

    public function relatorios(){
        return view('financeiro/relatorios',['arquivos' => $this->getFiles()]);
    }

    public function relatorioBuscar(Request $request){

        $erro = false;
        if(!empty($request->dtInicio)){
            if(\Helper::validaData($request->dtInicio) != true){
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if(!empty($request->dtFim)){
            if(\Helper::validaData($request->dtFim) != true){
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if(!empty($request->dtInicioBaixa)){
            if(\Helper::validaData($request->dtInicioBaixa) != true){
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if(!empty($request->dtFimBaixa)){
            if(\Helper::validaData($request->dtFimBaixa) != true){
                $erro = true;
                Flash::error('Data(s) inválida(s) !');
            }
        }

        if($erro == false){

            if($request->relatorio == 'relatorio-por-processo'){
                $this->relatorioBalancoDetalhado($request);
            }

            if($request->relatorio == 'relatorio-sumarizado'){
                $this->relatorioBalancoSumarizado($request);   
            }
        }

        if(empty($request->despesas))
            $request->despesas = 'N';

        if(empty($request->saidas))
            $request->saidas = 'N';

        if(empty($request->entradas ))
            $request->entradas = 'N';
        

        return \Redirect::back()->with('dtInicio',str_replace('/','',$request->dtInicio))
                                ->with('dtFim' ,str_replace('/','',$request->dtFim))
                                ->with('dtInicioBaixa',str_replace('/','',$request->dtInicioBaixa))
                                ->with('dtFimBaixa' ,str_replace('/','',$request->dtFimBaixa))
                                ->with('relatorio',$request->relatorio)                                
                                ->with('finalizado',$request->finalizado)
                                ->with('cliente',$request->cd_cliente_cli)
                                ->with('nmCliente',$request->nm_cliente_cli)
                                ->with('correspondente',$request->cd_correspondente_cor)
                                ->with('nmCorrespondente',$request->nm_correspondente_cor)
                                ->with('despesas',$request->despesas)
                                ->with('saidas',$request->saidas)
                                ->with('entradas',$request->entradas);
        
    }

    private function getFiles(){

        \File::makeDirectory(storage_path().'/reports/financeiro/balanco/'.$this->conta, $mode = 0777, true, true);

        $arquivos = array();

        $files = collect(\File::allFiles(storage_path()."/reports/financeiro/balanco/".$this->conta))
                 ->sortByDesc(function ($file) {
                    return $file->getCTime();
                });
        
        foreach($files as $file){
            
            $arquivos[] = array('nome' => $file->getFilename(), 'data' => date('d/m/Y H:i:s',$file->getCTime()),'tamanho' => round($file->getSize()/1024,2) );           
        }

        return $arquivos;
    }

    public function excluir($nome){
    
       \Storage::disk('reports')->delete("/financeiro/balanco/$this->conta/".$nome);
        
        return \Response::json(array('message' => 'Registro excluído com sucesso'), 200);    

    }

    public function arquivo($nome){
        //dd(\Storage::disk('reports')->get("$this->conta/".$nome));
        return response()->download(storage_path('reports/financeiro/balanco/'."$this->conta/".$nome));

    }

}