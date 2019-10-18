@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Erro</li>
    </ol>
</div>
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
            <div class="well">
                <h1>
                    <small class="text-danger slideInRight fast animated"><strong><i class="fa-fw fa fa-warning"></i> Ocorreu um erro ao processar sua requisição</strong></small>
                </h1>
                <h4>Entre em contato com nosso suporte para resolver seu problema</h4>
                <h5><strong>Endereço da requisição: </strong>{{ ($request->url()) ? $request->url() : 'URL Não identificada' }}</h5>
                @if($erro->errorInfo)
                    <h5><strong>Código do erro: </strong>{{ $erro->errorInfo[0] }}</h5>
                @endif                        
            </div>    
        </div>
    </div>
</div>
@endsection