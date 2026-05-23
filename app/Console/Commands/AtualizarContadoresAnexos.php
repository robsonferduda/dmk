<?php

namespace App\Console\Commands;

use App\AnexoProcesso;
use App\Processo;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AtualizarContadoresAnexos extends Command
{
    protected $signature = 'processos:atualizar-contadores-anexos
                            {--desde=2026-05-01 : Data de criação mínima dos processos (Y-m-d)}';

    protected $description = 'Atualiza nu_total_arquivos_pro e nu_tamanho_anexos_pro em todos os processos criados a partir da data informada';

    public function handle()
    {
        $desde = Carbon::createFromFormat('Y-m-d', $this->option('desde'))->startOfDay();

        $processos = Processo::where('created_at', '>=', $desde)
                             ->select('cd_processo_pro')
                             ->orderBy('cd_processo_pro')
                             ->get();

        $total = $processos->count();

        if ($total === 0) {
            $this->info("Nenhum processo encontrado a partir de {$desde->format('d/m/Y')}.");
            return 0;
        }

        $this->info("Atualizando contadores de {$total} processo(s) criado(s) a partir de {$desde->format('d/m/Y')}...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $atualizados = 0;

        foreach ($processos as $processo) {
            $id = $processo->cd_processo_pro;

            $anexos = AnexoProcesso::where('cd_processo_pro', $id)->get();

            $totalArquivos = 0;
            $tamanhoTotal  = 0;

            foreach ($anexos as $anexo) {
                $caminho = storage_path($anexo->nm_local_anexo_processo_apr . $anexo->nm_anexo_processo_apr);
                if (file_exists($caminho)) {
                    $totalArquivos++;
                    $tamanhoTotal += filesize($caminho);
                }
            }

            Processo::where('cd_processo_pro', $id)->update([
                'nu_total_arquivos_pro' => $totalArquivos,
                'nu_tamanho_anexos_pro' => round($tamanhoTotal / 1048576, 4), // bytes → MB
            ]);

            $atualizados++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Concluído. {$atualizados} processo(s) atualizado(s).");

        return 0;
    }
}
