<?php

namespace App\Services\Email;

use App\Conta;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Swift_Mailer;
use Swift_Message;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_Loggers_LoggerPlugin;
use Swift_SmtpTransport;

class EmailDiagnosticoService
{
    /**
     * Retorna configuração de e-mail com credenciais mascaradas.
     */
    public function getConfiguracao(): array
    {
        $username = config('mail.username');
        $password = config('mail.password');

        return [
            'driver'          => config('mail.driver'),
            'host'            => config('mail.host'),
            'port'            => config('mail.port'),
            'encryption'      => config('mail.encryption'),
            'from_address'    => config('mail.from.address'),
            'from_name'       => config('mail.from.name'),
            'username'        => $username,
            'username_masked' => $this->mascarar($username),
            'password_set'    => ! empty($password),
            'password_masked' => $this->mascararSenha($password),
            'app_env'         => config('app.env'),
            'app_url'         => config('app.url'),
            'queue'           => config('queue.default'),
            'config_cacheada' => app()->configurationIsCached(),
        ];
    }

    /**
     * Executa diagnóstico completo e, opcionalmente, envia e-mail de teste.
     */
    public function executar(?string $emailDestino = null, string $tipoTeste = 'simples'): array
    {
        $log = [];
        $config = $this->getConfiguracao();
        $sucesso = false;
        $erro = null;
        $smtpTranscript = null;

        $this->addLog($log, 'info', 'Início do diagnóstico de e-mail', now()->format('d/m/Y H:i:s'));

        $this->addLog($log, 'config', 'Configuração carregada do .env / config/mail.php', $config);

        $this->verificarDriver($log, $config);
        $this->verificarConfigCacheada($log, $config);
        $this->verificarPreferenciasConta($log);
        $this->verificarExtensaoOpenssl($log);
        $this->verificarConexaoTcp($log, $config);
        $this->verificarFila($log);
        $this->verificarLogMail($log);

        if ($emailDestino) {
            $this->addLog($log, 'info', 'Iniciando envio de teste', [
                'destino' => $emailDestino,
                'tipo'    => $tipoTeste,
            ]);

            try {
                if ($tipoTeste === 'recuperacao_senha_real') {
                    $this->enviarTesteRecuperacaoSenhaReal($emailDestino, $log);
                    $sucesso = true;
                } elseif ($tipoTeste === 'recuperacao_senha') {
                    $smtpTranscript = $this->enviarViaSwiftTranscript($emailDestino, '[DMK] Teste - Recuperação de senha (simulado)', $this->corpoRecuperacaoSimulado());
                    $sucesso = true;
                } elseif ($tipoTeste === 'laravel_mail') {
                    $this->enviarTesteLaravelMail($emailDestino, $log);
                    $sucesso = true;
                } else {
                    $smtpTranscript = $this->enviarViaSwiftTranscript(
                        $emailDestino,
                        '[DMK] Teste SMTP com transcript',
                        "Teste de envio com log completo da conversa SMTP.\nData/hora: " . now()->format('d/m/Y H:i:s')
                    );
                    $sucesso = true;
                }

                if ($smtpTranscript) {
                    $this->addLog($log, 'debug', 'Transcript SMTP (conversa completa com o servidor)', $smtpTranscript);
                }

                $this->addLog($log, 'success', 'Nenhuma exceção lançada pelo PHP/Laravel', [
                    'destino' => $emailDestino,
                    'interpretacao' => 'O servidor SMTP aceitou a mensagem. Se o e-mail não chegou, verifique spam, filtros e se o remetente (' . $config['from_address'] . ') está autorizado.',
                ]);
            } catch (\Throwable $e) {
                $erro = $e->getMessage();
                $this->addLog($log, 'error', 'Exceção capturada (Throwable)', $this->formatarExcecao($e));

                Log::error('[EMAIL-DIAGNOSTICO] Falha no teste', [
                    'destino' => $emailDestino,
                    'tipo'    => $tipoTeste,
                    'erro'    => $this->formatarExcecao($e),
                ]);
            }
        } else {
            $this->addLog($log, 'info', 'Nenhum e-mail de teste informado', 'Informe um endereço para testar o envio real via SMTP.');
        }

        $this->addLog($log, 'info', 'Pontos de falha silenciosa no sistema', $this->pontosFalhaSilenciosa());

        $this->addLog($log, 'info', 'Diagnóstico finalizado', $sucesso ? 'Sem exceção PHP' : ($erro ?: 'Verifique os passos acima'));

        return [
            'config'  => $config,
            'log'     => $log,
            'sucesso' => $sucesso,
            'erro'    => $erro,
        ];
    }

