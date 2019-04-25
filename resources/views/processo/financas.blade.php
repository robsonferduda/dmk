@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Honorários por Tipo de Serviço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Honorários por Tipo de Serviço </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('cliente/editar/') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                        <h2>Despesas</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        
                                            <div class="col-md-6"> 
                                                <h5>Lista de Despesas</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorarios" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>

                                                <a href="{{ url('cliente/limpar-selecao/') }}" class="btn btn-warning pull-right header-btn" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Seleção</a>
                                            </div>                                                                                             
                                            <div class="tabelah">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <th>Despesas</th>
                                                        <th>Cliente</th>
                                                        <th>Correspondente</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($despesas as $despesa)
                                                            <tr>
                                                                <td>{{ $despesa->nm_tipo_despesa_tds }}</td>
                                                                <td>
                                                                    <div class="col-md-3">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-honorario" data-cidade="" data-servico="" value="">
                                                                        </div>                                                                               
                                                                    </div>     
                                                                     <div class="onoffswitch-container col-md-4">
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" class="onoffswitch-checkbox" name="fl_nota_fiscal_cli" value="S" id="fl_nota_fiscal_cli">
                                                                            <label class="onoffswitch-label" for="fl_nota_fiscal_cli"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                    <span class="onoffswitch-switch"></span>
                                                                            </label> 
                                                                        </span> 
                                                                    </div>                                                                        
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-3">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-honorario" data-cidade="" data-servico="" value="">
                                                                        </div>
                                                                    </div>
                                                                     <div class="onoffswitch-container col-md-4">
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" class="onoffswitch-checkbox" name="fl_nota_fiscal_cli" value="S" id="fl_nota_fiscal_cli">
                                                                            <label class="onoffswitch-label" for="fl_nota_fiscal_cli"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                <span class="onoffswitch-switch"></span>
                                                                            </label> 
                                                                        </span> 
                                                                    </div>         
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
