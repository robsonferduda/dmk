<?php

namespace App\Http\Controllers;

use App\Services\Email\EmailDiagnosticoService;
use Illuminate\Http\Request;

class EmailDiagnosticoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role()->first();

            if (! $role || $role->slug !== 'administrator') {
                abort(403, 'Acesso restrito a administradores.');
            }

            return $next($request);
        });
    }

    public function index(Request $request, EmailDiagnosticoService $service)
    {
        $resultado = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'email_teste' => 'nullable|email',
                'tipo_teste'  => 'nullable|in:simples,recuperacao_senha',
            ]);

            $resultado = $service->executar(
                $request->input('email_teste'),
                $request->input('tipo_teste', 'simples')
            );
        }

        $config = $service->getConfiguracao();

        return view('configuracoes.email-diagnostico', compact('config', 'resultado'));
    }
}
