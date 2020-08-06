<table>
    <thead>
        <tr>
            <td colspan="2" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
	    <tr>
	     	<td style="font-weight: bold;">Categoria</td>
            <td style="font-weight: bold;">Valor</td>

	    </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
      
        @foreach($dados['despesas'] as $dado)
        <tr>
            <td>{{ $dado['despesa'] }}</td>
            <td>{{ number_format($dado['valor'],2,',',' ') }} </td>
            @php
                $total += $dado['valor'];
            @endphp
        </tr>
        @endforeach
        <tr>
            <td colspan="1" style="font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ 'R$ '.number_format($total,2,',',' ')  }} </td>      
        </tr>
     
    </tbody>
</table>