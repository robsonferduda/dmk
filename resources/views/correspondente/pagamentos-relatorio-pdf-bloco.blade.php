@php
    $badgeMap = [1=>'gerado',2=>'enviado',3=>'aprovado',4=>'pago',5=>'recusado'];
    $badgeCls = $badgeMap[$pag->cd_status_pag] ?? 'gerado';
    $itensAtivos = $pag->itens->filter(function ($item) {
        return strtoupper((string) ($item->fl_excluido_pai ?? 'N')) !== 'S';
    });
@endphp

<div class="pagamento-bloco {{ !empty($zebra) ? 'pagamento-bloco--zebra' : '' }}">

    {{-- Cabeçalho do correspondente --}}
    <div class="pagamento-bloco__header">
        <table class="pagamento-bloco__header-tbl">
            <tr>
                <td>
                    <div class="pagamento-bloco__nome">
                        {{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}
                    </div>
                    @if($banco && $banco->nu_cpf_cnpj_dba)
                    <div class="pagamento-bloco__meta">
                        CPF/CNPJ: {{ $banco->nu_cpf_cnpj_dba }}
                        @if($banco->nm_titular_dba && $banco->nm_titular_dba !== ($pag->correspondente->nm_razao_social_con ?? ''))
                            &middot; Titular: {{ $banco->nm_titular_dba }}
                        @endif
                    </div>
                    @endif
                </td>
                <td class="pagamento-bloco__header-right">
                    <span class="badge badge-{{ $badgeCls }}">{{ $pag->nm_status }}</span>
                    <span class="pagamento-bloco__valor">R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Dados bancários --}}
    @if($banco && ($banco->nm_titular_dba || $banco->nm_banco_ban || $banco->dc_pix_dba || $banco->nu_conta_dba))
    <div class="pagamento-bloco__banco">
        <table class="pagamento-bloco__banco-tbl">
            <tr>
                @if($banco->nm_titular_dba)
                <td>
                    <div class="pagamento-bloco__label">Titular</div>
                    <div class="pagamento-bloco__valor-texto">{{ $banco->nm_titular_dba }}</div>
                </td>
                @endif
                @if($banco->nm_banco_ban)
                <td>
                    <div class="pagamento-bloco__label">Banco</div>
                    <div class="pagamento-bloco__valor-texto">{{ $banco->cd_banco_ban }} &ndash; {{ $banco->nm_banco_ban }}</div>
                </td>
                @endif
                @if($banco->nu_agencia_dba)
                <td>
                    <div class="pagamento-bloco__label">Agência</div>
                    <div class="pagamento-bloco__valor-texto">{{ $banco->nu_agencia_dba }}</div>
                </td>
                @endif
                @if($banco->nu_conta_dba)
                <td>
                    <div class="pagamento-bloco__label">Conta{{ $banco->nm_tipo_conta_tcb ? ' ('.$banco->nm_tipo_conta_tcb.')' : '' }}</div>
                    <div class="pagamento-bloco__valor-texto">{{ $banco->nu_conta_dba }}</div>
                </td>
                @endif
                @if($banco->dc_pix_dba)
                <td class="pix">
                    <div class="pagamento-bloco__label pagamento-bloco__label--pix">Chave PIX</div>
                    <div class="pagamento-bloco__valor-texto pagamento-bloco__valor-texto--pix">{{ $banco->dc_pix_dba }}</div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @else
    <div class="pagamento-bloco__sem-banco">Dados bancários não cadastrados.</div>
    @endif

    {{-- Processos --}}
    <table class="proc-table proc-table--detalhe">
        <thead>
            <tr>
                <th style="width:18%;">Processo</th>
                <th>Descrição</th>
                <th style="width:14%; text-align:right;">Honorário</th>
                <th style="width:14%; text-align:right;">Despesa</th>
                <th style="width:14%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($pag->itens->isEmpty())
            <tr>
                <td colspan="5" style="text-align:center; color:#aaa; padding:14px; font-style:italic; font-size:11px;">
                    Nenhum processo registrado.
                </td>
            </tr>
            @else
            @foreach($pag->itens as $itemIndex => $item)
            @php
                $excluido = strtoupper((string) ($item->fl_excluido_pai ?? 'N')) === 'S';
                $linhaZebra = $itemIndex % 2 === 1;
            @endphp
            <tr class="{{ $linhaZebra ? 'proc-zebra' : '' }}{{ $excluido ? ' proc-excluido' : '' }}">
                <td class="proc-numero">
                    @if($item->cd_processo_pro && $item->processo)
                        {{ $item->processo->nu_processo_pro ?? '#'.$item->cd_processo_pro }}
                    @elseif($item->cd_processo_pro)
                        #{{ $item->cd_processo_pro }}
                    @else
                        &mdash;
                    @endif
                </td>
                <td class="proc-descricao">{{ $item->ds_descricao_pai }}</td>
                <td style="text-align:right;">R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
                <td style="text-align:right;">R$ {{ number_format($item->vl_despesa_pai, 2, ',', '.') }}</td>
                <td style="text-align:right;"><strong>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</strong></td>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right; padding-right:10px;">Subtotal</td>
                <td style="text-align:right;">R$ {{ number_format($itensAtivos->sum('vl_honorario_pai'), 2, ',', '.') }}</td>
                <td style="text-align:right;">R$ {{ number_format($itensAtivos->sum('vl_despesa_pai'), 2, ',', '.') }}</td>
                <td style="text-align:right; color:#1a7bb9;">R$ {{ number_format($pag->vl_total_pag, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</div>
