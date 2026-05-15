<?php

/*
|--------------------------------------------------------------------------
| ChatPro / WhatsApp
|--------------------------------------------------------------------------
|
| Integração com a API ChatPro (https://chatpro.readme.io/).
|
| As credenciais (instance_id + token) e o telefone destino são por CONTA
| (colunas em conta_con). Aqui ficam apenas defaults globais (URL base,
| timeouts) e o template das mensagens.
|
*/

return [

    // URL base da API ChatPro. A instância entra como segmento na chamada.
    // Ex.: https://v5.chatpro.com.br/{instance_id}/api/v1/send_message
    'base_url' => env('CHATPRO_BASE_URL', 'https://v5.chatpro.com.br'),

    // Timeout de conexão / requisição (segundos).
    'timeout'         => (int) env('CHATPRO_TIMEOUT', 8),
    'connect_timeout' => (int) env('CHATPRO_CONNECT_TIMEOUT', 4),

    // Templates de mensagem. Placeholders disponíveis estão entre {chaves}.
    'templates' => [

        'checkin' =>
            "📍 *Check-in realizado*\n" .
            "Processo: {processo}\n" .
            "Correspondente: {correspondente}\n" .
            "Local: {coordenadas}\n" .
            "Mapa: {maps_url}\n" .
            "Data/Hora: {datahora}\n" .
            "Cliente: {cliente}",

    ],

];
