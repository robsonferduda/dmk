<?php

namespace App\Services\Contrato;

use App\ContaCorrespondente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ContratoCorrespondenteGenerator
{
    /**
     * Gera o PDF do contrato a partir do HTML (Blade) e atualiza flag/data/caminho.
     */
    public function gerar(ContaCorrespondente $vinculo): string
    {
        $vinculo->loadMissing([
            'entidade.atuacao.cidade.estado',
            'entidade.cpf',
            'entidade.oab',
            'entidade.cnpj',
            'correspondente',
        ]);

        $pendencias = $this->pendenciasGeracao($vinculo);
        if (! empty($pendencias)) {
            throw new RuntimeException(
                'Não é possível gerar o contrato. Complete os dados abaixo e tente novamente: '
                . implode('; ', $pendencias) . '.'
            );
        }

        $dadosContratado = $this->dadosContratado($vinculo);

        $html = view('correspondente.contrato-pdf', [
            'textoPartes'     => $this->textoPartesPadrao(),
            'trechoComarcas'  => $this->montarTrechoComarcas($vinculo),
            'trechoBancario'  => $this->montarTrechoBancario($vinculo),
            'contratadaNome'  => $this->campo($dadosContratado['nome'], 28),
            'contratadaOab'   => $this->campo($dadosContratado['oab'], 18),
            'contratadaCpf'   => $this->campo($dadosContratado['cpf'], 18),
            'vinculo'         => $vinculo,
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
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 18,
            'margin_right'  => 18,
            'margin_top'    => 18,
            'margin_bottom' => 18,
            'tempDir'       => $tmpDir,
            'default_font'  => 'dejavusans',
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
     * Lista o que falta nos campos de preenchimento automático do contrato.
     *
     * @return string[]
     */
    public function pendenciasGeracao(ContaCorrespondente $vinculo): array
    {
        $vinculo->loadMissing([
            'entidade.atuacao.cidade.estado',
            'entidade.cpf',
            'entidade.oab',
            'entidade.cnpj',
            'correspondente',
        ]);
        $faltando = [];

        if (! $this->temComarcasValidas($vinculo)) {
            $faltando[] = 'comarca(s) de atuação com cidade e estado';
        }

        $dadosContratado = $this->dadosContratado($vinculo);

        if ($this->vazio($dadosContratado['nome'])) {
            $faltando[] = 'nome do correspondente';
        }

        if ($this->vazio($dadosContratado['oab'])) {
            $faltando[] = 'OAB do correspondente';
        }

        if ($this->vazio($dadosContratado['cpf'])) {
            $faltando[] = 'CPF/CNPJ do correspondente';
        }

        $banco = $this->buscarDadosBancarios($vinculo);

        if ($this->vazio($banco->nm_titular_dba ?? null)) {
            $faltando[] = 'favorecido (titular da conta)';
        }

        if ($this->vazio($this->formatarBanco($banco))) {
            $faltando[] = 'banco';
        }

        if ($this->vazio($banco->nu_agencia_dba ?? null)) {
            $faltando[] = 'agência';
        }

        if ($this->vazio($banco->nu_conta_dba ?? null)) {
            $faltando[] = 'conta';
        }

        if ($this->vazio($banco->dc_pix_dba ?? null)) {
            $faltando[] = 'chave PIX';
        }

        return $faltando;
    }

    /**
     * Nome, OAB e CPF/CNPJ do correspondente para o quadro do CONTRATADO.
     */
    public function dadosContratado(ContaCorrespondente $vinculo): array
    {
        $vinculo->loadMissing(['entidade.cpf', 'entidade.oab', 'entidade.cnpj', 'correspondente']);

        $nome = trim((string) ($vinculo->nm_conta_correspondente_ccr
            ?: optional($vinculo->correspondente)->nm_razao_social_con
            ?: optional($vinculo->correspondente)->nm_fantasia_con
            ?: ''));

        $oab = trim((string) optional(optional($vinculo->entidade)->oab)->nu_identificacao_ide);
        $cpf = trim((string) optional(optional($vinculo->entidade)->cpf)->nu_identificacao_ide);

        // Pessoa jurídica: usa CNPJ no campo de documento se não houver CPF.
        if ($cpf === '') {
            $cpf = trim((string) optional(optional($vinculo->entidade)->cnpj)->nu_identificacao_ide);
        }

        return [
            'nome' => $nome !== '' ? $nome : null,
            'oab'  => $oab !== '' ? $oab : null,
            'cpf'  => $cpf !== '' ? $cpf : null,
        ];
    }

    private function temComarcasValidas(ContaCorrespondente $vinculo): bool
    {
        $atuacoes = optional($vinculo->entidade)->atuacao ?? collect();

        foreach ($atuacoes as $atuacao) {
            $cidade = trim((string) optional($atuacao->cidade)->nm_cidade_cde);
            $estado = trim((string) optional(optional($atuacao->cidade)->estado)->nm_estado_est);

            if ($cidade !== '' && $estado !== '') {
                return true;
            }
        }

        return false;
    }

    private function vazio($valor): bool
    {
        return trim((string) $valor) === '';
    }

    /**
     * Monta o trecho "nas comarcas de X, Estado de Y" a partir de cidade_atuacao_cat.
     * Valores preenchidos em negrito.
     */
    public function montarTrechoComarcas(ContaCorrespondente $vinculo): string
    {
        $vinculo->loadMissing(['entidade.atuacao.cidade.estado']);

        $porEstado = [];
        $atuacoes = optional($vinculo->entidade)->atuacao ?? collect();

        foreach ($atuacoes as $atuacao) {
            $cidade = optional($atuacao->cidade)->nm_cidade_cde;
            $estado = optional(optional($atuacao->cidade)->estado)->nm_estado_est;

            if (! $cidade || ! $estado) {
                continue;
            }

            $cidade = trim($cidade);
            $estado = trim($estado);

            if (! isset($porEstado[$estado])) {
                $porEstado[$estado] = [];
            }

            $porEstado[$estado][$cidade] = $cidade;
        }

        if (empty($porEstado)) {
            return 'nas comarcas de _____________________, Estado de ____________________';
        }

        ksort($porEstado);

        $partes = [];

        foreach ($porEstado as $estado => $cidades) {
            $nomes = array_values($cidades);
            sort($nomes, SORT_NATURAL | SORT_FLAG_CASE);
            $nomesBold = array_map(function ($nome) {
                return $this->negrito($nome);
            }, $nomes);

            $partes[] = $this->juntarNomes($nomesBold) . ', Estado de ' . $this->negrito($estado);
        }

        if (count($partes) === 1) {
            return 'nas comarcas de ' . $partes[0];
        }

        return 'nas comarcas de ' . $this->juntarNomes($partes, ', e ');
    }

    /**
     * Dados bancários do correspondente (favorecido, banco, agência, conta, PIX).
     */
    public function montarTrechoBancario(ContaCorrespondente $vinculo): string
    {
        $banco = $this->buscarDadosBancarios($vinculo);

        $favorecido = $banco->nm_titular_dba ?? null;
        $nmBanco    = $this->formatarBanco($banco);
        $agencia    = $banco->nu_agencia_dba ?? null;
        $conta      = $banco->nu_conta_dba ?? null;
        $pix        = $banco->dc_pix_dba ?? null;

        return 'Favorecido: ' . $this->campo($favorecido, 28) . '<br>'
            . 'BANCO: ' . $this->campo($nmBanco, 18) . '. '
            . 'AGÊNCIA: ' . $this->campo($agencia, 12) . ' '
            . 'CONTA: ' . $this->campo($conta, 18) . ' '
            . 'PIX: ' . $this->campo($pix, 28) . '.';
    }

    private function buscarDadosBancarios(ContaCorrespondente $vinculo)
    {
        $entidade = $vinculo->cd_entidade_ete;
        if (! $entidade) {
            return null;
        }

        $rows = DB::select("
            SELECT
                COALESCE(main.cd_dados_bancarios_dba, pix.cd_dados_bancarios_dba) AS cd_dados_bancarios_dba,
                COALESCE(main.nm_titular_dba, pix.nm_titular_dba)                 AS nm_titular_dba,
                main.cd_banco_ban,
                ban.nm_banco_ban,
                main.nu_agencia_dba,
                main.nu_conta_dba,
                COALESCE(main.dc_pix_dba, pix.dc_pix_dba)                         AS dc_pix_dba
            FROM (SELECT 1) AS dummy
            LEFT JOIN dados_bancarios_dba main ON (
                main.cd_entidade_ete = ?
                AND main.deleted_at IS NULL
                AND main.cd_tipo_conta_tcb != 3
            )
            LEFT JOIN dados_bancarios_dba pix ON (
                pix.cd_entidade_ete = ?
                AND pix.deleted_at IS NULL
                AND pix.cd_tipo_conta_tcb = 3
            )
            LEFT JOIN banco_ban ban ON (main.cd_banco_ban = ban.cd_banco_ban)
            LIMIT 1
        ", [$entidade, $entidade]);

        return ! empty($rows) ? (object) $rows[0] : null;
    }

    private function formatarBanco($banco): ?string
    {
        if (! $banco) {
            return null;
        }

        $codigo = trim((string) ($banco->cd_banco_ban ?? ''));
        $nome   = trim((string) ($banco->nm_banco_ban ?? ''));

        if ($codigo !== '' && $nome !== '') {
            return $codigo . ' – ' . $nome;
        }

        if ($nome !== '') {
            return $nome;
        }

        if ($codigo !== '') {
            return $codigo;
        }

        return null;
    }

    /**
     * Valor preenchido em negrito, ou lacuna se vazio.
     */
    private function campo($valor, int $tamanhoLacuna = 16): string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return str_repeat('_', max(4, $tamanhoLacuna));
        }

        return $this->negrito($valor);
    }

    private function negrito(string $texto): string
    {
        return '<strong>' . e($texto) . '</strong>';
    }

    /**
     * Junta nomes em português: "A e B" / "A, B e C".
     */
    private function juntarNomes(array $nomes, string $separadorFinal = ' e '): string
    {
        $nomes = array_values(array_filter($nomes));
        $qtd = count($nomes);

        if ($qtd === 0) {
            return '';
        }

        if ($qtd === 1) {
            return $nomes[0];
        }

        if ($qtd === 2) {
            return $nomes[0] . $separadorFinal . $nomes[1];
        }

        $ultimo = array_pop($nomes);

        return implode(', ', $nomes) . $separadorFinal . $ultimo;
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
