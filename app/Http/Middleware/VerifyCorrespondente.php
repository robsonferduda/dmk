<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class VerifyCorrespondente
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(Auth::user() and Auth::user()->cd_nivel_niv == \Nivel::CORRESPONDENTE) {
            return $next($request);
        }

        return redirect('autenticacao');
    }
}
