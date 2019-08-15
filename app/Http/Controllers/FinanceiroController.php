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

    public function entradaSaidaIndex(){   

        $entrasSaidas = ProcessoTaxaHonorario::with(array('tipoServico' => function($query){
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
        }))->has('processo')->where('cd_conta_con',$this->conta)->select('cd_processo_taxa_honorario_pth','vl_taxa_honorario_cliente_pth','vl_taxa_honorario_correspondente_pth','cd_processo_pro','cd_tipo_servico_tse')->get();

        return view('financeiro/entrada-saida',['entrasSaidas' => $entrasSaidas]);
    }


}