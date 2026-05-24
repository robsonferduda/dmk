<?php

namespace App\Services\ChatPro;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * [CHATPRO]
 * Cliente HTTP fino para a API ChatPro (https://chatpro.readme.io/).
 *
 * Por design, NUNCA propaga exceção: erros são logados e refletidos
 * no array de retorno ['success' => false, 'message' => ...]. Isso
 * impede que falhas de WhatsApp derrubem fluxos de negócio (check-in,
 * lembretes etc.) que apenas disparam notificação acessória.
 *
 * As credenciais (instance_id + token) são por CONTA — instancie um
 * client por conta usando ChatProClient::forConta($conta).
 */
class ChatProClient
{
    /** @var string */
    private $instanceId;
    /** @var string */
    private $token;
    /** @var string */
    private $baseUrl;
    /** @var Client */
    private $http;

    public function __construct($instanceId, $token, array $opts = [])
    {
        $this->instanceId = trim((string) $instanceId);
        $this->token      = trim((string) $token);
        $this->baseUrl    = rtrim($opts['base_url'] ?? config('chatpro.base_url'), '/');

        $this->http = new Client([
            'base_uri'        => $this->baseUrl . '/',
            'timeout'         => $opts['timeout']         ?? config('chatpro.timeout', 8),
            'connect_timeout' => $opts['connect_timeout'] ?? config('chatpro.connect_timeout', 4),
            'http_errors'     => false,
            'headers'         => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => $this->token,
            ],
        ]);
    }

    /**
     * Cria um client a partir de um \App\Conta (lê os campos da conta).
     * Retorna null se a conta não tiver integração ativa/configurada.
     */
    public static function forConta($conta)
    {
        if (!$conta) {
            return null;
        }
        // fl_chatpro_ativo_con é BOOLEAN no Postgres → vem como bool (true/false)
        // do Eloquent, mas pode chegar como 't'/'f' em consultas raw. Normalizamos.
        $ativo = $conta->fl_chatpro_ativo_con ?? false;
        if (is_string($ativo)) {
            $ativo = in_array(strtolower($ativo), ['t', 'true', '1', 's', 'y', 'yes'], true);
        }
        if (!$ativo) {
            return null;
        }
        if (empty($conta->ds_chatpro_instance_id_con) || empty($conta->ds_chatpro_token_con)) {
            return null;
        }

        return new self(
            $conta->ds_chatpro_instance_id_con,
            $conta->ds_chatpro_token_con
        );
    }

    /**
     * Envia mensagem de texto.
     *
     * @param string $numero  Telefone destino (E.164 sem '+'), ex.: 5548999999999
     * @param string $mensagem
     * @return array ['success' => bool, 'status' => int, 'body' => mixed, 'message' => ?string]
     */
    public function sendText($numero, $mensagem)
    {
        $numero = $this->normalizarNumero($numero);
        if (!$numero) {
            return ['success' => false, 'status' => 0, 'body' => null, 'message' => 'Número destino inválido'];
        }
        if (trim((string) $mensagem) === '') {
            return ['success' => false, 'status' => 0, 'body' => null, 'message' => 'Mensagem vazia'];
        }

        $endpoint = $this->instanceId . '/api/v1/send_message';

        return $this->request('POST', $endpoint, [
            'json' => [
                'number'  => $numero,
                'message' => $mensagem,
            ],
        ]);
    }

    /**
     * Envia um documento/arquivo (PDF, imagem etc.) via URL pública acessível.
     *
     * Endpoint: POST {instance_id}/api/v1/send_message_file_from_url
     *
     * @param string $numero   Telefone destino (E.164 sem '+'), ex.: 5548999999999
     * @param string $url      URL pública do arquivo
     * @param string $caption  Legenda opcional exibida junto ao documento
     * @return array ['success' => bool, 'status' => int, 'body' => mixed, 'message' => ?string]
     */
    public function sendDocument($numero, $url, $caption = '')
    {
        $numero = $this->normalizarNumero($numero);
        if (!$numero) {
            return ['success' => false, 'status' => 0, 'body' => null, 'message' => 'Número destino inválido'];
        }

        $endpoint = $this->instanceId . '/api/v1/send_message_file_from_url';

        return $this->request('POST', $endpoint, [
            'json' => [
                'number'  => $numero,
                'url'     => $url,
                'caption' => $caption,
            ],
        ]);
    }

    /**
     * Status da instância (útil para diagnóstico/healthcheck).
     */
    public function status()
    {
        $endpoint = $this->instanceId . '/api/v1/status';
        return $this->request('GET', $endpoint);
    }

    // -----------------------------------------------------------------

    private function request($method, $endpoint, array $options = [], int $tentativa = 1)
    {
        $maxTentativas = 3;
        try {
            $resp = $this->http->request($method, $endpoint, $options);
            $code = $resp->getStatusCode();
            $raw  = (string) $resp->getBody();
            $body = json_decode($raw, true);
            if ($body === null && $raw !== '') {
                $body = $raw;
            }

            $ok = ($code >= 200 && $code < 300);
            if (!$ok) {
                Log::warning('[CHATPRO] HTTP ' . $code . ' em ' . $endpoint, ['body' => $body]);
            }

            return [
                'success' => $ok,
                'status'  => $code,
                'body'    => $body,
                'message' => $ok ? null : ('ChatPro respondeu HTTP ' . $code),
            ];
        } catch (GuzzleException $e) {
            if ($tentativa < $maxTentativas) {
                Log::warning('[CHATPRO] Falha na tentativa ' . $tentativa . '/' . $maxTentativas . ', aguardando para retry: ' . $e->getMessage(), [
                    'endpoint' => $endpoint,
                ]);
                sleep(3 * $tentativa); // backoff: 3s, depois 6s
                return $this->request($method, $endpoint, $options, $tentativa + 1);
            }
            Log::error('[CHATPRO] Falha de comunicação após ' . $maxTentativas . ' tentativas: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
            ]);
            return [
                'success' => false,
                'status'  => 0,
                'body'    => null,
                'message' => 'Falha de comunicação: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            Log::error('[CHATPRO] Erro inesperado: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
            ]);
            return [
                'success' => false,
                'status'  => 0,
                'body'    => null,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Normaliza o número para E.164 sem '+': remove tudo que não for dígito.
     */
    private function normalizarNumero($numero)
    {
        $n = preg_replace('/\D+/', '', (string) $numero);
        if (strlen($n) < 10) {
            return null;
        }
        return $n;
    }
}
