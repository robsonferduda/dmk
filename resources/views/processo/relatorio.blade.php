@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Processos</a></li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-info"></i> Processos <span>> Relatório </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 boxBtnTopo">
            <a title="Despesas" class="btn btn-warning pull-right header-btn btnMargin" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i>Despesas</a>
            <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a>       
            <a data-toggle="modal" href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-default pull-right header-btn"><i class="fa fa-file-text-o fa-lg"></i> Processo</a>       
           
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
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Dados do Relatório </h2>             
                    </header>
                
                    <div class="col-sm-12">

                        <div class="col-md-6">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-info"></i> <strong>Relatório</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                           
                                                <li>
                                                    <strong>Cliente: </strong><a href="{{'../../cliente/detalhes/'.$processo->cliente->cd_cliente_cli}}">{{ $processo->cliente->nm_fantasia_cli ? :  $processo->cliente->nm_razao_social_cli }}</a> 
                                                </li>
                                                <li>
                                                    <strong>Autor: </strong> {{ $processo->nm_autor_pro }}
                                                </li>
                                                <li>
                                                    <strong>Réu: </strong> {{ $processo->nm_reu_pro }}
                                                </li>
                                                <li>
                                                    <strong>Correspondente: </strong>  
                                                    @if(!empty($processo->correspondente))
                                                        <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">
                                                        {{ ($processo->correspondente->nm_fantasia_con) ? $processo->correspondente->nm_fantasia_con : $processo->correspondente->nm_razao_social_con }}</a>
                                                    @endif
                                                </li> 
                                                <li>
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : ' ' }}
                                                </li> 
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : ' ' }}
                                                </li>   
                                                <legend><i class="fa">Cliente:</i> </legend>
                                                <li>
                                                    <strong>Honorários: </strong> <span style="color: green;font-weight: bold;">R$ {{ str_replace('.',',',$honorarioCliente) }}</span>
                                                </li>
                                                <li>
                                                    <strong>Despesas: </strong><span style="color: red;font-weight: bold;">R$ {{ str_replace('.',',',$despesasCliente) }}</span>
                                                </li>
                                                <li>
                                                    <strong>Despesas Reembolsáveis: </strong><span style="color: green;font-weight: bold;">R$ {{ str_replace('.',',',$despesasReembolsaveisCliente) }}</span>
                                                </li>
                                                <li>
                                                    <strong>Nota Fiscal: </strong> {{ $processo->nm_preposto_pro }}
                                                </li>
                                                <legend><i class="fa">Correpondente:</i> </legend>    
                                                <li>
                                                    <strong>Honorários: </strong> <span style="color: green;font-weight: bold;">R$ {{ str_replace('.',',',$honorarioCorrespondente) }}</span>
                                                </li>
                                                <li>
                                                    <strong>Despesas: </strong> <span style="color: red;font-weight: bold;">R$ {{ str_replace('.',',',$despesasCorrespondente) }}</span>
                                                </li>        
                                                <li>
                                                    <strong>Despesas Reembolsáveis: </strong><span style="color: red;font-weight: bold;">R$ {{ str_replace('.',',',$despesasReembolsaveisCorrespondente) }}</span>
                                                </li>                                                                        
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-fw"></i> <strong></strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                                
                                                <legend><i class="fa">Totais:</i> </legend>
                                                <li>
                                                    <strong>Entrada: </strong> </strong><span style="color: green;font-weight: bold;">R$ {{ str_replace('.',',',$entrada) }}</span>
                                                </li>
                                                <li>
                                                    <strong>Saída: </strong> <span style="color: red;font-weight: bold;">R$ {{ str_replace('.',',',$saida) }}</span>
                                                </li>
                                                <li>
                                                    @if($receita >= 0)
                                                        <strong>Receita: </strong> <span style="color: green;font-weight: bold;">R$ {{ str_replace('.',',',$receita) }}</span>
                                                    @else
                                                        <strong>Receita: </strong> <span style="color: red;font-weight: bold;">R$ {{ str_replace('.',',',$receita) }}</span>
                                                    @endif
                                                </li>
                                              
                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>                  
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection