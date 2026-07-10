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
    'timeout'         => (int) env('CHATPRO_TIMEOUT', 15),
    'connect_timeout' => (int) env('CHATPRO_CONNECT_TIMEOUT', 5),

    // ----------------------------------------------------------------
    // Webhook (recebimento de mensagens / acks)
    // ----------------------------------------------------------------
    // URL pública que cadastramos no painel ChatPro. Apenas referência.
    'webhook_url'   => env('CHATPRO_WEBHOOK_URL', 'https://sistema.lawyerexpress.com.br/api/chatpro/webhook'),

    // Token (segredo compartilhado) que o ChatPro envia como query string
    // (?token=...) na URL do webhook. Se vazio, NÃO valida (use só em dev).
    'webhook_token' => env('CHATPRO_WEBHOOK_TOKEN', ''),

    // Janela (em dias) usada pelo webhook para amarrar uma mensagem
    // INBOUND a um processo, quando não há "quoted reply": procura a
    // última outbound para o mesmo telefone com cd_processo_pro
    // preenchido dentro desse intervalo. 0 desliga o fallback.
    'inbound_link_window_days' => (int) env('CHATPRO_INBOUND_LINK_WINDOW_DAYS', 30),


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
        // Placeholders: {correspondente}, {processo}, {autor}, {reu}, {vara}, {cidade}, {tipo_servico},
        //               {data}, {hora_audiencia}, {responsaveis}, {link_checkin}.
        'lembrete_diligencia' =>
            "Olá, {correspondente}!\n" .
            "Você tem audiência/diligência do DMK marcada para hoje:\n" .
            "• Data e Horário: {data} às {hora_audiencia}\n" .
            "• Processo: {processo}\n" .
            "• Autor: {autor}\n" .
            "• Réu: {reu}\n" .
            "• Vara: {vara}\n" .
            "• Cidade: {cidade}\n" .
            "• Tipo de Serviço: {tipo_servico}\n" .
            "{responsaveis}\n" .
            "🚨 *Obrigatório:*\n" .
            "• Confirme o recebimento dos documentos, orientações e link (se virtual) no sistema.\n" .
            "• Confirme o local, a manutenção do ato antes do deslocamento e chegue com antecedência.\n" .
            "• Ausência de advogado/preposto: ligar imediatamente para evitar revelia/confissão.\n" .
            "• Não dispensar testemunhas/depoimentos sem autorização e registrar protestos em ata.\n" .
            "• *CHECK-IN NO LOCAL OBRIGATÓRIO* para liberação do pagamento.\n\n" .
            "📲 Ao chegar no local, realize o check-in no link abaixo:\n" .
            "{link_checkin}\n\n" .
            "Urgências, ligue: (48) 99130-8024",


        // Lembrete PRÉ-diligência enviado ao correspondente um dia antes da audiência.
        // Placeholders: {correspondente}, {processo}, {autor}, {reu}, {vara}, {cidade}, {tipo_servico},
        //               {data}, {hora_audiencia}, {responsaveis}, {link_confirmacao_audiencia}
        'lembrete_prediligencia' =>
            "Olá, {correspondente}! Tudo bem?\n\n" .
            "Gostaríamos de confirmar se está tudo certo para a realização da(s) audiência(s)/diligência(s) de amanhã:\n\n" .
            "📋 Processo: {processo}\n" .
            "👤 Autor: {autor}\n" .
            "👤 Réu: {reu}\n" .
            "🏛️ Vara: {vara}\n" .
            "📍 Cidade: {cidade}\n" .
            "🔧 Tipo de Serviço: {tipo_servico}\n" .
            "📅 Data: {data} às {hora_audiencia}\n" .
            "{responsaveis}\n" .
            "Todos os documentos e orientações serão anexados em nosso sistema. Assim que receberem, pedimos que confirmem o recebimento AINDA HOJE pelo botão localizado abaixo dos documentos: \"CONFIRMA O RECEBIMENTO DOS DOCUMENTOS E A REALIZAÇÃO DO ATO\".\n\n" .
            "Caso as orientações ainda não estejam disponíveis, é porque ainda não as recebemos do cliente. Assim que forem encaminhadas ao DMK, disponibilizaremos imediatamente no sistema.\n\n" .
            "🚨 Reforçamos que é OBRIGATÓRIO:\n" .
            "* Confirmar o recebimento dos documentos e a realização do ato no sistema;\n" .
            "* Confirmar presença no link abaixo ainda hoje;\n" .
            "* Realizar o CHECK-IN amanhã, no local do ato, pelo link que será enviado antes da audiência/diligência.\n\n" .
            "📲 CONFIRME SUA AUDIÊNCIA/DILIGÊNCIA AQUI AGORA:\n" .
            "{link_confirmacao_audiencia}\n\n" .
            "Em caso de dúvidas, utilize o WhatsApp. Para urgências, não aguarde mensagem: LIGUE imediatamente para (48) 99130-8024.\n\n" .
            "DMK Advogados",

    ],

];
