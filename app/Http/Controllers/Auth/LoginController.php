<?php

namespace App\Http\Controllers\Auth;

use Auth;
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

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'cd_nivel_niv' => \Nivel::ADMIN]) or Auth::attempt(['email' => $request->email, 'password' => $request->password, 'cd_nivel_niv' => \Nivel::COLABORADOR])) {

            Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
            Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 

            Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);

            dd(Session::get('SESSION_CD_CONTA'));
            
            return redirect()->intended('home');
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    public function loginCorrespondente(Request $request)
    {

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }


        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'cd_nivel_niv' => \Nivel::CORRESPONDENTE ])) {

            Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
            Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 
            Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);
            return redirect()->intended('home');
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
}
