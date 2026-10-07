<?php

namespace App\Services\Autentique;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;

/**
 * Cliente mínimo da API GraphQL da Autentique (https://docs.autentique.com.br/api).
 */
class AutentiqueClient
{
    const CAMPOS_ASSINATURAS = 'signatures { public_id name email action { name } link { short_link } user { name email phone } }';

    /** @var Client */
    private $http;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?: new Client([
            'timeout'     => config('autentique.timeout'),
            'http_errors' => false,
        ]);
    }

    public function configurado(): bool
    {
        return (string) config('autentique.token') !== '';
    }

    /**
     * Cria o documento e dispara os pedidos de assinatura. Retorna id e assinaturas (public_id, email, user...).
     */
    public function criarDocumento(string $arquivo, array $documento, array $signatarios, bool $sandbox): array
    {
        $query = 'mutation CreateDocumentMutation($document: DocumentInput!, $signers: [SignerInput!]!, $file: Upload!) {'
            . ' createDocument(sandbox: ' . ($sandbox ? 'true' : 'false') . ', document: $document, signers: $signers, file: $file) {'
            . ' id name created_at ' . self::CAMPOS_ASSINATURAS . ' } }';

        $operacoes = json_encode([
            'query'     => $query,
            'variables' => ['document' => $documento, 'signers' => $signatarios, 'file' => null],
        ], JSON_UNESCAPED_UNICODE);

        if (! is_readable($arquivo)) {
            throw new AutentiqueException('Não foi possível ler o PDF do contrato.');
        }

        $dados = $this->enviar([
            'multipart' => [
                ['name' => 'operations', 'contents' => $operacoes],
                ['name' => 'map', 'contents' => json_encode(['file' => ['variables.file']])],
                ['name' => 'file', 'contents' => Utils::tryFopen($arquivo, 'r'), 'filename' => basename($arquivo), 'headers' => ['Content-Type' => 'application/pdf']],
            ],
        ]);

        return $dados['createDocument'];
    }

    public function documento(string $id): ?array
    {
        $query = 'query ($id: UUID!) { document(id: $id) { id name deleted_at files { original signed }'
            . ' signatures { public_id name email action { name } user { name email phone }'
            . ' viewed { created_at } signed { created_at } rejected { created_at reason }'
            . ' email_events { refused reason } } } }';

        return $this->graphql($query, ['id' => $id])['document'] ?? null;
    }

    public function assinarComoDono(string $id): bool
    {
        return (bool) ($this->graphql('mutation ($id: UUID!) { signDocument(id: $id) }', ['id' => $id])['signDocument'] ?? false);
    }

    public function reenviar(array $publicIds): void
    {
        $this->graphql('mutation ($ids: [UUID!]!) { resendSignatures(public_ids: $ids) }', ['ids' => array_values($publicIds)]);
    }

    /**
     * Bloqueia novas assinaturas (prazo = agora) e exclui o documento. Excluir sozinho não bloqueia
     * documentos que já têm alguma assinatura.
     */
    public function cancelar(string $id): void
    {
        try {
            $this->graphql(
                'mutation ($id: UUID!, $document: UpdateDocumentInput!) { updateDocument(id: $id, document: $document) { id } }',
                ['id' => $id, 'document' => ['deadline_at' => gmdate('Y-m-d\TH:i:s.000\Z')]]
            );
        } finally {
            $this->graphql('mutation ($id: UUID!) { deleteDocument(id: $id) }', ['id' => $id]);
        }
    }

    public function baixarArquivo(string $url, string $destino): void
    {
        try {
            $resposta = $this->http->request('GET', $url, ['sink' => $destino]);
            if (in_array($resposta->getStatusCode(), [401, 403], true)) {
                $resposta = $this->http->request('GET', $url, [
                    'headers' => ['Authorization' => 'Bearer ' . config('autentique.token')],
                    'sink'    => $destino,
                ]);
            }
        } catch (GuzzleException $e) {
            throw new AutentiqueException('Falha ao baixar o PDF assinado: ' . $e->getMessage(), 0, $e);
        }

        if ($resposta->getStatusCode() !== 200) {
            @unlink($destino);
            throw new AutentiqueException('Falha ao baixar o PDF assinado (HTTP ' . $resposta->getStatusCode() . ').');
        }
    }

    public function graphql(string $query, array $variaveis = []): array
    {
        return $this->enviar(['json' => ['query' => $query, 'variables' => (object) $variaveis]]);
    }

    private function enviar(array $opcoes): array
    {
        if (! $this->configurado()) {
            throw new AutentiqueException('Token da Autentique não configurado (AUTENTIQUE_TOKEN).');
        }

        $opcoes['headers'] = array_merge($opcoes['headers'] ?? [], [
            'Authorization' => 'Bearer ' . config('autentique.token'),
            'Accept'        => 'application/json',
        ]);

        try {
            $resposta = $this->http->request('POST', config('autentique.url'), $opcoes);
        } catch (GuzzleException $e) {
            throw new AutentiqueException('Falha de comunicação com a Autentique: ' . $e->getMessage(), 0, $e);
        }

        $corpo = json_decode((string) $resposta->getBody(), true);

        if (! empty($corpo['errors'])) {
            $mensagens = array_map(function ($erro) {
                return $erro['message'] ?? json_encode($erro);
            }, $corpo['errors']);
            throw new AutentiqueException('Autentique: ' . implode('; ', $mensagens));
        }

        if ($resposta->getStatusCode() >= 400 || ! isset($corpo['data'])) {
            throw new AutentiqueException('Autentique respondeu HTTP ' . $resposta->getStatusCode() . '.');
        }

        return $corpo['data'];
    }
}
