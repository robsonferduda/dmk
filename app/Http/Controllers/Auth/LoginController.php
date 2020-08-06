<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Entidade;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function selecionaPerfil($perfil)
    {
        $nivel_url = \Crypt::decrypt($perfil);
        return redirect('/login')->with('nivel_url', $nivel_url);
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'cd_nivel_niv' => $request->nivel])){

            if (Auth::user() && Auth::user()->active == '1') {

                Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);
             
                return redirect()->intended('home');
                
            }else{

                Auth::logout();
                Flash::error('Conta não ativada. <br/> Ative sua conta clicando na opção "<strong>Primeiro Acesso</strong>"');
                return redirect('/login');
            }

        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

}