    /**
     * Envio com Swift + plugin de log — captura toda a conversa SMTP.
     */
    private function enviarViaSwiftTranscript(string $destino, string $assunto, string $corpo): string
    {
        $host       = config('mail.host');
        $port       = (int) config('mail.port');
        $encryption = config('mail.encryption');
        $username   = config('mail.username');
        $password   = config('mail.password');

        $transport = new Swift_SmtpTransport($host, $port, $encryption ?: null);
        $transport->setUsername($username);
        $transport->setPassword($password);
        $transport->setTimeout(30);

        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $transport->registerPlugin(new Swift_Plugins_Loggers_LoggerPlugin($logger));

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message($assunto))
            ->setFrom([config('mail.from.address') => config('mail.from.name')])
            ->setTo([$destino])
            ->setBody($corpo);

        $falhas = [];
        $enviados = $mailer->send($message, $falhas);

        $transcript = trim($logger->dump());

        if (! empty($falhas)) {
            throw new \RuntimeException('Swift reportou destinatários rejeitados: ' . implode(', ', $falhas));
        }

        if ($enviados < 1) {
            throw new \RuntimeException('Swift retornou 0 mensagens enviadas (sem exceção, mas sem entrega).');
        }

        return $transcript ?: '(transcript vazio)';
    }

    private function enviarTesteLaravelMail(string $emailDestino, array &$log): void
    {
        $this->addLog($log, 'info', 'Teste via Mail::raw (mesmo caminho das notificações Laravel)', null);

        Mail::raw(
            'Teste via facade Mail::raw em ' . now()->format('d/m/Y H:i:s'),
            function ($message) use ($emailDestino) {
                $message->to($emailDestino)->subject('[DMK] Teste Mail::raw');
            }
        );
    }

    private function enviarTesteRecuperacaoSenhaReal(string $emailDestino, array &$log): void
    {
        $this->addLog($log, 'info', 'Teste via Password::sendResetLink (fluxo real de recuperação de senha)', null);

        $usuario = User::where('email', $emailDestino)->first();

        if (! $usuario) {
            $this->addLog($log, 'warning', 'Usuário não encontrado com este e-mail', [
                'observacao' => 'O formulário público de recuperação também não envia e-mail se o usuário não existir (por segurança, mas sem erro visível).',
            ]);
        }

        $response = Password::broker()->sendResetLink(['email' => $emailDestino]);

        $this->addLog($log, 'info', 'Resposta do Password broker', [
            'codigo'  => $response,
            'sucesso' => $response === Password::RESET_LINK_SENT,
            'mensagem_traduzida' => trans($response),
        ]);

        if ($response !== Password::RESET_LINK_SENT) {
            throw new \RuntimeException('Password broker não confirmou envio: ' . trans($response) . " ({$response})");
        }
    }

    private function corpoRecuperacaoSimulado(): string
    {
        return "Simulação do template de recuperação de senha.\n\n"
            . 'Link de exemplo: ' . url('/password/reset/token-exemplo') . "\n\n"
            . 'Data/hora: ' . now()->format('d/m/Y H:i:s');
    }

    private function verificarDriver(array &$log, array $config): void
    {
        $driver = strtolower((string) $config['driver']);

        if (in_array($driver, ['log', 'array'], true)) {
            $this->addLog($log, 'error', 'Driver de e-mail não envia mensagens reais', [
                'driver' => $driver,
                'acao'   => 'Altere MAIL_DRIVER para smtp no .env e rode php artisan config:clear',
            ]);
        } else {
            $this->addLog($log, 'success', 'Driver de e-mail', ['driver' => $driver]);
        }
    }

    private function verificarConfigCacheada(array &$log, array $config): void
    {
        if ($config['config_cacheada']) {
            $this->addLog($log, 'warning', 'Configuração em cache', [
                'observacao' => 'bootstrap/cache/config.php existe. Alterações no .env só valem após php artisan config:clear',
            ]);
        } else {
            $this->addLog($log, 'success', 'Configuração lida diretamente do .env', null);
        }
    }

    private function verificarPreferenciasConta(array &$log): void
    {
        $cdConta = \Session::get('SESSION_CD_CONTA');

        if (! $cdConta) {
            $this->addLog($log, 'warning', 'Preferências de notificação da conta', 'Sessão sem SESSION_CD_CONTA — não foi possível verificar flags.');
            return;
        }

        $conta = Conta::where('cd_conta_con', $cdConta)->first();

        if (! $conta) {
            return;
        }

        $this->addLog($log, $conta->fl_envio_notificacao_con === 'S' ? 'success' : 'warning', 'Flag fl_envio_notificacao_con', [
            'valor' => $conta->fl_envio_notificacao_con,
            'efeito' => $conta->fl_envio_notificacao_con === 'S'
                ? 'Notificações de processo habilitadas'
                : 'Algumas notificações de correspondente são BLOQUEADAS silenciosamente (retorno false, sem e-mail)',
        ]);

        $this->addLog($log, $conta->fl_notificacao_correspondente_con === 'S' ? 'success' : 'warning', 'Flag fl_notificacao_correspondente_con', [
            'valor' => $conta->fl_notificacao_correspondente_con,
        ]);
    }

    private function verificarExtensaoOpenssl(array &$log): void
    {
        $openssl = extension_loaded('openssl');
        $this->addLog($log, $openssl ? 'success' : 'error', 'Extensão OpenSSL (PHP)', [
            'disponivel' => $openssl,
        ]);
    }

    private function verificarConexaoTcp(array &$log, array $config): void
    {
        $host = $config['host'];
        $port = (int) $config['port'];
        $encryption = strtolower((string) $config['encryption']);

        $prefixo = ($encryption === 'ssl' || $port === 465) ? 'ssl://' : 'tcp://';
        $alvo = $prefixo . $host;

        $errno = 0;
        $errstr = '';
        $inicio = microtime(true);
        $socket = @fsockopen($alvo, $port, $errno, $errstr, 15);
        $duracao = round((microtime(true) - $inicio) * 1000);

        if ($socket) {
            fclose($socket);
            $this->addLog($log, 'success', 'Conexão TCP com servidor SMTP', [
                'alvo'       => $alvo . ':' . $port,
                'duracao_ms' => $duracao,
            ]);
        } else {
            $this->addLog($log, 'error', 'Conexão TCP com servidor SMTP', [
                'alvo'  => $alvo . ':' . $port,
                'errno' => $errno,
                'erro'  => $errstr ?: 'Não foi possível conectar',
            ]);
        }

        if (empty($config['username']) || ! $config['password_set']) {
            $this->addLog($log, 'error', 'Credenciais SMTP', [
                'username' => $config['username'] ?: '(vazio)',
                'password' => $config['password_set'] ? 'definida' : 'NÃO DEFINIDA no .env',
            ]);
        } else {
            $this->addLog($log, 'success', 'Credenciais SMTP presentes no .env', [
                'username' => $config['username_masked'],
                'password' => $config['password_masked'],
            ]);
        }
    }

    private function verificarFila(array &$log): void
    {
        $queue = config('queue.default');
        $dados = ['driver' => $queue];

        if ($queue === 'redis') {
            try {
                app('redis')->connection()->ping();
                $dados['redis'] = 'conectado';
                $this->addLog($log, 'success', 'Fila de jobs (Redis)', $dados + [
                    'observacao' => 'Jobs enfileirados (ex.: EnviarEmailAtualizacaoCadastroJob) precisam de queue:work ativo.',
                ]);
            } catch (\Throwable $e) {
                $dados['redis'] = 'falha: ' . $e->getMessage();
                $this->addLog($log, 'warning', 'Fila de jobs (Redis)', $dados);
            }
        } else {
            $this->addLog($log, 'info', 'Fila de jobs', $dados);
        }
    }

    private function verificarLogMail(array &$log): void
    {
        $arquivo = storage_path('logs/mail.log');

        if (! file_exists($arquivo)) {
            $this->addLog($log, 'info', 'Log de envios (storage/logs/mail.log)', 'Ainda vazio — será preenchido a partir de agora a cada tentativa de envio.');
            return;
        }

        $linhas = array_slice(file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -15);

        $this->addLog($log, 'info', 'Últimas linhas de storage/logs/mail.log', $linhas ?: ['(vazio)']);
    }

    private function pontosFalhaSilenciosa(): array
    {
        return [
            'catch_parcial' => 'Alguns fluxos em ProcessoController capturam só Swift_RfcComplianceException — falhas SMTP (auth, timeout) podem estourar sem mensagem amigável ou serem engolidas pelo handler global.',
            'log_antes_envio' => 'LogNotificacao é gravado ANTES do notify() — log existe mesmo quando o e-mail falha.',
            'flags_conta' => 'Correspondente::notificarCadastroConta/Filiacao/Senha retornam false sem enviar se fl_envio_notificacao_con ou fl_notificacao_correspondente_con = N.',
            'jobs_fila' => 'EnviarEmailAtualizacaoCadastroJob usa fila Redis — sem worker, e-mail nunca sai e não há exceção na tela.',
            'recuperacao_senha' => 'Se o e-mail não existir na base, Laravel responde com erro; se existir mas SMTP falhar, depende da exceção ser propagada.',
            'sucesso_prematuro' => '"Nenhuma exceção" não garante entrega na caixa de entrada — apenas que o SMTP aceitou a mensagem.',
        ];
    }

    private function formatarExcecao(\Throwable $e): array
    {
        $dados = [
            'classe'   => get_class($e),
            'mensagem' => $e->getMessage(),
            'arquivo'  => $e->getFile() . ':' . $e->getLine(),
        ];

        if ($e->getPrevious()) {
            $dados['causa_anterior'] = $e->getPrevious()->getMessage();
        }

        if (strpos($e->getMessage(), '535') !== false || stripos($e->getMessage(), 'BadCredentials') !== false) {
            $dados['diagnostico_provavel'] = 'Credenciais SMTP rejeitadas pelo servidor.';
            $dados['acao_sugerida'] = 'Atualize MAIL_USERNAME e MAIL_PASSWORD no .env. Para Gmail, use Senha de App.';
        }

        $dados['stack_trace'] = $e->getTraceAsString();

        return $dados;
    }

    private function mascarar(?string $valor): string
    {
        if (empty($valor)) {
            return '(não definido)';
        }

        if (strlen($valor) <= 4) {
            return str_repeat('*', strlen($valor));
        }

        return substr($valor, 0, 2) . str_repeat('*', max(3, strlen($valor) - 4)) . substr($valor, -2);
    }

    private function mascararSenha(?string $valor): string
    {
        if (empty($valor)) {
            return '(não definida)';
        }

        return '******** (' . strlen($valor) . ' caracteres)';
    }

    private function addLog(array &$log, string $nivel, string $titulo, $detalhe = null): void
    {
        $log[] = [
            'hora'    => now()->format('H:i:s'),
            'nivel'   => $nivel,
            'titulo'  => $titulo,
            'detalhe' => $detalhe,
        ];
    }
}
