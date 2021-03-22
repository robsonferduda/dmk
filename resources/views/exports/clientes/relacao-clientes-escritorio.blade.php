<table>
    <thead>
    
    <tr>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center" colspan="{{ count($valores) }}">Lista de Clientes</th>
    </tr>
    <tr>
        @for($i = 0; $i < count($labels); $i++)
            <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">{{ $labels[$i] }}</th>
        @endfor
    </tr>
    </thead>
    <tbody>
        @foreach($dados as $cliente)
        <tr>  
            @if(in_array('nu_cliente_cli', $valores)) 
                @if($cliente['nu_cliente_cli'])     
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        {{ $cliente['nu_cliente_cli'] }}
                    </td>  
                @endif
            @endif
            @if(in_array('nm_razao_social_cli', $valores)) 
                @if($cliente['nu_cliente_cli'])     
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        {{ $cliente['nm_razao_social_cli'] }}
                    </td>   
                @endif
            @endif
            @if(in_array('email', $valores))  
                @if($cliente['email'])   
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        @foreach($cliente['email'] as $key => $email)
                            {{ $email->dc_endereco_eletronico_ede }}
                            @if($key > 0) {{"\r\n"}} @endif
                        @endforeach
                    </td>
                @else
                    <td style="border: 1px hair #000000;vertical-align: center" >
                    Não Informado
                    </td>
                @endif
            @endif
            @if(in_array('fone', $valores)) 
                @if($cliente['fone'])    
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        @foreach($cliente['fone'] as $key => $fone)
                            {{ $fone->nu_fone_fon }}
                            @if($key > 0) <br style="mso-data-placement:same-cell;" /> @endif
                        @endforeach
                    </td>  
                @else
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        Não Informado
                    </td>
                @endif 
            @endif 
            @if(in_array('flag', $valores)) 
                @if($cliente['nu_cliente_cli'])     
                    <td style="border: 1px hair #000000;vertical-align: center" >
                        {{ $cliente['flag'] }}
                    </td>   
                @endif
            @endif
        </tr>
        @endforeach   
    </tbody>
</table>