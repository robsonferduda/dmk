@extends('layouts.admin')
@section('content')
@php
    $cores    = [1=>'default',2=>'warning',3=>'info',4=>'success',5=>'danger'];
    $icones   = [1=>'circle-o',2=>'paper-plane',3=>'check',4=>'dollar',5=>'times-circle'];
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
            @if($pagamentos->filter(fn($p) => $p->podeEnviarAprovacao())->isNotEmpty())
            <form method="POST" action="{{ url('correspondente/pagamentos/enviar-aprovacao-todos') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <button type="submit" class="btn btn-warning pull-right header-btn"
                        style="margin-right:6px;"
                        onclick="return confirm('Enviar notificação para TODOS os correspondentes elegíveis de {{ $mesAnoFmt }}?')">
                    <i class="fa fa-paper-plane"></i> Enviar Todos
                </button>
            </form>
            @endif
            @if($pagamentos->filter(fn($p) => $p->podeReenviarAprovacao())->isNotEmpty())
            <form method="POST" action="{{ url('correspondente/pagamentos/reenviar-aprovacao-todos') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <input type="hidden" name="ano" value="{{ $ano }}">
                <button type="submit" class="btn btn-warning pull-right header-btn"
                        style="margin-right:6px;"
                        onclick="return confirm('Reenviar notificação para TODOS os correspondentes com pagamento pendente de aprovação em {{ $mesAnoFmt }}?')">
                    <i class="fa fa-repeat"></i> Reenviar Todos
                </button>
            </form>
            @endif
            @if($pagamentos->isNotEmpty())
            <a href="{{ url('correspondente/pagamentos/relatorio-pdf?mes='.$mes.'&ano='.$ano) }}"
               class="btn btn-default pull-right" style="margin-right: 4px; margin-top: 5px;" target="_blank"
               title="Baixar relatório completo em PDF">
                <i class="fa fa-file-pdf-o"></i> Relatório PDF
            </a>
            <a href="{{ url('correspondente/pagamentos/relatorio-excel?mes='.$mes.'&ano='.$ano) }}"
               class="btn btn-default pull-right" style="margin-right: 4px; margin-top: 5px;"
               title="Baixar relatório completo em Excel">
                <i class="fa fa-file-excel-o"></i> Relatório Excel
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
    </div>

    {{-- Navegação por competência --}}
    <div class="row" style="margin-bottom:15px;">
        <div class="col-md-12">
            <label for="competenciaPagamentos" class="control-label" style="margin-right:8px;">Competência</label>
            <select id="competenciaPagamentos" class="form-control input-sm"
                    style="min-width:120px; display:inline-block; width:auto;">
                @foreach($mesesNavegacao as $m)
                    @php
                        $label = str_pad($m['mes'], 2, '0', STR_PAD_LEFT) . '/' . $m['ano'];
                        $valor = $m['mes'] . '-' . $m['ano'];
                        $atual = $mes . '-' . $ano;
                    @endphp
                    <option value="{{ $valor }}" {{ $valor === $atual ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

     {{-- Card total geral --}}
    @php
        $totalGeral    = $pagamentos->sum('vl_total_pag');
        $totalPendente = $pagamentos->whereIn('cd_status_pag', [1,2,3,5])->sum('vl_total_pag');
        $totalPago     = $pagamentos->where('cd_status_pag', 4)->sum('vl_total_pag');
    @endphp
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-12">
            <div style="background:#fff;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.10);display:flex;overflow:hidden;">
                <div style="flex:1;padding:18px 24px;border-right:1px solid #eee;text-align:center;">
                    <div style="font-size:0.72em;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:#999;margin-bottom:4px;">Total do Mês</div>
                    <div style="font-size:1.9em;font-weight:700;color:#1a7bb9;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
                </div>
                <div style="flex:1;padding:18px 24px;border-right:1px solid #eee;text-align:center;">
                    <div style="font-size:0.72em;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:#999;margin-bottom:4px;">A Pagar</div>
                    <div style="font-size:1.9em;font-weight:700;color:#d68910;">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
                </div>
                <div style="flex:1;padding:18px 24px;text-align:center;">
                    <div style="font-size:0.72em;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:#999;margin-bottom:4px;">Pago</div>
                    <div style="font-size:1.9em;font-weight:700;color:#27ae60;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumo por status --}}
    @php
        $statusConfig = [
            1 => ['cor'=>'#95a5a6', 'bg'=>'#f4f6f7', 'icone'=>'file-text-o'],
            2 => ['cor'=>'#e67e22', 'bg'=>'#fef9f0', 'icone'=>'paper-plane'],
            3 => ['cor'=>'#2980b9', 'bg'=>'#eaf4fb', 'icone'=>'check-circle'],
            4 => ['cor'=>'#27ae60', 'bg'=>'#eafaf1', 'icone'=>'dollar'],
            5 => ['cor'=>'#e74c3c', 'bg'=>'#fdedec', 'icone'=>'times-circle'],
        ];
    @endphp
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-12">
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @foreach($statusLabels as $cd => $nm)
                @php
                    $qtd      = $pagamentos->where('cd_status_pag', $cd)->count();
                    $vlStatus = $pagamentos->where('cd_status_pag', $cd)->sum('vl_total_pag');
                    $cfg      = $statusConfig[$cd] ?? ['cor'=>'#aaa','bg'=>'#f5f5f5','icone'=>'circle'];
                @endphp
                <div style="flex:1 1 0;min-width:160px;">
                    <div style="background:{{ $cfg['bg'] }};border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,0.07);padding:14px 16px;border-left:4px solid {{ $cfg['cor'] }};display:flex;align-items:center;gap:14px;height:100%;">
                        <div style="color:{{ $cfg['cor'] }};font-size:2em;line-height:1;flex-shrink:0;">
                            <i class="fa fa-{{ $cfg['icone'] }}"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.68em;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:#999;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $nm }}</div>
                            <div style="font-size:1.7em;font-weight:700;color:#333;line-height:1.1;">{{ $qtd }}</div>
                            <div style="font-size:0.78em;color:#aaa;">R$ {{ number_format($vlStatus, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
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
                                    <th>PIX</th>
                                    <th class="text-right">Valor Total</th>
                                    <th class="text-center">Processos</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Atualizado em</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamentos as $pag)
                                @php
                                    $cor   = $cores[$pag->cd_status_pag] ?? 'default';
                                    if ($pag->isParcialmentePago()) {
                                        $cor = 'warning';
                                    }
                                    $banco = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
                                    $pix   = $banco->dc_pix_dba ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        {{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}
                                    </td>
                                    <td>
                                        @if($pix)
                                            <span title="{{ $pix }}">{{ $pix }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong>R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong>
                                        @if($pag->vl_pago_total > 0 && $pag->vl_saldo_total > 0)
                                        <div class="text-muted" style="font-size:11px;">
                                            pago R$ {{ number_format($pag->vl_pago_total, 2, ',', '.') }} ·
                                            saldo R$ {{ number_format($pag->vl_saldo_total, 2, ',', '.') }}
                                        </div>
                                        @endif
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

                                        @if($pag->podeReenviarAprovacao())
                                        <form method="POST" action="{{ url('correspondente/pagamentos/'.$pag->cd_pagamento_correspondente_pag.'/enviar-aprovacao') }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-warning"
                                                    title="Reenviar para aprovação"
                                                    onclick="return confirm('Reenviar notificação de aprovação para este correspondente?')">
                                                <i class="fa fa-repeat"></i>
                                            </button>
                                        </form>
                                        @elseif($pag->podeEnviarAprovacao())
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
                                        <a href="{{ url('correspondente/pagamentos/'.$pag->cd_pagamento_correspondente_pag.'/detalhe') }}#lancamentos"
                                           class="btn btn-xs btn-success"
                                           title="Registrar pagamento (parcial ou total)">
                                            <i class="fa fa-dollar"></i>
                                        </a>
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
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#competenciaPagamentos').on('change', function () {
            var partes = String($(this).val() || '').split('-');
            if (partes.length !== 2) {
                return;
            }
            window.location.href = '{{ url('correspondente/pagamentos') }}?mes=' + partes[0] + '&ano=' + partes[1];
        });
    });
</script>
@endsection