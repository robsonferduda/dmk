<?php

namespace App\Console\Commands;

use App\CampanhaCadastro;
use App\CampanhaCadastroEnvio;
use App\ContaCorrespondente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ZerarCampanhaCadastro extends Command
{
    protected $signature = 'cadastro:zerar
        {--conta=64 : conta do escritório dona das campanhas}
        {--executar : apaga de fato; sem esta opção só mostra o que seria apagado}';

    protected $description = 'Apaga as campanhas de atualização cadastral da conta e os envios, zerando os contadores do painel';

    public function handle()
    {
        $conta = (int) $this->option('conta');
        $campanhas = CampanhaCadastro::where('cd_conta_con', $conta)->pluck('cd_campanha_cadastro_cmc');
        if ($campanhas->isEmpty()) {
            $this->info("Nenhuma campanha na conta {$conta}.");
            return 0;
        }

        $envios = CampanhaCadastroEnvio::whereIn('cd_campanha_cadastro_cmc', $campanhas);
        $resumo = (clone $envios)->selectRaw('count(*) total, count(dt_envio_cme) enviados, count(dt_abertura_cme) abertos, count(dt_clique_cme) cliques, count(dt_confirmacao_cme) confirmados')->first();

        // O fluxo antigo marcava o vínculo como "Acessado" só por clicar no e-mail; quem enviou o formulário continua marcado.
        $soClicaram = (clone $envios)->whereNotNull('dt_clique_cme')->whereNull('dt_confirmacao_cme')
            ->pluck('cd_conta_correspondente_ccr')->unique()->values();

        $this->table(['Campanhas', 'Envios', 'Enviados', 'Abertos', 'Cliques', 'Confirmados', 'Vínculos voltam para "Pendente"'], [[
            $campanhas->count(), $resumo->total, $resumo->enviados, $resumo->abertos, $resumo->cliques, $resumo->confirmados, $soClicaram->count(),
        ]]);

        if (! $this->option('executar')) {
            $this->comment('Nada foi apagado. Rode de novo com --executar para zerar.');
            return 0;
        }
        if (! $this->confirm('Apagar definitivamente esses dados?')) {
            return 1;
        }

        DB::transaction(function () use ($campanhas, $soClicaram) {
            foreach ($soClicaram->chunk(500) as $lote) {
                ContaCorrespondente::whereIn('cd_conta_correspondente_ccr', $lote)->update(['fl_atualizacao_cadastro_ccr' => false]);
            }
            CampanhaCadastroEnvio::whereIn('cd_campanha_cadastro_cmc', $campanhas)->delete();
            CampanhaCadastro::whereIn('cd_campanha_cadastro_cmc', $campanhas)->delete();
        });

        $this->info('Contadores zerados. Em /v2/atualizacao-cadastral, monte a nova lista e inicie o envio.');

        return 0;
    }
}
