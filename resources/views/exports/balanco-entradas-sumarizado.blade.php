<table>
    <thead>
        <tr>
            <td colspan="4" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
	    <tr>
	     	<td style="font-weight: bold;">Cliente</td>
            <td style="font-weight: bold;" >Honorários</td>
            <td style="font-weight: bold;">Despesas</td>
            <td style="font-weight: bold;">Total</td>

	    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
      
        @foreach($dados['entradas'] as $dado)
        <tr>
            <td>{{ $dado['cliente'] }}</td>
        	<td>{{ number_format($dado['valor'],2,',',' ') }}</td>
            <td>{{ number_format($dado['despesa'],2,',',' ') }}</td>   
            <td>{{ number_format($dado['total'],2,',',' ') }}</td>                         
        </tr>
        @php
            $total += $dado['total'];
        @endphp
        @endforeach
        <tr>
            <td colspan="3" style="font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ 'R$ '.number_format($total,2,',',' ')  }} </td>      
        </tr>
     
    </tbody>
</table>