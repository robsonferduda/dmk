<?php

namespace App\Services\Contrato;

use App\ContaCorrespondente;
use App\EnderecoEletronico;
use App\Enums\TipoEnderecoEletronico;
use App\Fone;
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
            'entidade.cnpj',
            'entidade.oab',
            'entidade.rg',
            'entidade.endereco.cidade.estado',
            'entidade.fone',
            'entidade.origem.cidade.estado',
            'correspondente',
        ]);

        // Geração liberada mesmo com pendências (lacunas no PDF); aviso permanece na tela.

        $dadosContratado = $this->dadosContratado($vinculo);

        $html = view('correspondente.contrato-pdf', [
            'textoPartes'     => $this->montarTextoPartes($vinculo),
            'trechoComarcas'  => $this->montarTrechoComarcas($vinculo),
            'trechoBancario'  => $this->montarTrechoBancario($vinculo),
            'contratadaNome'  => $this->campo($dadosContratado['nome'], 28),
            'contratadaOab'   => $this->campo($dadosContratado['oab'], 18),
            'contratadaCpf'   => $this->campo($dadosContratado['cpf'], 18),
            'localData'       => $this->montarLocalData(),
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
            'entidade.cnpj',
            'entidade.oab',
            'entidade.rg',
            'entidade.endereco.cidade.estado',
            'entidade.fone',
            'entidade.origem.cidade.estado',
            'correspondente',
        ]);

        $faltando = [];
        $dados = $this->dadosPartesContratado($vinculo);

        if (! $this->temComarcasValidas($vinculo)) {
            $faltando[] = 'comarca(s) de atuação com cidade e estado';
        }

        $obrigatorios = [
            'nome'       => 'nome do correspondente',
            'rg'         => 'RG do correspondente',
            'cpf'        => 'CPF/CNPJ do correspondente',
            'oab_uf'     => 'UF da OAB do correspondente',
            'oab_numero' => 'número da OAB do correspondente',
            'rua'        => 'logradouro (endereço)',
            'numero'     => 'número do endereço',
            'bairro'     => 'bairro',
            'cidade'     => 'cidade do endereço',
            'cep'        => 'CEP',
            'telefone'   => 'telefone de contato',
            'email'      => 'e-mail',
        ];

        foreach ($obrigatorios as $chave => $rotulo) {
            if ($this->vazio($dados[$chave] ?? null)) {
                $faltando[] = $rotulo;
            }
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
        $dados = $this->dadosPartesContratado($vinculo);

        $oab = null;
        if (! $this->vazio($dados['oab_uf']) || ! $this->vazio($dados['oab_numero'])) {
            $oab = trim(($dados['oab_uf'] ?? '') . ' ' . ($dados['oab_numero'] ?? ''));
        }

        return [
            'nome' => $dados['nome'],
            'oab'  => $oab !== '' ? $oab : null,
            'cpf'  => $dados['cpf'],
        ];
    }

    /**
     * Dados variáveis do CONTRATADO no preâmbulo do contrato.
     */
    public function dadosPartesContratado(ContaCorrespondente $vinculo): array
    {
        $vinculo->loadMissing([
            'entidade.cpf',
            'entidade.cnpj',
            'entidade.oab',
            'entidade.rg',
            'entidade.endereco.cidade.estado',
            'entidade.fone',
            'entidade.origem.cidade.estado',
            'correspondente',
        ]);

        $entidade = $vinculo->entidade;
        $endereco = optional($entidade)->endereco;

        $nome = trim((string) ($vinculo->nm_conta_correspondente_ccr
            ?: optional($vinculo->correspondente)->nm_razao_social_con
            ?: optional($vinculo->correspondente)->nm_fantasia_con
            ?: ''));

        $cpf = trim((string) optional(optional($entidade)->cpf)->nu_identificacao_ide);
        if ($cpf === '') {
            $cpf = trim((string) optional(optional($entidade)->cnpj)->nu_identificacao_ide);
        }

        $rg = trim((string) optional(optional($entidade)->rg)->nu_identificacao_ide);
        $oabBruta = trim((string) optional(optional($entidade)->oab)->nu_identificacao_ide);

        $ufFallback = trim((string) (
            optional(optional(optional($endereco)->cidade)->estado)->sg_estado_est
            ?? optional(optional(optional(optional($entidade)->origem)->cidade)->estado)->sg_estado_est
            ?? ''
        ));

        [$oabUf, $oabNumero] = $this->parseOab($oabBruta, $ufFallback !== '' ? $ufFallback : null);

        $cep = $this->formatarCep(optional($endereco)->nu_cep_ede);

        return [
            'nome'       => $nome !== '' ? $nome : null,
            'rg'         => $rg !== '' ? $rg : null,
            'cpf'        => $cpf !== '' ? $cpf : null,
            'oab_uf'     => $oabUf,
            'oab_numero' => $oabNumero,
            'rua'        => $this->limpo(optional($endereco)->dc_logradouro_ede),
            'numero'     => $this->limpo(optional($endereco)->nu_numero_ede),
            'bairro'     => $this->limpo(optional($endereco)->nm_bairro_ede),
            'cidade'     => $this->limpo(optional(optional($endereco)->cidade)->nm_cidade_cde),
            'cep'        => $cep,
            'telefone'   => $this->telefoneContratado($vinculo),
            'email'      => $this->emailContratado($vinculo),
        ];
    }

    private function telefoneContratado(ContaCorrespondente $vinculo): ?string
    {
        $fone = trim((string) optional(optional($vinculo->entidade)->fone)->nu_fone_fon);
        if ($fone !== '') {
            return $fone;
        }

        if ($vinculo->cd_entidade_ete) {
            $outro = Fone::where('cd_entidade_ete', $vinculo->cd_entidade_ete)
                ->whereNull('deleted_at')
                ->orderBy('cd_fone_fon')
                ->value('nu_fone_fon');
            $outro = trim((string) $outro);
            if ($outro !== '') {
                return $outro;
            }
        }

        $whats = trim((string) optional($vinculo->correspondente)->nu_telefone_whatsapp_con);

        return $whats !== '' ? $whats : null;
    }

    private function emailContratado(ContaCorrespondente $vinculo): ?string
    {
        if (! $vinculo->cd_entidade_ete) {
            return null;
        }

        $emails = EnderecoEletronico::where('cd_entidade_ete', $vinculo->cd_entidade_ete)
            ->whereNull('deleted_at')
            ->whereNotNull('dc_endereco_eletronico_ede')
            ->where('dc_endereco_eletronico_ede', '!=', '')
            ->orderBy('cd_endereco_eletronico_ele')
            ->get();

        if ($emails->isEmpty()) {
            return null;
        }

        $notif = $emails->firstWhere('cd_tipo_endereco_eletronico_tee', TipoEnderecoEletronico::NOTIFICACAO);
        $contato = $emails->firstWhere('cd_tipo_endereco_eletronico_tee', TipoEnderecoEletronico::CONTATO);
        $escolhido = $notif ?: ($contato ?: $emails->first());

        $email = trim((string) optional($escolhido)->dc_endereco_eletronico_ede);

        return $email !== '' ? $email : null;
    }

    /**
     * @return array{0:?string,1:?string} [UF, número]
     */
    private function parseOab(?string $oab, ?string $ufFallback): array
    {
        $oab = trim((string) $oab);
        if ($oab === '') {
            return [null, null];
        }

        if (preg_match('/(?:OAB\s*[\/\-]?\s*)?([A-Za-z]{2})\s*[\/\-]?\s*(.+)$/u', $oab, $m)) {
            return [strtoupper($m[1]), trim($m[2])];
        }

        return [
            $ufFallback ? strtoupper($ufFallback) : null,
            $oab,
        ];
    }

    private function formatarCep($cep): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $cep);
        if ($digitos === null || $digitos === '') {
            return null;
        }

        $digitos = str_pad($digitos, 8, '0', STR_PAD_LEFT);
        if (strlen($digitos) !== 8) {
            return $digitos;
        }

        return substr($digitos, 0, 5) . '-' . substr($digitos, 5);
    }

    private function limpo($valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor !== '' ? $valor : null;
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
     * Local e data do contrato em português (ex.: Florianópolis, 14 de setembro de 2026).
     */
    private function montarLocalData(?Carbon $data = null): string
    {
        $data = $data ? $data->copy() : Carbon::now();

        return 'Florianópolis, ' . $this->formatarDataPortugues($data) . '.';
    }

    private function formatarDataPortugues(Carbon $data): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        return (int) $data->format('d') . ' de ' . $meses[(int) $data->format('n')] . ' de ' . $data->format('Y');
    }

    /**
     * Preâmbulo das partes com dados do CONTRATADO preenchidos do cadastro.
     */
    private function montarTextoPartes(ContaCorrespondente $vinculo): string
    {
        $d = $this->dadosPartesContratado($vinculo);

        return 'De um lado, DEBORAH MEKACHESKI PEREIRA SOCIEDADE INDIVIDUAL DE ADVOCACIA, '
            . 'sociedade de advogados inscrita no CNPJ/MF sob o nº 19.439.096/0001-17, '
            . 'e na Ordem dos Advogados do Brasil – Seção de Florianópolis/SC sob nº 2178/2013, '
            . 'com sede no Município de Florianópolis, Estado de Santa Catarina, '
            . 'na Rua Saldanha Marinho, 374 sala 806, Centro, CEP 88053-300, '
            . 'neste ato representada por sua sócia DEBORAH MEKACHESKI PEREIRA, '
            . 'advogada inscrita na OAB/SC sob o número 33.565B, doravante denominada CONTRATANTE '
            . 'e, de outro, ' . $this->campo($d['nome'], 26) . ', brasileiro (a), portador do RG '
            . $this->campo($d['rg'], 12) . ', inscrito (a) no CPF sob o nº ' . $this->campo($d['cpf'], 18)
            . ', e na OAB/ ' . $this->campo($d['oab_uf'], 4) . '- sob nº ' . $this->campo($d['oab_numero'], 12)
            . ', com endereço profissional na Rua ' . $this->campo($d['rua'], 22)
            . ', n ' . $this->campo($d['numero'], 8)
            . ' Bairro: ' . $this->campo($d['bairro'], 12)
            . ', Cidade ' . $this->campo($d['cidade'], 14)
            . ' - CEP ' . $this->campo($d['cep'], 10)
            . ' contato: ' . $this->campo($d['telefone'], 16)
            . ' e-mail ' . $this->campo($d['email'], 24)
            . ' doravante denominada CONTRATADO (A).';
    }
}
