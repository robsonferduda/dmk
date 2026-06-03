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

{{-- Resumo geral --}}
<div class="section-title">Resumo — Todos os Correspondentes</div>
<table class="resumo-table">
    <thead>
        <tr>
            <th style="width:35%">Correspondente</th>
            <th style="width:15%; text-align:center;">Status</th>
            <th style="width:35%">Dado de Pagamento</th>
            <th style="width:15%; text-align:right;">Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @php $somaTotal = 0; @endphp
        @forelse($pagamentos as $pag)
        @php
            $banco      = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
            $somaTotal += $pag->vl_total_pag;
            $badgeMap   = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
            $badgeCls   = $badgeMap[$pag->cd_status_pag] ?? 'gerado';
            $dadoPgto   = '';
            if ($banco) {
                if ($banco->dc_pix_dba) {
                    $dadoPgto = 'PIX: ' . $banco->dc_pix_dba;
                } elseif ($banco->nu_conta_dba) {
                    $dadoPgto = ($banco->cd_banco_ban ? $banco->cd_banco_ban . ' · ' : '')
                               . 'Ag ' . ($banco->nu_agencia_dba ?? '—')
                               . ' · CC ' . $banco->nu_conta_dba;
                }
            }
        @endphp
        <tr>
            <td><strong>{{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}</strong></td>
            <td style="text-align:center;"><span class="badge badge-{{ $badgeCls }}">{{ $pag->nm_status }}</span></td>
            <td style="color:#555; font-size:9px;">{{ $dadoPgto ?: '—' }}</td>
            <td style="text-align:right;"><strong>R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong></td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; color:#aaa; padding:14px;">Nenhum pagamento no período.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total Geral</td>
            <td style="text-align:right; color:#1a7bb9;">R$ {{ number_format($somaTotal, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

