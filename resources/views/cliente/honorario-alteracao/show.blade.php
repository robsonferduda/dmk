@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('cliente/honorario-alteracoes') }}">Alteração de Honorário</a></li>
        <li>Detalhe do Pedido</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs hidden-sm col-sm-12 col-md-6 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i> Pedido de Alteração de Honorário
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7 box-button">
            <div class="boxBtnTopo sub-box-button">
                <a href="{{ url('cliente/honorario-alteracoes') }}" class="btn btn-default pull-right">
                    <i class="fa fa-list"></i> Meus Pedidos
                </a>
                @if($alteracao->processo)
                    <a href="{{ url('cliente/processos/acompanhamento/'.safe_encrypt($alteracao->processo->cd_processo_pro)) }}"
                       class="btn btn-default pull-right">
                        <i class="fa fa-file-text-o"></i> Ver Processo
                    </a>
                @endif
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
                        <span class="widget-icon"><i class="fa fa-money"></i></span>
                        <h2>Detalhes do Pedido</h2>
                    </header>
                    <div class="col-sm-12" style="padding: 20px;">

                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend><i class="fa fa-file-text-o"></i> <strong>Dados do Processo</strong></legend>
                                    <ul class="list-unstyled" style="line-height: 2;">
                                        <li>
                                            <strong>Processo:</strong>
                                            {{ $alteracao->processo->nu_processo_pro ?? '—' }}
                                        </li>
                                        <li>
                                            <strong>Tipo de Serviço:</strong>
                                            {{ optional(optional(optional($alteracao->processo)->honorario)->tipoServico)->nm_tipo_servico_tse ?? '—' }}
                                        </li>
                                        <li>
                                            <strong>Solicitado em:</strong>
                                            {{ $alteracao->created_at->format('d/m/Y H:i') }}
                                        </li>
                                        <li>
                                            <strong>Situação:</strong>
                                            @if($alteracao->isPendente())
                                                <span class="label label-warning">Pendente — aguardando análise do escritório</span>
                                            @elseif($alteracao->isAprovado())
                                                <span class="label label-success">Aprovado</span>
                                            @else
                                                <span class="label label-danger">Reprovado</span>
                                            @endif
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>
                                    <legend><i class="fa fa-money"></i> <strong>Valores</strong></legend>
                                    <ul class="list-unstyled" style="line-height: 2;">
                                        <li>
                                            <strong>Valor Atual (no processo):</strong>
                                            R$ {{ number_format($alteracao->nu_valor_antigo_tha, 2, ',', '.') }}
                                        </li>
                                        <li>
                                            <strong>Valor Proposto por você:</strong>
                                            <span class="text-primary" style="font-size: 16px; font-weight: bold;">
                                                R$ {{ number_format($alteracao->nu_valor_novo_tha, 2, ',', '.') }}
                                            </span>
                                        </li>
                                    </ul>
                                </fieldset>
                            </div>
                        </div>

                        @if($alteracao->isAprovado())
                            <hr>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>Pedido aprovado!</strong> O valor do honorário foi atualizado conforme sua solicitação.
                            </div>
                        @elseif($alteracao->isReprovado())
                            <hr>
                            <div class="alert alert-danger">
                                <i class="fa fa-times-circle"></i>
                                <strong>Pedido reprovado.</strong> O escritório não aprovou esta alteração. Entre em contato para mais informações.
                            </div>
                        @else
                            <hr>
                            <div class="alert alert-warning">
                                <i class="fa fa-clock-o"></i>
                                <strong>Aguardando análise.</strong> Seu pedido está sendo analisado pelo escritório.
                            </div>
                        @endif

                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
