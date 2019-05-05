<?php

namespace App\Http\Controllers;

use DB;
use App\Cidade;
use App\CidadeAtuacao;
use App\Http\Requests\CidadeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CidadeController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function buscaCidadePorEstado($estados)
    {

        $estados = explode(",", $estados);

        $cidades = Cidade::select('cd_cidade_cde','nm_cidade_cde','cd_estado_est')->whereIn('cd_estado_est', $estados)->orderBy('nm_cidade_cde')->with(['estado' => function($query) {
                $query->select('cd_estado_est', 'sg_estado_est');
        }])->orderBy('nm_cidade_cde')->get(); 
        echo json_encode($cidades);
    }

    public function buscaCidadePorEstadoCorrespondente($entidade,$estado)
    {

        $cidades = DB::table('public.cidade_atuacao_cat')
                       ->select('public.cidade_cde.cd_cidade_cde','nm_cidade_cde','public.cidade_cde.cd_estado_est')
                       ->join('public.cidade_cde', function($join) use ($estado){

                        $join->on('public.cidade_atuacao_cat.cd_cidade_cde','=','public.cidade_cde.cd_cidade_cde');
                        $join->where('cd_estado_est','=',$estado);

                       })
                       ->join('public.estado_est','public.cidade_cde.cd_estado_est','=','public.estado_est.cd_estado_est')
                       ->where('cd_entidade_ete',$entidade)
                       ->get();

        echo json_encode($cidades);
    }

}