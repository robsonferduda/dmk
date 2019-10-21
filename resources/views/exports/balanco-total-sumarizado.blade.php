<table>
    <thead>
        <tr>
            <td colspan="4" style="text-align: center; vertical-align: center; height:50px;">{{ $dados['conta']->nm_razao_social_con }}</td>
        </tr>
    </thead>
    <tbody>
        @php
            $entradaTotal = 0;
            $saidaTotal = 0;
            $despesaTotal = 0;
        @endphp
      
        @foreach($dados['entradas'] as $dado)
            @php
               $entradaTotal += $dado['valor']+$dado['despesa'];
            @endphp
           
        @endforeach

      

        @foreach($dados['saidas'] as $dado)
            @php                 
               $saidaTotal += $dado['valor']+$dado['despesa'];
            @endphp

        @endforeach

        @foreach($dados['despesas'] as $dado)
        
            @php
                $despesaTotal += $dado['valor'];
            @endphp
    
        @endforeach
        @php
            $total = $entradaTotal - ($despesaTotal+$saidaTotal)
        @endphp

        <tr>
            <td colspan="3" style="font-weight: bold;">Despesas</td>
            <td>{{ number_format($despesaTotal,2,',',' ')  }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">Saídas</td>
            <td>{{ number_format($saidaTotal,2,',',' ')  }}</td>
        </tr>
        <tr>
            <td colspan="3"  style="font-weight: bold;">Entradas</td>
            <td>{{ number_format($entradaTotal,2,',',' ')  }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-weight: bold;">Saldo</td>
            <td style="font-weight: bold;" >{{ 'R$ '.number_format($total,2,',',' ')  }} </td>
        </tr>
            
    </tbody>
</table>