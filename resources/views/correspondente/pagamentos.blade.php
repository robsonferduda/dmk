@extends('layouts.admin')
@section('content')
@php
    $cores    = [1=>'default',2=>'warning',3=>'info',4=>'success'];
    $icones   = [1=>'circle-o',2=>'paper-plane',3=>'check',4=>'dollar'];
    $mesPad   = str_pad($mes, 2, '0', STR_PAD_LEFT);
    $mesAnoFmt = $mesPad . '/' . $ano;
@endphp
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Pagamentos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i> Correspondentes <span> > Pagamentos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <form method="POST" action="{{ url('correspondente/pagamentos/consolidar') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <button type="submit" class="btn btn-primary pull-right header-btn"
                        onclick="return confirm('Consolidar pagamentos para {{ $mesAnoFmt }}?')">
                    <i class="fa fa-refresh"></i> Consolidar Mês
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>

    {{-- Navegação por mês --}}
    <div class="row" style="margin-bottom:15px;">
        <div class="col-md-12">
            <div class="btn-group">
                @foreach($mesesNavegacao as $m)
                    @php $label = str_pad($m['mes'],2,'0',STR_PAD_LEFT).'/'.$m['ano']; @endphp
                    <a href="{{ url('correspondente/pagamentos?mes='.$m['mes'].'&ano='.$m['ano']) }}"
                       class="btn btn-sm {{ ($m['mes'] == $mes && $m['ano'] == $ano) ? 'btn-primary' : 'btn-default' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <span class="text-muted" style="margin-left:10px;">
                Exibindo: <strong>{{ $mesAnoFmt }}</strong>
            </span>
        </div>
    </div>

    {{-- Resumo por status --}}
    <div class="row" style="margin-bottom:15px;">
        @foreach($statusLabels as $cd => $nm)
        @php $qtd = $pagamentos->where('cd_status_pag', $cd)->count(); @endphp
        <div class="col-xs-6 col-sm-3">
            <div class="well text-center" style="padding:10px;">
                <span class="label label-{{ $cores[$cd] ?? 'default' }}" style="font-size:1.1em;padding:4px 8px;">
                    <i class="fa fa-{{ $icones[$cd] ?? 'circle' }}"></i> {{ $nm }}
                </span>
                <div style="font-size:1.8em;font-weight:bold;margin-top:5px;">{{ $qtd }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabela --}}
    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-color-blueDark">
                <header>
                    <span class="widget-icon"><i class="fa fa-table"></i></span>
                    <h2>Pagamentos – {{ $mesAnoFmt }}</h2>
                </header>
                <div>
                    <div class="widget-body">
                        @if($pagamentos->isEmpty())
                            <div class="alert alert-info">
                                Nenhum pagamento encontrado para este mês.
                                Clique em <strong>Consolidar Mês</strong> para gerar os registros.
                            </div>
                        @else
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Correspondente</th>
                                    <th class="text-right">Valor Total</th>
                                    <th class="text-center">Processos</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Atualizado em</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamentos as $pag)
                                @php $cor = $cores[$pag->cd_status_pag] ?? 'default'; @endphp
                                <tr>
                                    <td>
                                        {{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}
                                    </td>
                                    <td class="text-right">
                                        <strong>R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center">{{ $pag->itens->count() }}</td>
                                    <td class="text-center">
                                        <span class="label label-{{ $cor }}">{{ $pag->nm_status }}</span>
                                    </td>
                                    <td class="text-center">{{ $pag->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center" style="white-space:nowrap;">
                                        <a href="{{ url('correspondente/pagamentos/'.$pag->cd_pagamento_correspondente_pag.'/detalhe') }}"
                                           class="btn btn-xs btn-default" title="Ver detalhes">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @if($pag->podeEnviarAprovacao())
                                        <form method="POST" action="{{ url('correspondente/pagamentos/'.$pag->cd_pagamento_correspondente_pag.'/enviar-aprovacao') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-warning"
                                                    title="Enviar para aprovação"
                                                    onclick="return confirm('Enviar para aprovação do correspondente?')">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if($pag->podeAprovar())
                                        <form method="POST" action="{{ url('correspondente/pagamentos/'.$pag->cd_pagamento_correspondente_pag.'/aprovar') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-info"
                                                    title="Aprovar"
                                                    onclick="return confirm('Confirmar aprovação deste pagamento?')">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if($pag->podePagar())
                                        <button type="button" class="btn btn-xs btn-success" title="Registrar pagamento"
                                                data-toggle="modal" data-target="#modalPagar"
                                                data-id="{{ $pag->cd_pagamento_correspondente_pag }}"
                                                data-nome="{{ $pag->correspondente->nm_razao_social_con ?? '' }}"
                                                data-valor="R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}">
                                            <i class="fa fa-dollar"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>

{{-- Modal Registrar Pagamento --}}
<div class="modal fade" id="modalPagar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formPagar" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-dollar"></i> Registrar Pagamento</h4>
                </div>
                <div class="modal-body">
                    <p>Correspondente: <strong id="modalPagarNome"></strong></p>
                    <p>Valor: <strong id="modalPagarValor"></strong></p>
                    <div class="form-group">
                        <label>Observação (opcional)</label>
                        <textarea name="observacao" class="form-control" rows="3"
                                  placeholder="Comprovante, banco, data, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#modalPagar').on('show.bs.modal', function (e) {
            var btn   = $(e.relatedTarget);
            var id    = btn.data('id');
            var nome  = btn.data('nome');
            var valor = btn.data('valor');
            $('#modalPagarNome').text(nome);
            $('#modalPagarValor').text(valor);
            $('#formPagar').attr('action', '{{ url("correspondente/pagamentos") }}/' + id + '/pagar');
        });
    });
</script>
@endsection