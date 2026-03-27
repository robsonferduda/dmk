@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('processos') }}">Processos</a></li>
        <li>Acompanhamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-6 col-lg-7">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Acompanhamento </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5 box-button">
            <div class="boxBtnTopo sub-box-button">
                <!-- Regras de negócio para Administrador e Colaborador -->
                @role('administrator|colaborador')

                    <div class="dropdown pull-right boxBtnTopo" style="display: inline;margin-right: 15px;">
                        <a href="javascript:void(0);" class="btn btn-info dropdown-toggle btn-responsive" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a title="Novo" href="{{ url('processos/novo') }}" ><i class="fa fa-plus fa-lg"></i> Novo</a> 
                            </li>
                            <li>
                                <a title="Editar" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-edit fa-lg"></i> Editar</a>
                            </li>
                            <li>
                                <a title="Clonar" href="{{ url('../processos/clonar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa fa-clone fa-lg"></i> Clonar</a>
                            </li>

                            <li>
                                <a title="Relatório Financeiro" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-usd fa-lg"></i> Relatório Financeiro</a>
                            </li>

                        </ul>
                    </div>   

                    <a title="Acompanhamento" href="{{ url('processos/acompanhamento') }}" class="btn btn-default pull-right header-btn btn-responsive"><i class="fa fa-list fa-lg"></i> Acompanhamento</a> 
                    
                    <a title="Despesas" class="btn btn-warning pull-right header-btn btn-responsive" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i> Despesas</a>          
                    
                @endrole

                <!-- Regras de negócio para Correspondente -->
                @role('correspondente')

                    @if(App\StatusProcesso::visivelCorrespondente($processo->cd_status_processo_stp))

                        @if($processo->cd_status_processo_stp != App\Enums\StatusProcesso::FINALIZADO_CORRESPONDENTE or
                            $processo->cd_status_processo_stp != App\Enums\StatusProcesso::FINALIZADO_CORRESPONDENTE)

                            <form class="pull-right" style="display: inline; float: left; margin-right: 15px; margin-top: 17px;" action="{{ url('processo/atualizar-status') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                                <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::FINALIZADO_CORRESPONDENTE }}">     
                                <button title="Finalizar Processo" class="btn btn-success" type="submit"><i class="fa fa-check"></i> Finalizar Processo</button>
                            </form>

                        @elseif($processo->cd_status_processo_stp == App\Enums\StatusProcesso::FINALIZADO)

                            <button title="Processo Finalizado" class="btn btn-success disabled pull-right header-btn" style="display: inline; float: left; margin-right: 15px; margin-top: 17px;" type="button"><i class="fa fa-check"></i> Finalizar Processo</button>

                        @else 
                            
                            <button title="Finalizar Processo indisponível. Só é possível finalizar o processo após confirmar o recebimento de documentos e enviar o arquivo comprabatório de realização do ato." class="btn btn-success disabled pull-right header-btn" style="display: inline; float: left; margin-right: 15px; margin-top: 17px;" type="button"><i class="fa fa-check"></i> Finalizar Processo</button>
                        @endif

                        @php

                        $agora = \Carbon\Carbon::now(); 
                        $prazo = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $processo->dt_prazo_fatal_pro." ".date('H:i', strtotime($processo->hr_audiencia_pro)));

                        $prazo_recusa = $agora->diffInHours($prazo);                     

                        @endphp


                        @if($processo->cd_status_processo_stp == App\Enums\StatusProcesso::ACEITO_CORRESPONDENTE and $prazo_recusa < 48 and $prazo > $agora)

                        <form class="pull-right" style="display: inline; float: left; margin-right: 10px; margin-top: 17px;" action="{{ url('processo/atualizar-status') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                            <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::RECUSADO_CORRESPONDENTE }}">     
                            <button title='Recusar Processo' class="btn btn-warning" type="submit"><i class="fa fa-ban"></i> Recusar Processo</button>
                        </form>

                        @else

                        <button title="Recusar Processo indisponível. Só é possível recusar um processo antes do seu aceite ou até 48 após seu recebimento" class="btn btn-warning disabled pull-right header-btn" style="display: inline; float: left; margin-right: 10px; margin-top: 17px;" type="button"><i class="fa fa-ban"></i> Recusar Processo</button>

                        @endif

                    @endif

                @endrole
                </div>


            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12">
                    @include('layouts/messages')
                </div>
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    @if($processo->cd_status_processo_stp == App\Enums\StatusProcesso::CANCELADO)
                    <div class="alert alert-danger fade in">
                        <i class="fa-fw fa fa-times"></i>
                        <strong>Processo Cancelado!</strong> O processo está cancelado.
                    </div>
                    @endif
                    @role('administrator|colaborador')
                    <div class="well">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12 box-button">
                                <div class='sub-box-button' >
                                    <form action="{{ url('processo/atualizar-status') }}" class="form-inline" method="POST">
                                        {{ csrf_field() }}

                                        <div style="float: left;  margin-right: 10px;" class="status-processo-acompanhamento-md">
                                            <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">
                                            <label class="label label-black" >Selecione um Status para o Processo</label>          
                                            <select id="status" name="status" class="select2">
                                                <option selected value="0">Selecione uma situação</option>
                                                @foreach(App\StatusProcesso::orderBy('nm_status_processo_conta_stp')->get() as $status)
                                                    <option value="{{ $status['cd_status_processo_stp'] }}" {{ ($processo->cd_status_processo_stp == $status['cd_status_processo_stp']) ? 'selected' : '' }} >{{ $status['nm_status_processo_conta_stp'] }}</option>
                                                @endforeach
                                            </select> 
                                        </div> 
                                        <div class="box-button-xs"  >
                                            <div class="sub-box-button-xs" >
                                                <button  title="Alterar Status" class="btn btn-primary marginTop17" type="submit"><i class="fa fa-refresh"></i><span class="hidden-xs hidden-sm"> Alterar Status</span></button>
                                            </div>                                     
                                        </div>                                 

                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12 box-button" >
                                <div class="sub-box-button marginTop17 sub-box-button-status-md sub-box-button-status-lg">
                                   
                                    @if($processo->cd_status_processo_stp != App\Enums\StatusProcesso::CONTRATAR_CORRESPONDENTE)
                                        <a title="Notificar Correspondente" class="btn btn-default  msg_processamento btn-responsive btn-m-bottom" href="{{ url('processos/notificar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-send-o"></i> Notificar Correspondente</a>   
                                    @endif
                                                                        
                                     <a title="Finalizar Processo"  class="btn btn-success btn-responsive" href="#" id="btnModalFinalizacao"><i class="fa fa-check"></i> Finalizar Processo</a>
                                                               
                                </div>
                            </div>
                        </div>
                        <form id="cancelarProcessoForm" action="{{ url('proctesso/atualizar-status') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                            <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::CANCELADO }}">     
                        </form>  
                        <div style="clear: both;"></div>
                    </div>
                    @endrole

                    @if($processo->cd_status_processo_stp == App\Enums\StatusProcesso::FINALIZADO and $processo->dt_finalizacao_pro)
                    <div class="well" style="border-radius: 8px;">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                                <p>
                                    PROCESSO FINALIZADO em <strong>{{ date('d/m/Y H:i:s', strtotime($processo->dt_finalizacao_pro)) }}</strong> por <strong>{{ ($processo->usuario) ? $processo->usuario->name : '' }}</strong>.
                                    <span style="font-size: 12px; background-color: {{ $processo->status->ds_color_stp }}" class="label label-default pull-right">{{ $processo->status->nm_status_processo_conta_stp }}</span>
                                </p>
                                <p><strong>Texto de Finalização</strong>: {!! ($processo->txt_finalizacao_pro) ? $processo->txt_finalizacao_pro : '<span class="text-danger">Nenhum texto informado</span>' !!}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="jarviswidget jarviswidget-sortable">
                        <header role="heading" class="ui-sortable-handle">
                            <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                            <h2>Dados do Processo </h2>             
                        </header>                
                        <div class="col-md-12 box-loader">
                            <div class="col-md-12 col-lg-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend>
                                        <i class="fa fa-file-text-o"></i> <strong>Dados Básicos</strong>
                                        <span style="font-size: 12px; background-color: {{ $processo->status->ds_color_stp }}" class="label label-default pull-right">{{ $processo->status->nm_status_processo_conta_stp }}</span>
                                    </legend>
                                    <div class="row" style="margin-left: 3px;">
                                        <input type="hidden" name="conta_logada" id="conta_logada" value="{{ Auth::user()->cd_conta_con }}">
                                        <input type="hidden" name="processo" id="processo" value="{{ $processo->cd_processo_pro }}">
                                        <input type="hidden" name="msg_correspondente" id="msg_correspondente" value="{{ $processo->cd_correspondente_cor }}">
                                        
                                            <ul class="list-unstyled" style=" line-height: 1.5;">
                                                <li>
                                                    <strong>Nº Processo: </strong> <a href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                                </li>
                                                @role('correspondente')
                                                    <li>
                                                        <strong>Status do Processo: </strong> {{ $processo->status->nm_status_processo_conta_stp }}
                                                    </li>
                                                @endrole
                                                <li>
                                                    <strong>Código Cliente: </strong>  {{ !empty($processo->nu_acompanhamento_pro) ? $processo->nu_acompanhamento_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Processo: </strong> {{ !empty($processo->tipoProcesso->nm_tipo_processo_tpo) ? $processo->tipoProcesso->nm_tipo_processo_tpo : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Serviço Cliente: </strong> {{ !empty($processo->honorario->tipoServico) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : 'Não informado' }}
                                                </li> 
                                                <li>
                                                    <strong>Valor do Serviço: </strong>
                                                    {{ !empty($processo->honorario) ? 'R$ '.number_format($processo->honorario->vl_taxa_honorario_cliente_pth, 2, ',', '.') : ' ' }}
                                                    @if(!empty($processo->honorario))
                                                        <a href="#" data-toggle="modal" data-target="#modalSolicitarAlteracao"
                                                           class="btn btn-xs btn-default" style="margin-left: 8px;">
                                                            <i class="fa fa-pencil"></i> Solicitar Alteração
                                                        </a>
                                                    @endif
                                                </li>

                                                <li>
                                                    <strong>Autor: </strong> {{ ($processo->nm_autor_pro) ? $processo->nm_autor_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : 'Não informado' }}
                                                </li>                                                
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : 'Não informada' }}
                                                </li>
                                                
                                                <li>
                                                    <strong>Data da Solicitação: </strong> {{ !empty($processo->dt_solicitacao_pro) ? date('d/m/Y', strtotime($processo->dt_solicitacao_pro)) : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Data Prazo Fatal: </strong> {{ !empty($processo->dt_prazo_fatal_pro) ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Hora da Audiência: </strong> {{ !empty($processo->hr_audiencia_pro) ? date('H:i', strtotime($processo->hr_audiencia_pro)) : 'Não informado' }}
                                                </li>                                                
                                                <li>
                                                    <strong>Réu: </strong> {{ ($processo->nm_reu_pro) ? $processo->nm_reu_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Vara: </strong> {{ !empty($processo->vara->nm_vara_var) ? $processo->vara->nm_vara_var : 'Não infomado' }}
                                                </li> 
                                                
                                            </ul>                                                
                                           
                                            @if($processo->tipoProcesso)
                                                <ul class="list-unstyled" style=" line-height: 1.5;">
                                                    <li>
                                                        <strong>Advogado: </strong> 
                                                        <p>{!! ($processo->nm_advogado_pro) ? nl2br(e($processo->nm_advogado_pro))  : 'Não informado' !!}</p>
                                                    </li>
                                                    <li>
                                                        <strong>Preposto: </strong> 
                                                        <p>{!! ($processo->nm_preposto_pro) ? nl2br(e($processo->nm_preposto_pro))  : 'Não informado' !!}</p>
                                                    </li>
                                                </ul>
                                            @endif
                                    </div>
                                    
                                   
                                </fieldset>
                            </div>

                            <div class="col-md-12 col-lg-6">
                                @include('cliente.processo.acompanhamento.arquivos')
                            </div>
                        </div>             
                    </div>
                </article>
                @include('cliente.processo.acompanhamento.chat')
                </div>
            </div>
        </div>


        <div class="modal fade modal_top_alto" id="modalFinalizacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="frm-finalizar-processo" action="{{ url('processo/finalizar-processo') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel"><i class="fa fa-check"></i> Finalizar Processo</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 upload-msg marginBottom5"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="processo" value="{{ $processo->cd_processo_pro }}">
                                    <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::FINALIZADO }}"> 

                                    <div class="form-group" id="despesas_finalizar"></div>

                                    <label class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> A notificação do cliente é opcional. Caso a opção não seja marcada o processo será finalizado mesmo assim.</label>    
                                    <div class="form-group">                                                    
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="checkbox" name="fl_envio_arquivo" id="fl_envio_arquivo">
                                                <span>Notificar o cliente sobre a finalização do processo (Opcional)</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="box-envio-email" style="display: none;">

                                        <div class="form-group">
                                            <label><strong>Email de Envio</strong> </label>
                                            <p>Lista de email carregada automaticamente com os emails de notificação do cliente.</p>
                                            <p>Caso não existam emails cadastrados ou queira adicionar novos endereços, preencha a lista, separando os endereços com vírgula.</p>
                                            <p><strong>Exemplo</strong>:
                                                email1@easyjuris.com.br, email2@easyjuris.com.br,email3@easyjuris.com.br
                                            </p>
                                            <input class="form-control"  
                                            placeholder="Email" 
                                            type="text" 
                                            name="lista_email"
                                            id="lista_email"
                                            value="{{ ($processo->cliente->entidade and $processo->cliente->entidade->getEmailsNotificacao()) 
                                                     ? $processo->cliente->entidade->getEmailsNotificacao() : 'Nenhum email informado' }}">

                                        
                                            <label style="margin-top: 8px; margin-bottom: 3px;"><strong>Texto de Finalização</strong> </label>
                                            <textarea style="border-radius: 8px !important;" class="form-control texto-processo" rows="6" name="txt_finalizacao_pro" id="txt_finalizacao_pro">{{ $processo->txt_finalizacao_pro }}</textarea>

                                            <label style="margin-top: 8px; margin-bottom: 3px;"><strong>Ata de Finalização</strong> </label>
                                            <input type="file" name="ata_finalizacao">
                                        </div>
                                        
                                        <label>Arquivos disponíveis para envio em anexo</label><hr style="margin: 0" />

                                        <div class="form-group">                                                    
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="checkbox" name="fl_enviar_todos" id="fl_enviar_todos">
                                                    <span>Enviar Todos</span>
                                                </label>
                                            </div>
                                        </div>

                                        @foreach($processo->anexos as $key => $anexo)
                                        <div class="row" style="width:100%; background-color: #fff; margin-bottom: 10px; ">
                                            <div style="float: left; width: 8%; text-align: center;">
                                                <label class="text-default" style="margin-top: 8px;">
                                                    <input type="checkbox" name="lista_arquivos[]" class="lista_arquivos" value="{{ $anexo->nm_local_anexo_processo_apr }}{{ $anexo->nm_anexo_processo_apr }}">
                                                </label>
                                            </div>
                                            <div style="float: left; width: 92%">
                                                <h4>{{ $anexo->nm_anexo_processo_apr }}</h4>
                                                <h6 style="margin: 0px; font-weight: 200;"><strong>{{ date('d/m/Y H:i:s', strtotime($anexo->created_at)) }}</strong> por <strong>{{ ($anexo->entidade and $anexo->entidade->usuario) ? $anexo->entidade->usuario->name : 'Sem responsável' }}</strong></h6>   
                                            </div> 
                                        </div>
                                        @if($key < count($processo->anexos)-1)
                                        <hr style="margin: 0" />
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button type="submit" class="btn btn-success btn-enviar-arquivo"><i class="fa fa-check"></i> Finalizar Processo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade in modal_top_alto" id="erro_envio_mensagem" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Mensagem do Sistema</h4>
                    </div>
                    <div class="modal-body center">
                        <h2>Erro durante envio de mensagem.</h2>
                    </div>
                    <div class="modal-footer center">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade in modal_top_alto" id="atualiza_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Mensagem do Sistema</h4>
                    </div>
                    <div class="modal-body center">
                        <h2><i class="fa fa-gear fa-spin"></i> Aguarde, atualizando status do processo...</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade in modal_top_alto" id="requisitarPreposto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">
                            <i class="icon-append fa fa-pencil"></i> Requisitar Dados de Advogado e/ou Preposto
                        </h4>
                    </div>
                    <div class="modal-body center">
                        <h5>Esse procedimento encaminha uma mensagem para o correspondente requisitando que o mesmo atualize os dados de Advogado e/ou Preposto.</h5>
                        <h5>Além do envio da mensagem via email, o status do processo é alterado para <strong>Aguardando Dados</strong></h5>
                        <h5>Confirma esse procedimento e o envio da mensagem?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <a href="{{ url('processos/acompanhamento/requisitar-dados/'.safe_encrypt($processo->cd_processo_pro)) }}" id="btn_requisitar_dados" class="btn btn-success msg_processamento"><i class="fa-fw fa fa-check"></i> Confirmar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="informarPreposto" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ url('processo/atualizar-dados') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cd_processo_pro" value="{{ \Crypt::encrypt($processo->cd_processo_pro) }}">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title">
                                <i class="icon-append fa fa-legal"></i> Dados da Audiência - Informar Advogado e/ou Preposto
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row box-cadastro">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Advogado</strong><span class="text-info"> Informe NOME COMPLETO - OAB - TELEFONE (Separados por traço)</span></label>
                                        <p class="text-danger">Digite cada sequencia de dados em uma linha</p>
                                        <textarea class="form-control texto-processo" rows="8" name="dados_advogado" id="dados_advogado" 
                                        placeholder="NOME COMPLETO - OAB - TELEFONE"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();">{!! ($processo->nm_advogado_pro) ?  $processo->nm_advogado_pro : '' !!}</textarea>
                                        <div id="msg_error_advogado" class="text-danger"></div>
                                    </div>    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Preposto</strong><span class="text-info"> Informe NOME COMPLETO - CPF - RG - TELEFONE (Separados por traço)</span></label>
                                        <p class="text-danger">Digite cada sequencia de dados em uma linha</p>
                                        <textarea class="form-control texto-processo" rows="8" name="dados_preposto" id="dados_preposto" 
                                        placeholder="NOME COMPLETO - CPF - RG - TELEFONE"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();">{!! ($processo->nm_preposto_pro) ? $processo->nm_preposto_pro : '' !!}</textarea>
                                        <div id="msg_error_preposto" class="text-danger"></div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button type="submit" class="btn btn-success" id="btnSalvarAdvogadoSolicitante"><i class="fa-fw fa fa-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modalLink" data-backdrop="static" tabindex="100" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ url('processo/informar-link-dados') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cd_processo_pro" value="{{ \Crypt::encrypt($processo->cd_processo_pro) }}">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title">
                                <i class="icon-append fa fa-pencil"></i> Informar Link de Documentos
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row box-cadastro">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Link dos Arquivos</strong><span class="text-info"> Informe o link do drive para os arquivos</span></label>
                                        <input type="text" class="form-control" placeholder="Link dos arquivos" required="required" name="link_dados" id="link_dados" value="{{ ($processo->ds_link_dados_pro) ? $processo->ds_link_dados_pro : '' }}">
                                        <div id="msg_error_preposto" class="text-danger"></div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button type="submit" class="btn btn-success" id="btnSalvarLink"><i class="fa-fw fa fa-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal_top_alto" id="modalLinkAudiencia" data-backdrop="static" tabindex="100" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ url('processo/informar-link-audiencia') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cd_processo_pro" value="{{ \Crypt::encrypt($processo->cd_processo_pro) }}">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title">
                                <i class="icon-append fa fa-pencil"></i> Informar Link da Audiência
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row box-cadastro">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Link da Audiência</strong><span class="text-info"> Informe o link da audiência</span></label>
                                        <input type="text" class="form-control" placeholder="Link da Audiência" required="required" name="link_audiencia" id="link_audiencia" value="{{ ($processo->ds_link_audiencia_pro) ? $processo->ds_link_audiencia_pro : '' }}">
                                        <div id="msg_error_preposto" class="text-danger"></div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button type="submit" class="btn btn-success" id="btnSalvarLink"><i class="fa-fw fa fa-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@if(!empty($processo->honorario))
<div class="modal fade" id="modalSolicitarAlteracao" tabindex="-1" role="dialog" aria-labelledby="modalSolicitarAlteracaoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalSolicitarAlteracaoLabel"><i class="fa fa-pencil"></i> Solicitar Alteração de Valor</h4>
            </div>
            <form action="{{ url('cliente/processos/honorario/solicitar') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="cd_processo_pro" value="{{ $processo->cd_processo_pro }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Valor Atual</label>
                        <input type="text" class="form-control" value="R$ {{ number_format($processo->honorario->vl_taxa_honorario_cliente_pth, 2, ',', '.') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Novo Valor Proposto <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="nu_valor_novo" class="form-control" placeholder="0,00" required>
                        <span class="help-block">Informe o valor que deseja propor. O escritório será notificado e poderá aprovar ou reprovar o pedido.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Enviar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
@section('script')
    <script type="text/javascript">

        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');

            //Posiciona a lista de mensagens na última mensagem enviada
            $('.msg_history_cliente').scrollTop($('.msg_history_cliente')[0].scrollHeight);

            //Verifica se a oção de envio com Enter está ativada
            var callback = function(e){

                var text = e.type;
                var code = e.which ? e.which : e.keyCode;
                if(13 === code){

                    flag = $('#fl_envio_enter').is(':checked'); 

                    if(flag){
                        $('.msg_send_cliente').trigger('click');
                    }
                } 
            };

            $('#texto_mensagem').keypress(callback);     

            //Verifica se a oção de envio com Enter está ativada
            var icallback = function(e){

                var text = e.type;
                var code = e.which ? e.which : e.keyCode;
                if(13 === code){

                    flag = $('#fl_envio_enter_interno').is(':checked'); 

                    if(flag){

                        $('.msg_send_interno').trigger('click');
                    }
                } 
            };

            $('#texto_mensagem').keypress(icallback);   
            
            // Envio de mensagem do cliente para o escritório
            $('.msg_send_cliente').click(function(){
                var processo = $("#processo").val();
                var msg = $("#texto_mensagem").val();

                $.ajax({
                    type: "POST",
                    url: host+"/cliente/processo/mensagem/enviar",
                    data: {
                        "_token": $('meta[name="token"]').attr('content'),
                        "processo": processo,
                        "msg": msg,
                        "tipo": 'cliente'
                    },
                    beforeSend: function() {
                        $('.msg_history_cliente').loader('show');
                    },
                    success: function(response) {
                        var m = '<div class="outgoing_msg">' +
                            '<div class="sent_msg">' +
                            '<p>' + msg + '</p>' +
                            '<span class="time_date">' + new Date().toLocaleString('pt-BR') + '</span>' +
                            '</div>' +
                            '</div>';

                        $(".msg_history_cliente").append(m);
                        $('.msg_history_cliente').loader('hide');
                        $('.msg_history_cliente').scrollTop($('.msg_history_cliente')[0].scrollHeight);
                        $("#texto_mensagem").val("").focus();
                    },
                    error: function(response) {
                        $('.msg_history_cliente').loader('hide');
                        alert('Erro ao enviar mensagem: ' + (response.responseJSON ? response.responseJSON.message : 'Tente novamente.'));
                    }
                });
            });



                $("#informarLink").click(function(){
                    $("#modalLink").modal('show');
                });

                $("#informarLinkAudiencia").click(function(){
                    $("#modalLinkAudiencia").modal('show');
                });

                $("#btnModalFinalizacao").click(function(){
                    var id_processo = $("#processo").val();
                    $.ajax(
                    {
                        url: "../../processo/"+id_processo+"/despesas",
                        type: 'GET',
                        success: function(response)
                        {                    
                            $('#modalFinalizacao .modal-dialog').loader('show');

                            despesas = JSON.parse(response);

                            $('#despesas_finalizar').empty();

                            var valor_total_despesas = 0;
                            despesas.forEach(function(despesa){
                                valor_total_despesas += parseFloat(despesa.vl_processo_despesa_pde) || 0 ;
                            });

                            if(despesas.length > 0 && valor_total_despesas > 0) {
                                despesas_tabela = "<a href='../../processos/despesas/"+$('#id_processo_encrypted').val()+"') }} ><h4 style='margin-bottom:5px'>Despesas</h4></a>"; 
                                despesas_tabela += "<table class='table'>";   
                                despesas_tabela += "<tr><th></th>"; 
                                despesas_tabela += "<th>Despesa</th>"; 
                                despesas_tabela += "<th>Valor</th></tr>";                             

                                despesas.forEach(function(despesa){
                                    
                                    if(despesa.vl_processo_despesa_pde){

                                        if(parseInt(despesa.cd_tipo_entidade_tpe) == 8)
                                            despesas_tabela += "<tr><td>Cliente</td>"; 
                                        
                                        if(parseInt(despesa.cd_tipo_entidade_tpe) == 6)
                                            despesas_tabela += "<tr><td>Correspondente</td>"; 

                                        despesas_tabela += "<td>"+despesa.tipo_despesa.nm_tipo_despesa_tds+"</td>"; 
                                        despesas_tabela += "<td>"+despesa.vl_processo_despesa_pde.replace('.',',')+"</td></tr>";
                                    }
                                });
                                despesas_tabela += "</table>";   

                                $('#despesas_finalizar').append(despesas_tabela);
                            } else {                  

                                despesas_aviso = "<div class='alert alert-info' role='alert'> <i style='color: red' class='fa fa-warning'></i><strong> Alerta!</strong> Não há <a href='../../processos/despesas/"+$('#id_processo_encrypted').val()+"') }} >despesas</a> cadastradas para o processo.</div>";              
                               
                                despesas_aviso += '<div class="checkbox"><label><input type="checkbox" class="checkbox" name="sem_despesas" id="sem_despesas"><span> Finalizar processo sem despesas cadastradas</span></label></div>';                               

                               $('#despesas_finalizar').append(despesas_aviso);

                            }
                            $('#modalFinalizacao .modal-dialog').loader('hide');
                        },
                        error: function(response)
                        {
                            

                        }
                    });

                    $("#modalFinalizacao").modal('show');
                });

                $("#cancelarProcesso").click(function(){
                    $("#cancelarProcessoForm").submit();
                });

                $(document).on("click", ".deleteFile", function () {



                });

                $('#filepicker_escritorio').filePicker({
                    url: '../../processos/arquivos-processo/escritorio',
                    ui: {
                        autoUpload: false
                    },
                    data: function(){
                        var _token = "{{ csrf_token() }}";
                        var id_processo = $("#processo").val();

                        return {
                            _token: _token,
                            id_processo: id_processo
                        }
                    },
                    plugins: ['ui', 'drop', 'camera', 'crop']
                })

                $('#filepicker_correspondente').filePicker({
                    url: '../../processos/arquivos-processo/correspondente',
                    ui: {
                        autoUpload: false
                    },
                    data: function(){
                        var _token = "{{ csrf_token() }}";
                        var id_processo = $("#processo").val();

                        return {
                            _token: _token,
                            id_processo: id_processo
                        }
                    },
                    plugins: ['ui', 'drop', 'camera', 'crop']
                })
                .on('done.filepicker', function (e, data) {

                    if(data.files[0].size){            

                        $.ajax({
                            url: "../../anexo-processo-add",
                            type: 'POST',
                            data: {
                                "_token": $('meta[name="token"]').attr('content'),
                                "id_processo": $("#processo").val(),
                                "nome_arquivo": data.files[0].name
                            },
                            success: function(response){   

                                $(".box-anexos-correspondente").removeClass('none');
                                $(".box-anexos-escritorio").removeClass('none');
                                $(".label-anexos-escritorio").addClass('none');

                            },
                            error: function(response){


                            }
                        });
                    }

                }).on('delete.filepicker', function (e, data) {
                    //Antes de excluir o arquivo, ele remove o registro do banco. Caso ocorra erro no banco, ele não exclui o arquivo e retorna false. Caso exclua do banco, mas não consiga remover o arquivo, ele recupera o arquivo no método deletedone
                    $.ajax({
                        url: '../../anexo-processo-delete',
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "_method": 'DELETE',
                            "id": $("#processo").val(),                    
                            "nome_arquivo": data.filename,
                            "_token": $('meta[name="token"]').attr('content'),
                        },
                        success: function(response)
                        {
                            location.reload();
                        },
                        error: function(response)
                        {
                            $(".fa").addClass("fa-times");
                            $(".msg_titulo").html("Erro");
                            $(".msg_mensagem").html("Erro ao excluir o arquivo");
                            $(".alert").addClass("alert-danger");
                            $(".alert").removeClass("none");

                            return false;
                        }
                    });

                })

                $('#filepicker').filePicker({
                    url: '../../processos/arquivos-processo',
                    ui: {
                        autoUpload: false
                    },
                    data: function(){
                        var _token = "{{ csrf_token() }}";
                        var id_processo = $("#processo").val();

                        return {
                            _token: _token,
                            id_processo: id_processo
                        }
                    },
                    plugins: ['ui', 'drop', 'camera', 'crop']
                })
                .on('done.filepicker', function (e, data) {

                    if(data.files[0].size){            

                        $.ajax({
                            url: "../../anexo-processo-add",
                            type: 'POST',
                            data: {
                                "_token": $('meta[name="token"]').attr('content'),
                                "id_processo": $("#processo").val(),
                                "nome_arquivo": data.files[0].name
                            },
                            success: function(response){   

                                $(".box-anexos-correspondente").removeClass('none');
                                $(".box-anexos-escritorio").removeClass('none');
                                $(".label-anexos-escritorio").addClass('none');

                            },
                            error: function(response){


                            }
                        });
                    }

                })
                .on('delete.filepicker', function (e, data) {
                    //Antes de excluir o arquivo, ele remove o registro do banco. Caso ocorra erro no banco, ele não exclui o arquivo e retorna false. Caso exclua do banco, mas não consiga remover o arquivo, ele recupera o arquivo no método deletedone
                    $.ajax({
                        url: '../../anexo-processo-delete',
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "_method": 'DELETE',
                            "id": $("#processo").val(),                    
                            "nome_arquivo": data.filename,
                            "_token": $('meta[name="token"]').attr('content'),
                        },
                        success: function(response)
                        {
                            location.reload();
                        },
                        error: function(response)
                        {
                            $(".fa").addClass("fa-times");
                            $(".msg_titulo").html("Erro");
                            $(".msg_mensagem").html("Erro ao excluir o arquivo");
                            $(".alert").addClass("alert-danger");
                            $(".alert").removeClass("none");

                            return false;
                        }
                    });

                })
                .on('fail.filepicker', function (e,data) {

                    console.log();

                    switch (data.xhr.status) {
                        case 413:
                        $(".msg_mensagem").html('<span class="text-danger">O arquivo excede o tamanho máximo permitido pelo sistema</span>');
                        break;
                        case 500:
                        $(".msg_mensagem").html('<span class="text-danger">'+response.responseJSON.message+'</span>');
                        break;
                        default:
                        $(".msg_mensagem").html('<span class="text-danger">Erro ao enviar o arquivo. Verifique tua conexão e tente novamente</span>');
                    }
                });

        //Reset do modal
        $('#modalFinalizacao').on('shown.bs.modal', function () {

            $("#box-envio-email").css( "display", 'none' );
            $("#fl_envio_arquivo").prop( "checked", false );
            $("#fl_enviar_todos").prop( "checked", false );

            $('.lista_arquivos').each(function () {
                $(this).prop( "checked", false );
            });

        });

        $("#fl_envio_arquivo").click( function(){

            if( $(this).is(':checked') ){
                $("#box-envio-email").css( "display", 'block' );
            }else{
                $("#box-envio-email").css( "display", 'none' );
            }
        });

        $("#fl_enviar_todos").click( function(){

            if( $(this).is(':checked') ){
                $('.lista_arquivos').each(function () {
                    $(this).prop( "checked", true );
                });
            }else{
                $('.lista_arquivos').each(function () {
                    $(this).prop( "checked", false );
                });
            }
        });
        

        

        $('#modalUpload').on('show.bs.modal', function () {
            $("#arquivo").empty();
            $("#poster").empty();
            $(".upload-msg").empty();
        });

        $("#fl_documento_representacao_pro").change(function(){

            processo = $("#processo").val();

            $.ajax(
            {
                url: "../../processos/atualiza/documento-representacao/"+processo,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function()
                {
                    $('.box-loader').loader('show');
                    $('.erro_atualiza_status').html('');
                },
                success: function(response)
                {                    
                    location.reload();
                },
                error: function(response)
                {
                    $('.box-loader').loader('hide');
                    $("#fl_documento_representacao_pro").prop('checked', false);
                    $('.erro_atualiza_status').html('<span>'+response.responseJSON.message+'</span>');

                }
            });

        });

        $("#fl_dados_enviados_pro").change(function(){

            processo = $("#processo").val();

            $.ajax(
            {
                url: "../../processos/atualiza/dados-enviados/"+processo,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function()
                {
                    $('.box-loader').loader('show');
                    $('.erro_atualiza_status').html('');
                },
                success: function(response)
                {                    
                    location.reload();
                },
                error: function(response)
                {
                    $('.box-loader').loader('hide');
                    $("#fl_dados_enviados_pro").prop('checked', false);
                    $('.erro_atualiza_status').html('<span>'+response.responseJSON.message+'</span>');

                }
            });

        });

        $("#fl_envio_anexos_pro").change(function(){

            processo = $("#processo").val();

            $.ajax(
            {
                url: "../../processos/atualiza/enviados/"+processo,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function()
                {
                    $('.box-loader').loader('show');
                    $('.erro_atualiza_status').html('');
                },
                success: function(response)
                {                    
                    location.reload();
                },
                error: function(response)
                {
                    $('.box-loader').loader('hide');
                    $("#fl_envio_anexos_pro").prop('checked', false);
                    $('.erro_atualiza_status').html('<span>'+response.responseJSON.message+'</span>');

                }
            });

        });

        $("#fl_recebimento_anexos_pro").change(function(){

            processo = $("#processo").val();

            $.ajax(
            {
                url: "../../processos/atualiza/recebidos/"+processo,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function()
                {
                    $('.box-loader').loader('show');
                    $('.erro_atualiza_status').html('');
                },
                success: function(response)
                {                    
                    location.reload();
                },
                error: function(response)
                {
                    $('.box-loader').loader('hide');
                    $("#fl_recebimento_anexos_pro").prop('checked', false);
                    $('.erro_atualiza_status').html('<span>'+response.responseJSON.message+'</span>');
                }
            });

        });

        $(".fl_envio_enter").change(function(){

            flag = $(this).is(':checked');   
            conta = $("#conta_logada").val();         

            $.ajax({

                type: "POST",
                url: "../../conta/configuracoes/flag_envio",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "conta": conta,
                    "flag": flag
                },
                beforeSend: function()
                {

                },
                success: function(response)
                {
                    //Formatando data
                    
                    //location.reload();
                },
                error: function(response)
                {

                    //location.reload();
                }
            });

        });

        

        $("#btn_requisitar_dados").click(function(){

            $("#requisitarPreposto").modal('hide');

        });

        $(document).on('click','.excluir_registro',function(){

            $(".msg_extra").html("");
            var id  = $(this).closest('tr').find('td[data-id]').data('id');
            var url = $(this).data('url');

            if(!id){
                id = $(this).data('id');
            }

            $("#modal_exclusao #url").val(url);
            $("#modal_exclusao #id_exclusao").val(id);
            $("#modal_exclusao").modal('show');
        });

    });

    function validate(formData, jqForm, options) {

        var form = jqForm[0];
        var fileExtension = ['exe', 'rar', 'php', 'js', 'zip'];

        if(!form.file.value) {
            $(".upload-msg").html('<span class="text-danger">Obrigatório selecionar um arquivo para envio</span>');
            return false;
        }

        if($.inArray(form.file.value.split('.').pop().toLowerCase(), fileExtension) != -1) {
            $(".upload-msg").html('<span class="text-danger">Formato do arquivo não permitido</span>');
            return false;
        }
    }

(function() {

    var validobj = $("#frm-finalizar-processo").validate({

        rules : {
            sem_despesas : {
                required: true,
            },                                       
        },
        messages : {
            sem_despesas : {
                required : 'Campo Obrigatório'
            },                                                                   
        },
        errorPlacement: function (error, element) {
            var elem = $(element);
            if(element.attr("name") == "sem_despesas"){
                error.appendTo( element.next("span") );
            } 
        },
                  
    });

    var bar = $('.progress-bar');
    var percent = $('.percent');
    var status = $('#status');

    $('#frm-anexo').ajaxForm({
        //beforeSubmit: validate,
        beforeSend: function(){
            $(".upload-msg").empty();
            status.empty();
            var percentVal = '0%';
            var posterValue = $('.btn-enviar-arquivo').fieldValue();
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        success: function(response) {
            var percentVal = 'Aguarde, estamos gravando o arquivo...';
            bar.width(percentVal)
            percent.html(percentVal);
            bar.width(0)
            percent.html("");
            $(".upload-msg").html('<span class="text-success">Arquivo enviado com sucesso, atualizando dados...</span>');
            location.reload();
        },
        error: function(response){

            bar.width(0);
            percent.html("");

            switch (response.status) {
                case 413:
                $(".upload-msg").html('<span class="text-danger">O arquivo excede o tamanho máximo permitido pelo sistema</span>');
                break;
                case 500:
                $(".upload-msg").html('<span class="text-danger">'+response.responseJSON.message+'</span>');
                break;
                default:
                $(".upload-msg").html('<span class="text-danger">Erro ao enviar o arquivo. Verifique tua conexão e tente novamente</span>');
            }
        },
        complete: function(xhr) {

        }
    });

})();
</script>

<script type="text/x-tmpl" id="uploadTemplate">
    <tr class="upload-template">
        <td class="column-name">
            <p class="name">{%= o.file.name %}</p>
            <span class="text-danger error">{%= o.file.error || '' %}</span>
        </td>
        <td>
            <p>{%= o.file.sizeFormatted || '' %}</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active"></div>
            </div>
        </td>
        <td style="font-size: 150%; text-align: center;">
            {% if (!o.file.autoUpload && !o.file.error) { %}
            <a href="#" class="action action-primary start" title="Enviar">
                <i class="fa fa-arrow-circle-o-up"></i>
            </a>
            {% } %}
            <a href="#" class="action action-warning cancel" title="Cancelar">
                <i class="fa fa-ban"></i>
            </a>
        </td>
    </tr>
</script>

<!-- Download Template -->
<script type="text/x-tmpl" id="downloadTemplate">
    {% o.timestamp = function (src) {
        return (src += (src.indexOf('?') > -1 ? '&' : '?') + new Date().getTime());
    }; %}
    <tr class="download-template">

        <td class="column-name">
            <div style="float: left; width: 8%; text-align: center;">
                <label class="text-default" style="margin-top: 8px;"><i class="fa fa-file-text-o fa-2x"></i></label>
            </div>
            <div style="float: left; width: 84%">

                <span>
                    {% if (o.file.url) { %}
                    <a href="{%= "../../processos/"+$("#processo").val()+"/anexo/"+o.file.url %}" data-id="{%= o.file.url %}" target="_blank">{%= o.file.name %}</a>
                    {% } else { %}
                    {%= o.file.name %}
                    {% } %}
                </span>
                <br/>

                {% if (o.file.time) { %}
                <time datetime="{%= o.file.timeISOString() %}">
                    {%= o.file.timeFormatted %}
                </time>
                {% } %}

                por

                {%= o.file.responsavel %}

                {% if (o.file.error) { %}
                <span class="text-danger">{%= o.file.error %}</span>
                {% } %}
            </div>
        </td>

        <td class="column-size center"><p>{%= o.file.sizeFormatted %}</p></td>

        {% if (o.file.flag_delete) { %}
            <td class="center">

                {% if (o.file.error) { %}
                <a href="#" class="action action-warning cancel" title="Cancelar">
                    <i class="fa fa-ban"></i>
                </a>
                {% } else { %}
                <a style="color: #cc0e00; font-size: 20px;" href="#" class="action action-danger delete deleteFile" title="Excluir">
                    <i class="fa fa-trash-o"></i>
                </a>
                {% } %}

            </td>
        {% } %}
    </tr>
</script>
<!-- Pagination Template -->
<script type="text/x-tmpl" id="paginationTemplate">
    {% if (o.lastPage > 1) { %}
    <ul class="pagination pagination-sm">
        <li {% if (o.currentPage === 1) { %} class="disabled" {% } %}>
            <a href="#!page={%= o.prevPage %}" data-page="{%= o.prevPage %}" title="Previous">&laquo;</a>
        </li>

        {% if (o.firstAdjacentPage > 1) { %}
        <li><a href="#!page=1" data-page="1">1</a></li>
        {% if (o.firstAdjacentPage > 2) { %}
        <li class="disabled"><a>...</a></li>
        {% } %}
        {% } %}

        {% for (var i = o.firstAdjacentPage; i <= o.lastAdjacentPage; i++) { %}
        <li {% if (o.currentPage === i) { %} class="active" {% } %}>
            <a href="#!page={%= i %}" data-page="{%= i %}">{%= i %}</a>
        </li>
        {% } %}

        {% if (o.lastAdjacentPage < o.lastPage) { %}
        {% if (o.lastAdjacentPage < o.lastPage - 1) { %}
        <li class="disabled"><a>...</a></li>
        {% } %}
        <li><a href="#!page={%= o.lastPage %}" data-page="{%= o.lastPage %}">{%= o.lastPage %}</a></li>
        {% } %}

        <li {% if (o.currentPage === o.lastPage) { %} class="disabled" {% } %}>
            <a href="#!page={%= o.nextPage %}" data-page="{%= o.nextPage %}" title="Next">&raquo</a>
        </li>
    </ul>
    {% } %}
</script><!-- end of #paginationTemplate -->
@endsection