<head>
    <title>Pauta Diária</title>
    <link href="{{ asset('css/relatorios-pdf.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    @include('relatorios.partes.cabecalho-horizontal')
    @php $zebra = true; @endphp
    <div id="corpo">
        <h4 style="text-align: center; margin-bottom: 0px; font-weight: 700; text-transform: uppercase; margin-top: 10px;"><strong>Pauta Diária</strong></h4>
        <p style="text-align: center; margin-top: 0px; font-size: 10px; margin-bottom: 5px;">Período de {{ $data_inicio }} a {{ $data_fim }}</p>

        <table style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr style="background:#DDD; }}">
                            <th style="text-align: center;">Documento de Representação</th>
                            <th style="">Dados Audiencistas</th>
                            <th style="">Correspondente</th>
                            <th style="">Responsável</th>
                            <th style="">Prazo Fatal</th>
                            <th style="">Parte Adversa</th>
                            <th style="">Réu</th>
                            <th style="">Comarca</th>
                            <th style="">Serviço</th>
                            <th style="">Cliente</th>                            
                            <th style="">Foro</th>
                            <th style="">Nº dos Autos</th>                            
                            <th style="">Tipo de Processo</th>
                            <th style="">Situação</th>
                            <th style="text-align: justify;">Observações</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($dados as $key => $processo)

        @php
            $cor_fundo = ($processo->fl_audiencia_confirmada_pro) ? '#c9ffcb' : 'white';
            $cor_borda = ($processo->fl_audiencia_confirmada_pro) ? '#95ff9a' : 'white';

            $cor_fundo = ($processo->fl_checkin_pro) ? '#c8e7ff' : $cor_fundo;
            $cor_borda = ($processo->fl_checkin_pro) ? '#a7d9ff' : $cor_borda;    

        @endphp 

        <tr style="background: {{ $cor_fundo }}; border: 1px solid {{ $cor_borda }}; border: node; border-bottom: 3px solid white;">
            <td style="text-align: center;">
                @if($processo->fl_documento_representacao_pro == 'S')
                    <p style="color: #739e73;">Protocolado</p>
                @else
                    <p style="color: #a90329;">Pendente</p>
                @endif
            </td>
            <td>
                <p><strong>Advogado</strong></p>
                {!! $processo->nm_advogado_pro ? $processo->nm_advogado_pro  : '<span class="text-danger">Não informado</span>' !!}
                <p><strong>Preposto</strong></p>
                {!! $processo->nm_preposto_pro ? $processo->nm_preposto_pro  : '<span class="text-danger">Não informado</span>' !!}
            </td>
            <td style="text-align: center;">{{ ($processo->correspondente and $processo->correspondente->contaCorrespondente) ? $processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  : '' }}</td>
            <td style="text-align: left; text-transform: uppercase; ">{{ $processo->responsavel ? $processo->responsavel->name : ''}}</td>
            <td style="text-align: left;">
                {{ $processo->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) : ' '}} 
                {{ $processo->hr_audiencia_pro ? date('H:i', strtotime($processo->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="text-align: center;"> {{ $processo->nm_autor_pro ? $processo->nm_autor_pro  : ' '}}</td>          
            <td style="text-align: center;">{{ $processo->nm_reu_pro ? $processo->nm_reu_pro : ' '}}</td>
            <td style="text-align: center;">{{ $processo->cidade ? $processo->cidade->nm_cidade_cde : ' '}}-{{ $processo->cidade->estado ? $processo->cidade->estado->sg_estado_est : ' '}}</td>
            <td style="text-align: center;"> {{ $processo->honorario ? $processo->honorario->tipoServico->nm_tipo_servico_tse : ' '}}</td>
            <td style="text-align: center;">{{ $processo->cliente->nm_razao_social_cli ? $processo->cliente->nm_razao_social_cli :'' }}</td>
            <td style="text-align: center;">{{ $processo->vara ? $processo->vara->nm_vara_var : ' '}} </td>
            <td style="text-align: center;">{{ $processo->nu_processo_pro ? $processo->nu_processo_pro : ' '}} </td>            
            <td style="text-align: center;">{{ $processo->tipoProcesso ? $processo->tipoProcesso->nm_tipo_processo_tpo : '' }}</td>
            <td style="text-align: center;">{{ $processo->status ? $processo->status->nm_status_processo_conta_stp : '' }}</td>
            <td style="text-align: justify;">{{ $processo->dc_observacao_processo_pro ? $processo->dc_observacao_processo_pro : '' }}</td>
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