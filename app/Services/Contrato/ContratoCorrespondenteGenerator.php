<?php

namespace App\Services\Contrato;

use App\ContaCorrespondente;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ContratoCorrespondenteGenerator
{
    /**
     * Gera o PDF do contrato a partir do HTML (Blade) e atualiza flag/data/caminho.
     * Variáveis do CONTRATADO entrarão depois; por enquanto usa o texto-base com lacunas.
     */
    public function gerar(ContaCorrespondente $vinculo): string
    {
        $html = view('correspondente.contrato-pdf', [
            'textoPartes' => $this->textoPartesPadrao(),
            'vinculo'     => $vinculo,
        ])->render();

        $relativeDir = 'contratos-correspondente/' . $vinculo->cd_conta_correspondente_ccr;
        $absoluteDir = storage_path('app/public/' . $relativeDir);

        if (! File::isDirectory($absoluteDir) && ! File::makeDirectory($absoluteDir, 0775, true) && ! File::isDirectory($absoluteDir)) {
            throw new RuntimeException('Não foi possível criar o diretório de contratos.');
        }

        $tmpDir = storage_path('app/mpdf-tmp');
        if (! File::isDirectory($tmpDir) && ! File::makeDirectory($tmpDir, 0775, true) && ! File::isDirectory($tmpDir)) {
            throw new RuntimeException('Não foi possível criar o diretório temporário do PDF.');
        }

        $fileName = 'contrato-' . Carbon::now()->format('Ymd-His') . '.pdf';
        $relative = $relativeDir . '/' . $fileName;
        $absolute = storage_path('app/public/' . $relative);

        $mpdf = new \Mpdf\Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'margin_left'       => 18,
            'margin_right'      => 18,
            'margin_top'        => 18,
            'margin_bottom'     => 18,
            'tempDir'           => $tmpDir,
            'default_font'      => 'dejavusans',
        ]);

        $mpdf->SetTitle('Contrato de Correspondência');
        $mpdf->SetAuthor('DMK');
        $mpdf->WriteHTML($html);
        $mpdf->Output($absolute, \Mpdf\Output\Destination::FILE);

        if (! is_file($absolute)) {
            throw new RuntimeException('Falha ao gravar o PDF do contrato.');
        }

        if ($vinculo->dc_caminho_contrato_ccr
            && $vinculo->dc_caminho_contrato_ccr !== $relative
            && is_file(storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr))) {
            @unlink(storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr));
        }

        $vinculo->fl_contrato_gerado_ccr  = true;
        $vinculo->dt_contrato_gerado_ccr  = Carbon::now();
        $vinculo->dc_caminho_contrato_ccr = $relative;
        $vinculo->save();

        return $relative;
    }

    public function caminhoAbsoluto(ContaCorrespondente $vinculo): ?string
    {
        if (! $vinculo->dc_caminho_contrato_ccr) {
            return null;
        }

        $path = storage_path('app/public/' . $vinculo->dc_caminho_contrato_ccr);

        return is_file($path) ? $path : null;
    }

    /**
     * Texto do preâmbulo das partes — lacunas do CONTRATADO até haver variáveis.
     */
    private function textoPartesPadrao(): string
    {
        return 'De um lado, DEBORAH MEKACHESKI PEREIRA SOCIEDADE INDIVIDUAL DE ADVOCACIA, '
            . 'sociedade de advogados inscrita no CNPJ/MF sob o nº 19.439.096/0001-17, '
            . 'e na Ordem dos Advogados do Brasil – Seção de Florianópolis/SC sob nº 2178/2013, '
            . 'com sede no Município de Florianópolis, Estado de Santa Catarina, '
            . 'na Rua Saldanha Marinho, 374 sala 806, Centro, CEP 88053-300, '
            . 'neste ato representada por sua sócia DEBORAH MEKACHESKI PEREIRA, '
            . 'advogada inscrita na OAB/SC sob o número 33.565B, doravante denominada CONTRATANTE '
            . 'e, de outro, __________________________, brasileiro (a), portador do RG ___________, '
            . 'inscrito (a) no CPF sob o nº ___________________, e na OAB/ ___________- sob nº ____________, '
            . 'com endereço profissional na Rua ______________________, n __________ '
            . 'Bairro: ____________, Cidade ____________ - CEP ____________ '
            . 'contato: (   ) ___________________ e-mail _______________________ '
            . 'doravante denominada CONTRATADO (A).';
    }
}
