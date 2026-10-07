<?php

namespace App\Console\Commands;

use App\Services\Cadastro\CampanhaAtualizacaoService;
use Illuminate\Console\Command;

class PrepararCampanhaCadastro extends Command
{
    protected $signature = 'cadastro:preparar-campanha
        {--conta=64 : cd_conta_con do escritório}
        {--meses=24 : correspondentes com processo nos últimos N meses}
        {--prazo=7 : dias para o correspondente atualizar, contados do envio de cada e-mail}
        {--simular : só mostra quantos entrariam, sem gravar nada}';

    protected $description = 'Monta a lista de envio da atualização cadastral (a campanha nasce pausada)';

    public function handle(CampanhaAtualizacaoService $service)
    {
        $conta = (int) $this->option('conta');
        $meses = (int) $this->option('meses');

        $candidatos = $service->candidatos($conta, $meses);
        $this->info(count($candidatos) . " correspondente(s) da conta {$conta} com processo nos últimos {$meses} meses.");

        if ($this->option('simular')) {
            foreach (array_slice($candidatos, 0, 5) as $c) {
                $this->line("  {$c->nm_conta_correspondente_ccr} <{$c->email}> último processo {$c->ultimo}");
            }
            return 0;
        }

        $campanha = $service->preparar($conta, $meses, null, (int) $this->option('prazo'));
        $this->info("Campanha #{$campanha->cd_campanha_cadastro_cmc} criada e PAUSADA. Inicie o envio pelo painel v2 › Atualização cadastral.");

        return 0;
    }
}
