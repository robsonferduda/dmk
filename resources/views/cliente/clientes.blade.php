@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Clientes</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span> > Listar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-file-pdf-o fa-lg"></i> Exportar como PDF</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-file-excel-o fa-lg"></i> Exportar como Planilha</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <i class="fa-fw fa fa-info-circle"></i>
                <strong>Informação!</strong> O sistema exibe os últimos clientes cadastrados. Para efetuar uma busca, utilize as opções abaixo.
            </div>
            <div class="well">
                <form action="{{ url('cliente/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Nome</span>
                        <input type="text" name="nome" class="form-control" id="Nome" placeholder="Nome">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">CPF/CNPJ</span>
                        <input type="text" name="identificacao" class="form-control" id="Nome" placeholder="CPF/CNPJ">
                    </div>
                    <div class="form-group">
                        <select name="tipo_pessoa" class="form-control">
                            <option value="0">Tipo de Pessoa</option>
                            @foreach(\App\TipoPessoa::all() as $tipo)
                                <option value="{{ $tipo->cd_tipo_pessoa_tpp }}">{{ $tipo->nm_tipo_pessoa_tpp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="situacao" class="form-control">
                            <option value="0">Situação</option>
                            <option value="S">Ativo</option>
                            <option value="N">Inativo</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                </form>
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Clientes</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if(isset($clientes))
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>    
                                    <th style="width: 5%;">Código</th>                                
                                    <th style="width: 20%;">Nome Fantasia</th>
                                    <th style="width: 15%;">Razão Social</th>
                                    <th style="width: 8%;" class="center">Tipo de Pessoa</th>
                                    <th style="width: 6%;" class="center">Nota Fiscal</th>
                                    <th style="width: 6%;" class="center">Situação</th>                                   
                                    <th style="width: 25%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td data-id="{{ $cliente->cd_cliente_cli }}">{{ $cliente->nu_cliente_cli }}</td>
                                        <td>{{ $cliente->nm_fantasia_cli }}</td>
                                        <td>{{ $cliente->nm_razao_social_cli }}</td>
                                        <td class="center">{{ ($cliente->tipoPessoa) ? $cliente->tipoPessoa->nm_tipo_pessoa_tpp : 'Não informado' }}</td>
                                        <td class="center">{!! ($cliente->fl_nota_fiscal_cli == "S") ? '<span class="label label-success">SIM</span>' : '<span class="label label-danger">NÃO</span>' !!}</td>
                                        <td class="center">{!! ($cliente->fl_ativo_cli == "S") ? '<span class="label label-success">ATIVO</span>' : '<span class="label label-danger">INATIVO</span>' !!}</td>
                                        <td class="center">
                                            <a class="btn btn-default btn-xs" style="width: 23%;" href="{{ url('cliente/detalhes/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-folder"></i> Detalhes</a>
                                            <a class="btn btn-warning btn-xs" style="width: 23%;" href="{{ url('cliente/honorarios/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-money"></i> Honorários</a>
                                            <a class="btn btn-primary btn-xs" style="width: 23%;" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-edit"></i> Editar</a>
                                            <button data-url="clientes/" class="btn btn-danger btn-xs excluir_registro" style="width: 23%;" href=""><i class="fa fa-trash"></i> Excluir</button>
                                        </td>
                                    </tr>
                                @endforeach                                
                            </tbody>
                        </table>
                        @else
                            <h5 class="center marginTop20"><i class="fa fa-info-circle"></i> Selecione os termos da sua busca e clique em <strong>Buscar</strong></h5>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection