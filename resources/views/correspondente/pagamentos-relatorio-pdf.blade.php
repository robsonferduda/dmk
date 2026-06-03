<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }

    /* ── Cabeçalho ── */
    .header { border-bottom: 3px solid #1a7bb9; padding-bottom: 10px; margin-bottom: 16px; }
    .header table { width: 100%; border-collapse: collapse; }
    .header-escritorio { font-size: 15px; font-weight: bold; color: #1a7bb9; }
    .header-sub { font-size: 9px; color: #888; margin-top: 2px; }
    .header-right { text-align: right; }
    .header-titulo { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1a7bb9; }
    .header-competencia { font-size: 20px; font-weight: bold; color: #333; }
    .header-gerado { font-size: 8px; color: #aaa; margin-top: 2px; }

    /* ── Totalizador ── */
    .totais-bar { display: table; width: 100%; border-collapse: collapse; margin-bottom: 16px; background: #f0f4f9; border-radius: 4px; border: 1px solid #d0dce8; }
    .totais-cell { display: table-cell; text-align: center; padding: 10px 6px; border-right: 1px solid #d0dce8; }
    .totais-cell:last-child { border-right: none; }
    .totais-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #888; }
    .totais-valor { font-size: 14px; font-weight: bold; color: #1a7bb9; margin-top: 3px; }
    .totais-valor.verde { color: #27ae60; }
    .totais-valor.laranja { color: #d68910; }

    /* ── Seção título ── */
    .section-title { font-size: 9px; text-transform: uppercase; font-weight: bold; color: #1a7bb9;
        border-left: 3px solid #1a7bb9; padding-left: 7px; margin-bottom: 8px; margin-top: 18px; }

    /* ── Tabela resumo ── */
    .resumo-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .resumo-table thead tr { background: #1a7bb9; color: white; }
    .resumo-table thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: bold; }
    .resumo-table thead th.right { text-align: right; }
    .resumo-table thead th.center { text-align: center; }
    .resumo-table tbody tr { border-bottom: 1px solid #eef0f3; }
    .resumo-table tbody tr:nth-child(even) { background: #f9fbfd; }
    .resumo-table tbody td { padding: 6px 8px; font-size: 9px; vertical-align: top; }
    .resumo-table tbody td.right { text-align: right; }
    .resumo-table tbody td.center { text-align: center; }
    .resumo-table tfoot td { padding: 8px; font-size: 10px; font-weight: bold; background: #f0f4f9; border-top: 2px solid #1a7bb9; }
    .resumo-table tfoot td.right { text-align: right; color: #1a7bb9; }

    /* ── Badges de status ── */
    .badge { display: inline-block; padding: 2px 7px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
    .badge-gerado    { background: #95a5a6; color: #fff; }
    .badge-enviado   { background: #e67e22; color: #fff; }
    .badge-aprovado  { background: #2980b9; color: #fff; }
    .badge-pago      { background: #27ae60; color: #fff; }
    .badge-recusado  { background: #e74c3c; color: #fff; }

    /* ── Bloco por correspondente ── */
    .correspondente-block { margin-bottom: 22px; page-break-inside: avoid; }
    .correspondente-header { background: #1a7bb9; color: white; padding: 7px 10px; border-radius: 3px 3px 0 0; }
    .correspondente-header-nome { font-size: 11px; font-weight: bold; }
    .correspondente-header-info { font-size: 8px; color: #cce0f5; margin-top: 2px; }
    .correspondente-body { border: 1px solid #d0dce8; border-top: none; border-radius: 0 0 3px 3px; }

    /* ── Info bancária ── */
    .bank-grid { display: table; width: 100%; border-collapse: collapse; padding: 8px 10px; }
    .bank-col { display: table-cell; vertical-align: top; padding-right: 10px; }
    .bank-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
    .bank-value { font-size: 9px; color: #222; margin-top: 1px; }
    .bank-pix-row { background: #eef6ff; border-top: 1px solid #d0dce8; padding: 5px 10px; }
    .bank-pix-row .bank-label { color: #1a7bb9; }

    /* ── Tabela de processos ── */
    .proc-table { width: 100%; border-collapse: collapse; }
    .proc-table thead tr { background: #eef2f7; }
    .proc-table thead th { padding: 5px 8px; text-align: left; font-size: 8px; font-weight: bold; color: #555;
        border-bottom: 1px solid #d0dce8; }
    .proc-table thead th.right { text-align: right; }
    .proc-table thead th.center { text-align: center; }
    .proc-table tbody tr { border-bottom: 1px solid #eef0f3; }
    .proc-table tbody tr:nth-child(even) { background: #fafbfd; }
    .proc-table tbody td { padding: 5px 8px; font-size: 9px; vertical-align: middle; }
    .proc-table tbody td.right { text-align: right; }
    .proc-table tbody td.center { text-align: center; }
    .proc-table tfoot tr { background: #f0f4f9; border-top: 1px solid #1a7bb9; }
    .proc-table tfoot td { padding: 6px 8px; font-size: 9px; font-weight: bold; }
    .proc-table tfoot td.right { text-align: right; color: #1a7bb9; }
    .proc-no-data { text-align: center; color: #aaa; padding: 10px; font-size: 9px; font-style: italic; }

    /* ── Rodapé ── */
    .footer { border-top: 1px solid #ddd; padding-top: 6px; margin-top: 20px; font-size: 8px; color: #aaa; text-align: center; }
</style>
</head>
<body>

{{-- ══ CABEÇALHO ══ --}}
<div class="header">
    <table>
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
<table style="width:100%; border-collapse:collapse; margin-bottom:16px; background:#f0f4f9; border:1px solid #d0dce8; border-radius:4px;">
    <tr>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">Correspondentes</div>
            <div style="font-size:18px;font-weight:bold;color:#1a7bb9;margin-top:3px;">{{ $qtdTotal }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">Total do Mês</div>
            <div style="font-size:13px;font-weight:bold;color:#1a7bb9;margin-top:3px;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px; border-right:1px solid #d0dce8;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;color:#888;">A Pagar</div>
            <div style="font-size:13px;font-weight:bold;color:#d68910;margin-top:3px;">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
        </td>
        <td style="text-align:center; padding:10px 6px;">
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
            <th style="width:28%">Correspondente</th>
            <th style="width:22%">Dados de Pagamento</th>
            <th class="center" style="width:10%">Processos</th>
            <th class="right"  style="width:14%">Honorários</th>
            <th class="right"  style="width:14%">Despesas</th>
            <th class="right"  style="width:12%">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $somaHon = 0; $somaDes = 0; $somaTotal = 0; @endphp
        @forelse($pagamentos as $pag)
        @php
            $banco     = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
            $hon       = $pag->itens->sum('vl_honorario_pai');
            $des       = $pag->itens->sum('vl_despesa_pai');
            $somaHon  += $hon;
            $somaDes  += $des;
            $somaTotal += $pag->vl_total_pag;

            $badgeMap  = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
            $badgeCls  = $badgeMap[$pag->cd_status_pag] ?? 'gerado';

            // Resumo do dado bancário mais relevante
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
            <td>
                <strong>{{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}</strong>
                <br><span class="badge badge-{{ $badgeCls }}">{{ $pag->nm_status }}</span>
            </td>
            <td style="color:#555;">{{ $dadoPgto ?: '—' }}</td>
            <td class="center">{{ $pag->itens->count() }}</td>
            <td class="right">R$ {{ number_format($hon, 2, ',', '.') }}</td>
            <td class="right">R$ {{ number_format($des, 2, ',', '.') }}</td>
            <td class="right"><strong>R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; color:#aaa; padding:14px;">Nenhum pagamento no período.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total Geral</td>
            <td class="right">R$ {{ number_format($somaHon, 2, ',', '.') }}</td>
            <td class="right">R$ {{ number_format($somaDes, 2, ',', '.') }}</td>
            <td class="right">R$ {{ number_format($somaTotal, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ══ DETALHE POR CORRESPONDENTE ══ --}}
@if($pagamentos->isNotEmpty())
<div class="section-title" style="margin-top:24px;">Detalhe por Correspondente</div>

@foreach($pagamentos as $pag)
@php
    $banco    = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
    $badgeMap = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
    $badgeCls = $badgeMap[$pag->cd_status_pag] ?? 'gerado';
@endphp
<div class="correspondente-block">
    {{-- Cabeçalho do bloco --}}
    <div class="correspondente-header">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td>
                    <div class="correspondente-header-nome">
                        {{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}
                    </div>
                    @if($banco && $banco->nu_cpf_cnpj_dba)
                    <div class="correspondente-header-info">CPF/CNPJ: {{ $banco->nu_cpf_cnpj_dba }}
                        @if($banco->nm_titular_dba && $banco->nm_titular_dba !== ($pag->correspondente->nm_razao_social_con ?? ''))
                            · Titular: {{ $banco->nm_titular_dba }}
                        @endif
                    </div>
                    @endif
                </td>
                <td style="text-align:right; white-space:nowrap;">
                    <span class="badge badge-{{ $badgeCls }}">{{ $pag->nm_status }}</span>
                    <span style="font-size:12px; font-weight:bold; margin-left:8px;">
                        R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="correspondente-body">
        {{-- Dados bancários --}}
        @if($banco)
        <table style="width:100%; border-collapse:collapse; padding:0; background:#f7fbff; border-bottom:1px solid #d0dce8;">
            <tr>
                @if($banco->nm_titular_dba)
                <td style="padding:6px 10px; vertical-align:top; width:22%;">
                    <div class="bank-label">Titular</div>
                    <div class="bank-value">{{ $banco->nm_titular_dba }}</div>
                </td>
                @endif
                @if($banco->nm_banco_ban)
                <td style="padding:6px 10px; vertical-align:top; width:22%;">
                    <div class="bank-label">Banco</div>
                    <div class="bank-value">{{ $banco->cd_banco_ban }} – {{ $banco->nm_banco_ban }}</div>
                </td>
                @endif
                @if($banco->nu_agencia_dba)
                <td style="padding:6px 10px; vertical-align:top; width:16%;">
                    <div class="bank-label">Agência</div>
                    <div class="bank-value">{{ $banco->nu_agencia_dba }}</div>
                </td>
                @endif
                @if($banco->nu_conta_dba)
                <td style="padding:6px 10px; vertical-align:top; width:20%;">
                    <div class="bank-label">Conta {{ $banco->nm_tipo_conta_tcb ? '('.$banco->nm_tipo_conta_tcb.')' : '' }}</div>
                    <div class="bank-value">{{ $banco->nu_conta_dba }}</div>
                </td>
                @endif
                @if($banco->dc_pix_dba)
                <td style="padding:6px 10px; vertical-align:top; background:#eef6ff; border-left:1px solid #d0dce8;">
                    <div class="bank-label" style="color:#1a7bb9;">&#x2022; Chave PIX</div>
                    <div class="bank-value" style="font-weight:bold;">{{ $banco->dc_pix_dba }}</div>
                </td>
                @endif
                @if(!$banco->nm_titular_dba && !$banco->nm_banco_ban && !$banco->dc_pix_dba)
                <td style="padding:6px 10px; color:#aaa; font-style:italic;">Dados bancários não cadastrados.</td>
                @endif
            </tr>
        </table>
        @else
        <div style="padding:6px 10px; color:#aaa; font-size:9px; font-style:italic; background:#f7fbff; border-bottom:1px solid #d0dce8;">
            Dados bancários não cadastrados.
        </div>
        @endif

        {{-- Processos --}}
        <table class="proc-table">
            <thead>
                <tr>
                    <th style="width:16%">Processo</th>
                    <th>Descrição</th>
                    <th class="right" style="width:14%">Honorário</th>
                    <th class="right" style="width:14%">Despesa</th>
                    <th class="right" style="width:13%">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pag->itens as $item)
                <tr>
                    <td style="color:#1a7bb9;">
                        @if($item->cd_processo_pro && $item->processo)
                            {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                        @elseif($item->cd_processo_pro)
                            #{{ $item->cd_processo_pro }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $item->ds_descricao_pai }}</td>
                    <td class="right">R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item->vl_despesa_pai,   2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item->vl_total,         2, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="proc-no-data">Nenhum processo registrado neste pagamento.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right; padding-right:8px;">Subtotal</td>
                    <td class="right">R$ {{ number_format($pag->itens->sum('vl_honorario_pai'), 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($pag->itens->sum('vl_despesa_pai'),   2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>{{-- /.correspondente-body --}}
</div>{{-- /.correspondente-block --}}
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
