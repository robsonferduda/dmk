<?php

return [

    'url' => env('AUTENTIQUE_URL', 'https://api.autentique.com.br/v2/graphql'),

    'token' => env('AUTENTIQUE_TOKEN'),

    // Documentos em sandbox não consomem créditos e são apagados pela Autentique após alguns dias.
    'sandbox' => env('AUTENTIQUE_SANDBOX', true),

    // Segredo do endpoint cadastrado no painel (Desenvolvedor > Webhooks), usado no HMAC do cabeçalho X-Autentique-Signature.
    'webhook_secret' => env('AUTENTIQUE_SECRET'),

    // O modelo do contrato é o da DMK; apenas estes escritórios (cd_conta_con) podem enviar para assinatura.
    'contas' => array_filter(array_map('intval', explode(',', (string) env('AUTENTIQUE_CONTAS', '64')))),

    // Conta dona do token: entra como signatária e assina automaticamente via signDocument.
    'contratante' => [
        'nome'  => env('AUTENTIQUE_CONTRATANTE_NOME', 'DMK Advogados'),
        'email' => env('AUTENTIQUE_CONTRATANTE_EMAIL'),
    ],

    'testemunhas' => [
        [
            'nome'  => env('AUTENTIQUE_TESTEMUNHA1_NOME'),
            'email' => env('AUTENTIQUE_TESTEMUNHA1_EMAIL'),
            'cpf'   => env('AUTENTIQUE_TESTEMUNHA1_CPF'),
            'rg'    => env('AUTENTIQUE_TESTEMUNHA1_RG'),
        ],
        [
            'nome'  => env('AUTENTIQUE_TESTEMUNHA2_NOME'),
            'email' => env('AUTENTIQUE_TESTEMUNHA2_EMAIL'),
            'cpf'   => env('AUTENTIQUE_TESTEMUNHA2_CPF'),
            'rg'    => env('AUTENTIQUE_TESTEMUNHA2_RG'),
        ],
    ],

    'mensagem' => env('AUTENTIQUE_MENSAGEM', 'Segue o contrato de prestação de serviços de advocacia em regime de correspondência da DMK Advogados para assinatura eletrônica.'),

    'timeout' => (int) env('AUTENTIQUE_TIMEOUT', 60),
];
