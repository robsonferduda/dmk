<?php

namespace App\Console\Commands;

use App\Services\Cadastro\CampanhaAtualizacaoService;
use Illuminate\Console\Command;

class EnviarLoteCampanhaCadastro extends Command
{
    protected $signature = 'cadastro:enviar-lote {--quantidade= : e-mails nesta execução (padrão: config cadastro.lote)}';

    protected $description = 'Envia o próximo lote da atualização cadastral respeitando o limite diário';

    public function handle(CampanhaAtualizacaoService $service)
    {
        $resultado = $service->enviarLote($this->option('quantidade') ? (int) $this->option('quantidade') : null, function ($envio) {
            $this->line(sprintf('%s %s', $envio->dc_status_cme === 'enviado' ? 'enviado' : 'FALHA  ', $envio->dc_email_cme));
        });

        $reavaliados = $service->atualizarPendencias();

        $this->info(sprintf(
            '%s · %d enviado(s), %d falha(s), %d pendência(s) reavaliada(s)%s',
            now()->format('d/m H:i'), $resultado['enviados'], $resultado['falhas'], $reavaliados,
            $resultado['motivo'] ? ' · ' . $resultado['motivo'] : ''
        ));

        return 0;
    }
}
