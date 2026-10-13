@extends('layouts.v2-guest')

@section('title', 'Link inválido')

@section('content')
<div class="card dmk-auth-card">
    <div class="card-body text-center">
        <div class="display-5 text-warning mb-2"><i class="bi bi-link-45deg"></i></div>
        <h1 class="dmk-auth-title">Link inválido</h1>
        <p class="dmk-auth-subtitle">
            Não encontramos o formulário deste endereço. Confira se o link foi copiado inteiro do e-mail
            ou escreva para <a href="mailto:{{ config('cadastro.responder_para.email') }}">{{ config('cadastro.responder_para.email') }}</a>.
        </p>
    </div>
</div>
@endsection
