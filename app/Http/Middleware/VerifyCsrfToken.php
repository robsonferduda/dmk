<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'webhooks/autentique',
        // Autorizado pelo link assinado, não pela sessão: um formulário aberto por horas não pode perder os dados ao enviar.
        'atualizar-dados/*',
    ];
}
