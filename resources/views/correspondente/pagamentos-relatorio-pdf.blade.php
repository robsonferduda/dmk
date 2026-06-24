<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }

    /* ── Cabeçalho ── */
    .header { border-bottom: 3px solid #1a7bb9; padding-bottom: 10px; margin-bottom: 16px; }
    .header-tbl { width: 100%; border-collapse: collapse; }
    .header-tbl td { vertical-align: middle; }
    .header-escritorio { font-size: 15px; font-weight: bold; color: #1a7bb9; }
    .header-sub { font-size: 9px; color: #888; margin-top: 2px; }
    .header-right { text-align: right; }
    .header-titulo { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1a7bb9; }
    .header-competencia { font-size: 20px; font-weight: bold; color: #333; }
    .header-gerado { font-size: 8px; color: #aaa; margin-top: 2px; }

    /* ── Seção título ── */
    .section-title { font-size: 9px; text-transform: uppercase; font-weight: bold; color: #1a7bb9;
        border-left: 3px solid #1a7bb9; padding-left: 7px; margin-bottom: 8px; margin-top: 18px; }

    /* ── Badges de status ── */
    .badge { padding: 2px 6px; font-size: 8px; font-weight: bold; text-transform: uppercase; color: #fff; }
    .badge-gerado    { background: #95a5a6; }
    .badge-enviado   { background: #e67e22; }
    .badge-aprovado  { background: #2980b9; }
    .badge-pago      { background: #27ae60; }
    .badge-recusado  { background: #e74c3c; }

    /* ── Tabela resumo ── */
    .resumo-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .resumo-table thead tr { background: #1a7bb9; color: white; }
    .resumo-table thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: bold; }
    .resumo-table tbody td { padding: 6px 8px; font-size: 9px; vertical-align: top; border-bottom: 1px solid #eef0f3; }
    .resumo-table tfoot td { padding: 8px; font-size: 10px; font-weight: bold; background: #f0f4f9; border-top: 2px solid #1a7bb9; }

    /* ── Tabela de processos ── */
    .proc-table { width: 100%; border-collapse: collapse; }
    .proc-table thead tr { background: #eef2f7; }
    .proc-table thead th { padding: 5px 8px; text-align: left; font-size: 8px; font-weight: bold; color: #555; border-bottom: 1px solid #d0dce8; }
    .proc-table tbody td { padding: 5px 8px; font-size: 9px; vertical-align: middle; border-bottom: 1px solid #eef0f3; }
    .proc-table tfoot td { padding: 6px 8px; font-size: 9px; font-weight: bold; background: #f0f4f9; border-top: 1px solid #1a7bb9; }

    /* ── Rodapé ── */
    .footer { border-top: 1px solid #ddd; padding-top: 6px; margin-top: 20px; font-size: 8px; color: #aaa; text-align: center; }
</style>
</head>
<body>

{{-- ══ CABEÇALHO ══ --}}
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

{{-- ══ TOTALIZADOR ══ --}}
@php
    $totalGeral    = $pagamentos->sum('vl_total_pag');
    $totalPago     = $pagamentos->where('cd_status_pag', 4)->sum('vl_total_pag');
    $totalPendente = $pagamentos->whereNotIn('cd_status_pag', [4])->sum('vl_total_pag');
    $qtdTotal      = $pagamentos->count();
@endphp
<table style="width:100%; border-collapse:collapse; margin-bottom:16px; background:#f0f4f9; border:1px solid #d0dce8;">
    <tr>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">Correspondentes</div>
            <div style="font-size:18px;font-weight:bold;color:#1a7bb9;margin-top:3px;">{{ $qtdTotal }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">Total do Mês</div>
            <div style="font-size:13px;font-weight:bold;color:#1a7bb9;margin-top:3px;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">A Pagar</div>
            <div style="font-size:13px;font-weight:bold;color:#d68910;margin-top:3px;">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; width:25%;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">Pago</div>
            <div style="font-size:13px;font-weight:bold;color:#27ae60;margin-top:3px;">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
        </td>
    </tr>
</table>

{{-- ══ RESUMO GERAL ══ --}}
<div class="section-title">Resumo — Todos os Correspondentes</div>
<table class="resumo-table">
    <thead>
        <tr>
            <th style="width:30%">Correspondente</th>
            <th style="width:10%; text-align:center;">Status</th>
            <th style="width:22%">Dado de Pagamento</th>
            <th style="width:10%; text-align:center;">Processos</th>
            <th style="width:14%; text-align:right;">Honorários</th>
            <th style="width:14%; text-align:right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $somaHon = 0; $somaTotal = 0; @endphp
        @forelse($pagamentos as $pag)
        @php
            $banco      = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
            $hon        = $pag->itens->sum('vl_honorario_pai');
            $somaHon   += $hon;
            $somaTotal += $pag->vl_total_pag;

            $badgeMap  = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
            $badgeCls  = $badgeMap[$pag->cd_status_pag] ?? 'gerado';

            $dadoPgto = '';
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
            <td style="text-align:center;">{{ $pag->itens->count() }}</td>
            <td style="text-align:right;">R$ {{ number_format($hon, 2, ',', '.') }}</td>
            <td style="text-align:right;"><strong>R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; color:#aaa; padding:14px;">Nenhum pagamento no período.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Total Geral</td>
            <td style="text-align:right;">R$ {{ number_format($somaHon, 2, ',', '.') }}</td>
            <td style="text-align:right; color:#1a7bb9;">R$ {{ number_format($somaTotal, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ══ DETALHE POR CORRESPONDENTE ══ --}}
@if($pagamentos->isNotEmpty())
<div class="section-title section-title--detalhe">Detalhe por Correspondente</div>

@foreach($pagamentos as $index => $pag)
@php
    $banco = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
    $zebra = $index % 2 === 1;
@endphp
@include('correspondente.pagamentos-relatorio-pdf-bloco', compact('pag', 'banco', 'zebra'))
@endforeach
@endif

{{-- ══ RODAPÉ ══ --}}
<div class="footer">
    Documento gerado automaticamente em {{ now()->format('d/m/Y \à\s H:i') }}
    — {{ $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? '' }}
    — Uso interno. Não possui validade como comprovante.
</div>

</body>
</html>
