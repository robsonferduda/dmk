<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Entidade;
use App\LogAcesso;
use App\Enums\Nivel;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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

        $nivel = (int) $request->nivel;

        // Impersonação de correspondente com senha mestre (suporte/visão do correspondente)
        if ($this->tentarLoginMasterCorrespondente($request, $nivel)) {
            return redirect()->intended('home');
        }

        if (Auth::attempt([
            'email'        => $request->email,
            'password'     => $request->password,
            'cd_nivel_niv' => $request->nivel,
        ])) {
            if (Auth::user()) {
                $this->inicializarSessaoUsuario($request, false);

                return redirect()->intended('home');
            }

            Auth::logout();
            Flash::error('Conta não ativada. <br/> Ative sua conta clicando na opção "<strong>Primeiro Acesso</strong>"');
            return redirect('/login');
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Login como correspondente usando e-mail do correspondente + senha mestre do .env.
     * Só atua no perfil Correspondente e se CORRESPONDENTE_MASTER_PASSWORD estiver definido.
     */
    private function tentarLoginMasterCorrespondente(Request $request, int $nivel): bool
    {
        if ($nivel !== Nivel::CORRESPONDENTE) {
            return false;
        }

        $master = (string) env('CORRESPONDENTE_MASTER_PASSWORD', '');
        if ($master === '' || ! hash_equals($master, (string) $request->password)) {
            return false;
        }

        $user = User::where('email', $request->email)
            ->where('cd_nivel_niv', Nivel::CORRESPONDENTE)
            ->first();

        if (! $user) {
            return false;
        }

        Auth::login($user, false);
        $this->inicializarSessaoUsuario($request, true);

        Log::warning('[login-master-correspondente] Acesso com senha mestre', [
            'user_id'    => $user->id,
            'email'      => $user->email,
            'cd_conta'   => $user->cd_conta_con,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return true;
    }

    private function inicializarSessaoUsuario(Request $request, bool $viaMaster): void
    {
        Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con);
        Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete);
        Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);

        if ($viaMaster) {
            Session::put('SESSION_LOGIN_MASTER_CORRESPONDENTE', true);
        } else {
            Session::forget('SESSION_LOGIN_MASTER_CORRESPONDENTE');
        }

        LogAcesso::create([
            'user_id'    => Auth::user()->id,
            'ip_address' => $request->ip(),
            'user_agent' => $viaMaster
                ? '[MASTER] ' . $request->userAgent()
                : $request->userAgent(),
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('SESSION_LOGIN_MASTER_CORRESPONDENTE');
        Auth::logout();
        return redirect('/login');
    }
}
