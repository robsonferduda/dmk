<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use App\User;
use App\Conta;
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

    	$input = $request->all();
    	$email = $input['email']; 
    	$nome  = $input['nm_razao_social_con'];
    	$senha = $input['password'];

    	$conta = new Conta();        
        $conta->fill($request->all());
        $conta->saveOrFail();

        if($conta->cd_conta_con){

        	$user = new User();
        	$user->cd_conta_con = $conta->cd_conta_con;
        	$user->name = $nome;
        	$user->email = $email;
        	$user->password = Hash::make($senha);
        	$user->save();

        	Auth::login($user);

        	return redirect('home');
        }

    }

}