<?php

namespace App\Services\Contrato;

use App\ContaCorrespondente;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ContratoCorrespondenteGenerator
{
    const TEMPLATE = 'contratos/contrato-correspondencia-2026.docx';

    /**
     * Gera PDF a partir do modelo DOCX (texto integral, sem variáveis ainda)
     * e atualiza flag/data/caminho no vínculo do correspondente.
     */
    public function gerar(ContaCorrespondente $vinculo): string
    {
        $template = resource_path(self::TEMPLATE);

        if (! is_file($template)) {
            throw new RuntimeException('Modelo de contrato não encontrado: ' . self::TEMPLATE);
        }

        $libreOffice = $this->binarioLibreOffice();
        if (! $libreOffice) {
            throw new RuntimeException('LibreOffice não está disponível no servidor para converter o contrato.');
        }

        $workDir = storage_path('app/tmp/contrato-' . $vinculo->cd_conta_correspondente_ccr . '-' . uniqid());
        if (! File::isDirectory($workDir) && ! File::makeDirectory($workDir, 0775, true) && ! File::isDirectory($workDir)) {
            throw new RuntimeException('Não foi possível criar diretório temporário para o contrato.');
        }

        $docxPath = $workDir . '/contrato.docx';
        $pdfWork  = $workDir . '/contrato.pdf';

        try {
            if (! @copy($template, $docxPath)) {
                throw new RuntimeException('Falha ao copiar o modelo de contrato.');
            }

            $cmd = sprintf(
                '%s --headless --nologo --nofirststartwizard --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg($libreOffice),
                escapeshellarg($workDir),
                escapeshellarg($docxPath)
            );

            $output = [];
            $code   = 0;
            exec($cmd, $output, $code);

            if ($code !== 0 || ! is_file($pdfWork)) {
                throw new RuntimeException(
                    'Falha na conversão do contrato para PDF. '
                    . implode(' ', $output)
                );
            }

            $relativeDir = 'contratos-correspondente/' . $vinculo->cd_conta_correspondente_ccr;
            $absoluteDir = storage_path('app/public/' . $relativeDir);

            if (! File::isDirectory($absoluteDir) && ! File::makeDirectory($absoluteDir, 0775, true) && ! File::isDirectory($absoluteDir)) {
                throw new RuntimeException('Não foi possível criar o diretório de contratos.');
            }

            $fileName = 'contrato-' . Carbon::now()->format('Ymd-His') . '.pdf';
            $relative = $relativeDir . '/' . $fileName;
            $absolute = storage_path('app/public/' . $relative);

            if (! @rename($pdfWork, $absolute) && ! @copy($pdfWork, $absolute)) {
                throw new RuntimeException('Falha ao gravar o PDF do contrato.');
            }

            // Remove PDF antigo do vínculo, se houver.
            if ($vinculo->dc_caminho_contrato_ccr
                && $vinculo->dc_caminho_contrato_ccr !== $relative
                && is_file(storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr))) {
                @unlink(storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr));
            }

            $vinculo->fl_contrato_gerado_ccr   = true;
            $vinculo->dt_contrato_gerado_ccr   = Carbon::now();
            $vinculo->dc_caminho_contrato_ccr  = $relative;
            $vinculo->save();

            return $relative;
        } finally {
            File::deleteDirectory($workDir);
        }
    }

    public function caminhoAbsoluto(ContaCorrespondente $vinculo): ?string
    {
        if (! $vinculo->dc_caminho_contrato_ccr) {
            return null;
        }

        $path = storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr);

        return is_file($path) ? $path : null;
    }

    private function binarioLibreOffice(): ?string
    {
        foreach (['/usr/bin/libreoffice', '/usr/bin/soffice', 'libreoffice', 'soffice'] as $bin) {
            if ($bin[0] === '/' && is_executable($bin)) {
                return $bin;
            }

            $which = trim((string) shell_exec('command -v ' . escapeshellarg($bin) . ' 2>/dev/null'));
            if ($which !== '' && is_executable($which)) {
                return $which;
            }
        }

        return null;
    }
}
