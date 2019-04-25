<?php

namespace App\Http\Controllers;

use App\Cidade;
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

}