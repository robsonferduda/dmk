<?php

namespace App\Http\Controllers;

use App\Services\Cadastro\CampanhaAtualizacaoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Links do e-mail de atualização cadastral (sem login): pixel de abertura e botão "atualizar cadastro".
 */
class CadastroCampanhaPublicoController extends Controller
{
    const GIF_TRANSPARENTE = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /** @var CampanhaAtualizacaoService */
    private $service;

    public function __construct(CampanhaAtualizacaoService $service)
    {
        $this->service = $service;
    }

    public function abertura($token)
    {
        try {
            $this->service->registrarAbertura($token);
        } catch (\Throwable $e) {
            Log::warning('[cadastro] Falha ao registrar abertura: ' . $e->getMessage());
        }

        return response(base64_decode(self::GIF_TRANSPARENTE), 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function atualizar($token)
    {
        $envio = null;
        try {
            $envio = $this->service->registrarClique($token);
        } catch (\Throwable $e) {
            Log::warning('[cadastro] Falha ao registrar clique: ' . $e->getMessage());
        }

        if ($envio) {
            Session::put('SESSION_ATUALIZACAO_CCR', $envio->cd_conta_correspondente_ccr);
        }

        return redirect()->route('autenticacao.correspondente')
            ->with('status', 'Faça login com seu e-mail e senha para atualizar seus dados cadastrais.');
    }
}
