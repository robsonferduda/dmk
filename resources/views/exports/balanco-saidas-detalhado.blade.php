<table>
    <thead>
        <tr>
            <td colspan="8" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
	    <tr>
            <td style="font-weight: bold;">Data da Baixa</td>
	     	<td style="font-weight: bold;">Correspondente</td>
            <td style="font-weight: bold;">Processo</td>
            <td style="font-weight: bold;">Prazo Fatal</td>
            <td style="font-weight: bold;">Tipo de Serviço</td>
            <td style="font-weight: bold;" >Honorários</td>
            <td style="font-weight: bold;">Despesas</td>
            <td style="font-weight: bold;">Total</td>

	    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
      
        @foreach($dados['saidas'] as $dado)
        <tr>
            <td> {{ !empty($dado->honorario->dt_baixa_correspondente_pth) ? date('d/m/Y', strtotime($dado->honorario->dt_baixa_correspondente_pth)) : '' }}</td>
        	<td>{{ $dado->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  }}</td>
            <td>{{ $dado->nu_processo_pro }}</td>   
            <td>{{ date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) }}</td>     
            <td>{{ !empty($dado->honorario->tipoServicoCorrespondente) ? $dado->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse : ' ' }}</td>
            <td>{{ $dado->honorario ? number_format($dado->honorario->vl_taxa_honorario_correspondente_pth, 2,',',' ') : number_format(0, 2,',',' ') }}</td>
             @php
                $totalDespesas = 0;
                foreach($dado->tiposDespesa as $despesa){
                    if($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S'){
                        $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                    }
                }

             @endphp
            <td>{{ number_format($totalDespesas,2,',',' ') }}</td>
        
            @php
                $totalLinha = 0;
                if(!empty($dado->honorario)){
                    $totalLinha = $dado->honorario->vl_taxa_honorario_correspondente_pth+$totalDespesas;
                }
                $total +=  $totalLinha;
            @endphp

            <td>{{ number_format($totalLinha,2,',',' ') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="7" style="font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ 'R$ '.number_format($total,2,',',' ')  }} </td>      
        </tr>
     
    </tbody>
</table>