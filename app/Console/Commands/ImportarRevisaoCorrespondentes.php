<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Aplica a planilha revisada de correspondentes (exportação de /correspondentes + colunas
 * OAB e ADVOGADO OU PREPOSTO) aos cadastros de um escritório:
 *
 *  - conta_con.fl_advogado_con   (ADVOGADO = true, PREPOSTO = false)
 *  - OAB (identificacao_ide tipo 3) na entidade do vínculo
 *  - CPF/CNPJ (identificacao_ide tipo 1/7) na entidade do vínculo
 *
 * Cada linha é identificada pelo e-mail do usuário correspondente; sem e-mail correspondente,
 * tenta pelo CPF/CNPJ já cadastrado no vínculo do escritório. Linhas sem identificação única
 * não são alteradas. OAB existente de PREPOSTO é mantida e apenas listada no relatório.
 *
 * Uso:
 *   php artisan correspondente:importar-revisao storage/app/importacao/correspondetes_revisada.xlsx --conta=64 --dry-run
 *   php artisan correspondente:importar-revisao storage/app/importacao/correspondetes_revisada.xlsx --conta=64
 */
class ImportarRevisaoCorrespondentes extends Command
{
    private const TIPO_CPF = 1;
    private const TIPO_OAB = 3;
    private const TIPO_CNPJ = 7;
    private const NIVEL_CORRESPONDENTE = 3;

    protected $signature = 'correspondente:importar-revisao
                            {arquivo : Caminho do .xlsx revisado (absoluto ou relativo à raiz do projeto).}
                            {--conta= : cd_conta_con do escritório dono dos vínculos (obrigatório).}
                            {--dry-run : Não grava nada; apenas gera o relatório.}';

    protected $description = 'Atualiza fl_advogado_con, OAB e CPF/CNPJ dos correspondentes a partir da planilha revisada.';

    private $contaEscritorio;
    private $agora;

    /** email => [cd_conta_con, ...] */
    private $contasPorEmail = [];
    /** cd_correspondente_cor => vínculo */
    private $vinculosPorCorrespondente = [];
    /** cd_entidade_ete => [tipo => [registros]] */
    private $identificacoes = [];
    /** dígitos do CPF/CNPJ => [cd_correspondente_cor, ...] */
    private $correspondentesPorDocumento = [];
    /** cd_conta_con => fl_advogado_con atual */
    private $advogadoAtual = [];

