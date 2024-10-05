<head>
    <title>Totais de Banheiros e Cabines</title>
    <link href="{{ asset('css/relatorios-pdf.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    @include('relatorios.partes.cabecalho-horizontal')
    @php $zebra = true; @endphp
    <div id="corpo">
        <h4 style="text-align: center; margin-bottom: 0px; font-weight: 700; text-transform: uppercase; margin-top: 10px;"><strong>Pauta Diária</strong></h4>
        <p style="text-align: center; margin-top: 0px; font-size: 10px; margin-bottom: 5px;">Período de {{ $data_inicio }} a {{ $data_fim }}</p>

        <table id="tabelaDados" border="0" cellspacing="0" width="100%">
                    <thead>
                        <tr style="background:#DDD; }}">
                            <th style="">Prazo Fatal</th>
                            <th style="">Responsável</th>
                            <th style="">Parte Adversa</th>
                            <th style="">Réu</th>
                            <th style="">Comarca</th>
                            <th style="">Cliente</th>
                            <th style="">Serviço</th>
                            <th style="">Foro</th>
                            <th style="">Nº dos Autos</th>
                            <th style="">Correspondente</th>
                            <th style="">Tipo de Processo</th>
                            <th style="">Situação</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($dados as $key => $processo)      

        <tr style="background: {{ ($zebra) ? '#FFFFFF;' : '#DDD;' }}">
            <td style="text-align: left;">
                {{ $processo->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : ' '}} 
                {{ $processo->hr_audiencia_pro ? date('H:i', strtotime($processo->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="text-align: left; text-transform: uppercase; ">{{ $processo->responsavel ? $processo->responsavel->name : ''}}</td>
            <td style="text-align: center;"> {{ $processo->nm_autor_pro ? $processo->nm_autor_pro  : ' '}}</td>
            <td style="text-align: center;">{{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</td>
            <td style="text-align: center;">{{ $processo->cidade ? $processo->cidade->nm_cidade_cde : ' '}}-{{ $processo->cidade->estado ? $processo->cidade->estado->sg_estado_est : ' '}}</td>
            <td style="text-align: center;">{{ $processo->cliente->nm_razao_social_cli ? $processo->cliente->nm_razao_social_cli :'' }}</td>
            <td style="text-align: center;"> {{ $processo->honorario ? $processo->honorario->tipoServico->nm_tipo_servico_tse : ' '}}</td>
            <td style="text-align: center;">{{ $processo->vara ? $processo->vara->nm_vara_var : ' '}} </td>
            <td style="text-align: center;">{{ $processo->nu_processo_pro ? $processo->nu_processo_pro : ' '}} </td>
            <td style="text-align: center;">{{ ($processo->correspondente and $processo->correspondente->contaCorrespondente) ? $processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  : '' }}</td>
            <td style="text-align: center;">{{ $processo->tipoProcesso ? $processo->tipoProcesso->nm_tipo_processo_tpo : '' }}</td>
            <td style="text-align: center;">{{ $processo->status ? $processo->status->nm_status_processo_conta_stp : '' }}</td>
        </tr>    
        @php 
            $zebra = !$zebra;     
        @endphp
    @empty
        <h4 class="center">Nenhum dado para ser exibido</h4>
    @endforelse
        </tbody>
    </table>    
</div>
    @include('relatorios.partes.footer')
</body>