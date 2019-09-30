<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Conta;
use App\Processo;
use App\ProcessoMensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    private $conta;
    
    public function __construct()
    {
         $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        if(!Auth::guest()){

            $role = Auth::user()->role()->first();

            $role = ($role) ? $role->slug : null; 

            switch ($role) {
                case 'correspondente':
                    return redirect('correspondente/dashboard/'.\Crypt::encrypt(Auth::user()->cd_entidade_ete));
                    break;
                
                default:
                    $conta = Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first();
                    $processos = Processo::where('cd_conta_con',$conta->cd_conta_con)->get();
                    return view('home',['conta' => $conta, 'processos' => $processos]);
                    break;
            }
            
        }else{
            return view('conta/novo');
        }
            
    }

    public function menu(Request $request, $id)
    {
        if(session('menu_pai') == $id)
            Session::put('menu_pai', "");
        else
            Session::put('menu_pai', $id);

        return $request->url();
    }

    public function minify()
    {
        if(session('menu_minify') == 'on')
            Session::put('menu_minify', 'off');
        else
            Session::put('menu_minify', 'on');

        return back();

    }
}