<?php

namespace App\Http\Controllers\Auth;

use DB;
use App\User;
use App\Nivel;
use App\PasswordResets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $hasher;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(HasherContract $hasher)
    {
        $this->middleware('guest');
        $this->hasher = $hasher;
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }


    public function showResetForm(Request $request, $token = null)
    {

        $perfil = User::where('email',urldecode($request->email))->get();

        $niveis = DB::table('users')->where('email', urldecode($request->email))->get()->toArray();
        $n = array_column($niveis, 'cd_nivel_niv');

        if(count($n) > 1){
            $niveis = Nivel::whereIn('cd_nivel_niv',$n)->get();
        }else{
            $niveis = $n;
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email, 'niveis' => $niveis]
        );
    }

    public function reset(Request $request)
    {

        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.


        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) use ($request) {

                $user = User::where('cd_nivel_niv',$request->nivel)->where('email',$request->email)->first();
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.

        if($response == Password::PASSWORD_RESET){

            Session::put('SESSION_CD_CONTA', Auth::user()->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
            Session::put('SESSION_CD_ENTIDADE', Auth::user()->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 
            Session::put('SESSION_NIVEL', Auth::user()->cd_nivel_niv);
        }


        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($response)
                    : $this->sendResetFailedResponse($request, $response);
    }

}
