@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Importar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-6 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Processos <span> > Importar </span>
            </h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 box-button-xs">
            <div class="boxBtnTopo sub-box-button-xs">
                <a href="{{ url('cliente/processos/importar?download=1') }}" class="btn btn-default pull-right">
                    <i class="fa fa-file-excel-o fa-lg"></i><span> Baixar Planilha Modelo</span>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <p class="text-muted">
                    <i class="fa fa-info-circle"></i>
                    Baixe a planilha modelo com seus dados já preenchidos, preencha os processos e envie o arquivo abaixo.
                </p>
                <div class="row">
                    {{ Form::open(['url' => 'cliente/processos/importar', 'method' => 'post', 'id' => 'form-importar-processos', 'enctype' => 'multipart/form-data']) }}
                        <section class="col col-xs-12 col-md-5 smart-form">
                            <div class="input input-file">
                                <span class="button">
                                    <input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Procurar Arquivo
                                </span>
                                <input type="text" placeholder="Arquivo" readonly="">
                            </div>
                        </section>
                        <section class="col col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success btn-importar">
                                <i class="fa fa-file-excel-o fa-lg"></i><span> Importar Planilha</span>
                            </button>
                        </section>
                    {{ Form::close() }}
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
