@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Agenda</li>
        <li>Todos os Contatos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-book"></i> Agenda <span> > Todos os Contatos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('contato/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well center">
                @foreach(range('A', 'Z') as $letra) 
                    <a class="btn btn-default btn-xs" href="{{ url('contato/buscar/'.$letra) }}">{{ $letra }}</a>
                @endforeach
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-book"></i> </span>
                    <h2>Contatos</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                       @foreach($contatos as $contato)
                            <pre>{{ $contato }}</pre>
                       @endforeach
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection