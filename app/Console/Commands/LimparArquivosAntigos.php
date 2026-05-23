<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LimparArquivosAntigos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arquivos:limpar {--dias=45 : Número de dias para considerar arquivo antigo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove arquivos antigos da pasta storage/arquivos/{conta}/processos de todas as contas de escritório ativas e envia relatório por email';

    /**
     * Diretório raiz dos arquivos
     *
     * @var string
     */
    protected $storageBase;

    /**
     * Destinatários do relatório
     *
     * @var array
     */
    protected $destinatarios = [
        'robsonferduda@gmail.com',
        'dmk@dmkadvogados.com.br'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->storageBase = storage_path('arquivos');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dias = $this->option('dias');
        $dataExecucao = Carbon::now()->format('d/m/Y H:i:s');

        $contas = $this->getContasEscritorioAtivas();

        if ($contas->isEmpty()) {
            $this->error("Nenhuma conta de escritório ativa encontrada.");
            return 1;
        }

        $this->info("Iniciando limpeza de arquivos com mais de {$dias} dias para {$contas->count()} conta(s)...");

        $totalArquivosRemovidos = 0;
        $totalBytesRemovidos    = 0;
        $blocos = [];

        foreach ($contas as $conta) {
            $baseDir = $this->storageBase . DIRECTORY_SEPARATOR . $conta->cd_conta_con . DIRECTORY_SEPARATOR . 'processos';

            if (!is_dir($baseDir)) {
                $this->warn("Diretório não encontrado para conta {$conta->cd_conta_con} ({$conta->nm_razao_social_con}), pulando...");
                continue;
            }

            $tamanhoAntes = $this->calcularTamanhoDiretorio($baseDir);

            list($arquivosRemovidos, $bytesRemovidos) = $this->limparArquivosAntigos($baseDir, $dias);

            $tamanhoDepois = $this->calcularTamanhoDiretorio($baseDir);

            $totalArquivosRemovidos += $arquivosRemovidos;
            $totalBytesRemovidos    += $bytesRemovidos;

            $blocos[] = [
                'conta'             => $conta,
                'baseDir'           => $baseDir,
                'tamanhoAntes'      => $tamanhoAntes,
                'tamanhoDepois'     => $tamanhoDepois,
                'bytesRemovidos'    => $bytesRemovidos,
                'arquivosRemovidos' => $arquivosRemovidos,
            ];

            $this->info("Conta {$conta->cd_conta_con} ({$conta->nm_razao_social_con}): {$arquivosRemovidos} arquivo(s) removido(s), {$this->formatarBytes($bytesRemovidos)} liberado(s).");
        }

        $relatorio = $this->gerarRelatorio($dataExecucao, $dias, $blocos, $totalArquivosRemovidos, $totalBytesRemovidos);

        $this->info($relatorio);
        $this->enviarEmail($relatorio);

        $this->info("Limpeza concluída! {$totalArquivosRemovidos} arquivo(s) removido(s) no total.");

        return 0;
    }

    /**
     * Retorna as contas de escritório ativas
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getContasEscritorioAtivas()
    {
        return DB::table('conta_con as t1')
            ->join('entidade_ete as t2', 't2.cd_conta_con', '=', 't1.cd_conta_con')
            ->where('t1.fl_correspondente_con', 'N')
            ->where('t2.cd_tipo_entidade_tpe', 5)
            ->whereNull('t1.deleted_at')
            ->select('t1.cd_conta_con', 't1.nm_razao_social_con')
            ->distinct()
            ->get();
    }

    /**
     * Calcula o tamanho total de um diretório
     *
     * @param string $path
     * @return int
     */
    protected function calcularTamanhoDiretorio($path)
    {
        $total = 0;

        if (!is_dir($path)) {
            return 0;
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $total += $file->getSize();
                }
            }
        } catch (\Exception $e) {
            $this->error("Erro ao calcular tamanho: " . $e->getMessage());
        }

        return $total;
    }

    /**
     * Remove arquivos antigos do diretório, apenas de processos finalizados (status = 6)
     *
     * @param string $baseDir
     * @param int $dias
     * @return array
     */
    protected function limparArquivosAntigos($baseDir, $dias)
    {
        $agora = time();
        $limite = $agora - ($dias * 86400);

        $arquivosRemovidos = 0;
        $bytesRemovidos = 0;

        if (!is_dir($baseDir)) {
            return [$arquivosRemovidos, $bytesRemovidos];
        }

        // Percorre apenas pastas numéricas (cada uma = cd_processo_pro)
        $pastas = scandir($baseDir);

        foreach ($pastas as $pasta) {
            if ($pasta === '.' || $pasta === '..') {
                continue;
            }

            if (!is_numeric($pasta)) {
                continue;
            }

            $caminhoPasta = $baseDir . DIRECTORY_SEPARATOR . $pasta;

            if (!is_dir($caminhoPasta)) {
                continue;
            }

            // Verifica se o processo está finalizado (cd_status_processo_stp = 6)
            $processoFinalizado = DB::table('processo_pro')
                ->where('cd_processo_pro', (int) $pasta)
                ->where('cd_status_processo_stp', 6)
                ->exists();

            if (!$processoFinalizado) {
                continue;
            }

            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($caminhoPasta, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $modificadoEm = $file->getMTime();

                        if ($modificadoEm < $limite) {
                            $tamanho = $file->getSize();

                            if (@unlink($file->getPathname())) {
                                $arquivosRemovidos++;
                                $bytesRemovidos += $tamanho;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Erro ao processar pasta {$pasta}: " . $e->getMessage());
            }
        }

        return [$arquivosRemovidos, $bytesRemovidos];
    }

    /**
     * Converte bytes para formato legível
     *
     * @param int $bytes
     * @return string
     */
    protected function formatarBytes($bytes)
    {
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($unidades) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return number_format($bytes, 2, ',', '.') . ' ' . $unidades[$i];
    }

    /**
     * Gera o texto do relatório consolidado
     *
     * @param string $dataExecucao
     * @param int $dias
     * @param array $blocos
     * @param int $totalArquivosRemovidos
     * @param int $totalBytesRemovidos
     * @return string
     */
    protected function gerarRelatorio($dataExecucao, $dias, $blocos, $totalArquivosRemovidos, $totalBytesRemovidos)
    {
        $linhas = [];
        $linhas[] = "Relatório de Limpeza Automática - Processos";
        $linhas[] = "Data/Hora: {$dataExecucao}";
        $linhas[] = "Critério: arquivos com mais de {$dias} dias";
        $linhas[] = str_repeat('-', 45);

        foreach ($blocos as $bloco) {
            $linhas[] = "";
            $linhas[] = "Conta: [{$bloco['conta']->cd_conta_con}] {$bloco['conta']->nm_razao_social_con}";
            $linhas[] = "Diretório: {$bloco['baseDir']}";
            $linhas[] = "Espaço antes : {$this->formatarBytes($bloco['tamanhoAntes'])}";
            $linhas[] = "Espaço depois: {$this->formatarBytes($bloco['tamanhoDepois'])}";
            $linhas[] = "Espaço liberado: {$this->formatarBytes($bloco['bytesRemovidos'])}";
            $linhas[] = "Arquivos removidos: {$bloco['arquivosRemovidos']}";
        }

        $linhas[] = "";
        $linhas[] = str_repeat('=', 45);
        $linhas[] = "TOTAL GERAL";
        $linhas[] = "Arquivos removidos: {$totalArquivosRemovidos}";
        $linhas[] = "Espaço total liberado: {$this->formatarBytes($totalBytesRemovidos)}";

        return implode("\n", $linhas);
    }

    /**
     * Envia relatório por email
     *
     * @param string $relatorio
     * @return void
     */
    protected function enviarEmail($relatorio)
    {
        try {
            Mail::raw($relatorio, function ($message) {
                $message->to($this->destinatarios)
                        ->subject('Relatório Diário de Limpeza - Processos');
            });

            $this->info("Email enviado com sucesso!");
        } catch (\Exception $e) {
            $this->error("Erro ao enviar email: " . $e->getMessage());
        }
    }
}