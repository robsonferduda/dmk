<?php

/*
|--------------------------------------------------------------------------
| Z-API / WhatsApp
|--------------------------------------------------------------------------
|
| Integração com a API Z-API (https://developer.z-api.io/).
|
| As credenciais (instance_id, token, client_token) são por CONTA
| (colunas em conta_con). Aqui ficam apenas defaults globais (URL base,
| timeouts) e configurações de webhook.
|
| URL do endpoint: https://api.z-api.io/instances/{instance_id}/token/{token}/{action}
| Cabeçalho obrigatório: Client-Token: {client_token}
|
*/

return [

    // URL base da API Z-API.
    'base_url' => env('ZAPI_BASE_URL', 'https://api.z-api.io'),

    // Timeout de conexão / requisição (segundos).
    'timeout'         => (int) env('ZAPI_TIMEOUT', 15),
    'connect_timeout' => (int) env('ZAPI_CONNECT_TIMEOUT', 5),

    // ----------------------------------------------------------------
    // Webhook (recebimento de mensagens / status)
    // ----------------------------------------------------------------
    // URL pública que cadastramos no painel Z-API.
    'webhook_url' => env('ZAPI_WEBHOOK_URL', 'https://sistema.lawyerexpress.com.br/api/zapi/webhook'),

    // Token de segurança que a Z-API envia como query-string (?token=...)
    // ou no corpo. Se vazio, NÃO valida (use só em dev).
    'webhook_token' => env('ZAPI_WEBHOOK_TOKEN', ''),

    // Janela (em dias) usada pelo webhook para amarrar uma mensagem
    // INBOUND a um processo. 0 desliga o fallback.
    'inbound_link_window_days' => (int) env('ZAPI_INBOUND_LINK_WINDOW_DAYS', 30),

];
