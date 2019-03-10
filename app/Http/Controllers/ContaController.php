<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Hash;
use App\User;
use App\Conta;
use App\Entidade;
use App\Enums\Nivel;
use Illuminate\Http\Request;
use App\Http\Requests\ContaRequest;
use Illuminate\Support\Facades\Session;

class ContaController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function store(ContaRequest $request)
    {

        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];

        	$conta = new Conta();        
            $conta->fill($request->all());
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTA;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::ADMIN;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }
}