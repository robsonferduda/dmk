<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Mail;

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
            'driver'     => config('mail.driver'),
            'host'       => config('mail.host'),
            'port'       => config('mail.port'),
            'encryption' => config('mail.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name'    => config('mail.from.name'),
            'username'   => $username,
            'username_masked' => $this->mascarar($username),
            'password_set' => ! empty($password),
            'password_masked' => $this->mascararSenha($password),
            'app_env'    => config('app.env'),
            'app_url'    => config('app.url'),
            'queue'      => config('queue.default'),
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

        $this->addLog($log, 'info', 'Início do diagnóstico de e-mail', now()->format('d/m/Y H:i:s'));

        $this->addLog($log, 'config', 'Configuração carregada do .env / config/mail.php', $config);

        $this->verificarExtensaoOpenssl($log);
        $this->verificarConexaoTcp($log, $config);
        $this->verificarFila($log);

        if ($emailDestino) {
            $this->addLog($log, 'info', 'Iniciando envio de teste', [
                'destino' => $emailDestino,
                'tipo'    => $tipoTeste,
            ]);

            try {
                if ($tipoTeste === 'recuperacao_senha') {
                    $this->enviarTesteRecuperacaoSenha($emailDestino);
                } else {
                    $this->enviarTesteSimples($emailDestino);
                }

                $sucesso = true;
                $this->addLog($log, 'success', 'E-mail enviado com sucesso', [
                    'destino' => $emailDestino,
                    'observacao' => 'Se não chegou na caixa de entrada, verifique spam e filtros do provedor.',
                ]);
            } catch (\Throwable $e) {
                $erro = $e->getMessage();
                $this->addLog($log, 'error', 'Falha no envio', $this->formatarExcecao($e));
            }
        } else {
            $this->addLog($log, 'info', 'Nenhum e-mail de teste informado', 'Informe um endereço para testar o envio real via SMTP.');
        }

        $this->addLog($log, 'info', 'Diagnóstico finalizado', $sucesso ? 'OK' : ($erro ?: 'Verifique os passos acima'));

        return [
            'config'  => $config,
            'log'     => $log,
            'sucesso' => $sucesso,
            'erro'    => $erro,
        ];
    }

    private function enviarTesteSimples(string $emailDestino): void
    {
        Mail::raw(
            'Este é um e-mail de teste do diagnóstico DMK.' . PHP_EOL
            . 'Data/hora: ' . now()->format('d/m/Y H:i:s') . PHP_EOL
            . 'Se você recebeu esta mensagem, o SMTP está funcionando.',
            function ($message) use ($emailDestino) {
                $message->to($emailDestino)
                    ->subject('[DMK] Teste de envio de e-mail');
            }
        );
    }

    private function enviarTesteRecuperacaoSenha(string $emailDestino): void
    {
        Mail::send('email.email', [], function ($message) use ($emailDestino) {
            $message->to($emailDestino)
                ->subject('[DMK] Teste - Recuperação de senha (simulado)')
                ->setBody(
                    "Simulação do template de recuperação de senha.\n\n"
                    . "Link de exemplo: " . url('/password/reset/token-exemplo') . "\n\n"
                    . "Data/hora: " . now()->format('d/m/Y H:i:s'),
                    'text/plain'
                );
        });
    }

    private function verificarExtensaoOpenssl(array &$log): void
    {
        $openssl = extension_loaded('openssl');
        $this->addLog($log, $openssl ? 'success' : 'error', 'Extensão OpenSSL (PHP)', [
            'disponivel' => $openssl,
            'necessaria' => 'Sim, para TLS/SSL no SMTP',
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
                'alvo'     => $alvo . ':' . $port,
                'duracao_ms' => $duracao,
            ]);
        } else {
            $this->addLog($log, 'error', 'Conexão TCP com servidor SMTP', [
                'alvo'   => $alvo . ':' . $port,
                'errno'  => $errno,
                'erro'   => $errstr ?: 'Não foi possível conectar',
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
                $this->addLog($log, 'success', 'Fila de jobs (Redis)', $dados);
            } catch (\Throwable $e) {
                $dados['redis'] = 'falha: ' . $e->getMessage();
                $this->addLog($log, 'warning', 'Fila de jobs (Redis)', $dados + [
                    'observacao' => 'E-mails enfileirados (Jobs) não serão enviados sem queue:work ativo.',
                ]);
            }
        } elseif ($queue === 'sync') {
            $this->addLog($log, 'info', 'Fila de jobs', $dados + [
                'observacao' => 'sync — envios síncronos, sem worker necessário.',
            ]);
        } else {
            $this->addLog($log, 'info', 'Fila de jobs', $dados);
        }

        $this->addLog($log, 'info', 'Notificações do sistema', [
            'observacao' => 'Notificações de processo e recuperação de senha são enviadas de forma síncrona (não usam fila).',
            'log_notificacao' => 'LogNotificacao grava tentativa de notificação de processo ANTES do envio — falha de SMTP não impede o log.',
            'recuperacao_senha' => 'Recuperação de senha NÃO grava em LogNotificacao; falha de SMTP gera erro silencioso na interface.',
        ]);
    }

    private function formatarExcecao(\Throwable $e): array
    {
        $dados = [
            'classe'  => get_class($e),
            'mensagem' => $e->getMessage(),
            'arquivo' => $e->getFile() . ':' . $e->getLine(),
        ];

        if ($e->getPrevious()) {
            $dados['causa_anterior'] = $e->getPrevious()->getMessage();
        }

        if (strpos($e->getMessage(), '535') !== false || strpos($e->getMessage(), 'BadCredentials') !== false) {
            $dados['diagnostico_provavel'] = 'Credenciais SMTP rejeitadas pelo servidor (usuário/senha inválidos ou App Password do Gmail expirada).';
            $dados['acao_sugerida'] = 'Atualize MAIL_USERNAME e MAIL_PASSWORD no .env. Para Gmail, use Senha de App em https://myaccount.google.com/apppasswords';
        }

        if (strpos($e->getMessage(), 'Connection could not be established') !== false) {
            $dados['diagnostico_provavel'] = 'Não foi possível conectar ao servidor SMTP (host/porta/firewall).';
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
