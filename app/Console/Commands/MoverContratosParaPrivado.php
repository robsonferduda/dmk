<?php

namespace App\Console\Commands;

use App\ContaCorrespondente;
use App\Services\Contrato\ContratoCorrespondenteGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Move os PDFs de contrato de storage/app/public (acessível por /storage) para storage/app (privado).
 * O caminho relativo gravado no vínculo não muda, então o banco não é alterado.
 *
 * Uso:
 *   php artisan contratos:mover-privado --dry-run
 *   php artisan contratos:mover-privado
 */
class MoverContratosParaPrivado extends Command
{
    protected $signature = 'contratos:mover-privado
                            {--dry-run : Apenas lista o que seria movido.}';

    protected $description = 'Move os PDFs de contrato de correspondente para fora da pasta pública.';

    public function handle(): int
    {
        $origemBase = storage_path('app/public/contratos-correspondente');

        if (! File::isDirectory($origemBase)) {
            $this->info('Nada a mover: storage/app/public/contratos-correspondente não existe.');
            return 0;
        }

        $dryRun = (bool) $this->option('dry-run');
        $movidos = 0;
        $falhas = 0;

        foreach (File::allFiles($origemBase) as $arquivo) {
            $relativo = 'contratos-correspondente/' . str_replace('\\', '/', $arquivo->getRelativePathname());
            $destino = ContratoCorrespondenteGenerator::caminhoPrivado($relativo);

            if ($dryRun) {
                $this->line("[dry-run] {$relativo}");
                $movidos++;
                continue;
            }

            if (! File::isDirectory(dirname($destino))) {
                File::makeDirectory(dirname($destino), 0775, true);
            }

            if (File::move($arquivo->getPathname(), $destino)) {
                $movidos++;
            } else {
                $falhas++;
                $this->error("Falha ao mover {$relativo}");
            }
        }

        if (! $dryRun) {
            foreach (array_reverse(File::directories($origemBase)) as $pasta) {
                if (! File::allFiles($pasta)) {
                    File::deleteDirectory($pasta);
                }
            }
            if (! File::allFiles($origemBase)) {
                File::deleteDirectory($origemBase);
            }
        }

        $semArquivo = ContaCorrespondente::whereNotNull('dc_caminho_contrato_ccr')
            ->get(['dc_caminho_contrato_ccr'])
            ->filter(function ($vinculo) {
                return ! ContratoCorrespondenteGenerator::localizarArquivo($vinculo->dc_caminho_contrato_ccr);
            })
            ->count();

        $this->info(($dryRun ? 'Seriam movidos: ' : 'Movidos: ') . $movidos . ($falhas ? " | Falhas: {$falhas}" : ''));
        if ($semArquivo) {
            $this->warn("{$semArquivo} vínculo(s) apontam para um contrato que não existe em nenhuma das pastas.");
        }

        return $falhas ? 1 : 0;
    }
}
