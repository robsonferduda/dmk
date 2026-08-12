{{-- Cabeçalho --}}
<div class="header">
    <table class="header-tbl">
        <tr>
            <td>
                <div class="header-escritorio">{{ $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório' }}</div>
                <div class="header-sub">Relatório de Pagamentos de Correspondentes</div>
            </td>
            <td class="header-right">
                <div class="header-titulo">Relatório de Pagamentos</div>
                <div class="header-competencia">{{ $mesAnoFmt }}</div>
                <div class="header-gerado">Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- Totalizador --}}
@php
    $totalGeral    = $pagamentos->sum('vl_total_pag');
    $totalPago     = $pagamentos->where('cd_status_pag', 4)->sum('vl_total_pag');
    $totalPendente = $pagamentos->whereNotIn('cd_status_pag', [4])->sum('vl_total_pag');
    $qtdTotal      = $pagamentos->count();
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:16px; background:#f0f4f9; border:1px solid #d0dce8;">
    <tr>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Correspondentes</div>
            <div style="font-size:18px;font-weight:bold;color:#1a7bb9;margin-top:3px;">{{ $qtdTotal }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Total do Mês</div>
            <div style="font-size:13px;font-weight:bold;color:#1a7bb9;margin-top:3px;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">A Pagar</div>
            <div style="font-size:13px;font-weight:bold;color:#d68910;margin-top:3px;">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Pago</div>
            <div style="font-size:13px;font-weight:bold;color:#27ae60;margin-top:3px;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
        </td>
    </tr>
</table>

{{-- Resumo — cards por correspondente --}}
<div class="section-title">Resumo — Todos os Correspondentes</div>

@if($pagamentos->isEmpty())
<div class="resumo-vazio">Nenhum pagamento no período.</div>
@else
<div class="resumo-lista">
    @php $somaTotal = 0; @endphp
    @foreach($pagamentos as $index => $pag)
    @php
        $banco    = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
        $somaTotal += $pag->vl_total_pag;
        $badgeCls = $pag->badgeRelatorio();
        $badgeStyle = $pag->badgeRelatorioEstilo();
        $dadoPgto = '';
        $dadoLabel = '';
        if ($banco) {
            if ($banco->dc_pix_dba) {
                $dadoLabel = 'PIX:';
                $dadoPgto  = $banco->dc_pix_dba;
            } elseif ($banco->nu_conta_dba) {
                $dadoLabel = 'Conta:';
                $dadoPgto  = ($banco->cd_banco_ban ? $banco->cd_banco_ban . ' · ' : '')
                           . 'Ag ' . ($banco->nu_agencia_dba ?? '—')
                           . ' · CC ' . $banco->nu_conta_dba;
            }
        }
        $zebra = $index % 2 === 1;
    @endphp
    <div class="resumo-card {{ $zebra ? 'resumo-card--zebra' : '' }}">
        <table class="resumo-card__tbl">
            <tr>
                <td class="resumo-card__col-esq" rowspan="2">
                    <div class="resumo-card__nome">
                        {{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}
                    </div>
                    @if($dadoPgto)
                    <div class="resumo-card__dado">
                        <span class="resumo-card__dado-label">{{ $dadoLabel }}</span>
                        {{ $dadoPgto }}
                    </div>
                    @else
                    <div class="resumo-card__dado resumo-card__dado--vazio">Dado de pagamento não informado</div>
                    @endif
                </td>
                <td class="resumo-card__col-dir">
                    <div class="resumo-card__valor">
                        R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="resumo-card__col-dir resumo-card__status">
                    <span class="badge badge-{{ $badgeCls }}" style="{{ $badgeStyle }}">{{ $pag->nm_status }}</span>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</div>

<div class="resumo-total">
    <table class="resumo-total__tbl">
        <tr>
            <td class="resumo-total__label">Total Geral</td>
            <td class="resumo-total__valor">R$ {{ number_format($somaTotal, 2, ',', '.') }}</td>
        </tr>
    </table>
</div>
@endif
