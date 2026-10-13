@extends('layouts.v2-guest')

@section('title', 'Cadastro atualizado')

@section('content')
<div class="card dmk-auth-card">
    <div class="card-body text-center">
        <div class="display-5 text-success mb-2"><i class="bi bi-check-circle"></i></div>
        <h1 class="dmk-auth-title">Dados recebidos!</h1>
        <p class="dmk-auth-subtitle">Obrigado, {{ \App\Mail\AtualizacaoCadastroMail::nomeFormatado($nome) }}. Seu cadastro foi atualizado.</p>

        <p class="small text-muted">
            Com esses dados, o DMK vai preparar o contrato de prestação de serviços e enviá-lo para assinatura eletrônica.
            Se precisar corrigir algo, use o mesmo link do e-mail.
        </p>

        <a href="{{ url('atualizar-dados/' . $chave) }}" class="btn btn-outline-primary w-100">
            <i class="bi bi-pencil me-1"></i> Revisar meus dados
        </a>
    </div>
</div>
@endsection
