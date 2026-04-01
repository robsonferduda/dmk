@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Meus Pedidos de Alteração de Honorário</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs hidden-sm col-sm-12 col-md-6 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i> Processos <span>> Alteração de Honorário</span>
            </h1>
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
                        <h2>Meus Pedidos de Alteração de Honorário</h2>
                    </header>
                    <div class="col-sm-12" style="padding: 15px;">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Tipo de Serviço</th>
                                    <th>Valor Atual</th>
                                    <th>Valor Proposto</th>
                                    <th>Solicitado em</th>
                                    <th>Situação</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alteracoes as $alteracao)
                                    <tr>
                                        <td>
                                            @if($alteracao->processo)
                                                <a href="{{ url('cliente/processos/acompanhamento/'.safe_encrypt($alteracao->processo->cd_processo_pro)) }}">
                                                    {{ $alteracao->processo->nu_processo_pro }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional(optional(optional($alteracao->processo)->honorario)->tipoServico)->nm_tipo_servico_tse ?? '—' }}
                                        </td>
                                        <td>R$ {{ number_format($alteracao->nu_valor_antigo_tha, 2, ',', '.') }}</td>
                                        <td><strong>R$ {{ number_format($alteracao->nu_valor_novo_tha, 2, ',', '.') }}</strong></td>
                                        <td>{{ $alteracao->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($alteracao->isPendente())
                                                <span class="label label-warning">Pendente</span>
                                            @elseif($alteracao->isAprovado())
                                                <span class="label label-success">Aprovado</span>
                                            @else
                                                <span class="label label-danger">Reprovado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('cliente/honorario-alteracoes/'.$alteracao->cd_taxa_honorario_alteracao_tha) }}"
                                               class="btn btn-default btn-xs">
                                                <i class="fa fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Nenhum pedido de alteração encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $alteracoes->links() }}
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
