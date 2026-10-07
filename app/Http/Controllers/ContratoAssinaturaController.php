<?php

namespace App\Http\Controllers;

use App\ContaCorrespondente;
use App\ContratoAssinatura;
use App\Services\Autentique\AutentiqueException;
use App\Services\Autentique\ContratoAssinaturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;

class ContratoAssinaturaController extends Controller
{
    public $conta;

    /** @var ContratoAssinaturaService */
    private $service;

    public function __construct(ContratoAssinaturaService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function enviar(Request $request, $id)
    {
        $cdCorrespondente = \Crypt::decrypt($id);
        $vinculo = $this->vinculo($cdCorrespondente);

        $canal = $request->input('canal') === ContratoAssinatura::CANAL_WHATSAPP
            ? ContratoAssinatura::CANAL_WHATSAPP
            : ContratoAssinatura::CANAL_EMAIL;
        $destino = (string) $request->input($canal === ContratoAssinatura::CANAL_WHATSAPP ? 'telefone' : 'email');

        try {
            $assinatura = $this->service->enviar($vinculo, $canal, $destino, Auth::id());
            Flash::success(
                'Contrato enviado para assinatura por ' . ($canal === ContratoAssinatura::CANAL_WHATSAPP ? 'WhatsApp' : 'e-mail') . '.'
                . ($assinatura->fl_sandbox_cas ? ' (Ambiente de testes da Autentique: o documento não tem validade jurídica.)' : '')
            );
        } catch (AutentiqueException $e) {
            Flash::error('Não foi possível enviar para assinatura: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('[autentique] Erro inesperado no envio do CCR ' . $vinculo->cd_conta_correspondente_ccr . ': ' . $e->getMessage());
            Flash::error('Não foi possível enviar para assinatura. Tente novamente em instantes.');
        }

        return $this->voltar($cdCorrespondente);
    }

    public function reenviar($id)
    {
        return $this->acaoNoEnvio($id, function (ContratoAssinatura $assinatura) {
            Flash::success($this->service->reenviar($assinatura));
        });
    }

    public function cancelar($id)
    {
        return $this->acaoNoEnvio($id, function (ContratoAssinatura $assinatura) {
            $this->service->cancelar($assinatura);
            Flash::success('Envio cancelado. As partes não conseguem mais assinar este documento.');
        });
    }

    public function sincronizar($id)
    {
        return $this->acaoNoEnvio($id, function (ContratoAssinatura $assinatura) {
            $assinatura = $this->service->sincronizar($assinatura);
            Flash::success($assinatura->dc_status_cas === ContratoAssinatura::ASSINADO
                ? 'Contrato assinado por todas as partes.'
                : 'Situação das assinaturas atualizada.');
        });
    }

    public function baixarAssinado($id)
    {
        $cdCorrespondente = \Crypt::decrypt($id);
        $vinculo = $this->vinculo($cdCorrespondente);

        $assinatura = ContratoAssinatura::where('cd_conta_correspondente_ccr', $vinculo->cd_conta_correspondente_ccr)
            ->where('dc_status_cas', ContratoAssinatura::ASSINADO)
            ->orderByDesc('cd_contrato_assinatura_cas')
            ->first();

        $caminho = $assinatura ? $this->service->caminhoAssinado($assinatura) : null;
        if (! $caminho) {
            Flash::error('Contrato assinado não disponível.');

            return $this->voltar($cdCorrespondente);
        }

        $nome = 'contrato-assinado-' . Str::slug($vinculo->nm_conta_correspondente_ccr ?: 'correspondente') . '.pdf';

        return response()->download($caminho, $nome);
    }

    private function acaoNoEnvio($id, callable $acao)
    {
        $cdCorrespondente = \Crypt::decrypt($id);
        $vinculo = $this->vinculo($cdCorrespondente);
        $assinatura = $this->service->assinaturaEmAndamento($vinculo);

        if (! $assinatura) {
            Flash::warning('Não há envio aguardando assinaturas para este correspondente.');

            return $this->voltar($cdCorrespondente);
        }

        try {
            $acao($assinatura->load('signatarios'));
        } catch (AutentiqueException $e) {
            Flash::error($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('[autentique] Erro na ação do envio ' . $assinatura->cd_contrato_assinatura_cas . ': ' . $e->getMessage());
            Flash::error('Não foi possível concluir a ação. Tente novamente em instantes.');
        }

        return $this->voltar($cdCorrespondente);
    }

    private function vinculo($cdCorrespondente): ContaCorrespondente
    {
        return ContaCorrespondente::where('cd_conta_con', $this->conta)
            ->where('cd_correspondente_cor', $cdCorrespondente)
            ->firstOrFail();
    }

    private function voltar($cdCorrespondente)
    {
        $url = request()->filled('v2')
            ? url('v2/correspondentes/' . safe_encrypt($cdCorrespondente))
            : url('correspondente/detalhes/' . \Crypt::encrypt($cdCorrespondente));

        return redirect()->to($url);
    }
}
