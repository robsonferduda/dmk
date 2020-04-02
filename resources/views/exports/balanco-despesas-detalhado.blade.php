<table>
    <thead>
        <tr>
            <td colspan="6" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
	    <tr>
            <td style="font-weight: bold;">Data de Pagamento</td>
	     	<td style="font-weight: bold;">Categoria</td>
            <td style="font-weight: bold;">Tipo</td>
            <td style="font-weight: bold;">Descrição</td>            
            <td style="font-weight: bold;">Data de Vencimento</td>
            <td style="font-weight: bold;">Valor</td>

	    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
      
        @foreach($dados['despesas'] as $dado)
        <tr>
            <td>{{ ($dado->dt_pagamento_des) ? date('d/m/Y', strtotime($dado->dt_pagamento_des)) : '' }}</td>
            <td> 
            {{ ($dado->tipo->categoriaDespesa) ? $dado->tipo->categoriaDespesa->nm_categoria_despesa_cad : '' }} </td>
        	<td>{{ ($dado->tipo) ? $dado->tipo->nm_tipo_despesa_tds : '' }}</td>
            <td>{{ ($dado->dc_descricao_des) ? $dado->dc_descricao_des : $dado->tipo->nm_tipo_despesa_tds }}</td>
            <td>{{ ($dado->dt_vencimento_des) ? date('d/m/Y', strtotime($dado->dt_vencimento_des)) : '' }}</td>
            <td>{{ $dado->vl_valor_des }}</td>
            @php
                $total += $dado->vl_valor_des;
            @endphp
        </tr>
        @endforeach
        <tr>
            <td colspan="5" style="font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ 'R$ '.number_format($total,2,',',' ')  }} </td>      
        </tr>
     
    </tbody>
</table>