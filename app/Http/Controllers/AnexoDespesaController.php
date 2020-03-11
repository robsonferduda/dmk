<?php

namespace App\Http\Controllers;

use App\AnexoDespesa;
use App\Http\Requests\CidadeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AnexoDespesaController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        
    }

    public function create(Request $request)
    {
        AnexoDespesa::create([
            'cd_conta_con'   => $this->cdContaCon,
            'cd_despesa_des' => $request->id_despesa
        ]);
    }

}