@extends('layouts.admin')
@section('content')
@php
    $nivelClasse = [
        'success' => 'label-success',
        'error'   => 'label-danger',
        'warning' => 'label-warning',
        'info'    => 'label-info',
        'config'  => 'label-primary',
        'debug'   => 'label-default',
    ];
@endphp
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Diagnóstico de E-mail</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-envelope"></i> Configurações <span> > Diagnóstico de E-mail</span>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <article class="col-sm-12" style="padding:0;">
                <div class="jarviswidget">
                    <header>
                        <span class="widget-icon"><i class="fa fa-cog"></i></span>
                        <h2>Configuração atual</h2>
                    </header>
                    <div class="widget-body">
                        <table class="table table-bordered table-condensed" style="margin:0;">
                            <tr><th style="width:40%">Driver</th><td><code>{{ $config['driver'] }}</code></td></tr>
                            <tr><th>Host</th><td><code>{{ $config['host'] }}</code></td></tr>
                            <tr><th>Porta</th><td><code>{{ $config['port'] }}</code></td></tr>
                            <tr><th>Criptografia</th><td><code>{{ $config['encryption'] ?: '(nenhuma)' }}</code></td></tr>
                            <tr><th>Remetente</th><td>{{ $config['from_name'] }} &lt;{{ $config['from_address'] }}&gt;</td></tr>
                            <tr><th>Usuário SMTP</th><td>{{ $config['username_masked'] }}</td></tr>
                            <tr><th>Senha SMTP</th><td>{{ $config['password_masked'] }}</td></tr>
                            <tr><th>Ambiente</th><td>{{ $config['app_env'] }}</td></tr>
                            <tr><th>APP_URL</th><td><code>{{ $config['app_url'] }}</code></td></tr>
                            <tr><th>Fila (QUEUE)</th><td><code>{{ $config['queue'] }}</code></td></tr>
                            <tr><th>Config cacheada</th><td>{{ $config['config_cacheada'] ? 'Sim — rode config:clear' : 'Não' }}</td></tr>
                        </table>
                        <p class="text-muted" style="margin:10px 0 0;font-size:12px;">
                            <i class="fa fa-info-circle"></i>
                            Credenciais lidas do <code>.env</code>. Alterações exigem editar o arquivo e limpar cache de config (<code>php artisan config:clear</code>).
                        </p>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-md-6">
            <article class="col-sm-12" style="padding:0;">
                <div class="jarviswidget">
                    <header>
                        <span class="widget-icon"><i class="fa fa-paper-plane"></i></span>
                        <h2>Enviar e-mail de teste</h2>
                    </header>
                    <div class="widget-body">
                        <form method="POST" action="{{ url('configuracoes/email-diagnostico') }}" class="smart-form">
                            @csrf
                            <section>
                                <label class="label">E-mail de destino</label>
                                <label class="input">
                                    <input type="email" name="email_teste" placeholder="seu@email.com (opcional)"
                                           value="{{ old('email_teste', auth()->user()->email) }}">
                                </label>
                            </section>
                            <section>
                                <label class="label">Tipo de teste</label>
                                <label class="select">
                                    <select name="tipo_teste">
                                        <option value="simples" {{ old('tipo_teste', 'simples') === 'simples' ? 'selected' : '' }}>
                                            SMTP com transcript completo (recomendado)
                                        </option>
                                        <option value="laravel_mail" {{ old('tipo_teste') === 'laravel_mail' ? 'selected' : '' }}>
                                            Mail::raw (mesmo caminho das notificações)
                                        </option>
                                        <option value="recuperacao_senha" {{ old('tipo_teste') === 'recuperacao_senha' ? 'selected' : '' }}>
                                            Simular template recuperação de senha
                                        </option>
                                        <option value="recuperacao_senha_real" {{ old('tipo_teste') === 'recuperacao_senha_real' ? 'selected' : '' }}>
                                            Recuperação de senha REAL (Password broker)
                                        </option>
                                    </select>
                                    <i></i>
                                </label>
                            </section>
                            <footer>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-play"></i> Executar diagnóstico
                                </button>
                            </footer>
                        </form>
                        <p class="text-warning" style="margin-top:12px;font-size:12px;">
                            <i class="fa fa-warning"></i>
                            O diagnóstico também pode ser executado sem envio — deixe o campo vazio e clique em executar (remova o required se quiser só checar conexão). Por padrão, informe um e-mail para teste completo.
                        </p>
                    </div>
                </div>
            </article>
        </div>
    </div>

    @if($resultado)
    <div class="row">
        <div class="col-md-12">
            <article class="col-sm-12" style="padding:0;">
                <div class="jarviswidget">
                    <header>
                        <span class="widget-icon"><i class="fa fa-list-alt"></i></span>
                        <h2>
                            Log do diagnóstico
                            @if($resultado['sucesso'])
                                <span class="label label-success" style="margin-left:8px;">Envio OK</span>
                            @elseif($resultado['erro'])
                                <span class="label label-danger" style="margin-left:8px;">Falha</span>
                            @endif
                        </h2>
                    </header>
                    <div class="widget-body" style="padding:0;">
                        @if($resultado['erro'])
                        <div class="alert alert-danger" style="margin:15px;">
                            <strong>Erro principal:</strong> {{ $resultado['erro'] }}
                        </div>
                        @elseif($resultado['sucesso'])
                        <div class="alert alert-warning" style="margin:15px;">
                            <strong>PHP não lançou exceção</strong> — o SMTP aceitou a mensagem. Isso <em>não garante</em> entrega na caixa de entrada.
                            Verifique spam e o transcript SMTP abaixo. Compare também <code>storage/logs/mail.log</code>.
                        </div>
                        @endif

                        <div style="max-height:520px;overflow:auto;padding:15px;background:#1e1e1e;color:#d4d4d4;font-family:monospace;font-size:12px;line-height:1.5;">
@foreach($resultado['log'] as $entrada)
<span style="color:#888;">[{{ $entrada['hora'] }}]</span>
<span class="{{ $nivelClasse[$entrada['nivel']] ?? 'label-default' }}" style="display:inline-block;padding:1px 6px;border-radius:3px;font-size:10px;color:#fff;">{{ strtoupper($entrada['nivel']) }}</span>
<strong style="color:#fff;"> {{ $entrada['titulo'] }}</strong>
@if($entrada['detalhe'])
@php $detalhe = is_array($entrada['detalhe']) ? json_encode($entrada['detalhe'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $entrada['detalhe']; @endphp
<pre style="margin:4px 0 12px 20px;color:#9cdcfe;white-space:pre-wrap;word-break:break-word;background:transparent;border:0;padding:0;">{{ $detalhe }}</pre>
@else
<br>
@endif
@endforeach
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="well well-sm">
                <h5 style="margin-top:0;"><i class="fa fa-lightbulb-o"></i> Causas comuns</h5>
                <ul style="margin-bottom:0;font-size:13px;">
                    <li><strong>Gmail 535 / BadCredentials:</strong> senha de app revogada ou conta com 2FA sem app password.</li>
                    <li><strong>Notificações sem log:</strong> recuperação de senha não usa <code>log_notificacao</code> — falha só aparece no log do Laravel ou nesta página.</li>
                    <li><strong>Notificações com log mas sem e-mail:</strong> <code>LogNotificacao</code> é gravado antes do envio; SMTP pode falhar depois.</li>
                    <li><strong>Jobs na fila:</strong> com <code>QUEUE_CONNECTION=redis</code>, e-mails via Job exigem <code>php artisan queue:work</code> rodando.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
