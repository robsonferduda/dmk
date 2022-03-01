<table>
    <thead>
    <tr>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Processo</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Razão Social</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Tipo de Serviço</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Prazo</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Valor</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Titular</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">CPF/CNPJ</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Tipo de Conta</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">Dados Bancários</th>
    </tr>
    </thead>
    <tbody>
        @php
            $i = 0;

        @endphp
        @foreach($dados['processos'] as $processo)
        @php
           $i++;
           $primeira_passada = true;
           //rowspan="{{ count($processo['registros_bancarios']) }}"
        @endphp

            @foreach ($processo['registros_bancarios'] as $dadosb)
               
                <tr>       
                @if($primeira_passada)
                    <td rowspan="{{ count($processo['registros_bancarios']) }}" style="border: 1px hair #000000;text-align: center;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}"  >
                        {{ $processo['processo']}}
                    </td>     
                    <td rowspan="{{ count($processo['registros_bancarios']) }}" style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $processo['razao_social'] }}
                    </td>
                    <td rowspan="{{ count($processo['registros_bancarios']) }}" style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $processo['tipo'] }}
                    </td>
                    <td rowspan="{{ count($processo['registros_bancarios']) }}" style="border: 1px hair #000000;text-align: center;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $processo['data'] }}
                    </td>
                    <td rowspan="{{ count($processo['registros_bancarios']) }}" style="border: 1px hair #000000;text-align: center;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $processo['valor'] }}
                    </td>  
               
                @endif
                @php
                    $primeira_passada = false;                
                @endphp
                    <td style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $dadosb['titular'] }}
                    </td> 
                    <td style="border: 1px hair #000000;text-align: center;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $dadosb['cpf_cnpj'] }}
                    </td>
                    <td style="border: 1px hair #000000;text-align: center;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                        {{ $dadosb['tipo'] }}
                    </td> 
                    @if(!empty($dadosb['tipo']))                    
                        @if($dadosb['tipo'] == 'PIX')
                            <td style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                                {{ $dadosb['pix'] }}
                            </td> 
                        @else 
                            <td style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                                {{ $dadosb['banco'] }} / Agência  {{ $dadosb['agencia'] }} / Conta  {{ $dadosb['conta'] }}
                            </td> 
                        @endif
                    @else 
                        <td style="border: 1px hair #000000;vertical-align: center;{{ ($i%2 == 0) ? 'background-color:#CDE5CD' : '' }}" >
                          
                        </td> 
                    @endif

                </tr>

            @endforeach
       
        @endforeach   
        <tr>
            <td colspan="4" style="border: 1px hair #000000;text-align: center;vertical-align: center;">TOTAL</td>    
            <td style="border: 1px hair #000000;text-align: center;vertical-align: center;">{{ $dados['valor_total'] }}</td>
        </tr>
    </tbody>
</table>