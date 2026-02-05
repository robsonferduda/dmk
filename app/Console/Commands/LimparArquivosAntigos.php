<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
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
    protected $signature = 'arquivos:limpar {--dias=90 : Número de dias para considerar arquivo antigo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove arquivos antigos da pasta storage/arquivos/64/processos e envia relatório por email';

    /**
     * Diretório base para limpeza
     *
     * @var string
     */
    protected $baseDir;

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
        $this->baseDir = storage_path('arquivos/64/processos');
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

        $this->info("Iniciando limpeza de arquivos com mais de {$dias} dias...");

        // Verifica se o diretório existe
        if (!is_dir($this->baseDir)) {
            $this->error("Diretório não encontrado: {$this->baseDir}");
            return 1;
        }

        // Calcula tamanho antes
        $tamanhoAntes = $this->calcularTamanhoDiretorio($this->baseDir);

        // Limpa arquivos antigos
        list($arquivosRemovidos, $bytesRemovidos) = $this->limparArquivosAntigos($dias);

        // Calcula tamanho depois
        $tamanhoDepois = $this->calcularTamanhoDiretorio($this->baseDir);

        // Gera relatório
        $relatorio = $this->gerarRelatorio(
            $dataExecucao,
            $dias,
            $tamanhoAntes,
            $tamanhoDepois,
            $bytesRemovidos,
            $arquivosRemovidos
        );

        // Exibe no console
        $this->info($relatorio);

        // Envia por email
        $this->enviarEmail($relatorio);

        $this->info("Limpeza concluída! {$arquivosRemovidos} arquivo(s) removido(s).");

        return 0;
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
     * Remove arquivos antigos do diretório
     *
     * @param int $dias
     * @return array
     */
    protected function limparArquivosAntigos($dias)
    {
        $agora = time();
        $limite = $agora - ($dias * 86400);

        $arquivosRemovidos = 0;
        $bytesRemovidos = 0;

        if (!is_dir($this->baseDir)) {
            return [$arquivosRemovidos, $bytesRemovidos];
        }

        // Percorre apenas pastas numéricas
        $pastas = scandir($this->baseDir);

        foreach ($pastas as $pasta) {
            // Ignora . e ..
            if ($pasta === '.' || $pasta === '..') {
                continue;
            }

            // Apenas pastas numéricas
            if (!is_numeric($pasta)) {
                continue;
            }

            $caminhoPasta = $this->baseDir . DIRECTORY_SEPARATOR . $pasta;

            if (!is_dir($caminhoPasta)) {
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
     * Gera o texto do relatório
     *
     * @param string $dataExecucao
     * @param int $dias
     * @param int $tamanhoAntes
     * @param int $tamanhoDepois
     * @param int $bytesRemovidos
     * @param int $arquivosRemovidos
     * @return string
     */
    protected function gerarRelatorio($dataExecucao, $dias, $tamanhoAntes, $tamanhoDepois, $bytesRemovidos, $arquivosRemovidos)
    {
        return "
Relatório de Limpeza Automática - Processos
Data/Hora: {$dataExecucao}

Diretório analisado: {$this->baseDir}
Critério: arquivos com mais de {$dias} dias

Espaço antes da limpeza: {$this->formatarBytes($tamanhoAntes)}
Espaço depois da limpeza: {$this->formatarBytes($tamanhoDepois)}
Espaço liberado: {$this->formatarBytes($bytesRemovidos)}

Total de arquivos removidos: {$arquivosRemovidos}
        ";
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
