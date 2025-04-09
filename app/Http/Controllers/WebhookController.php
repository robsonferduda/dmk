<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function log()
    {
        Log::info('Teste de Log');
    }

    /**
     * Manipula as notificações do webhook da Autentique.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleAutentiqueWebhook(Request $request)
    {
        // Recupera os dados enviados pelo webhook
        $payload = $request->all();

        // Registra os dados no log para depuração
        Log::info('Webhook recebido da Autentique:', $payload);

        // Validação básica (opcional, depende da API)
        if (!isset($payload['event']) || !isset($payload['data'])) {
            return response()->json(['error' => 'Payload inválido'], 400);
        }

        // Processa o evento
        $event = $payload['event'];
        $data = $payload['data'];

        switch ($event) {
            case 'document.signed':
                $this->handleDocumentSigned($data);
                break;

            case 'document.rejected':
                $this->handleDocumentRejected($data);
                break;

            default:
                Log::warning("Evento desconhecido recebido: {$event}");
                break;
        }

        // Retorna uma resposta de sucesso
        return response()->json(['message' => 'Webhook recebido com sucesso'], 200);
    }

    /**
     * Processa o evento de documento assinado.
     *
     * @param array $data
     */
    protected function handleDocumentSigned(array $data)
    {
        // Exemplo: Atualizar o status do documento no banco de dados
        Log::info('Documento assinado:', $data);
        // Aqui você pode salvar o status no banco de dados ou disparar outras ações
    }

    /**
     * Processa o evento de documento rejeitado.
     *
     * @param array $data
     */
    protected function handleDocumentRejected(array $data)
    {
        // Exemplo: Registrar a rejeição
        Log::info('Documento rejeitado:', $data);
        // Aqui você pode notificar o administrador ou tomar outras ações
    }
}