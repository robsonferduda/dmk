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
            <label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label>
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
                                    <th style="">Código</th>   
                                    <th style="">Razão Social</th>                             
                                    <th style="">Nome Fantasia</th>                                    
                                    <th style="" class="center">Tipo de Pessoa</th>
                                    <th style="" class="center">Usuário</th>
                                    <th style="" class="center">Nota Fiscal</th>
                                    <th style="" class="center">Situação</th>                                   
                                    <th style="width:100px;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td data-id="{{ $cliente->cd_cliente_cli }}">{{ $cliente->nu_cliente_cli }}</td>
                                        <td>{{ $cliente->nm_razao_social_cli }}</td>
                                        <td>{{ $cliente->nm_fantasia_cli }}</td>                                        
                                        <td class="center">{{ ($cliente->tipoPessoa) ? $cliente->tipoPessoa->nm_tipo_pessoa_tpp : 'Não informado' }}</td>
                                        <td>
                                            {!! ($cliente->entidade->usuario) ? $cliente->entidade->usuario->email : '<span class="text-danger">Nenhum usuário cadastrado</span>' !!}
                                        </td>
                                        <td class="center">{!! ($cliente->fl_nota_fiscal_cli == "S") ? '<span class="label label-success">SIM</span>' : '<span class="label label-danger">NÃO</span>' !!}</td>
                                        <td class="center">{!! ($cliente->fl_ativo_cli == "S") ? '<span class="label label-success">ATIVO</span>' : '<span class="label label-danger">INATIVO</span>' !!}</td>
                                        <td class="center">
                                            <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('cliente/detalhes/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-file-text-o"></i></a>
                                            <a title="Editar" class="btn btn-primary btn-xs" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-edit"></i></a>                                 
                                            <div class="dropdown" style="display: inline;">
                                                <a href="javascript:void(0);" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a title="Honorários" href="{{ url('cliente/acessos/'.\Crypt::encrypt($cliente->cd_cliente_cli)) }}"><i class="fa fa-lock"></i> Acessos</a></li>
                                                    <li><a title="Contatos" href="{{ url('cliente/contatos/'.$cliente->cd_entidade_ete) }}"><i class="fa fa-book"></i> Contatos</a></li>
                                                    <li><a title="Honorários" href="{{ url('cliente/honorarios/'.$cliente->cd_cliente_cli) }}"><i class="fa fa-money"></i> Honorários</a></li>                                                    
                                                    <li><a title="Excluir" data-id="{{ $cliente->cd_cliente_cli }}" class="remover_cliente"><i class="fa fa-trash"></i> Excluir</a></li>
                                                </ul>
                                            </div>
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
 <div class="modal fade modal_top_alto" id="modal_excluir_cliente" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> <strong> Excluir Cliente</strong></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 center">
                                <form id="frm_excluir_cliente" class="form-inline" action="{{ url('clientes') }}" method="POST">
                                    {!! method_field('DELETE') !!}
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <h4>Essa operação irá excluir o registro definitivamente.</h4>
                                    <h4>Deseja continuar?</h4>
                                
                                    <div class="center marginTop20">
                                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection