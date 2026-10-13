<?php

namespace App\Http\Controllers;

use App\Services\Cadastro\CampanhaAtualizacaoService;
use App\Services\Cadastro\FormularioCadastro;
use Illuminate\Support\Facades\Log;

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

    /**
     * Botão dos e-mails da primeira versão: agora leva direto ao formulário, sem login.
     */
    public function atualizar($token)
    {
        $envio = $this->service->envioDoToken($token);
        if (! $envio) {
            return redirect()->route('autenticacao.correspondente');
        }

        return redirect(FormularioCadastro::url($envio->cd_conta_correspondente_ccr));
    }
}
