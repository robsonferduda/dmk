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
                <i class="fa-fw fa fa-group"></i> Processos <span>> Finanças </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('cliente/editar/') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
            <input type="hidden" id="cd_processo_pro" value="{{ $id }}">
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
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarDespesasProcesso" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>

                                                <a href="{{ url('cliente/limpar-selecao/') }}" class="btn btn-warning pull-right header-btn" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Seleção</a>
                                            </div>                                                                                             
                                            <div class="tabelah">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <th>Tipos de Despesas</th>
                                                        <th>Cliente</th>
                                                        <th>Correspondente</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($despesas as $despesa)
                                                            <tr>
                                                                <td>{{ $despesa->nm_tipo_despesa_tds }}</td>
                                                                <td {{ (!empty($despesa->vl_processo_despesa_pde) && $despesa->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE) ? "class=info" : '' }} >
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-despesa" data-entidade="cliente" data-despesa="{{$despesa->cd_tipo_despesa_tds}}" data-identificador="DCL{{$despesa->cd_tipo_despesa_tds}}" data-oldvalue="{{ $despesa->vl_processo_despesa_pde }}" value="{{ $despesa->vl_processo_despesa_pde }}">
                                                                        </div>                                                                               
                                                                    </div>     
                                                                     <div class="onoffswitch-container col-md-7">     
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" {{ (!empty($despesa->vl_processo_despesa_pde) && $despesa->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE) ?  ($despesa->fl_reembolsavel_processo == 'S') ? 'checked' : ''  : ($despesa->fl_reembolsavel_cliente == 'S') ? 'checked' : '' }} class="onoffswitch-checkbox" name="DCL{{$despesa->cd_tipo_despesa_tds}}" value="S" id="DCL{{$despesa->cd_tipo_despesa_tds}}">
                                                                            <label class="onoffswitch-label" for="DCL{{$despesa->cd_tipo_despesa_tds}}"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                <span class="onoffswitch-switch"></span>  
                                                                            </label>                             
                                                                        </span> 
                                                                        @if(!empty($despesa->vl_processo_despesa_pde) && ($despesa->fl_reembolsavel_cliente != $despesa->fl_reembolsavel_processo) && $despesa->cd_tipo_entidade_tpe == \TipoEntidade::CLIENTE)
                                                                            <i style="color: red" class='fa fa-warning'></i>
                                                                        @endif
                                                                    </div>                                                                        
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-honorario" data-cidade="" data-servico="" value="">
                                                                        </div>
                                                                    </div>
                                                                     <div class="onoffswitch-container col-md-7">
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" class="onoffswitch-checkbox" name="DCO{{$despesa->cd_tipo_despesa_tds}}" value="S" id="DCO{{$despesa->cd_tipo_despesa_tds}}">
                                                                            <label class="onoffswitch-label" for="DCO{{$despesa->cd_tipo_despesa_tds}}"> 
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
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                        <h2>Honorários</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        
                                            <div class="col-md-6"> 
                                                <h5>Lista de Honorários</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorarios" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>

                                                <a href="{{ url('cliente/limpar-selecao/') }}" class="btn btn-warning pull-right header-btn" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Seleção</a>
                                            </div>                                                                                             
                                            <div class="tabelah">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <th>Tipos de Serviços</th>
                                                        <th style="border-right: none;text-align: center;width:15%">Cliente</th>
                                                        <th style="border-left: none;text-align: center; ">Valor Cliente</th>
                                                        <th style="border-right: none;text-align: center;width:15%">Correspondente</th>
                                                        <th style="border-left: none;text-align: center; ">Valor Correspondente</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tiposDeServico as $tipoDeServico)
                                                            <tr>
                                                                <td>{{ $tipoDeServico->nm_tipo_servico_tse }}</td>
                                                                <td style="border-right: none;" {{ (!empty($tipoDeServico->nu_taxa_the)) ? "class=info" : '' }}>
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-honorario" data-cidade="" data-servico="" value="{{ $tipoDeServico->nu_taxa_the }}">
                                                                        </div>                                                                               
                                                                    </div>     
                                                                </td>
                                                                <td  {{ (!empty($tipoDeServico->nu_taxa_the)) ? "class=info" : '' }} style="border-left: none;text-align: right;">
                                                                     <div class="onoffswitch-container col-md-7">
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" {{ (empty($tipoDeServico->nu_taxa_the)) ? ' ' : 'checked' }} class="onoffswitch-checkbox" name="SCL{{$tipoDeServico->cd_tipo_servico_tse}}" value="S" id="SCL{{$tipoDeServico->cd_tipo_servico_tse}}">
                                                                            <label class="onoffswitch-label" for="SCL{{$tipoDeServico->cd_tipo_servico_tse}}"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                    <span class="onoffswitch-switch"></span>
                                                                            </label> 
                                                                        </span> 
                                                                    </div>                                                                        
                                                                </td>
                                                                <td style="border-right: none;">
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-honorario" data-cidade="" data-servico="" value="">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td style="border-left: none;">
                                                                     <div class="onoffswitch-container col-md-7">
                                                                        
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" class="onoffswitch-checkbox" name="SCO{{$despesa->cd_tipo_despesa_tds}}" value="S" id="SCO{{$despesa->cd_tipo_despesa_tds}}">
                                                                            <label class="onoffswitch-label" for="SCO{{$despesa->cd_tipo_despesa_tds}}"> 
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
