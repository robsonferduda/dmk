@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Processo</a></li>
        <li>Detalhes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs hidden-sm col-sm-12 col-md-6 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Detalhes </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7 box-button">
            <div class="boxBtnTopo sub-box-button">
                <a data-toggle="modal" href="{{ url('cliente/processos') }}" class="btn btn-default pull-right header-btn" style="margin-right: 15px;"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            </div>
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
                                            <ul class="list-unstyled" style=" line-height: 1.5;">
                                                <li>
                                                    <strong>Nº Processo: </strong> {{ $processo->nu_processo_pro }}
                                                </li>
                                                @role('administrator|colaborador') 
                                                    <li>
                                                        <strong>Cliente: </strong><a href="{{'../../cliente/detalhes/'.$processo->cliente->cd_cliente_cli}}">{{ $processo->cliente->nm_fantasia_cli ? :  $processo->cliente->nm_razao_social_cli }}</a> 
                                                    </li>
                                                @endrole
                                                <li>
                                                    <strong>Código Cliente: </strong>  {{ !empty($processo->nu_acompanhamento_pro) ? $processo->nu_acompanhamento_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Processo: </strong> {{ !empty($processo->tipoProcesso->nm_tipo_processo_tpo) ? $processo->tipoProcesso->nm_tipo_processo_tpo : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Serviço Cliente: </strong> {{ !empty($processo->honorario and $processo->honorario->tipoServico) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : ' ' }}
                                                </li> 
                                                @role('administrator|colaborador')
                                                <li>
                                                    <strong>Valor do Cliente: </strong> {{ !empty($processo->honorario) ? str_replace('.',',',$processo->honorario->vl_taxa_honorario_cliente_pth) : ' ' }}
                                                </li>   
                                                <li>
                                                    <strong>Valor Nota Fiscal do Cliente: </strong> {{ !empty($processo->honorario) ? str_replace('.',',',$processo->honorario->vl_taxa_cliente_pth) : ' ' }}
                                                </li>                                                 
                                                <li>
                                                    <strong>Advogado Solicitante: </strong>  {{ !empty($processo->advogadoSolicitante->nm_contato_cot) ? $processo->advogadoSolicitante->nm_contato_cot : 'Não informado' }}
                                                </li>
                                                @endrole 
                                                @role('administrator|colaborador')
                                                    <li>
                                                        <strong>Responsável: </strong>
                                                        @if(!empty($processo->responsavel))
                                                         <a href="{{ url('usuarios/detalhes/'.\Crypt::encrypt($processo->responsavel->id)) }}">{{ $processo->responsavel->name }}</a>
                                                        @else
                                                            <span>Não Alocado</span>
                                                        @endif
                                                    </li>
                                                @endrole                                         
                                                                                    
                                                <li>
                                                    <strong>Autor: </strong> {{ ($processo->nm_autor_pro) ? $processo->nm_autor_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : ' ' }}
                                                </li> 
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : ' ' }}
                                                </li>
                                                @role('administrator|colaborador')
                                                    <li>
                                                        <strong>Correspondente: </strong> 
                                                        @if(!empty($processo->correspondente->contaCorrespondente))
                                                            <a href="{{ url('correspondente/detalhes/'.\Crypt::encrypt($processo->correspondente->cd_conta_con)) }}">{{$processo->correspondente->load('contaCorrespondente')->contaCorrespondente->nm_conta_correspondente_ccr}}</a>
                                                        @endif
                                                    </li> 
                                                @endrole    
                                                <li>
                                                    <strong>Tipo de Serviço Correspondente: </strong> {{ !empty($processo->honorario->tipoServicoCorrespondente) ? $processo->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse : 'Não informado' }}
                                                </li> 
                                                <li>
                                                    <strong>Valor do Correspondente: </strong> {{ !empty($processo->honorario->tipoServicoCorrespondente) ? str_replace('.',',',$processo->honorario->vl_taxa_honorario_correspondente_pth) : 'Não informado' }}
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
                                            <ul class="list-unstyled" style=" line-height: 1.5;">
                                                <li>
                                                    <strong>Data da Solicitação: </strong> {{ !empty($processo->dt_solicitacao_pro) ? date('d/m/Y', strtotime($processo->dt_solicitacao_pro)) : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Data Prazo Fatal: </strong> {{ !empty($processo->dt_prazo_fatal_pro) ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Hora da Audiência: </strong> {{ !empty($processo->hr_audiencia_pro) ? date('H:i', strtotime($processo->hr_audiencia_pro)) : ' ' }}
                                                </li>                                                
                                                <li>
                                                    <strong>Réu: </strong> {{ ($processo->nm_reu_pro) ? $processo->nm_reu_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Vara: </strong> {{ !empty($processo->vara->nm_vara_var) ? $processo->vara->nm_vara_var : 'Não infomado' }}
                                                </li>
                                                <h6 style="font-weight: 400;">Audiência com: </h6>
                                                <li>
                                                    <strong>Preposto: </strong> {{ ($processo->nm_preposto_pro) ? $processo->nm_preposto_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Advogado: </strong> {{ ($processo->nm_advogado_pro) ? $processo->nm_advogado_pro : 'Não informado'}}
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
                                                    <strong>Observações: </strong> {!! $processo->dc_observacao_pro !!} 
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