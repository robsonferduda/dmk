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
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Processos <span>> Despesas </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 boxBtnTopo">
            <a href="{{ url('processos') }}" class="btn btn-default pull-right"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            <a title="Relatório" class="btn btn-default pull-right header-btn" href="{{ url('processos/relatorio/'.\Crypt::encrypt($id)) }}"><i class="fa fa-usd fa-lg"></i> Relatório Financeiro</a>
            <a data-toggle="modal" href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($id)) }}" class="btn btn-default pull-right header-btn"><i class="fa fa-calendar fa-lg"></i> Acompanhamento</a> 
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('processos/editar/'.\Crypt::encrypt($id)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a>          
            <input type="hidden" id="cd_processo_pro" value="{{ \Crypt::encrypt($id) }}">   
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <div class="col-md-12">            
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                        <h2>Despesas</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            <div class="alert alert-info" role="alert">
                            <i class="fa-fw fa fa-info"></i>
                                <strong>Informação!</strong> O ícone <i style="color: red" class='fa fa-warning'></i> indica que o comportamento da opção <i>reembolsável</i> está diferente do padrão cadastrado em Cliente e/ou Correspondente.
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        
                                            <div class="col-md-6"> 
                                                <h5>Lista de Despesas</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarDespesasProcesso" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>

                                                <button class="btn btn-warning pull-right header-btn" id="limparValoresDespesa" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Valores</button>
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
                                                                <td>
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-despesa" data-entidade="cliente" data-despesa="{{$despesa->cd_tipo_despesa_tds}}" data-identificador="DCL{{$despesa->cd_tipo_despesa_tds}}" data-oldvalue="{{ $despesa->vl_despesa_cliente }}" value="{{ $despesa->vl_despesa_cliente }}">
                                                                        </div>                                                                               
                                                                    </div>     
                                                                     <div class="onoffswitch-container col-md-7">     
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" {{ (!empty($despesa->vl_despesa_cliente)) ?  ($despesa->fl_reembolsavel_processo_cliente == 'S') ? 'checked' : ''  : ($despesa->fl_reembolsavel_cliente == 'S') ? 'checked' : '' }} class="onoffswitch-checkbox" name="DCL{{$despesa->cd_tipo_despesa_tds}}" value="S" id="DCL{{$despesa->cd_tipo_despesa_tds}}" data-vreembolso='{{$conta->fl_despesa_nao_reembolsavel_con}}' >
                                                                            <label class="onoffswitch-label" for="DCL{{$despesa->cd_tipo_despesa_tds}}"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                <span class="onoffswitch-switch"></span>  
                                                                            </label>                             
                                                                        </span> 
                                                                        @if(!empty($despesa->vl_despesa_cliente) && ($despesa->fl_reembolsavel_cliente != $despesa->fl_reembolsavel_processo_cliente))
                                                                            <i style="color: red" class='fa fa-warning'></i>
                                                                        @endif
                                                                    </div>                                                                        
                                                                </td>
                                                                <td> 
                                                                    <div class="col-md-4">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input type="text" class="form-control taxa-despesa" data-entidade="correspondente"  data-despesa="{{$despesa->cd_tipo_despesa_tds}}" data-identificador="DCO{{$despesa->cd_tipo_despesa_tds}}" data-oldvalue="{{ $despesa->vl_despesa_correspondente }}" value="{{ $despesa->vl_despesa_correspondente }}">
                                                                        </div>
                                                                    </div>
                                                                     <div class="onoffswitch-container col-md-7">
                                                                        <span class="onoffswitch-title">Reembolsável</span> 
                                                                        <span class="onoffswitch">
                                                                            <input type="checkbox" {{ (!empty($despesa->vl_despesa_correspondente)) ?  ($despesa->fl_reembolsavel_processo_correspondente == 'S') ? 'checked' : ''  : ($despesa->fl_reembolsavel_correspondente == 'S') ? 'checked' : '' }}  class="onoffswitch-checkbox" name="DCO{{$despesa->cd_tipo_despesa_tds}}" value="S" id="DCO{{$despesa->cd_tipo_despesa_tds}}" data-vreembolso='{{$conta->fl_despesa_nao_reembolsavel_con}}' >
                                                                            <label class="onoffswitch-label" for="DCO{{$despesa->cd_tipo_despesa_tds}}"> 
                                                                                <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                                                <span class="onoffswitch-switch"></span>
                                                                            </label> 
                                                                        </span> 
                                                                        @if(!empty($despesa->vl_despesa_correspondente) && ($despesa->fl_reembolsavel_correspondente != $despesa->fl_reembolsavel_processo_correspondente))
                                                                            <i style="color: red" class='fa fa-warning'></i>
                                                                        @endif
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
          {{--  <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                        <h2>Honorário</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="well">
                            <div class="alert alert-info" role="alert">
                                <i class="fa-fw fa fa-info"></i>
                                <strong>Informação!</strong> Os campos de valor serão preenchidos com os valores padrões cadastrados no Cliente e/ou Correspondente ao selecionar o tipo de serviço. Sendo permitida sua mudança.                          
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        
                                            <div class="col-md-6"> 
                                                <h5>Honorário por tipo de serviço</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorariosProcesso" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>
                                               
                                            </div>                                                                                             
                                            <div class="tabelah">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <th style="width: 50%">Tipos de Serviços</th>
                                                        <th style="text-align: center; ">Valor Cliente</th>
                                                        <th style="text-align: center; ">Valor Correspondente</th>
                                                    </thead>
                                                    <tbody>  
                                                        <tr>  
                                                            <td>                                           
                                                                <select id="tipoServico" name="cd_tipo_servico_tse" class="select2">
                                                                    <option data-cliente="" data-correspondente="" selected value="">Selecione...</option>  
                                                                    @foreach($tiposDeServico as $tipoDeServico)
                                                                        <option {{ (!empty($honorariosProcesso) && $honorariosProcesso->cd_tipo_servico_tse ==  $tipoDeServico->cd_tipo_servico_tse) ? 'selected' : '' }} data-cliente="{{ $tipoDeServico->nu_taxa_the_cliente }}" data-correspondente="{{ $tipoDeServico->nu_taxa_the_correspondente }}" value="{{$tipoDeServico->cd_tipo_servico_tse}}">{{$tipoDeServico->nm_tipo_servico_tse}}</option>  
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div class="col-md-4 col-md-offset-2">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon">$</span>
                                                                    <input style="width: 100px;" id="taxa-honorario-cliente" type="text" class="form-control taxa-honorario" value="{{ ( !empty($honorariosProcesso->vl_taxa_honorario_cliente_pth)) ? $honorariosProcesso->vl_taxa_honorario_cliente_pth : '' }}">
                                                                </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="col-md-4 col-md-offset-2">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon">$</span>
                                                                    <input style="width: 100px;" id="taxa-honorario-correspondente" type="text" class="form-control taxa-honorario"  value="{{ ( !empty($honorariosProcesso->vl_taxa_honorario_correspondente_pth)) ? $honorariosProcesso->vl_taxa_honorario_correspondente_pth : '' }}">
                                                                </div>
                                                                </div>
                                                            </td>
                                                            
                                                        </tr>
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
            </article> --}}

        </div>
    </div>
</div>
@endsection
