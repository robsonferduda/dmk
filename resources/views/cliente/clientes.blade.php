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
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label>
                <form action="{{ url('cliente/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Razão Social</span>
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
                                    <th style="width: 25%;">Razão Social</th>                             
                                    <th style="width: 15%;">Nome Fantasia</th>                                    
                                    <th style="width: 8%;" class="center">Tipo de Pessoa</th>
                                    <th style="width: 6%;" class="center">Nota Fiscal</th>
                                    <th style="width: 6%;" class="center">Situação</th>                                   
                                    <th style="width: 10%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td data-id="{{ $cliente->cd_cliente_cli }}">{{ $cliente->nu_cliente_cli }}</td>
                                        <td>{{ $cliente->nm_razao_social_cli }}</td>
                                        <td>{{ $cliente->nm_fantasia_cli }}</td>                                        
                                        <td class="center">{{ ($cliente->tipoPessoa) ? $cliente->tipoPessoa->nm_tipo_pessoa_tpp : 'Não informado' }}</td>
                                        <td class="center">{!! ($cliente->fl_nota_fiscal_cli == "S") ? '<span class="label label-success">SIM</span>' : '<span class="label label-danger">NÃO</span>' !!}</td>
                                        <td class="center">{!! ($cliente->fl_ativo_cli == "S") ? '<span class="label label-success">ATIVO</span>' : '<span class="label label-danger">INATIVO</span>' !!}</td>
                                        <td class="center">
                                            <a class="btn btn-default btn-xs" href="{{ url('cliente/detalhes/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-file-text-o"></i></a>
                                            <a class="btn btn-primary btn-xs" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-warning btn-xs" href="{{ url('cliente/honorarios/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-money"></i></a>                                            
                                            <button data-url="clientes/" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i></button>
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