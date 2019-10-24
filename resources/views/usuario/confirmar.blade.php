@extends('layouts.logado')
@section('content')
    <div style="padding: 15px 10px;">
        <div class="well">
            <h1>
                Só mais um passo...<br>
            </h1>
            <p>Por questões de segurança, pedimos que informe a senha cadastrada para o usário informado e sua conta de </p>
        </div>    
        <div class="well">
            <h4>
                <span class="semi-bold">{{ $user->name }} / {{ $user->nivel->dc_nome_padronizado_niv }}  </span>
                <a class="btn btn-primary pull-right" href="javascript:void(0);"><i class="fa fa-check"></i> Selecionar</a>
            </h4>
        </div>
    </div>
@endsection