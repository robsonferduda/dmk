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
                                                <li>
                                                    <strong>Código Cliente: </strong>  {{ !empty($processo->nu_acompanhamento_pro) ? $processo->nu_acompanhamento_pro : 'Não informado' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Processo: </strong> {{ !empty($processo->tipoProcesso->nm_tipo_processo_tpo) ? $processo->tipoProcesso->nm_tipo_processo_tpo : ' ' }}
                                                </li>
                                                <li>
                                                    <strong>Tipo de Serviço Cliente: </strong> {{ !empty($processo->honorario and $processo->honorario->tipoServico) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : ' ' }}
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
                                                    <strong>Estado: </strong> {{ !empty($processo->cidade->estado->nm_estado_est) ? $processo->cidade->estado->nm_estado_est : ' ' }}
                                                </li> 
                                                <li>
                                                    <strong>Cidade: </strong> {{ !empty($processo->cidade->nm_cidade_cde) ? $processo->cidade->nm_cidade_cde : ' ' }}
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
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection

@if(!empty($processo->honorario))
@section('modal')
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
@endsection
@endif