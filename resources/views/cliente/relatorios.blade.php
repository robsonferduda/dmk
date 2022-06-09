@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Relatórios </span> 
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Relatório de Clientes </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-add-cliente', 'url' => 'cliente/relatorios', 'class' => 'smart-form']) !!}
                            <section>
                                <header>
                                    <i class="fa fa-file-excel-o"></i> Relatório de Clientes
                                </header>
                                <fieldset>
                                    <section>
                                        <div class="row">
                                            <div class="col col-12 marginBottom10">
                                                <label class="checkbox">
                                                    <input type="checkbox" name="fl_ativo_cli">
                                                    <i></i>Somente Clientes Ativos 
                                                </label>
                                            </div> 
                                        </div>
                                        <label class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> Selecione os campos que deseja no relatório</label>
                                            <div class="row">
                                                <div class="col col-12">
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="campos[]" value="nu_cliente_cli">
                                                        <i></i>Código 
                                                    </label>
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="campos[]" value="nm_razao_social_cli">
                                                        <i></i>Nome 
                                                    </label>
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="campos[]" value="email">
                                                        <i></i>Email 
                                                    </label>
                                                    <label class="checkbox">
                                                        <input type="checkbox" name="campos[]" value="fone">
                                                        <i></i>Telefone 
                                                    </label>
                                                </div> 
                                            </div>
                                    </section>
                                </fieldset>
                            </section>
                            <footer>
                                <button type="submit" class="btn btn-success" id="btnSaveDespesas"><i class="fa fa-file-excel-o"></i> Gerar Relatório</button>
                                <button type="button" class="btn btn-danger" onclick="window.history.back();"><i class="fa fa-times"></i> Cancelar</button>
                            </footer>
                        {!! Form::close() !!}  
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection