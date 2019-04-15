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
            <a data-toggle="modal" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
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
                        <h2>Honorários por Tipo de Serviço</h2>             
                    </header>
                    <div class="col-sm-12">

                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">  
                                            <form action="{{ url('cliente/buscar-honorarios') }}" class="form-inline" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Grupos de Cidades</span>
                                                     <select name="grupo_cidade" id="grupo_cidade" class="form-control">
                                                        <option value="0">Selecione um grupo</option>
                                                        @foreach($grupos as $grupo)
                                                            <option value="{{ $grupo->cd_grupo_cidade_grc }}">{{ $grupo->nm_grupo_cidade_grc }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-addon">Cidade</span>
                                                     <select name="cidade_honorario" id="cidade_honorario" class="form-control">
                                                        <option value="">Selecione uma cidade</option>
                                                        <option value="0">Todas</option>
                                                    </select>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-addon">Tipos de Serviço</span>
                                                     <select name="servico" class="form-control">
                                                        <option value="">Selecione um servico</option>
                                                        <option value="0">Todos</option>
                                                        @foreach($servicos as $servico)
                                                            <option value="{{ $servico->cd_tipo_servico_tse }}">{{ $servico->nm_tipo_servico_tse }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="input-group">
                                                    <span class="input-group-addon">Organizar Por</span>
                                                     <select name="organizar" class="form-control">
                                                        <option value="0">Selecione uma opção</option>
                                                        <option value="1">Tipos de Serviço</option>
                                                        <option value="2">Cidades</option>
                                                    </select>
                                                </div>
                                                
                                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        @if(isset($cidades))
                                            <div class="col-md-6"> 
                                                <h5>Honorários por Tipo de Serviço</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorarios" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>
                                            </div> 
                                            
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades as $cidade)
                                                                        <th>{{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos as $servico)
                                                                    <tr>
                                                                        <td>{{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                    <div class="input-group">
                                                                                        <span class="input-group-addon">$</span>
                                                                                        <input type="text" class="form-control taxa-honorario" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" value="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">
                                                                                        <div class="input-group-btn">
                                                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false">
                                                                                                <span class="caret"></span>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                                                <li><a href="javascript:void(0);">Aplicar valor em toda linha</a></li>
                                                                                                <li><a href="javascript:void(0);">Aplicar valor em toda coluna</a></li>
                                                                                                <li><a href="javascript:void(0);">Aplicar em toda tabela</a></li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cidade</th>
                                                                    @foreach($lista_servicos as $servico)
                                                                        <th>{{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades as $cidade)
                                                                    <tr>
                                                                        <td>{{ $cidade->nm_cidade_cde  }}</td>
                                                                        @foreach($lista_servicos as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                    <div class="input-group">
                                                                                        <span class="input-group-addon">$</span>
                                                                                        <input type="text" class="form-control taxa-honorario" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" value="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">
                                                                                        <div class="input-group-btn">
                                                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false">
                                                                                                <span class="caret"></span>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                                                <li><a href="javascript:void(0);">Aplicar valor em toda linha</a></li>
                                                                                                <li><a href="javascript:void(0);">Aplicar valor em toda coluna</a></li>
                                                                                                <li><a href="javascript:void(0);">Aplicar em toda tabela</a></li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            @endif
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