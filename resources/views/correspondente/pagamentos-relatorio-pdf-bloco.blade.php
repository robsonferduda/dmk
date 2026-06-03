@php
    $badgeMap = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
    $badgeCls = $badgeMap[$pag->cd_status_pag] ?? 'gerado';
@endphp

{{-- Cabeçalho do bloco --}}
<table style="width:100%; border-collapse:collapse; margin-top:14px; margin-bottom:0; background:#1a7bb9; color:#fff;">
    <tr>
        <td style="padding:7px 10px; vertical-align:middle;">
            <strong style="font-size:11px;">{{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}</strong>
            @if($banco && $banco->nu_cpf_cnpj_dba)
            <br><span style="font-size:8px; color:#cce0f5;">CPF/CNPJ: {{ $banco->nu_cpf_cnpj_dba }}
                @if($banco->nm_titular_dba && $banco->nm_titular_dba !== ($pag->correspondente->nm_razao_social_con ?? ''))
                    &middot; Titular: {{ $banco->nm_titular_dba }}
                @endif
            </span>
            @endif
        </td>
        <td style="padding:7px 10px; text-align:right; vertical-align:middle;">
            <span class="badge badge-{{ $badgeCls }}">{{ $pag->nm_status }}</span>
            <strong style="font-size:12px; margin-left:8px;">R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</strong>
        </td>
    </tr>
</table>

{{-- Dados bancários --}}
@if($banco && ($banco->nm_titular_dba || $banco->nm_banco_ban || $banco->dc_pix_dba || $banco->nu_conta_dba))
<table style="width:100%; border-collapse:collapse; background:#f7fbff; border:1px solid #d0dce8; border-top:none;">
    <tr>
        @if($banco->nm_titular_dba)
        <td style="padding:6px 10px; vertical-align:top; border-right:1px solid #e0e8f0;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Titular</div>
            <div style="font-size:9px;color:#222;">{{ $banco->nm_titular_dba }}</div>
        </td>
        @endif
        @if($banco->nm_banco_ban)
        <td style="padding:6px 10px; vertical-align:top; border-right:1px solid #e0e8f0;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Banco</div>
            <div style="font-size:9px;color:#222;">{{ $banco->cd_banco_ban }} &ndash; {{ $banco->nm_banco_ban }}</div>
        </td>
        @endif
        @if($banco->nu_agencia_dba)
        <td style="padding:6px 10px; vertical-align:top; border-right:1px solid #e0e8f0;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Agência</div>
            <div style="font-size:9px;color:#222;">{{ $banco->nu_agencia_dba }}</div>
        </td>
        @endif
        @if($banco->nu_conta_dba)
        <td style="padding:6px 10px; vertical-align:top; border-right:1px solid #e0e8f0;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#888;">Conta{{ $banco->nm_tipo_conta_tcb ? ' ('.$banco->nm_tipo_conta_tcb.')' : '' }}</div>
            <div style="font-size:9px;color:#222;">{{ $banco->nu_conta_dba }}</div>
        </td>
        @endif
        @if($banco->dc_pix_dba)
        <td style="padding:6px 10px; vertical-align:top; background:#eef6ff;">
            <div style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#1a7bb9;">Chave PIX</div>
            <div style="font-size:9px;font-weight:bold;color:#222;">{{ $banco->dc_pix_dba }}</div>
        </td>
        @endif
    </tr>
</table>
@else
<table style="width:100%; border-collapse:collapse; background:#f7fbff; border:1px solid #d0dce8; border-top:none;">
    <tr><td style="padding:6px 10px; color:#aaa; font-style:italic; font-size:9px;">Dados bancários não cadastrados.</td></tr>
</table>
@endif

{{-- Processos --}}
<table class="proc-table" style="border:1px solid #d0dce8; border-top:none;">
    <thead>
        <tr>
            <th style="width:16%;">Processo</th>
            <th>Descrição</th>
            <th style="width:14%; text-align:right;">Honorário</th>
            <th style="width:14%; text-align:right;">Despesa</th>
            <th style="width:13%; text-align:right;">Total</th>
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
                    &mdash;
                @endif
            </td>
            <td>{{ $item->ds_descricao_pai }}</td>
            <td style="text-align:right;">R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
            <td style="text-align:right;">R$ {{ number_format($item->vl_despesa_pai,   2, ',', '.') }}</td>
            <td style="text-align:right;">R$ {{ number_format($item->vl_total,         2, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center; color:#aaa; padding:10px; font-style:italic;">Nenhum processo registrado.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align:right; padding-right:8px;">Subtotal</td>
            <td style="text-align:right;">R$ {{ number_format($pag->itens->sum('vl_honorario_pai'), 2, ',', '.') }}</td>
            <td style="text-align:right;">R$ {{ number_format($pag->itens->sum('vl_despesa_pai'),   2, ',', '.') }}</td>
            <td style="text-align:right; color:#1a7bb9;">R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
