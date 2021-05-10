@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Acessos </span> <span>> {{ $cliente->nm_razao_social_cli }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                
                <div class="well">
                   
                </div>
            </article>
        </div>
    </div>
</div>
@endsection