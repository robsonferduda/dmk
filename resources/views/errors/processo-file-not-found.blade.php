@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente/processos') }}">Processos</a></li>
        <li><a href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($anexo->cd_processo_pro)) }}">Acompanhamento</a></li>
        <li>Anexos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Acompanhamento </span> <span>> Anexos</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger fade in">
                <i class="fa-fw fa fa-times"></i>
                <strong>Erro do Sistema</strong> Erro ao acessar o anexo
            </div>
            <div class="well" style="padding: 3px 15px;">
                <h5><strong>Processo</strong>: {{ $processo->nu_processo_pro }}</h5>  
                <h5><strong>Detalhes do arquivo</strong>: {{ $anexo->nm_anexo_processo_apr }}</h5> 
                <hr/>
                <h5 class="center">
                    <a class="btn btn-primary btn-xs" href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($anexo->cd_processo_pro)) }}"><i class="fa fa-arrow-circle-left"></i> Voltar </a>
                </h5>
            </div> 
        </div>
    </div>
</div>
@endsection