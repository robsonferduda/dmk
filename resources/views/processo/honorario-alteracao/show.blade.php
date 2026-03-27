@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('processos') }}">Processos</a></li>
        <li><a href="{{ url('processos/honorario-alteracao') }}">Pedidos de Alteração</a></li>
        <li>Analisar Pedido</li>
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
                <a href="{{ url('processos/honorario-alteracao') }}" class="btn btn-default pull-right">
                    <i class="fa fa-list"></i> Listar Pedidos
                </a>
                @if($alteracao->processo)
                    <a href="{{ url('processos/acompanhamento/'.safe_encrypt($alteracao->processo->cd_processo_pro)) }}"
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
                                            {{ optional($alteracao->processo?->honorario?->tipoServico)->nm_tipo_servico_tse ?? '—' }}
                                        </li>
                                        <li>
                                            <strong>Solicitado em:</strong>
                                            {{ $alteracao->created_at->format('d/m/Y H:i') }}
                                        </li>
                                        <li>
                                            <strong>Situação:</strong>
                                            @if($alteracao->isPendente())
                                                <span class="label label-warning">Pendente</span>
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
                                            <strong>Valor Proposto pelo Cliente:</strong>
                                            <span class="text-primary" style="font-size: 16px; font-weight: bold;">
                                                R$ {{ number_format($alteracao->nu_valor_novo_tha, 2, ',', '.') }}
                                            </span>
                                        </li>
                                        @if($alteracao->taxaHonorario)
                                            <li>
                                                <strong>Valor Base Atual (tabela):</strong>
                                                R$ {{ number_format($alteracao->taxaHonorario->nu_taxa_the, 2, ',', '.') }}
                                            </li>
                                        @endif
                                    </ul>
                                </fieldset>
                            </div>
                        </div>

                        @if($alteracao->isPendente())
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><strong>Ação</strong></h5>

                                    {{-- Reprovar --}}
                                    <form action="{{ url('processos/honorario-alteracao/'.$alteracao->cd_taxa_honorario_alteracao_tha.'/reprovar') }}"
                                          method="POST" style="display: inline-block; margin-right: 10px;">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Confirma a reprovação deste pedido?')">
                                            <i class="fa fa-times"></i> Reprovar
                                        </button>
                                    </form>

                                    {{-- Aprovar --}}
                                    <form action="{{ url('processos/honorario-alteracao/'.$alteracao->cd_taxa_honorario_alteracao_tha.'/aprovar') }}"
                                          method="POST" style="display: inline-block;">
                                        {{ csrf_field() }}

                                        <div class="well" style="display: inline-block; padding: 10px 15px; margin-bottom: 0; vertical-align: top;">
                                            <p><strong>Ao aprovar, selecione o que deseja atualizar:</strong></p>

                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="atualizar_processo_origem" value="1" checked>
                                                    Atualizar o processo que originou este pedido
                                                    @if($alteracao->processo)
                                                        <small class="text-muted">({{ $alteracao->processo->nu_processo_pro }})</small>
                                                    @endif
                                                </label>
                                            </div>

                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="atualizar_processos_futuros" value="1">
                                                    Atualizar demais processos futuros com o mesmo tipo de serviço
                                                    <small class="text-muted">(processos com prazo fatal a partir de hoje, não finalizados)</small>
                                                </label>
                                            </div>

                                            <button type="submit" class="btn btn-success"
                                                    onclick="return confirm('Confirma a aprovação deste pedido?')"
                                                    style="margin-top: 8px;">
                                                <i class="fa fa-check"></i> Aprovar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
