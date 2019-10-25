@extends('layouts.logado')
@section('content')
    <div style="padding: 15px 10px;">
        <div class="well">
            <h1>
                Olá <span class="semi-bold text-primary">{{ Auth::user()->name }}</span>, você está logado com perfil <span class="semi-bold text-primary">{{ Auth::user()->nivel->dc_nome_padronizado_niv }}</span><br>
            </h1>
            <h5>Você possui <strong>{{ count($usuarios) }}</strong> perfis de usuário no nosso sistema. Escolha abaixo com qual deseja acessar.</h5>
            <h5>Lembramos que você pode alterar o perfil de acesso a qualquer momento, utilizando a opção "<strong>Alterar Perfil</strong>"</h5>
        </div>    
        @foreach($usuarios as $user)

            <div class="well">
                <form class="smart-form client-form" method="POST" action="{{ route('selecionar-nivel') }}">
                {{ csrf_field() }}
                    <h4>
                        <input type="hidden" name="cd_nivel_niv" value="{{ $user->cd_nivel_niv }}">
                        <span class="semi-bold">{{ $user->name }} / {{ $user->nivel->dc_nome_padronizado_niv }}  </span>
                        <button style="padding: 5px 7px" class="btn btn-success pull-right" type="submit"><i class="fa fa-check"></i> Selecionar</button>
                    </h4>
                </form>
            </div>

        @endforeach
        <hr/>
        <div style="text-align: center;">
            <a class="btn btn-primary" href="{{ url('home') }}"><i class="fa fa-home"></i> Continuar Navegação</a>
        </div>
    </div>
@endsection