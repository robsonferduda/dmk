@extends('layouts.register')
@section('content')
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-desktop"></i> Mensagem do Sistema
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="well center">
                @if(Session::get('retorno')['tipo'] == 'erro')
                    
                    <h1>
                        <small class="text-danger slideInRight fast animated"><strong><i class="fa-fw fa fa-warning"></i> Ocorreu um erro ao processar sua requisição</strong></small>
                    </h1>
                    <h4>{{ Session::get('retorno')['msg'] }}</h4>  

                @elseif(Session::get('retorno')['tipo'] == 'sucesso')

                    <h1>
                        <small class="text-success slideInRight fast animated"><strong><i class="fa-fw fa fa-check"></i> Sua requisição foi realizada com sucesso</strong></small>
                    </h1>
                    <h4>{{ Session::get('retorno')['msg'] }}</h4> 

                @elseif(Session::get('retorno')['tipo'] == 'alerta')

                    <h1>
                        <small class="text-warning slideInRight fast animated"><strong><i class="fa-fw fa fa-check"></i> Processo em Andamento</strong></small>
                    </h1>
                    <h4>{{ Session::get('retorno')['msg'] }}</h4> 

                @elseif(Session::get('retorno')['tipo'] == 'token')

                    <h1>
                        <small class="text-warning slideInRight fast animated"><strong><i class="fa-fw fa fa-check"></i>URL de Confirmação Inválida</strong></small>
                    </h1>
                    <h4>{{ Session::get('retorno')['msg'] }}</h4> 

                @endif                   
            </div>   
        </div>
        <div class="center" style="text-align: center;">
            <a href="{{ url(route('seleciona.perfil', ['nivel_url' => \Crypt::encrypt(3)], false)) }}" class="btn btn-success"><i class="fa fa-sign-in"></i> LOGIN CORRESPONDENTE</a> 
        </div>
    </div>
</div>
@endsection