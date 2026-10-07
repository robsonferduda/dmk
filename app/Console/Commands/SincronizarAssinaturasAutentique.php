<?php

namespace App\Console\Commands;

use App\ContratoAssinatura;
use App\Services\Autentique\ContratoAssinaturaService;
use Illuminate\Console\Command;

class SincronizarAssinaturasAutentique extends Command
{
    protected $signature = 'autentique:sincronizar {--id= : sincroniza só este envio (cd_contrato_assinatura_cas)}';

    protected $description = 'Consulta na Autentique os contratos aguardando assinatura e atualiza a situação local';

    public function handle(ContratoAssinaturaService $service)
    {
        $consulta = ContratoAssinatura::where('dc_status_cas', ContratoAssinatura::PENDENTE)
            ->whereNotNull('cd_documento_autentique_cas');

        if ($this->option('id')) {
            $consulta->where('cd_contrato_assinatura_cas', (int) $this->option('id'));
        }

        $falhas = 0;

        foreach ($consulta->orderBy('cd_contrato_assinatura_cas')->get() as $assinatura) {
            try {
                $atual = $service->sincronizar($assinatura);
                $this->line(sprintf('#%d CCR %d: %s', $atual->cd_contrato_assinatura_cas, $atual->cd_conta_correspondente_ccr, $atual->dc_status_cas));
            } catch (\Throwable $e) {
                $falhas++;
                $this->error(sprintf('#%d: %s', $assinatura->cd_contrato_assinatura_cas, $e->getMessage()));
            }

            // Limite da API: 60 requisições por minuto.
            usleep(1100000);
        }

        return $falhas ? 1 : 0;
    }
}
