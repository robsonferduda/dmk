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
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Detalhes </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 boxBtnTopo">

            <a title="Relatório" class="btn btn-default pull-right header-btn btnMargin" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-info fa-lg"></i>Relatório</a>
            <a title="Despesas" class="btn btn-warning pull-right header-btn" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i>Despesas</a>
            <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
            
           
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
                        <h2>Dados do Processo </h2>             
                    </header>
                
                    <div class="col-sm-12">

                        <div class="col-md-6">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-file-text-o"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>
                                            <ul class="list-unstyled">
                                           
                                                <li>
                                                    <strong>Cliente: </strong><a href="{{'../../cliente/detalhes/'.$processo->cliente->cd_cliente_cli}}">{{ $processo->cliente->nm_fantasia_cli ? :  $processo->cliente->nm_razao_social_cli }}</a> 
                                                </li>
                                                <li>
                                                    <strong>Advogado Solicitante: </strong>  {{ !empty($processo->advogadoSolicitante->nm_contato_cot) ? $processo->advogadoSolicitante->nm_contato_cot : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Nº Processo: </strong> {{ $processo->nu_processo_pro }}
                                                </li>
                                                                                       
                                                <li>
                                                    <strong>Tipo de Processo: </strong> {{ !empty($processo->tipoProcesso->nm_tipo_processo_tpo) ? $processo->tipoProcesso->nm_tipo_processo_tpo : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Autor: </strong> {{ $processo->nm_autor_pro }}
                                                </li>
                                                <li>
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : ' ' }}
                                                </li> 
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Correspondente: </strong> 
                                                    @if(!empty($processo->correspondente))
                                                        <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">{{ ($processo->correspondente->nm_fantasia_con) ? $processo->correspondente->nm_fantasia_con : $processo->correspondente->nm_razao_social_con }}</a>
                                                    @endif
                                                </li>                                                
                                                <li>
                                                    <strong>Processo Criado em: </strong> {{ date('d/m/Y H:i', strtotime($processo->created_at))  }} 
                                                </li>
                                                <li>
                                                    <strong>Processo Criado por: </strong>  {{ !empty($processo->audits()->where('event','created')->with('user')->get()->last()->user->name) ?  $processo->audits()->where('event','created')->with('user')->get()->last()->user->name : ' ' }} 
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
                                                <li>
                                                    <strong>Data da Solicitação: </strong> {{ !empty($processo->dt_solicitacao_pro) ? date('d/m/Y', strtotime($processo->dt_solicitacao_pro)) : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Hora da Audiência: </strong> {{ !empty($processo->hr_audiencia_pro) ? date('H:i', strtotime($processo->hr_audiencia_pro)) : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Data Prazo Fatal: </strong> {{ !empty($processo->dt_prazo_fatal_pro) ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Réu: </strong> {{ $processo->nm_reu_pro }}
                                                </li>
                                                <li>
                                                    <strong>Vara: </strong> {{ !empty($processo->vara->nm_vara_var) ? $processo->vara->nm_vara_var : ' ' }}
                                                </li>
                                                <legend><i class="fa">Audiência com:</i> </legend>
                                                <li>
                                                    <strong>Preposto: </strong> {{ $processo->nm_preposto_pro }}
                                                </li>
                                                <li>
                                                    <strong>Advogado: </strong> {{ $processo->nm_advogado_pro }}
                                                </li>

                                            </ul>
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                        </div>  
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-fw"></i> <strong></strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <p>    
                                            <ul class="list-unstyled">
                                                <li style="display: inline-block;max-width: 100%;word-break:break-all;">
                                                    <strong>Observações: </strong> {{ $processo->dc_observacao_pro }} 
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