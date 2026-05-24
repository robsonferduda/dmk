<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

    /* ── Cabeçalho ── */
    .header { border-bottom: 3px solid #1a7bb9; padding-bottom: 10px; margin-bottom: 18px; }
    .header-top { width: 100%; }
    .header-top td { vertical-align: middle; }
    .header-escritorio { font-size: 16px; font-weight: bold; color: #1a7bb9; }
    .header-sub { font-size: 10px; color: #888; }
    .header-doc { text-align: right; }
    .header-doc .titulo { font-size: 13px; font-weight: bold; text-transform: uppercase; color: #1a7bb9; }
    .header-doc .competencia { font-size: 18px; font-weight: bold; color: #333; }
    .header-doc .gerado-em { font-size: 9px; color: #aaa; }

    /* ── Info boxes ── */
    .info-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
    .info-box { background: #f5f7fa; border: 1px solid #e2e7ef; padding: 10px 12px; border-radius: 4px; }
    .info-box-title { font-size: 9px; text-transform: uppercase; color: #888; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 6px; }
    .info-box dl { }
    .info-box dt { font-weight: bold; font-size: 10px; color: #555; float: left; clear: left; width: 90px; }
    .info-box dd { font-size: 10px; margin-left: 90px; margin-bottom: 2px; }

    /* ── Status badge ── */
    .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
    .badge-success { background: #27ae60; color: white; }
    .badge-warning { background: #d68910; color: white; }
    .badge-info    { background: #2980b9; color: white; }
    .badge-default { background: #95a5a6; color: white; }

    /* ── Tabela de itens ── */
    .section-title { font-size: 10px; text-transform: uppercase; font-weight: bold; color: #1a7bb9; margin-bottom: 6px; border-left: 3px solid #1a7bb9; padding-left: 8px; }
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .items-table thead tr { background: #1a7bb9; color: white; }
    .items-table thead th { padding: 8px; text-align: left; font-size: 10px; font-weight: bold; }
    .items-table thead th.right { text-align: right; }
    .items-table tbody tr { border-bottom: 1px solid #eef0f3; }
    .items-table tbody tr:nth-child(even) { background: #f9fbfd; }
    .items-table tbody td { padding: 7px 8px; font-size: 10px; vertical-align: top; }
    .items-table tbody td.right { text-align: right; }
    .items-table tfoot tr { background: #f0f4f9; border-top: 2px solid #1a7bb9; }
    .items-table tfoot td { padding: 9px 8px; font-size: 11px; font-weight: bold; }
    .items-table tfoot td.right { text-align: right; color: #1a7bb9; font-size: 13px; }
    .items-table .processo-link { color: #1a7bb9; }

    /* ── Dados bancários ── */
    .bank-box { background: #f0f7ff; border: 1px solid #b8d4f0; padding: 10px 12px; border-radius: 4px; }
    .bank-box-title { font-size: 10px; text-transform: uppercase; font-weight: bold; color: #1a7bb9; margin-bottom: 8px; }
    .bank-table { width: 100%; border-collapse: collapse; }
    .bank-table td { font-size: 10px; padding: 2px 0; vertical-align: top; }
    .bank-table td.label { font-weight: bold; color: #555; width: 110px; }

    /* ── Rodapé ── */
    .footer { border-top: 1px solid #ddd; padding-top: 8px; margin-top: 20px; font-size: 9px; color: #aaa; text-align: center; }
</style>
</head>
<body>

{{-- ══ CABEÇALHO ══ --}}
<div class="header">
    <table class="header-top">
        <tr>
            <td>
                <div class="header-escritorio">{{ $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório' }}</div>
                <div class="header-sub">Demonstrativo de Honorários e Despesas</div>
            </td>
            <td class="header-doc">
                <div class="titulo">Demonstrativo de Pagamento</div>
                <div class="competencia">{{ $pagamento->nm_mes_ano }}</div>
                <div class="gerado-em">Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ INFO: Correspondente + Status ══ --}}
<table class="info-table">
    <tr>
        <td style="width:60%; padding-right:10px; vertical-align:top;">
            <div class="info-box">
                <div class="info-box-title">Correspondente</div>
                <dl>
                    <dt>Nome</dt>
                    <dd>{{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}</dd>
                    @if(isset($banco) && $banco && $banco->nu_cpf_cnpj_dba)
                    <dt>CPF/CNPJ</dt>
                    <dd>{{ $banco->nu_cpf_cnpj_dba }}</dd>
                    @endif
                    @if(isset($banco) && $banco && $banco->nm_titular_dba)
                    <dt>Titular</dt>
                    <dd>{{ $banco->nm_titular_dba }}</dd>
                    @endif
                </dl>
            </div>
        </td>
        <td style="width:40%; vertical-align:top;">
            <div class="info-box">
                <div class="info-box-title">Situação do Pagamento</div>
                <dl>
                    <dt>Competência</dt>
                    <dd>{{ $pagamento->nm_mes_ano }}</dd>
                    <dt>Status</dt>
                    <dd>
                        @php
                            $badgeMap = [1=>'default',2=>'warning',3=>'info',4=>'success'];
                            $badgeCls = $badgeMap[$pagamento->cd_status_pag] ?? 'default';
                        @endphp
                        <span class="badge badge-{{ $badgeCls }}">{{ $pagamento->nm_status }}</span>
                    </dd>
                    @if($pagamento->dt_envio_aprovacao_pag)
                    <dt>Enviado</dt>
                    <dd>{{ $pagamento->dt_envio_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                    @if($pagamento->dt_aprovacao_pag)
                    <dt>Aprovado</dt>
                    <dd>{{ $pagamento->dt_aprovacao_pag->format('d/m/Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </td>
    </tr>
</table>

{{-- ══ TABELA DE ITENS ══ --}}
<div class="section-title">Processos e Serviços</div>
<table class="items-table">
    <thead>
        <tr>
            <th style="width:14%">Processo</th>
            <th>Descrição</th>
            <th class="right" style="width:14%">Honorário</th>
            <th class="right" style="width:14%">Despesa</th>
            <th class="right" style="width:14%">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pagamento->itens as $item)
        <tr>
            <td class="processo-link">
                @if($item->cd_processo_pro && $item->processo)
                    {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                @elseif($item->cd_processo_pro)
                    #{{ $item->cd_processo_pro }}
                @else
                    —
                @endif
            </td>
            <td>{{ $item->ds_descricao_pai }}</td>
            <td class="right">R$&nbsp;{{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
            <td class="right">R$&nbsp;{{ number_format($item->vl_despesa_pai, 2, ',', '.') }}</td>
            <td class="right">R$&nbsp;{{ number_format($item->vl_total, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center; color:#aaa; padding:15px;">Nenhum item registrado.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align:right; padding-right:8px;">Valor Total</td>
            <td class="right">R$&nbsp;{{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

{{-- ══ DADOS BANCÁRIOS ══ --}}
@if(isset($banco) && $banco && $banco->nm_titular_dba)
<div class="section-title">Dados Bancários para Pagamento</div>
<div class="bank-box">
    <table class="bank-table">
        <tr>
            <td class="label">Titular</td>
            <td>{{ $banco->nm_titular_dba }}</td>
            @if($banco->nu_cpf_cnpj_dba)
            <td class="label">CPF/CNPJ</td>
            <td>{{ $banco->nu_cpf_cnpj_dba }}</td>
            @endif
        </tr>
        @if($banco->nm_banco_ban)
        <tr>
            <td class="label">Banco</td>
            <td>{{ $banco->cd_banco_ban }} – {{ $banco->nm_banco_ban }}</td>
            @if($banco->nu_agencia_dba)
            <td class="label">Agência</td>
            <td>{{ $banco->nu_agencia_dba }}</td>
            @endif
        </tr>
        @endif
        @if($banco->nu_conta_dba)
        <tr>
            <td class="label">Conta</td>
            <td>
                {{ $banco->nu_conta_dba }}
                @if($banco->nm_tipo_conta_tcb)
                    ({{ $banco->nm_tipo_conta_tcb }})
                @endif
            </td>
            @if($banco->dc_pix_dba)
            <td class="label">PIX</td>
            <td>{{ $banco->dc_pix_dba }}</td>
            @endif
        </tr>
        @endif
    </table>
</div>
@endif

{{-- ══ RODAPÉ ══ --}}
<div class="footer">
    Documento gerado automaticamente em {{ now()->format('d/m/Y \à\s H:i') }}
    — {{ $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? '' }}
</div>

</body>
</html>
