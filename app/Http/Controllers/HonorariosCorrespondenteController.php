<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\TaxaHonorario;
use App\Correspondente;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HonorariosCorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth');        
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }

    public function index()
    {

    }

    public function getHonorariosOrdenados(Request $request)
    {
    	$id = \Crypt::decrypt($request->id);

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_cidade_cde')
                                    ->groupBy('cd_cidade_cde')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                $comarcas[] = $honorario->cidade;
            }
        } 

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->select('cd_tipo_servico_tse')
                                    ->groupBy('cd_tipo_servico_tse')
                                    ->get(); 

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {
                if($honorario->tipoServico){
                    $servicos[] = $honorario->tipoServico;
                }
            }
        } 

        $honorarios = TaxaHonorario::where('cd_conta_con',$this->conta)
                                    ->where('cd_entidade_ete',$id)
                                    ->get();

        if(count($honorarios) > 0){
            foreach ($honorarios as $honorario) {

            	$chave = $honorario->cd_cidade_cde."-".$honorario->cd_tipo_servico_tse;

                $valores[$chave] = $honorario->nu_taxa_the;
                		
            }
        } 

        $dados = array('honorarios' => $valores,
    				   'comarcas' => $comarcas,
    				   'servicos' => $servicos);

    	return Response::json($dados);
    }
}