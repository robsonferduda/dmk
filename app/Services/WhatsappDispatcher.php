<?php

namespace App\Services;

use App\Services\ChatPro\ChatProClient;
use App\Services\ZApi\ZApiClient;
use Illuminate\Support\Facades\Log;

/**
 * [WHATSAPP DISPATCHER]
 * Factory que escolhe qual cliente WhatsApp usar para uma dada Conta.
 *
 * Ordem de prioridade:
 *   1. Z-API  (fl_zapi_ativo_con = true e credenciais preenchidas)
 *   2. ChatPro (fl_chatpro_ativo_con = true e credenciais preenchidas) — fallback
 *
 * Retorna uma instância de ZApiClient ou ChatProClient, ambos com
 * a mesma interface pública:
 *   - sendText($numero, $mensagem)
 *   - sendDocument($numero, $url, $caption = '')
 *   - status()
 *
 * Retorna null se nenhum provedor estiver ativo/configurado.
 *
 * Uso:
 *   $client = WhatsappDispatcher::forConta($conta);
 *   if ($client) {
 *       $res = $client->sendText($destino, $mensagem);
 *   }
 */
class WhatsappDispatcher
{
    /**
     * Retorna o cliente WhatsApp ativo para a conta informada.
     *
     * @param \App\Conta|null $conta
     * @return ZApiClient|ChatProClient|null
     */
    public static function forConta($conta)
    {
        if (!$conta) {
            return null;
        }

        // Tenta Z-API primeiro (nova integração, prioritária).
        $zapiClient = ZApiClient::forConta($conta);
        if ($zapiClient) {
            Log::info('[WHATSAPP-DISPATCHER] Provedor selecionado: Z-API', ['conta' => $conta->cd_conta_con]);
            return $zapiClient;
        }

        // Fallback: ChatPro (integração legada, mantida enquanto houver contas
        // ainda configuradas com ela).
        $chatproClient = ChatProClient::forConta($conta);
        if ($chatproClient) {
            Log::info('[WHATSAPP-DISPATCHER] Provedor selecionado: ChatPro (fallback)', ['conta' => $conta->cd_conta_con]);
        }
        return $chatproClient;
    }
}
