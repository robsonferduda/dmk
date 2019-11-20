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
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Acompanhamento </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 boxBtnTopo">

            @role('administrator|colaborador')

                <a title="Listar Processos" href="{{ url('processos/acompanhamento') }}" style="margin-right: 15px;" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Acompanhamentos </a> 
                <a title="Relatório" class="btn btn-default pull-right header-btn " href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-usd fa-lg"></i> Relatório</a>
                <a title="Editar" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
                <a title="Despesas" class="btn btn-warning pull-right header-btn" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i> Despesas</a>
           
            @endrole

            @role('correspondente')

                <form class="pull-right" style="display: inline; float: left; margin-right: 10px; margin-top: 17px;" action="{{ url('processo/atualizar-status') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                    <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::FINALIZADO_CORRESPONDENTE }}">     
                    <button class="btn btn-success" type="submit"><i class="fa fa-check"></i> Finalizar Processo</button>
                </form>

                <form class="pull-right" style="display: inline; float: left; margin-right: 10px; margin-top: 17px;" action="{{ url('processo/atualizar-status') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                    <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::RECUSADO_CORRESPONDENTE }}">     
                    <button class="btn btn-warning" type="submit"><i class="fa fa-ban"></i> Recusar Processo</button>
                </form>

            @endrole
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
                    <div style="float: left; margin-right: 10px;">
                        <form action="{{ url('processo/atualizar-status') }}" class="form-inline" method="POST">
                            {{ csrf_field() }}
                            
                                
                                    <div style="float: left; width: 300px; margin-right: 10px;">
                                        <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">
                                        <label class="label label-black" >Selecione um Status para o Processo</label>          
                                        <select id="status" name="status" class="select2">
                                            <option selected value="0">Selecione uma situação</option>
                                            @foreach(App\StatusProcesso::orderBy('nm_status_processo_conta_stp')->get() as $status)
                                                <option value="{{ $status['cd_status_processo_stp'] }}" {{ ($processo->cd_status_processo_stp == $status['cd_status_processo_stp']) ? 'selected' : '' }} >{{ $status['nm_status_processo_conta_stp'] }}</option>
                                            @endforeach
                                        </select> 
                                    </div> 
                                    <div  style="float: left; ">
                                        <div style="" >
                                            <button class="btn btn-primary marginTop17" type="submit"><i class="fa fa-refresh"></i> Alterar Status</button>
                                        </div>                                     
                                    </div>                                 
                            
                        </form>
                    </div>

                    <form style="display: inline; float: left; margin-top: 17px;"  action="{{ url('processo/atualizar-status') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" id="processo" name="processo" value="{{ $processo->cd_processo_pro }}">  
                        <input type="hidden" id="status_cancelamento" name="status" value="{{ App\Enums\StatusProcesso::CANCELADO }}">     
                        <button class="btn btn-danger" type="submit"><i class="fa fa-ban"></i> Cancelar Processo</button>
                    </form>

                    <a class="btn btn-success marginTop17" style="margin-left: 10px;" href="#" data-toggle="modal" data-target="#modalFinalizacao"><i class="fa fa-check"></i> Finalizar Processo</a>

                    <a class="btn btn-default marginTop17" style="margin-left: 10px;" href="{{ url('processos/notificar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-send-o"></i> Notificar Correspondente</a>          
                    
                    <div style="clear: both;"></div>
                </div>
                @endrole

                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Dados do Processo </h2>             
                    </header>                
                        <div class="col-md-12 box-loader">
                            <div class="col-md-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-file-text-o"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        <input type="hidden" name="conta_logada" id="conta_logada" value="{{ Auth::user()->cd_conta_con }}">
                                        <input type="hidden" name="processo" id="processo" value="{{ $processo->cd_processo_pro }}">
                                        <input type="hidden" name="msg_correspondente" id="msg_correspondente" value="{{ $processo->cd_correspondente_cor }}">
                                        <p>
                                            <ul class="list-unstyled" style=" line-height: 1.5;">
                                           
                                                <li>
                                                    <strong>Nº Processo: </strong> <a href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                                </li>
                                                @role('administrator|colaborador') 
                                                    <li>
                                                        <strong>Cliente: </strong><a href="{{'../../cliente/detalhes/'.$processo->cliente->cd_cliente_cli}}">{{ $processo->cliente->nm_fantasia_cli ? :  $processo->cliente->nm_razao_social_cli }}</a> 
                                                    </li>
                                                @endrole
                                                <li>
                                                    <strong>Nº Externo: </strong>  {{ !empty($processo->nu_acompanhamento_pro) ? $processo->nu_acompanhamento_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Processo: </strong> {{ !empty($processo->tipoProcesso->nm_tipo_processo_tpo) ? $processo->tipoProcesso->nm_tipo_processo_tpo : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Serviço Cliente: </strong> {{ !empty($processo->honorario->tipoServico) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : 'Não informado' }}
                                                </li> 
                                                @role('administrator|colaborador')
                                                <li>
                                                    <strong>Valor do Cliente: </strong> {{ !empty($processo->honorario) ? str_replace('.',',',$processo->honorario->vl_taxa_honorario_cliente_pth) : 'Não informado' }}
                                                </li>   
                                                <li>
                                                    <strong>Valor Nota Fiscal do Cliente: </strong> {{ !empty($processo->honorario) ? str_replace('.',',',$processo->honorario->vl_taxa_cliente_pth) : 'Não informado' }}
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
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : 'Não informado' }}
                                                </li>                                                
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : 'Não informada' }}
                                                </li>
                                                @role('administrator|colaborador')
                                                    <li>
                                                        <strong>Correspondente: </strong> 
                                                        @if(!empty($processo->correspondente->contaCorrespondente))
                                                            <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">{{$processo->correspondente->load('contaCorrespondente')->contaCorrespondente->nm_conta_correspondente_ccr}}</a>
                                                        @endif
                                                    </li> 
                                                @endrole    
                                                <li>
                                                    <strong>Tipo de Serviço Correspondente: </strong> {{ !empty($processo->honorario->tipoServicoCorrespondente) ? $processo->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse : 'Não informado' }}
                                                </li> 
                                                <li>
                                                    <strong>Valor do Correspondente: </strong> {{ !empty($processo->honorario->tipoServicoCorrespondente) ? str_replace('.',',',$processo->honorario->vl_taxa_honorario_correspondente_pth) : 'Não informado' }}
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
                                        </p> 
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-files-o"></i> <strong>Arquivos do Processo</strong><span class="btn-upload" data-toggle="modal" data-target="#modalUpload"><i class="fa fa-plus-circle"></i> Novo </span></legend>
                                    @foreach($processo->anexos as $anexo)
                                        
                                            <div class="row" style="width:100%; background-color: #fff; margin-bottom: 10px; border-bottom: 1px solid #eaeaea;">
                                                <div style="float: left; width: 8%; text-align: center;">
                                                    <label class="text-default" style="margin-top: 8px;"><i class="fa fa-file-text-o fa-2x"></i></label>
                                                </div>
                                                <div style="float: left; width: 84%">
                                                    <h4><a href="{{ url('files/'.$anexo->cd_anexo_processo_apr) }}">{{ $anexo->nm_anexo_processo_apr }}</a></h4>
                                                    <h6 style="margin: 0px; font-weight: 200;"><strong>{{ date('d/m/Y H:i:s', strtotime($anexo->created_at)) }}</strong> por <strong>{{ $anexo->entidade->usuario->name }}</strong></h6>   
                                                </div>
                                                <div style="float: left; width: 8% text-align: center;">
                                                    <label class="text-danger" style="margin-top: 8px; cursor: pointer;"><i title="Excluir" data-id="{{ $anexo->cd_anexo_processo_apr }}" data-url="../../files/" class="fa fa-trash fa-2x pull-right excluir_registro"></i></label>
                                                </div>    
                                            </div>
                                        
                                    @endforeach
                                </fieldset>
                                @role('administrator|colaborador')
                                    <section>                          
                                        <div class="onoffswitch-container">
                                            <span class="onoffswitch-title">Todos os documentos para a realização do ato foram anexados?</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" {{ ($processo->fl_envio_anexos_pro == 'S') ? 'checked' : '' }} name="fl_envio_anexos_pro" class="onoffswitch-checkbox" id="fl_envio_anexos_pro">
                                                <label class="onoffswitch-label" for="fl_envio_anexos_pro"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>
                                    </section>
                                @endrole
                                @role('correspondente') 
                                    <section>                          
                                        <div class="onoffswitch-container">
                                            <span class="onoffswitch-title">Confirmo o recebimento dos documentos e a realização do ato contratado?</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" {{ ($processo->fl_recebimento_anexos_pro == 'S') ? 'checked' : '' }} name="fl_recebimento_anexos_pro" class="onoffswitch-checkbox" id="fl_recebimento_anexos_pro">
                                                <label class="onoffswitch-label" for="fl_recebimento_anexos_pro"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>
                                    </section>
                                @endrole
                                <section> 
                                    <div class="erro_atualiza_status" style="padding: 5px 6px; color: #cc1d1d;">
                                        
                                    </div>
                                </section>
                            </div>
                        </div>             
                </div>
            </article>
            
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="well">
                    <div class="col-sm-12 col-md-6">

                        @if(Session::get('SESSION_NIVEL') != 3)
                            <h4><i class="fa fa-envelope marginBottom5"></i> Histórico de Mensagens Correspondente</h4>
                        @else
                            <h4><i class="fa fa-envelope marginBottom5"></i> Histórico de Mensagens</h4>
                        @endif

                        <div class="messaging">
                            <div class="inbox_msg">
                                <div class="mesgs">
                                    <div class="msg_history msg_history_externo">

                                        @if(count($mensagens_externas) > 0)
                                            @foreach($mensagens_externas as $mensagem)                                                

                                                    @if($mensagem->remetente_prm == Auth::user()->cd_conta_con)
                                                        
                                                        <div class="outgoing_msg">
                                                          <div class="sent_msg">
                                                            @if($mensagem->deleted_at)
                                                                <p style="background: #e8e7e7 !important; color: #686868;">
                                                                    Mensagem excluída
                                                                </p>
                                                            @else
                                                                <p>
                                                                    {{ $mensagem->texto_mensagem_prm }}
                                                                </p>
                                                                <span class="time_date">
                                                                    <a href="#" data-url="{{ url('processo/mensagem/excluir/'.\Crypt::encrypt($mensagem->cd_processo_mensagem_prm)) }}" class="excluir_registro"><i class="fa fa-trash"></i> Excluir</a>
                                                                    {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        </div>
                                                        
                                                    @else
                                                         <div class="incoming_msg">
                                                            <div class="incoming_msg_img">                                                                

                                                                @if(file_exists('public/img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png')) 
                                                                    <img class="img_msg" src="{{ asset('img/users/ent'.$mensagem->entidadeRemetente->entidade->cd_entidade_ete.'.png') }}" alt="user_profile"> 
                                                                @else
                                                                    <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="user_profile"> 
                                                                @endif
                                                            </div>
                                                            <div class="received_msg">
                                                                <div class="received_withd_msg">
                                                                    @if($mensagem->deleted_at)
                                                                        <p style="background: #e8e7e7 !important; color: #686868;">
                                                                            Mensagem excluída
                                                                        </p>
                                                                    @else
                                                                        <p>
                                                                            {{ $mensagem->texto_mensagem_prm }}
                                                                        </p>
                                                                        <span class="time_date">
                                                                            <strong>
                                                                                @if($mensagem->entidadeRemetenteColaborador)
                                                                                    {{ $mensagem->entidadeRemetenteColaborador->usuario->name }}
                                                                                @else
                                                                                    {{ $mensagem->entidadeRemetente->nm_razao_social_con }}
                                                                                @endif                                                                               
                                                                            </strong>
                                                                            disse em 
                                                                            {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                               
                                           @endforeach
                                        @else

                                            <div class="outgoing_msg">
                                                <div class="sent_msg">
                                                <p>Nenhum histórico de mensagens</p>
                                                <span class="time_date"></span> </div>
                                            </div>

                                        @endif

                                    </div>

                                    <div class="checkbox">
                                      
                                        @if(\App\Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first()->fl_envio_enter_con == 'S')
                                            <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter" id="fl_envio_enter" value="S" checked="checked">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                                        @else
                                            <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter" id="fl_envio_enter" value="S">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                                        @endif
                                    </div>

                                    <div class="type_msg">
                                        <div class="input_msg_write">
                                            <textarea id="texto_mensagem" rows="3" class="write_msg" placeholder="Escrever mensagem"></textarea>                                         
                                            <button class="msg_send_btn msg_send_externo" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    
                    @if(Session::get('SESSION_NIVEL') != 3)
                        <div class="col-sm-12 col-md-6">
                            <h4><i class="fa fa-envelope marginBottom5"></i> Histórico de Mensagens Escritório</h4>
                            <div class="messaging">
                                <div class="inbox_msg">
                                    <div class="mesgs">
                                        <div class="msg_history msg_history_interno">

                                            @if(count($mensagens_internas) > 0)
                                                @foreach($mensagens_internas as $mensagem)                                                

                                                    
                                                        @if($mensagem->remetente_prm == Auth::user()->cd_conta_con or $mensagem->remetente_prm == Session::get('SESSION_CD_ENTIDADE'))
                                                            
                                                            <div class="outgoing_msg">
                                                                <div class="sent_msg">
                                                                    @if($mensagem->deleted_at)
                                                                        <p style="background: #e8e7e7 !important; color: #686868;">
                                                                            Mensagem excluída
                                                                        </p>
                                                                    @else
                                                                        <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                                        <span class="time_date">
                                                                            <a href="#" data-url="{{ url('processo/mensagem/excluir/'.\Crypt::encrypt($mensagem->cd_processo_mensagem_prm)) }}" class="excluir_registro"><i class="fa fa-trash"></i> Excluir</a>
                                                                            {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}
                                                                        </span> 
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                        @else
                                                           
                                                                 <div class="incoming_msg">
                                                                    <div class="incoming_msg_img">                                                            
                                                                        @if(file_exists('public/img/users/ent'.$mensagem->entidadeInterna->cd_entidade_ete.'.png')) 
                                                                            <img class="img_msg" src="{{ asset('img/users/ent'.$mensagem->entidadeInterna->cd_entidade_ete.'.png') }}" alt="user_profile"> 
                                                                        @else
                                                                            <img class="img_msg" src="{{ asset('img/users/user.png') }}" alt="user_profile"> 
                                                                        @endif
                                                                    </div>
                                                                    <div class="received_msg">
                                                                        <div class="received_withd_msg">
                                                                            @if($mensagem->deleted_at)
                                                                                <p style="background: #e8e7e7 !important; color: #686868;">
                                                                                    Mensagem excluída
                                                                                </p>
                                                                            @else
                                                                                <p>{{ $mensagem->texto_mensagem_prm }}</p>
                                                                                <span class="time_date"><strong>{{ $mensagem->entidadeInterna->usuario->name }}</strong> disse em {{ date('d/m/Y H:i:s', strtotime($mensagem->created_at)) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            
                                                        @endif
                                                    
                                                   
                                               @endforeach
                                            @else

                                                <div class="outgoing_msg">
                                                    <div class="sent_msg">
                                                    <p>Nenhum histórico de mensagens</p>
                                                    <span class="time_date"></span> </div>
                                                </div>

                                            @endif
                                           
                                        </div>
                                        <div class="checkbox">
                                          
                                            @if(\App\Conta::where('cd_conta_con',Auth::user()->cd_conta_con)->first()->fl_envio_enter_con == 'S')
                                                <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_interno" id="fl_envio_enter_interno" value="S" checked="checked">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                                            @else
                                                <label><input type="checkbox" class="fl_envio_enter" name="fl_envio_enter_interno" id="fl_envio_enter_interno" value="S">Enviar as mensagens apertando a tecla <strong>Enter</strong></label>
                                            @endif
                                        </div>
                                        <div class="type_msg">
                                            <div class="input_msg_write">
                                                <textarea id="texto_mensagem_interno" rows="3" class="write_msg" placeholder="Escrever mensagem"></textarea>  
                                            <button class="msg_send_btn msg_send_interno" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div style="clear: both;"></div>
                </div>
            </article>
            
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalUpload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="frm-anexo" action="{{ url('file-upload') }}" enctype="multipart/form-data">
            @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-files-o"></i> Adicionar Arquivos</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 upload-msg marginBottom5"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id_processo" id="id_processo" value="{{ $processo->cd_processo_pro }}">
                            <div class="well" style="padding: 5px;">
                            <p>
                                <h5 class="center" style="margin-bottom: 5px;"><strong class="text-info">Instruções sobre os arquivos</strong></h5>
                                <h4 style="font-size: 15px; margin-bottom: 5px;"><strong>Quantidade de arquivos</strong>: Você pode enviar quantos arquivos desejar, basta clicar em "Escolher arquivos" e selecionar os arquvos que deseja.</h4>
                                <h4 style="font-size: 15px; margin-bottom: 5px;"><strong>Tamanho dos arquivos</strong>: O sistema limita o envio em 40MB por arquivo.</h4>
                                <h4 style="font-size: 15px; margin-bottom: 5px;"><strong>Formato dos arquivos</strong>: São permitidos arquivos de imagens (png, jpg, jpeg), mídias (mp3), textos (doc, docx, pdf, txt) e planilhas (xls, xlsx, csv).</h4>
                                <h5 class="center"><strong class="text-danger">O tamanho e quantidade dos arquivos interfere diretamente no tempo de envio dos mesmos.</strong></h5>
                            </p>
                        </div>
                            <div class="form-group">
                                <input name="file[]" id="poster" type="file" multiple class="form-control"><br/>
                                <div class="progress progress-striped active">
                                    <div class="progress-bar bg-color-darken" role="progressbar" style="width: 0%"><span class="percent"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-success btn-enviar-arquivo"><i class="fa fa-upload"></i> Enviar Arquivo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="modalFinalizacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="frm-anexo" action="{{ url('processo/finalizar-processo') }}" method="POST">
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
                                    <label><strong>Email de Envio</strong> <a href="{{'../../cliente/editar/'.$processo->cliente->cd_cliente_cli}}"><i class="fa fa-plus-circle"></i> Novo</a> </label>
                                    <input class="form-control" disabled="disabled" placeholder="Email" type="text" value="{{ ($processo->cliente->entidade->getEmailsNotificacao()) ? $processo->cliente->entidade->getEmailsNotificacao() : 'Nenhum email informado' }}">
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
                                                    <input type="checkbox" name="lista_arquivos[]" class="lista_arquivos" value="{{ $anexo->nm_local_anexo_processo_apr }}">
                                                </label>
                                            </div>
                                            <div style="float: left; width: 92%">
                                                <h4>{{ $anexo->nm_anexo_processo_apr }}</h4>
                                                <h6 style="margin: 0px; font-weight: 200;"><strong>{{ date('d/m/Y H:i:s', strtotime($anexo->created_at)) }}</strong> por <strong>{{ $anexo->entidade->usuario->name }}</strong></h6>   
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

@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

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
        

        //Posiciona a lista de mensagens na última mensagem enviada
        $('.msg_history').scrollTop($('.msg_history')[0].scrollHeight);

        //Verifica se a oção de envio com Enter está ativada
        var callback = function(e){
                
            var text = e.type;
            var code = e.which ? e.which : e.keyCode;
            if(13 === code){

                flag = $('#fl_envio_enter').is(':checked'); 

                if(flag){
                    $('.msg_send_externo').trigger('click');
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

        $('#texto_mensagem_interno').keypress(icallback);   

        $('#modalUpload').on('show.bs.modal', function () {
            $("#arquivo").empty();
            $("#poster").empty();
            $(".upload-msg").empty();
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
                    $("#atualiza_status").modal('show');
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
                    $('.erro_atualiza_status').html('<span>Houve um erro ao atualizar o status do processo</span>');
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

        $('.msg_send_externo').click(function(){

            processo = $("#processo").val();
            correspondente = $("#msg_correspondente").val();
            conta = $("#conta").val();
            msg = $("#texto_mensagem").val();

            $.ajax(
            {
                type: "POST",
                url: "../../processo/mensagem/enviar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "processo": processo,
                    "conta": conta,
                    "correspondente": correspondente,
                    "msg": msg,
                    "tipo": 'externo'
                },
                beforeSend: function()
                {
                    $('.msg_history_externo').loader('show');
                },
                success: function(response)
                {
                    //Formatando data
                    data = response.objeto.created_at.split(' ');
                    dt_msg = data[0].split('-').reverse().join('/')+' '+data[1];

                    var m = '<div class="outgoing_msg">'+
                                '<div class="sent_msg">' +
                                    '<p>'+response.objeto.texto_mensagem_prm+'</p>'+
                                    '<span class="time_date">'+
                                    '<a href="#" data-url="../../processo/mensagem/excluir/'+response.id+'" class="excluir_registro"><i class="fa fa-trash"></i> Excluir</a> '+dt_msg+'</span>'+
                                '</div>'+
                            '</div>';

                    $(".msg_history_externo").append(m);

                    $('.msg_history_externo').loader('hide');
                    
                    $(".msg_history_externo").append('');
                    $('.msg_history_externo').scrollTop($('.msg_history_externo')[0].scrollHeight);
                    $("#texto_mensagem").val("");
                    $("#texto_mensagem").focus();
                    //location.reload();
                },
                error: function(response)
                {
                    $('.msg_history_externo').loader('hide');
                    $("#erro_envio_mensagem").modal('show');
                }
            });

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

        $('.msg_send_interno').click(function(){

            processo = $("#processo").val();
            correspondente = $("#msg_correspondente").val();
            conta = $("#conta").val();
            msg = $("#texto_mensagem_interno").val();

            $.ajax(
            {
                type: "POST",
                url: "../../processo/mensagem/enviar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "processo": processo,
                    "conta": conta,
                    "correspondente": correspondente,
                    "msg": msg,
                    "tipo": 'interna'
                },
                beforeSend: function()
                {
                    $('.msg_history_interno').loader('show');
                },
                success: function(response)
                {
                    //Formatando data
                    data = response.objeto.created_at.split(' ');
                    dt_msg = data[0].split('-').reverse().join('/')+' '+data[1];

                    var m = '<div class="outgoing_msg">'+
                                '<div class="sent_msg">' +
                                    '<p>'+response.objeto.texto_mensagem_prm+'</p>'+
                                    '<span class="time_date">'+
                                    '<a href="#" data-url="../../processo/mensagem/excluir/'+response.id+'" class="excluir_registro"><i class="fa fa-trash"></i> Excluir</a> '+dt_msg+'</span>'+
                                '</div>'+
                            '</div>';

                    $(".msg_history_interno").append(m);

                    $('.msg_history_interno').loader('hide');
                    
                    $(".msg_history_interno").append('');
                    $('.msg_history_interno').scrollTop($('.msg_history_interno')[0].scrollHeight);
                    $("#texto_mensagem_interno").val("");
                    $("#texto_mensagem_interno").focus();
                    //location.reload();
                },
                error: function(response)
                {
                    $('.msg_history_interno').loader('hide');
                    //location.reload();
                }
            });

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
@endsection