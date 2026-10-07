<?php

namespace App\Http\Controllers;

use App\Enums\Nivel;
use App\Services\Cadastro\CampanhaAtualizacaoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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
            $usuario = Auth::user();
            if ($usuario && (int) $usuario->cd_nivel_niv === Nivel::CORRESPONDENTE
                && $envio->vinculo && (int) $envio->vinculo->cd_correspondente_cor === (int) $usuario->cd_conta_con) {
                return redirect('correspondente/ficha/' . Crypt::encrypt($envio->cd_conta_correspondente_ccr));
            }

            Session::put('SESSION_ATUALIZACAO_CCR', $envio->cd_conta_correspondente_ccr);
        }

        return redirect()->route('autenticacao.correspondente')
            ->with('status', 'Faça login com seu e-mail e senha para atualizar seus dados cadastrais.');
    }
}
