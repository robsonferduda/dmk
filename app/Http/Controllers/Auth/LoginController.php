<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Entidade;
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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){

            //Se o candidato logou, verifica todos os níveis 
            $users = User::where('email',$request->email)->orderBy('cd_nivel_niv')->get();

            //Se possuir apenas um nível, loga com ele
            if(count($users) == 1){

                Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);

            }else{

                Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);

                return redirect()->intended('seleciona/perfil');

            }            
                
            return redirect()->intended('home');
        }


        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

}
