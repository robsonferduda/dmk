<head>
    <title>Relatório de Cobrança</title>
    <link href="{{ asset('css/relatorios-pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body { font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4a4a4a; color: #fff; padding: 5px 4px; text-align: center; font-size: 9px; }
        td { padding: 4px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .tfoot-row td { background-color: #333; color: #fff; font-weight: bold; text-align: right; padding: 5px 4px; }
        h4 { text-align: center; margin-bottom: 3px; font-weight: 700; text-transform: uppercase; }
        p.sub { text-align: center; font-size: 9px; margin-top: 0; margin-bottom: 8px; }
    </style>
</head>
<body>
    @include('relatorios.partes.cabecalho-horizontal')

    <div id="corpo">
        <h4>Relatório de Cobrança</h4>
        <p class="sub">
            {{ $processos->first()->cliente->nm_razao_social_cli ?? '' }} &mdash;
            Período: {{ $dtInicio }} a {{ $dtFim }}
        </p>

        @php $total = 0; @endphp

        <table>
            <thead>
                <tr>
                    <th>ADV. SOLICITANTE</th>
                    <th>DT. SOLICITAÇÃO</th>
                    <th>DT. SERVIÇO</th>
                    <th>AUTOR</th>
                    <th>RÉU</th>
                    <th>Nº PROCESSO</th>
                    <th>VARA</th>
                    <th>COMARCA</th>
                    <th>SERVIÇO</th>
                    <th>Nº EXTERNO</th>
                    <th>HONORÁRIOS</th>
                    @foreach($despesas as $despesa)
                        <th>{{ strtoupper($despesa->nm_tipo_despesa_tds) }}</th>
                    @endforeach
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processos as $dado)
                    @php $totalDespesas = 0; @endphp
                    <tr>
                        <td>{{ $dado->advogadoSolicitante ? $dado->advogadoSolicitante->nm_contato_cot : '-' }}</td>
                        <td style="text-align:center">{{ $dado->dt_solicitacao_pro ? date('d/m/Y', strtotime($dado->dt_solicitacao_pro)) : '-' }}</td>
                        <td style="text-align:center">{{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : '-' }}</td>
                        <td>{{ $dado->nm_autor_pro ?? '-' }}</td>
                        <td>{{ $dado->nm_reu_pro ?? '-' }}</td>
                        <td style="text-align:center">{{ $dado->nu_processo_pro ?? '-' }}</td>
                        <td>{{ $dado->vara ? $dado->vara->nm_vara_var : '-' }}</td>
                        <td>{{ $dado->cidade ? $dado->cidade->nm_cidade_cde : '-' }}{{ $dado->cidade && $dado->cidade->estado ? '/' . $dado->cidade->estado->sg_estado_est : '' }}</td>
                        <td>{{ $dado->honorario && $dado->honorario->tipoServico ? $dado->honorario->tipoServico->nm_tipo_servico_tse : '-' }}</td>
                        <td style="text-align:center">{{ $dado->nu_acompanhamento_pro ?? '-' }}</td>
                        <td style="text-align:right">{{ number_format($dado->honorario ? $dado->honorario->vl_taxa_honorario_cliente_pth : 0, 2, ',', '.') }}</td>
                        @foreach($despesas as $despesa)
                            @php
                                $item = $dado->tiposDespesa->where('cd_tipo_despesa_tds', $despesa->cd_tipo_despesa_tds)->first();
                                $v = $item ? (float) $item->pivot->vl_processo_despesa_pde : 0;
                                $totalDespesas += $v;
                            @endphp
                            <td style="text-align:right">{{ number_format($v, 2, ',', '.') }}</td>
                        @endforeach
                        <td style="text-align:right; font-weight:bold">
                            @php
                                $taxaHonorario = $dado->honorario ? (float) $dado->honorario->vl_taxa_honorario_cliente_pth : 0;
                                $totalLinha = $totalDespesas + $taxaHonorario;
                                $total += $totalLinha;
                            @endphp
                            {{ number_format($totalLinha, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tfoot-row">
                    <td colspan="{{ 11 + count($despesas) }}">TOTAL GERAL</td>
                    <td>R$ {{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
