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
            $dtInicio = '';
            $dtFim = '';

        }else{
            $dtInicio = session('dtInicio');
            $dtFim = session('dtFim');
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
        }))->has('processo');

        if(!empty($dtInicio) && !empty($dtFim)){

            $dtInicio = date('Y-m-d', strtotime(str_replace('/','-',$dtInicio)));
            $dtFim    = date('Y-m-d', strtotime(str_replace('/','-',$dtFim)));

            $entradas = $entradas->whereHas('processo', function($query) use ($dtInicio, $dtFim) {
                    $query->whereBetween('dt_prazo_fatal_pro',[$dtInicio,$dtFim]);
            });
        }

        $entradas = $entradas->where('fl_pago_cliente_pth','N')->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_tse','fl_pago_cliente_pth')->get()->sortBy('processo.dt_prazo_fatal_pro');

        \Session::put('flBuscar',false);

        return view('financeiro/entrada',['entradas' => $entradas])->with(['dtInicio' => $dtInicio,'dtFim' => $dtFim]);
    }

    public function entradaBuscar(Request $request){

        if(\Helper::validaData($request->dtInicio) && \Helper::validaData($request->dtFim) && strtotime(str_replace('/','-',$request->dtInicio)) <= strtotime(str_replace('/','-',$request->dtFim))){

           

            \Session::put('dtInicio',$request->dtInicio);
            \Session::put('dtFim',$request->dtFim);
            \Session::put('flBuscar',true);
            

        }else{

            Flash::error('Data(s) inválida(s) !');
        }

        //dd($entradas);

        return redirect('financeiro/entrada');

    }

    public function baixaCliente(Request $request){

        $processoTaxaHonorario = ProcessoTaxaHonorario::where('cd_conta_con', $this->conta)->where('cd_processo_taxa_honorario_pth',$request->id)->first();
            
        $processoTaxaHonorario->fl_pago_cliente_pth = $request->checked;

        if($processoTaxaHonorario->saveOrFail()){
            echo json_encode(true);
        }else{
            echo json_encode(false);
        }
    }

}