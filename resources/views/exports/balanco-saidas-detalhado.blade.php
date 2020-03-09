<table>
    <thead>
        <tr>
            <td colspan="8" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
	    <tr>            
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
            $tipo = $dados['tipo'];
        @endphp
      
        @foreach($dados['saidas'] as $dado)
        @php
            $totalHonorarioSaida = 0;
            if($tipo == 'R'){

                if(!empty($dado->honorario->baixaHonorario))
                     $totalHonorarioSaida = $dado->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::SAIDA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::HONORARIO)->sum('vl_baixa_honorario_bho');
            }
        @endphp
        <tr>            
        	<td>{{ $dado->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  }}</td>
            <td>{{ $dado->nu_processo_pro }}</td>   
            <td>{{ date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) }}</td>     
            <td>{{ !empty($dado->honorario->tipoServicoCorrespondente) ? $dado->honorario->tipoServicoCorrespondente->nm_tipo_servico_tse : ' ' }}</td>

            @if($tipo == 'P')
                <td>{{ $dado->honorario ? number_format($dado->honorario->vl_taxa_honorario_correspondente_pth, 2,',',' ') : number_format(0, 2,',',' ') }}</td>
            @else
                <td>{{ $dado->honorario ? number_format($totalHonorarioSaida, 2,',',' ') : number_format(0, 2,',',' ') }}</td>
            @endif
           
             @php
                $totalDespesas = 0;

                if($tipo == 'P'){
                    foreach($dado->tiposDespesa as $despesa){
                        if($despesa->pivot->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->pivot->fl_despesa_reembolsavel_pde == 'S'){
                            $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                        }
                    }
                }else{
                    $totalDespesas = $dado->honorario->baixaHonorario->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::SAIDA)->where('cd_tipo_baixa_honorario_bho', \TipoBaixaHonorario::DESPESA)->sum('vl_baixa_honorario_bho');
                }

             @endphp
            <td>{{ number_format($totalDespesas,2,',',' ') }}</td>
        
            @php
                $totalLinha = 0;
                if(!empty($dado->honorario)){

                    if($tipo == 'P'){

                        $totalLinha = $dado->honorario->vl_taxa_honorario_correspondente_pth+$totalDespesas;

                    }else{

                       $totalLinha = $totalHonorarioSaida+$totalDespesas;
                    }

                    
                }
                $total +=  $totalLinha;
            @endphp

            <td>{{ number_format($totalLinha,2,',',' ') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6" style="font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ 'R$ '.number_format($total,2,',',' ')  }} </td>      
        </tr>
     
    </tbody>
</table>