<?php

namespace App\Services\ZApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * [Z-API]
 * Cliente HTTP para a API Z-API (https://developer.z-api.io/).
 *
 * Interface intencionalmente idêntica à do ChatProClient:
 *   - forConta($conta)  → instancia o client a partir de uma Conta
 *   - sendText(...)     → envia mensagem de texto
 *   - sendDocument(...) → envia documento via URL pública
 *   - status()          → verifica status da instância
 *
 * Todos os métodos retornam o mesmo envelope:
 *   ['success' => bool, 'status' => int, 'body' => mixed, 'message' => ?string]
 *
 * Credenciais lidas da conta:
 *   - ds_zapi_instance_id_con  → ID da instância no painel Z-API
 *   - ds_zapi_token_con        → Token da instância
 *   - ds_zapi_client_token_con → Client-Token (segurança extra, opcional)
 *
 * URL de cada endpoint:
 *   https://api.z-api.io/instances/{instanceId}/token/{token}/{action}
 */
class ZApiClient
{
    /** @var string */
    private $instanceId;
    /** @var string */
    private $token;
    /** @var string|null */
    private $clientToken;
    /** @var string */
    private $baseUrl;
    /** @var Client */
    private $http;

    public function __construct($instanceId, $token, $clientToken = null, array $opts = [])
    {
        $this->instanceId  = trim((string) $instanceId);
        $this->token       = trim((string) $token);
        $this->clientToken = $clientToken ? trim((string) $clientToken) : null;
        $this->baseUrl     = rtrim($opts['base_url'] ?? config('zapi.base_url', 'https://api.z-api.io'), '/');

        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
        if ($this->clientToken) {
            $headers['Client-Token'] = $this->clientToken;
        }

        $this->http = new Client([
            'base_uri'        => $this->baseUrl . '/',
            'timeout'         => $opts['timeout']         ?? config('zapi.timeout', 15),
            'connect_timeout' => $opts['connect_timeout'] ?? config('zapi.connect_timeout', 5),
            'http_errors'     => false,
            'headers'         => $headers,
        ]);
    }

    /**
     * Cria um client a partir de um \App\Conta.
     * Retorna null se a conta não tiver integração Z-API ativa/configurada.
     */
    public static function forConta($conta)
    {
        if (!$conta) {
            return null;
        }

        $ativo = $conta->fl_zapi_ativo_con ?? false;
        if (is_string($ativo)) {
            $ativo = in_array(strtolower($ativo), ['t', 'true', '1', 's', 'y', 'yes'], true);
        }
        if (!$ativo) {
            return null;
        }
        if (empty($conta->ds_zapi_instance_id_con) || empty($conta->ds_zapi_token_con)) {
            return null;
        }

        return new self(
            $conta->ds_zapi_instance_id_con,
            $conta->ds_zapi_token_con,
            $conta->ds_zapi_client_token_con ?? null
        );
    }

    /**
     * Envia mensagem de texto.
     *
     * @param string $numero   Telefone destino (E.164 sem '+'), ex.: 5548999999999
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

        return $this->request('POST', 'send-text', [
            'json' => [
                'phone'   => $numero,
                'message' => $mensagem,
            ],
        ]);
    }

    /**
     * Envia um documento/arquivo (PDF, imagem etc.) via URL pública acessível.
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
        if (empty(trim((string) $url))) {
            return ['success' => false, 'status' => 0, 'body' => null, 'message' => 'URL do documento vazia'];
        }

        // Z-API detecta automaticamente o tipo pelo Content-Type da URL.
        // O campo "fileName" é exibido como nome do arquivo no WhatsApp.
        $fileName = basename(parse_url($url, PHP_URL_PATH)) ?: 'documento.pdf';

        return $this->request('POST', 'send-document', [
            'json' => [
                'phone'    => $numero,
                'document' => $url,
                'fileName' => $fileName,
                'caption'  => $caption,
            ],
        ]);
    }

    /**
     * Status da instância (útil para diagnóstico/healthcheck).
     */
    public function status()
    {
        return $this->request('GET', 'status');
    }

    // -----------------------------------------------------------------
    // Internos
    // -----------------------------------------------------------------

    /**
     * Monta a URL do endpoint para esta instância:
     * {baseUrl}/instances/{instanceId}/token/{token}/{action}
     */
    private function buildUrl($action)
    {
        return 'instances/' . rawurlencode($this->instanceId)
             . '/token/' . rawurlencode($this->token)
             . '/' . ltrim($action, '/');
    }

    private function request($method, $action, array $options = [], int $tentativa = 1)
    {
        $maxTentativas = 3;
        $url = $this->buildUrl($action);

        try {
            $resp = $this->http->request($method, $url, $options);
            $code = $resp->getStatusCode();
            $raw  = (string) $resp->getBody();
            $body = json_decode($raw, true);
            if ($body === null && $raw !== '') {
                $body = $raw;
            }

            $ok = ($code >= 200 && $code < 300);

            if (!$ok) {
                Log::warning('[ZAPI] HTTP ' . $code . ' em ' . $action, ['body' => $body]);

                // Retry em 429 (rate limit) ou 5xx transitórios
                if (in_array($code, [429, 500, 502, 503, 504], true) && $tentativa < $maxTentativas) {
                    $wait = 5 * $tentativa;
                    Log::warning('[ZAPI] Código ' . $code . ' na tentativa ' . $tentativa . '/' . $maxTentativas . ', aguardando ' . $wait . 's.');
                    sleep($wait);
                    return $this->request($method, $action, $options, $tentativa + 1);
                }
            }

            return [
                'success' => $ok,
                'status'  => $code,
                'body'    => $body,
                'message' => $ok ? null : ('Z-API respondeu HTTP ' . $code),
            ];

        } catch (GuzzleException $e) {
            if ($tentativa < $maxTentativas) {
                Log::warning('[ZAPI] Falha na tentativa ' . $tentativa . '/' . $maxTentativas . ', aguardando para retry: ' . $e->getMessage(), [
                    'action' => $action,
                ]);
                sleep(3 * $tentativa);
                return $this->request($method, $action, $options, $tentativa + 1);
            }
            Log::error('[ZAPI] Falha de comunicação após ' . $maxTentativas . ' tentativas: ' . $e->getMessage(), [
                'action' => $action,
            ]);
            return [
                'success' => false,
                'status'  => 0,
                'body'    => null,
                'message' => 'Falha de comunicação: ' . $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            Log::error('[ZAPI] Erro inesperado: ' . $e->getMessage(), [
                'action' => $action,
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
