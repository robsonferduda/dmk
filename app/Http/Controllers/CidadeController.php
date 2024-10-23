<?php

namespace App\Http\Controllers;

use DB;
use App\Cidade;
use App\CidadeAtuacao;
use App\ContaCorrespondente;
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
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function buscaCidadePorEstado($estados)
    {
        $cidades = array();

        if($estados){
            if (empty(\Cache::tags(['estados', $estados])->get('cidades'))) {
                $cidades = Cidade::select('cd_cidade_cde', 'nm_cidade_cde', 'cd_estado_est')->whereIn('cd_estado_est', explode(",", $estados))->orderBy('nm_cidade_cde')->with(['estado' => function ($query) {
                    $query->select('cd_estado_est', 'sg_estado_est');
                }])->orderBy('nm_cidade_cde')
                ->get();

                \Cache::tags(['estados', $estados])->put('cidades', $cidades, now()->addMinutes(1440));
            } else {
                $cidades = \Cache::tags(['estados', $estados])->get('cidades');
            }
        }
        echo json_encode($cidades);
    }

    public function buscaCidadePorEstadoCorrespondente($entidade, $estado)
    {
        $cidades = array();

        $cidades = DB::table('cidade_atuacao_cat')
                    ->select('cidade_cde.cd_cidade_cde', 'nm_cidade_cde')
                    ->join('cidade_cde', 'cidade_atuacao_cat.cd_cidade_cde', '=', 'cidade_cde.cd_cidade_cde')
                    ->join('estado_est', 'estado_est.cd_estado_est', '=', 'cidade_cde.cd_estado_est')
                    ->where('cd_entidade_ete', $entidade)
                    ->where('estado_est.cd_estado_est', $estado)
                    ->whereNull('cidade_atuacao_cat.deleted_at')
                    ->get();
                             
        echo json_encode($cidades);
    }
}
