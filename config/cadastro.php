<?php

/*
 * Campanha de atualização cadastral dos correspondentes (e-mail em lotes).
 */
return [

    // Teto de e-mails da campanha por dia, somando todas as campanhas (o SMTP é compartilhado).
    'limite_diario' => (int) env('CADASTRO_LIMITE_DIARIO', 200),

    // E-mails por execução do agendador (roda a cada 10 minutos, das 8h às 18h, em dias úteis).
    'lote' => (int) env('CADASTRO_LOTE', 5),

    // Pausa entre um e-mail e outro dentro do lote, em segundos.
    'intervalo_segundos' => (int) env('CADASTRO_INTERVALO', 3),

    'tentativas' => 3,

    'remetente_nome' => env('CADASTRO_REMETENTE_NOME', 'DMK Advogados'),

    'responder_para' => [
        'email' => env('CADASTRO_RESPONDER_PARA', 'contato@dmkadvogados.com.br'),
        'nome'  => env('CADASTRO_REMETENTE_NOME', 'DMK Advogados'),
    ],

    // Endereço público do sistema usado nos links do e-mail (o APP_URL local não serve para o destinatário).
    'url_publica' => rtrim(env('CADASTRO_URL_PUBLICA', 'https://sistema.lawyerexpress.com.br'), '/'),
];
