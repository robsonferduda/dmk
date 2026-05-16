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

    // ----------------------------------------------------------------
    // Webhook (recebimento de mensagens / acks)
    // ----------------------------------------------------------------
    // URL pública que cadastramos no painel ChatPro. Apenas referência.
    'webhook_url'   => env('CHATPRO_WEBHOOK_URL', 'https://sistema.lawyerexpress.com.br/api/chatpro/webhook'),

    // Token (segredo compartilhado) que o ChatPro envia como query string
    // (?token=...) na URL do webhook. Se vazio, NÃO valida (use só em dev).
    'webhook_token' => env('CHATPRO_WEBHOOK_TOKEN', ''),


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

        // Lembrete diário enviado ao CORRESPONDENTE no dia do prazo fatal.
        // Placeholders: {correspondente}, {processo}, {cliente}, {vara},
        // {cidade}, {hora_audiencia}, {link_checkin}.
        'lembrete_diligencia' =>
            "⚖️ *Diligência hoje*\n" .
            "Olá, {correspondente}!\n" .
            "Você tem diligência marcada para hoje:\n" .
            "• Processo: {processo}\n" .
            "• Cliente: {cliente}\n" .
            "• Vara: {vara}\n" .
            "• Cidade: {cidade}\n" .
            "• Horário: {hora_audiencia}\n\n" .
            "Ao chegar no local, faça o check-in:\n" .
            "{link_checkin}",

    ],

];
