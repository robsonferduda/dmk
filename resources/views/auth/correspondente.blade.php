@extends('layouts.v2-guest')

@section('title', 'Acesso do correspondente')

@section('content')
<div class="card dmk-auth-card">
    <div class="card-body">
        <h1 class="dmk-auth-title">Acesso do correspondente</h1>
        <p class="dmk-auth-subtitle">Entre com o e-mail e a senha cadastrados.</p>

        @if (session('status'))
            <div class="alert alert-info d-flex align-items-start gap-2 small">
                <i class="bi bi-info-circle mt-1"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('autenticacao') }}" novalidate>
            {{ csrf_field() }}
            <input type="hidden" name="nivel" value="{{ \App\Enums\Nivel::CORRESPONDENTE }}">

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           autocomplete="username" required autofocus>
                </div>
            </div>

            <div class="mb-2">
                <label for="password" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           autocomplete="current-password" required>
                </div>
            </div>

            <div class="text-end mb-3">
                <a href="{{ url('password/reset') }}" class="small">Esqueceu sua senha?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
            </button>
        </form>
    </div>
</div>
@endsection
