<?php

namespace App\Http\Controllers;

use DB;
use App\Contato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class ContatoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
    	$contatos = array();
    	$contato = new Contato();

    	if(session('inicial')){

    		$inicial = session('inicial');
    		$contatos = $contato->where('cd_conta_con',$this->conta)->where('nm_contato_cot', 'LIKE', $inicial.'%')->get();
    	}

    	return view('contato/index',['contatos' => $contatos]);
    }

    public function buscar($inicial)
    {
    	Session::put('inicial',$inicial); 
       	return redirect('contatos');
    }

    public function novo(){

    	$c = Contato::create([
    		'cd_conta_con'              => $this->conta, 
            'cd_entidade_ete'           => 100,
            'cd_entidade_contato_ete'   => 100,
            'cd_tipo_contato_tct'       => 1,
            'nm_contato_cot'            => "Bárbara"
        ]);

    }
}