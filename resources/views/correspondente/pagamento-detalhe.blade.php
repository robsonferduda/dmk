@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente/pagamentos?mes='.$pagamento->nu_mes_pag.'&ano='.$pagamento->nu_ano_pag) }}">Pagamentos</a></li>
        <li>Detalhe</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i>
                Pagamento –
                {{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}
                <span> – {{ $pagamento->nm_mes_ano }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 boxBtnTopo">
            <a href="{{ url('correspondente/pagamentos?mes='.$pagamento->nu_mes_pag.'&ano='.$pagamento->nu_ano_pag) }}"
               class="btn btn-default pull-right header-btn">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>

    @php
        $cores  = [1=>'default',2=>'warning',3=>'info',4=>'success'];
        $cor    = $cores[$pagamento->cd_status_pag] ?? 'default';
    @endphp

    {{-- Cabeçalho do pagamento --}}
    <div class="row">
        <div class="col-md-4">
            {{-- Resumo do pagamento --}}
            <div class="well" style="margin-bottom:15px;">
                <h5 style="margin-top:0; font-weight:700; border-bottom:1px solid #ddd; padding-bottom:6px;">
                    <i class="fa fa-file-text-o"></i> Resumo
                </h5>
                <dl class="dl-horizontal" style="margin-bottom:0;">
                    <dt>Correspondente</dt>
                    <dd>{{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}</dd>
                    <dt>Competência</dt>
                    <dd>{{ $pagamento->nm_mes_ano }}</dd>
                    <dt>Valor Total</dt>
                    <dd><strong>R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</strong></dd>
                    <dt>Status</dt>
                    <dd><span class="label label-{{ $cor }}">{{ $pagamento->nm_status }}</span></dd>
                    @if($pagamento->dt_envio_aprovacao_pag)
                    <dt>Enviado em</dt>
                    <dd>{{ $pagamento->dt_envio_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->dt_aprovacao_pag)
                    <dt>Aprovado em</dt>
                    <dd>{{ $pagamento->dt_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->dt_pagamento_pag)
                    <dt>Pago em</dt>
                    <dd>{{ $pagamento->dt_pagamento_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->ds_observacao_pag)
                    <dt>Obs.</dt>
                    <dd>{{ $pagamento->ds_observacao_pag }}</dd>
                    @endif
                </dl>
            </div>

            {{-- Dados bancários --}}
            <div class="well" style="margin-bottom:15px;">
                <h5 style="margin-top:0; font-weight:700; border-bottom:1px solid #ddd; padding-bottom:6px;">
                    <i class="fa fa-bank"></i> Dados Bancários
                </h5>
                @if($banco && $banco->nm_titular_dba)
                <dl class="dl-horizontal" style="margin-bottom:0;">
                    <dt>Titular</dt>
                    <dd>{{ $banco->nm_titular_dba }}</dd>
                    @if($banco->nu_cpf_cnpj_dba)
                    <dt>CPF/CNPJ</dt>
                    <dd>{{ $banco->nu_cpf_cnpj_dba }}</dd>
                    @endif
                    @if($banco->nm_banco_ban)
                    <dt>Banco</dt>
                    <dd>{{ $banco->cd_banco_ban }} – {{ $banco->nm_banco_ban }}</dd>
                    @endif
                    @if($banco->nu_agencia_dba)
                    <dt>Agência</dt>
                    <dd>{{ $banco->nu_agencia_dba }}</dd>
                    @endif
                    @if($banco->nu_conta_dba)
                    <dt>Conta</dt>
                    <dd>
                        {{ $banco->nu_conta_dba }}
                        @if($banco->nm_tipo_conta_tcb)
                        <span class="text-muted">({{ $banco->nm_tipo_conta_tcb }})</span>
                        @endif
                    </dd>
                    @endif
                    @if($banco->dc_pix_dba)
                    <dt>PIX</dt>
                    <dd>{{ $banco->dc_pix_dba }}</dd>
                    @endif
                </dl>
                @else
                <p class="text-muted" style="margin:0;"><i class="fa fa-exclamation-triangle"></i> Dados bancários não cadastrados.</p>
                @endif
            </div>

            {{-- Ações conforme status --}}
            @if($pagamento->podeEnviarAprovacao())
            <form method="POST" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/enviar-aprovacao') }}">
                @csrf
                <button type="submit" class="btn btn-warning btn-block"
                        onclick="return confirm('Enviar para aprovação do correspondente via e-mail e WhatsApp?')">
                    <i class="fa fa-paper-plane"></i> Enviar para Aprovação
                </button>
            </form>
            @endif

            @if($pagamento->podeAprovar())
            <form method="POST" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/aprovar') }}">
                @csrf
                <button type="submit" class="btn btn-info btn-block"
                        onclick="return confirm('Confirmar aprovação deste pagamento?')">
                    <i class="fa fa-check"></i> Aprovar Pagamento
                </button>
            </form>
            @endif

            @if($pagamento->podePagar())
            <button type="button" class="btn btn-success btn-block"
                    data-toggle="modal" data-target="#modalPagar"
                    data-id="{{ $pagamento->cd_pagamento_correspondente_pag }}"
                    data-nome="{{ $pagamento->correspondente->nm_razao_social_con ?? '' }}"
                    data-valor="R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}">
                <i class="fa fa-dollar"></i> Registrar Pagamento
            </button>
            @endif

            {{-- Botão de teste de notificação --}}
            <div style="margin-top:20px; border-top:1px dashed #ccc; padding-top:12px;">
                <p class="text-muted" style="font-size:11px; margin-bottom:6px;">
                    <i class="fa fa-flask"></i> <strong>Ambiente de Teste</strong><br>
                    Envia e-mail e WhatsApp para contatos fixos (não altera o status).
                </p>
                <form method="POST" action="{{ url('correspondente/pagamentos/'.$pagamento->cd_pagamento_correspondente_pag.'/testar-notificacao') }}">
                    @csrf
                    <button type="submit" class="btn btn-default btn-block"
                            style="border-style:dashed; color:#888;"
                            onclick="return confirm('Enviar notificação de TESTE para robsonferduda@gmail.com e WhatsApp 48991030204?')">
                        <i class="fa fa-flask"></i> [TESTE] Enviar Notificação
                    </button>
                </form>
            </div>
        </div>

        {{-- Lista de itens --}}
        <div class="col-md-8">
            <div class="jarviswidget jarviswidget-color-blueDark">
                <header>
                    <span class="widget-icon"><i class="fa fa-list"></i></span>
                    <h2>Processos ({{ $pagamento->itens->count() }})</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table class="table table-striped table-hover" style="margin:0;">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Descrição</th>
                                    <th class="text-right">Honorário</th>
                                    <th class="text-right">Despesa</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagamento->itens as $item)
                                <tr>
                                    <td>
                                        @if($item->cd_processo_pro)
                                        <a href="{{ url('processos/detalhes/'.$item->cd_processo_pro) }}" target="_blank">
                                            {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                                        </a>
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $item->ds_descricao_pai }}</td>
                                    <td class="text-right">R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
                                    <td class="text-right">R$ {{ number_format($item->vl_despesa_pai, 2, ',', '.') }}</td>
                                    <td class="text-right"><strong>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</strong></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Nenhum item.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="4" class="text-right">Total Geral</th>
                                    <th class="text-right">R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Confirmar</button>
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
            $('#modalPagarNome').text(btn.data('nome'));
            $('#modalPagarValor').text(btn.data('valor'));
            $('#formPagar').attr('action', '{{ url("correspondente/pagamentos") }}/' + btn.data('id') + '/pagar');
        });
    });
</script>
@endsection
