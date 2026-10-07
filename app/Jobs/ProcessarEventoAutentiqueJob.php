<?php

namespace App\Jobs;

use App\AutentiqueEvento;
use App\ContratoAssinatura;
use App\Services\Autentique\ContratoAssinaturaService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarEventoAutentiqueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 80;

    /** @var int */
    private $cdEvento;

    public function __construct(int $cdEvento)
    {
        $this->cdEvento = $cdEvento;
    }

    public function handle(ContratoAssinaturaService $service)
    {
        $evento = AutentiqueEvento::find($this->cdEvento);
        if (! $evento || $evento->dt_processado_aev) {
            return;
        }

        $assinatura = $evento->cd_documento_autentique_aev
            ? ContratoAssinatura::where('cd_documento_autentique_cas', $evento->cd_documento_autentique_aev)->first()
            : null;

        if (! $assinatura) {
            $evento->update([
                'dt_processado_aev' => Carbon::now(),
                'dc_erro_aev'       => 'Documento não pertence a um envio de contrato.',
            ]);

            return;
        }

        if ($evento->dc_tipo_aev === 'signature.delivery_failed') {
            $this->registrarFalhaEntrega($assinatura, $evento->payload());
        }

        try {
            $service->sincronizar($assinatura);
        } catch (\Throwable $e) {
            $evento->update(['dc_erro_aev' => mb_substr($e->getMessage(), 0, 1000)]);
            throw $e;
        }

        $evento->update(['dt_processado_aev' => Carbon::now(), 'dc_erro_aev' => null]);
    }

    private function registrarFalhaEntrega(ContratoAssinatura $assinatura, array $payload): void
    {
        $data = $payload['event']['data'] ?? [];
        $publicId = $data['public_id'] ?? null;
        if (! $publicId) {
            return;
        }

        $signatario = $assinatura->signatarios()->where('dc_public_id_csi', $publicId)->first();
        if (! $signatario) {
            return;
        }

        $motivo = $data['reason'] ?? $data['delivery']['reason'] ?? null;
        $signatario->update([
            'dt_falha_entrega_csi' => Carbon::now(),
            'dc_motivo_csi'        => mb_substr($motivo ?: 'Falha na entrega do pedido de assinatura.', 0, 500),
        ]);
    }
}
