@component('mail::message')
# Orientações e documentos disponíveis

As orientações e documentos do processo **{{ $processo->nu_processo_pro }}** foram disponibilizados no sistema para seu aceite.

@if ($temAnexos)
**Os documentos do processo seguem em anexo neste e-mail (arquivo .zip).** Verifique o anexo antes de prosseguir.
@endif

Utilize os botões abaixo para confirmar o recebimento dos documentos e a realização do ato contratado.

@if (!empty($processo->ds_link_dados_pro))
@component('mail::button', ['url' => url($processo->ds_link_dados_pro), 'color' => 'green'])
Acessar Link Externo de Documentos
@endcomponent
@endif

@component('mail::button', ['url' => $urlProcesso, 'color' => 'blue'])
Ver Processo no Sistema
@endcomponent

Acesse o sistema para verificar os processos.

@lang('Saudações'),<br>{{ config('app.name') }}

@component('mail::subcopy')
Se você estiver com problemas para clicar nos botões, copie e cole o endereço abaixo em outra janela do seu navegador:

[{{ $urlProcesso }}]({!! $urlProcesso !!})
@endcomponent

<strong style="color: red; font-size:11px; text-align: center;">@lang("Esta é uma mensagem automática, favor não responder este e-mail.")</strong>
@endcomponent
