<table>
    <thead>
    <tr>
        <th colspan="13" style="background-color:#969696;height:50px;border-bottom: 1px hair #000000;text-align: center;vertical-align: center;font-weight:bold;font-size:16px">{{ !empty($dados['cliente']->nm_razao_social_con) ? $dados['cliente']->nm_razao_social_con : 'Todos Clientes' }} 
        </th>       
    </tr>
    <tr>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">DATA DA SOLICITÇÃO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">DATA DO SERVIÇO REALIZADO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">ADVOGADO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">AUTOR</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">RÉU</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">NÚMERO DO PROCESSO</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">VARA</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">COMARCA</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">TIPO DO SERVIÇO CLIENTE</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">TIPO DO SERVIÇO CORRESPONDENTE</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">HONORÁRIOS</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">DESPESAS</th>
        <th style="background-color:#D99594;height:50px;border: 1px hair #000000;text-align: center;vertical-align: center">TOTAL</th>

    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach($dados['processos'] as $dado)
        <tr>            
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->dt_solicitacao_pro ? date('d/m/Y', strtotime($dado->dt_solicitacao_pro)) : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : ' '}} {{ $dado->hr_audiencia_pro ? date('H:i', strtotime($dado->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center">
                {{ ( $dado->nm_advogado_pro ? $dado->nm_advogado_pro : ' ' )  }}
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
                {{ $dado->honorario->tipoServico ? $dado->honorario->tipoServico->nm_tipo_servico_tse : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->honorario->tipoServicoCorrespondente ? $dado->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse : ' '}}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $dado->honorario ? 'R$ '.number_format($dado->honorario->vl_taxa_honorario_correspondente_pth, 2,',',' ') : number_format(0, 2,',',' ') }}
            </td>
            @php

                $totalDespesas = 0;
                foreach($dado->processoDespesa as $despesa){
                    if($despesa->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->fl_despesa_reembolsavel_pde == 'S'){
                        $totalDespesas += $despesa->vl_processo_despesa_pde;
                                            
                    }
                }

            @endphp
            <td>{{ 'R$ '.number_format($totalDespesas,2,',',' ') }}</td>
            @php
                $total += $totalDespesas+$dado->honorario->vl_taxa_honorario_correspondente_pth;
            @endphp
            <td>{{ 'R$ '.number_format($totalDespesas+$dado->honorario->vl_taxa_honorario_correspondente_pth,2,',',' ') }}</td>
        
        </tr>
        @endforeach
        <tr>
            <td colspan="13" style="background-color:#969696;height:50px;border-bottom: 1px hair #000000;text-align: center;vertical-align: center;font-weight:bold;font-size:12px">Total: {{ 'R$ '.number_format($total,2,',',' ') }}</td>
            
        </tr>
   
    </tbody>
</table>