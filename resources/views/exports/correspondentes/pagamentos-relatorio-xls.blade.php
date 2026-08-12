@php
    $totalGeral    = $pagamentos->sum('vl_total_pag');
    $totalPago     = $pagamentos->sum('vl_pago_total');
    $totalPendente = $pagamentos->sum('vl_saldo_total');
    $qtdTotal      = $pagamentos->count();
    $nmEscritorio  = $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? 'Escritório';
    $thStyle = 'background-color:#1a7bb9;color:#ffffff;font-weight:bold;border:1px solid #000000;text-align:center;vertical-align:center;';
    $tdStyle = 'border:1px solid #cccccc;vertical-align:center;';
    $tdRight = $tdStyle . 'text-align:right;';
@endphp
<table>
    <thead>
        <tr>
            <th colspan="5" style="{{ $thStyle }}background-color:#eef4fa;color:#1a7bb9;font-size:14px;">
                {{ $nmEscritorio }} — Relatório de Pagamentos de Correspondentes — {{ $mesAnoFmt }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="{{ $tdStyle }}text-align:right;color:#888888;font-size:10px;">
                Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
            </th>
        </tr>
        <tr>
            <th style="{{ $thStyle }}">Correspondentes</th>
            <th style="{{ $thStyle }}">Total do Mês</th>
            <th style="{{ $thStyle }}">A Pagar</th>
            <th style="{{ $thStyle }}">Pago</th>
            <th style="{{ $thStyle }}"></th>
        </tr>
        <tr>
            <td style="{{ $tdStyle }}text-align:center;font-weight:bold;">{{ $qtdTotal }}</td>
            <td style="{{ $tdRight }}font-weight:bold;">{{ number_format($totalGeral, 2, ',', '.') }}</td>
            <td style="{{ $tdRight }}font-weight:bold;">{{ number_format($totalPendente, 2, ',', '.') }}</td>
            <td style="{{ $tdRight }}font-weight:bold;">{{ number_format($totalPago, 2, ',', '.') }}</td>
            <td style="{{ $tdStyle }}"></td>
        </tr>
        <tr><td colspan="5"></td></tr>
        <tr>
            <th style="{{ $thStyle }}">Correspondente</th>
            <th style="{{ $thStyle }}">Tipo</th>
            <th style="{{ $thStyle }}">Dado de Pagamento</th>
            <th style="{{ $thStyle }}">Valor (R$)</th>
            <th style="{{ $thStyle }}">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $somaTotal = 0; @endphp
        @foreach($pagamentos as $index => $pag)
        @php
            $banco = $bancoPorPag[$pag->cd_pagamento_correspondente_pag] ?? null;
            $somaTotal += $pag->vl_total_pag;
            $tipoPgto = '';
            $dadoPgto = '';
            if ($banco) {
                if ($banco->dc_pix_dba) {
                    $tipoPgto = 'PIX';
                    $dadoPgto = $banco->dc_pix_dba;
                } elseif ($banco->nu_conta_dba) {
                    $tipoPgto = 'Conta';
                    $dadoPgto = ($banco->cd_banco_ban ? $banco->cd_banco_ban . ' · ' : '')
                              . 'Ag ' . ($banco->nu_agencia_dba ?? '—')
                              . ' · CC ' . $banco->nu_conta_dba;
                }
            }
            $zebra = $index % 2 === 1 ? 'background-color:#f4f8fc;' : '';
        @endphp
        <tr>
            <td style="{{ $tdStyle }}{{ $zebra }}">{{ $pag->correspondente->nm_razao_social_con ?? $pag->correspondente->nm_fantasia_con ?? '—' }}</td>
            <td style="{{ $tdStyle }}{{ $zebra }}text-align:center;">{{ $tipoPgto ?: '—' }}</td>
            <td style="{{ $tdStyle }}{{ $zebra }}">{{ $dadoPgto ?: 'Dado de pagamento não informado' }}</td>
            <td style="{{ $tdRight }}{{ $zebra }}">{{ number_format($pag->vl_total_pag, 2, ',', '.') }}</td>
            <td style="{{ $tdStyle }}{{ $zebra }}text-align:center;">{{ $pag->nm_status }}</td>
        </tr>
        @endforeach
        @if($pagamentos->isNotEmpty())
        <tr>
            <td colspan="3" style="{{ $tdStyle }}font-weight:bold;text-align:right;">Total Geral</td>
            <td style="{{ $tdRight }}font-weight:bold;">{{ number_format($somaTotal, 2, ',', '.') }}</td>
            <td style="{{ $tdStyle }}"></td>
        </tr>
        @endif
    </tbody>
</table>