    public function handle(): int
    {
        $this->contaEscritorio = (int) $this->option('conta');
        $dryRun = (bool) $this->option('dry-run');
        $this->agora = Carbon::now();

        if ($this->contaEscritorio <= 0) {
            $this->error('Informe o escritório com --conta=<cd_conta_con>.');
            return 1;
        }

        $arquivo = $this->resolverArquivo($this->argument('arquivo'));
        if (! $arquivo) {
            $this->error('Arquivo não encontrado: ' . $this->argument('arquivo'));
            return 1;
        }

        try {
            $linhas = $this->lerPlanilha($arquivo);
        } catch (\Throwable $e) {
            $this->error('Falha ao ler a planilha: ' . $e->getMessage());
            return 1;
        }

        $this->info(sprintf(
            '[importar-revisao] escritório=%d  linhas=%d%s',
            $this->contaEscritorio,
            count($linhas),
            $dryRun ? '  DRY-RUN' : ''
        ));

        $this->carregarDados();

        $relatorio = [];
        $totais = [
            'linhas' => count($linhas),
            'por_email' => 0,
            'por_documento' => 0,
            'nao_encontrado' => 0,
            'ambiguo' => 0,
            'sem_vinculo' => 0,
            'advogado_alterado' => 0,
            'oab_criada' => 0,
            'oab_atualizada' => 0,
            'documento_criado' => 0,
            'documento_atualizado' => 0,
            'preposto_com_oab' => 0,
        ];

        DB::beginTransaction();

        try {
            if (! $dryRun) {
                $this->criarBackup();
            }

            foreach ($linhas as $linha) {
                $relatorio[] = $this->processarLinha($linha, $dryRun, $totais);
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('[importar-revisao] falha, nada foi gravado: ' . $e->getMessage());
            return 1;
        }

        $caminhoRelatorio = $this->salvarRelatorio($relatorio, $dryRun);

        foreach ($totais as $chave => $valor) {
            $this->line(sprintf('  %-22s %d', $chave, $valor));
        }

        $this->info('Relatório: ' . $caminhoRelatorio);
        $this->info($dryRun ? 'Simulação concluída; nada foi gravado.' : 'Importação concluída.');

        return 0;
    }

    private function resolverArquivo(string $arquivo): ?string
    {
        foreach ([$arquivo, base_path($arquivo)] as $caminho) {
            if (is_file($caminho)) {
                return $caminho;
            }
        }

        return null;
    }

    private function lerPlanilha(string $arquivo): array
    {
        $leitor = IOFactory::createReaderForFile($arquivo);
        $leitor->setReadDataOnly(true);
        $planilha = $leitor->load($arquivo)->getActiveSheet()->toArray(null, true, false, false);

        $colunas = null;
        $linhas = [];

        foreach ($planilha as $indice => $valores) {
            if ($colunas === null) {
                $cabecalho = array_map(function ($v) {
                    return mb_strtoupper(trim((string) $v));
                }, $valores);

                if (in_array('EMAIL', $cabecalho, true) && in_array('ADVOGADO OU PREPOSTO', $cabecalho, true)) {
                    $colunas = array_flip($cabecalho);
                }
                continue;
            }

            $valor = function ($nome) use ($valores, $colunas) {
                return isset($colunas[$nome]) ? trim((string) ($valores[$colunas[$nome]] ?? '')) : '';
            };

            if ($valor('EMAIL') === '' && $valor('NOME') === '') {
                continue;
            }

            $linhas[] = [
                'linha' => $indice + 1,
                'nome' => $valor('NOME'),
                'email' => mb_strtolower($valor('EMAIL')),
                'documento' => $this->valorInformado($valor('CPF/CNPJ')),
                'oab' => $this->valorInformado($valor('OAB')),
                'tipo' => mb_strtoupper($valor('ADVOGADO OU PREPOSTO')),
            ];
        }

        if ($colunas === null) {
            throw new \RuntimeException('Cabeçalho com as colunas EMAIL e ADVOGADO OU PREPOSTO não encontrado.');
        }

        return $linhas;
    }

    private function carregarDados(): void
    {
        $vinculos = DB::table('conta_correspondente_ccr')
            ->where('cd_conta_con', $this->contaEscritorio)
            ->whereNull('deleted_at')
            ->get(['cd_conta_correspondente_ccr', 'cd_correspondente_cor', 'cd_entidade_ete']);

        foreach ($vinculos as $vinculo) {
            $this->vinculosPorCorrespondente[$vinculo->cd_correspondente_cor] = $vinculo;
        }

        $correspondentes = array_keys($this->vinculosPorCorrespondente);

        $usuarios = DB::table('users')
            ->where('cd_nivel_niv', self::NIVEL_CORRESPONDENTE)
            ->whereNotNull('email')
            ->get(['email', 'cd_conta_con']);

        foreach ($usuarios as $usuario) {
            $email = mb_strtolower(trim($usuario->email));
            $this->contasPorEmail[$email][$usuario->cd_conta_con] = (int) $usuario->cd_conta_con;
        }

        $entidades = [];
        foreach ($this->vinculosPorCorrespondente as $vinculo) {
            if ($vinculo->cd_entidade_ete) {
                $entidades[] = $vinculo->cd_entidade_ete;
            }
        }

        foreach (array_chunk($entidades, 1000) as $lote) {
            $registros = DB::table('identificacao_ide')
                ->whereIn('cd_entidade_ete', $lote)
                ->whereIn('cd_tipo_identificacao_tpi', [self::TIPO_CPF, self::TIPO_OAB, self::TIPO_CNPJ])
                ->whereNull('deleted_at')
                ->orderBy('cd_identificacao_ide')
                ->get(['cd_identificacao_ide', 'cd_entidade_ete', 'cd_tipo_identificacao_tpi', 'nu_identificacao_ide']);

            foreach ($registros as $registro) {
                $this->identificacoes[$registro->cd_entidade_ete][(int) $registro->cd_tipo_identificacao_tpi][] = $registro;
            }
        }

        foreach ($this->vinculosPorCorrespondente as $correspondente => $vinculo) {
            foreach ($this->documentosDaEntidade($vinculo->cd_entidade_ete) as $registro) {
                $digitos = $this->digitos($registro->nu_identificacao_ide);
                if ($digitos !== '') {
                    $this->correspondentesPorDocumento[$digitos][$correspondente] = $correspondente;
                }
            }
        }

        foreach (array_chunk($correspondentes, 1000) as $lote) {
            foreach (DB::table('conta_con')->whereIn('cd_conta_con', $lote)->get(['cd_conta_con', 'fl_advogado_con']) as $conta) {
                $this->advogadoAtual[$conta->cd_conta_con] = $conta->fl_advogado_con;
            }
        }
    }

    private function criarBackup(): void
    {
        $sufixo = $this->agora->format('YmdHis');
        $conta = $this->contaEscritorio;

        DB::statement("CREATE TABLE bkp_conta_con_advogado_{$sufixo} AS
            SELECT cd_conta_con, fl_advogado_con
            FROM conta_con
            WHERE cd_conta_con IN (
                SELECT cd_correspondente_cor FROM conta_correspondente_ccr WHERE cd_conta_con = {$conta}
            )");

        DB::statement("CREATE TABLE bkp_identificacao_ide_revisao_{$sufixo} AS
            SELECT *
            FROM identificacao_ide
            WHERE cd_tipo_identificacao_tpi IN (1, 3, 7)
              AND cd_entidade_ete IN (
                SELECT cd_entidade_ete FROM conta_correspondente_ccr WHERE cd_conta_con = {$conta}
              )");

        $this->line("  backup: bkp_conta_con_advogado_{$sufixo}, bkp_identificacao_ide_revisao_{$sufixo}");
    }

    private function processarLinha(array $linha, bool $dryRun, array &$totais): array
    {
        $item = [
            'linha' => $linha['linha'],
            'email' => $linha['email'],
            'nome' => $linha['nome'],
            'status' => '',
            'identificado_por' => '',
            'cd_conta_con' => '',
            'cd_conta_correspondente_ccr' => '',
            'advogado_antigo' => '',
            'advogado_novo' => '',
            'oab_antiga' => '',
            'oab_nova' => '',
            'oab_acao' => '',
            'documento_antigo' => '',
            'documento_novo' => '',
            'documento_acao' => '',
            'observacao' => '',
        ];

        if (! in_array($linha['tipo'], ['ADVOGADO', 'PREPOSTO'], true)) {
            $item['status'] = 'tipo_invalido';
            $item['observacao'] = 'ADVOGADO OU PREPOSTO = "' . $linha['tipo'] . '"';
            return $item;
        }

        [$correspondente, $identificadoPor, $motivo] = $this->identificar($linha);

        if (! $correspondente) {
            $item['status'] = $motivo;
            $totais[$motivo === 'ambiguo' ? 'ambiguo' : 'nao_encontrado']++;
            return $item;
        }

        $vinculo = $this->vinculosPorCorrespondente[$correspondente] ?? null;
        if (! $vinculo || ! $vinculo->cd_entidade_ete) {
            $item['status'] = 'sem_vinculo';
            $item['cd_conta_con'] = $correspondente;
            $item['observacao'] = 'Correspondente sem vínculo ativo (ou sem entidade) neste escritório.';
            $totais['sem_vinculo']++;
            return $item;
        }

        $totais[$identificadoPor === 'email' ? 'por_email' : 'por_documento']++;

        $item['status'] = 'ok';
        $item['identificado_por'] = $identificadoPor;
        $item['cd_conta_con'] = $correspondente;
        $item['cd_conta_correspondente_ccr'] = $vinculo->cd_conta_correspondente_ccr;

        $this->aplicarAdvogado($correspondente, $linha['tipo'] === 'ADVOGADO', $dryRun, $item, $totais);
        $this->aplicarOab($correspondente, $vinculo->cd_entidade_ete, $linha, $dryRun, $item, $totais);
        $this->aplicarDocumento($correspondente, $vinculo->cd_entidade_ete, $linha['documento'], $dryRun, $item, $totais);

        return $item;
    }

    /**
     * @return array [cd_correspondente_cor|null, 'email'|'documento'|null, motivo]
     */
    private function identificar(array $linha): array
    {
        if ($linha['email'] !== '' && isset($this->contasPorEmail[$linha['email']])) {
            $contas = array_values($this->contasPorEmail[$linha['email']]);

            if (count($contas) === 1) {
                return [$contas[0], 'email', ''];
            }

            $comVinculo = array_values(array_filter($contas, function ($conta) {
                return isset($this->vinculosPorCorrespondente[$conta]);
            }));

            if (count($comVinculo) === 1) {
                return [$comVinculo[0], 'email', ''];
            }

            return [null, null, 'ambiguo'];
        }

        $digitos = $this->digitos($linha['documento']);
        if ($digitos !== '' && isset($this->correspondentesPorDocumento[$digitos])) {
            $contas = array_values($this->correspondentesPorDocumento[$digitos]);

            return count($contas) === 1
                ? [$contas[0], 'documento', '']
                : [null, null, 'ambiguo'];
        }

        return [null, null, 'nao_encontrado'];
    }

    private function aplicarAdvogado(int $correspondente, bool $advogado, bool $dryRun, array &$item, array &$totais): void
    {
        $atual = $this->advogadoAtual[$correspondente] ?? null;
        if (is_string($atual) && in_array(strtolower($atual), ['t', 'f'], true)) {
            $atual = strtolower($atual) === 't';
        }
        $atualBool = $atual === null ? null : filter_var($atual, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $item['advogado_antigo'] = $atualBool === null ? '' : ($atualBool ? 'true' : 'false');
        $item['advogado_novo'] = $advogado ? 'true' : 'false';

        if ($atualBool === $advogado) {
            return;
        }

        $totais['advogado_alterado']++;
        $this->advogadoAtual[$correspondente] = $advogado;

        if (! $dryRun) {
            DB::table('conta_con')
                ->where('cd_conta_con', $correspondente)
                ->update(['fl_advogado_con' => $advogado, 'updated_at' => $this->agora]);
        }
    }

    private function aplicarOab(int $correspondente, int $entidade, array $linha, bool $dryRun, array &$item, array &$totais): void
    {
        $existentes = $this->identificacoes[$entidade][self::TIPO_OAB] ?? [];
        $atual = $existentes[0] ?? null;
        $item['oab_antiga'] = $atual ? (string) $atual->nu_identificacao_ide : '';

        if ($linha['tipo'] === 'PREPOSTO') {
            if ($atual && trim((string) $atual->nu_identificacao_ide) !== '') {
                $item['oab_acao'] = 'mantida_preposto';
                $item['observacao'] = 'PREPOSTO com OAB cadastrada (mantida).';
                $totais['preposto_com_oab']++;
            }
            return;
        }

        $nova = $linha['oab'];
        if ($nova === null) {
            $item['oab_acao'] = 'sem_oab_na_planilha';
            return;
        }

        $item['oab_nova'] = $nova;

        if ($atual && trim((string) $atual->nu_identificacao_ide) === $nova) {
            $item['oab_acao'] = 'igual';
            return;
        }

        if ($atual) {
            $item['oab_acao'] = 'atualizada';
            $totais['oab_atualizada']++;

            if (! $dryRun) {
                DB::table('identificacao_ide')
                    ->where('cd_identificacao_ide', $atual->cd_identificacao_ide)
                    ->update(['nu_identificacao_ide' => $nova, 'updated_at' => $this->agora]);
            }
            return;
        }

        $item['oab_acao'] = 'criada';
        $totais['oab_criada']++;

        if (! $dryRun) {
            DB::table('identificacao_ide')->insert([
                'cd_entidade_ete' => $entidade,
                'cd_conta_con' => $correspondente,
                'cd_tipo_identificacao_tpi' => self::TIPO_OAB,
                'nu_identificacao_ide' => $nova,
                'created_at' => $this->agora,
                'updated_at' => $this->agora,
            ]);
        }
    }

    private function aplicarDocumento(int $correspondente, int $entidade, ?string $documento, bool $dryRun, array &$item, array &$totais): void
    {
        $atual = $this->documentosDaEntidade($entidade)[0] ?? null;
        $item['documento_antigo'] = $atual ? (string) $atual->nu_identificacao_ide : '';

        $digitos = $this->digitos($documento);
        if ($digitos === '') {
            return;
        }

        $tipo = strlen($digitos) === 14 ? self::TIPO_CNPJ : (strlen($digitos) === 11 ? self::TIPO_CPF : null);
        if ($tipo === null) {
            $item['documento_acao'] = 'invalido';
            $item['observacao'] = trim($item['observacao'] . ' CPF/CNPJ com quantidade de dígitos inválida.');
            return;
        }

        $item['documento_novo'] = $documento;

        if ($atual && $this->digitos($atual->nu_identificacao_ide) === $digitos) {
            $item['documento_acao'] = 'igual';
            return;
        }

        if ($atual) {
            $item['documento_acao'] = 'atualizado';
            $totais['documento_atualizado']++;

            if (! $dryRun) {
                DB::table('identificacao_ide')
                    ->where('cd_identificacao_ide', $atual->cd_identificacao_ide)
                    ->update([
                        'cd_tipo_identificacao_tpi' => $tipo,
                        'nu_identificacao_ide' => $documento,
                        'updated_at' => $this->agora,
                    ]);
            }
            return;
        }

        $item['documento_acao'] = 'criado';
        $totais['documento_criado']++;

        if (! $dryRun) {
            DB::table('identificacao_ide')->insert([
                'cd_entidade_ete' => $entidade,
                'cd_conta_con' => $correspondente,
                'cd_tipo_identificacao_tpi' => $tipo,
                'nu_identificacao_ide' => $documento,
                'created_at' => $this->agora,
                'updated_at' => $this->agora,
            ]);
        }
    }

    private function documentosDaEntidade($entidade): array
    {
        if (! $entidade) {
            return [];
        }

        return array_merge(
            $this->identificacoes[$entidade][self::TIPO_CPF] ?? [],
            $this->identificacoes[$entidade][self::TIPO_CNPJ] ?? []
        );
    }

    private function salvarRelatorio(array $relatorio, bool $dryRun): string
    {
        $diretorio = storage_path('app/importacao');
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $caminho = $diretorio . '/revisao-correspondentes-' . $this->agora->format('Ymd-His') . ($dryRun ? '-dry-run' : '') . '.csv';

        $arquivo = fopen($caminho, 'w');
        fwrite($arquivo, "\xEF\xBB\xBF");

        if (! empty($relatorio)) {
            fputcsv($arquivo, array_keys($relatorio[0]), ';');
            foreach ($relatorio as $item) {
                fputcsv($arquivo, $item, ';');
            }
        }

        fclose($arquivo);

        return $caminho;
    }

    private function valorInformado(string $valor): ?string
    {
        $valor = trim($valor);

        if ($valor === '' || in_array(mb_strtolower($valor), ['não informado', 'nao informado', 'não informada'], true)) {
            return null;
        }

        return $valor;
    }

    private function digitos($valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor);
    }
}
