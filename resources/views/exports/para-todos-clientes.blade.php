<table>
    <thead>
    <tr>
        <th colspan="3" style="background-color:#969696;height:50px;border-bottom: 1px hair #000000;text-align: center;vertical-align: center;font-weight:bold;font-size:16px">Todos Clientes ({{ $dados['dtInicio']}} - {{ $dados['dtFim']}})</th>
        <th colspan="{{count($dados['despesas'])+10}}" style="background-color:#969696;height:50px;border-bottom:1px hair #000000;vertical-align: center;font-weight:bold;font-size:16px"></th>
    </tr>
    <tr>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">CLIENTE</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">ADVOGADO SOLICITANTE/CONTATO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">DATA DA SOLICITÇÃO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">DATA DO SERVIÇO REALIZADO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">AUTOR</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">RÉU</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">NÚMERO DO PROCESSO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">VARA</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">COMARCA</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">TIPO DO SERVIÇO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">Nº EXTERNO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">HONORÁRIOS</th>       
        @foreach($dados['despesas'] as $despesa)
            <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">{{ strtoupper($despesa->nm_tipo_despesa_tds) }}</th>
        @endforeach      
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">TOTAL</th>

    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach($dados['processos'] as $dado)
        <tr>
            <td style="border: 1px hair #000000;vertical-align: center">
                {{ $dado->cliente->nm_razao_social_cli  }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center">
                {{ ( $dado->advogadoSolicitante ? $dado->advogadoSolicitante->nm_contato_cot : ' ' )  }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->dt_solicitacao_pro ? date('d/m/Y', strtotime($dado->dt_solicitacao_pro)) : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : ' '}} {{ $dado->hr_audiencia_pro ? date('H:i', strtotime($dado->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nm_autor_pro ? $dado->nm_autor_pro : ' '}} 
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nm_reu_pro ? $dado->nm_reu_pro : ' '}} 
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nu_processo_pro ? $dado->nu_processo_pro : ' '}} 
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->vara ? $dado->vara->nm_vara_var : ' '}} 
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->cidade ? $dado->cidade->nm_cidade_cde : ' '}}-{{ $dado->cidade->estado ? $dado->cidade->estado->sg_estado_est : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->honorario ? $dado->honorario->tipoServico->nm_tipo_servico_tse : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nu_acompanhamento_pro ? $dado->nu_acompanhamento_pro : ' '}}
            </td>

            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->honorario ? $dado->honorario->vl_taxa_honorario_cliente_pth : 0 }}
            </td>
            @php
                $totalDespesas = 0;
                $despesaValor = 0;
            @endphp
            @foreach($dados['despesas'] as $despesa)
                <td style="border: 1px hair #000000;vertical-align: center" >
                @if($dado->tiposDespesa->where('cd_tipo_despesa_tds',$despesa->cd_tipo_despesa_tds)->first())
                    @php                       

                        $despesaValor = $dado->tiposDespesa->where('cd_tipo_despesa_tds',$despesa->cd_tipo_despesa_tds)->first()->pivot->vl_processo_despesa_pde;

                        $totalDespesas += $despesaValor;
                       
                    @endphp
                    {{ $despesaValor }}

                @else
                    {{ 0 }}
                @endif
                </td>
            @endforeach
           
            <td style="border: 1px hair #000000;vertical-align: center" >
                @php
                    $taxaHonorario = 0;
                    if(!empty($dado->honorario))
                        $taxaHonorario = $dado->honorario->vl_taxa_honorario_cliente_pth;
                    
                    $total += (float)$totalDespesas+(float)$taxaHonorario;
                @endphp

                {{ $totalDespesas+$taxaHonorario }}
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3" style="background-color:#969696;height:50px;border-bottom: 1px hair #000000;text-align: center;vertical-align: center;font-weight:bold;font-size:12px">Total: {{ 'R$ '.number_format($total,2,',',' ') }}</td>
            <td colspan="{{count($dados['despesas'])+10}}" style="background-color:#969696;height:50px;border-bottom: 1px hair #000000;vertical-align: center;font-weight:bold;font-size:12px"></td>
        </tr>
   
    </tbody>
</table>