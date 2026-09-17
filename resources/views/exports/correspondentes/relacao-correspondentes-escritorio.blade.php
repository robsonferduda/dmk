<table>
    <thead>
    <tr>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center" colspan="6">LISTA DE CORRESPONDENTES</th>
    </tr>
    <tr>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">CATEGORIA</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">COMARCA DE ORIGEM</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">CPF/CNPJ</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">NOME</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">OAB</th>
        <th style="background-color:#d2d2d2;height:20px;border: 1px hair #000000;text-align: center;vertical-align: center">EMAIL</th>
    </tr>
    </thead>
    <tbody>
        @foreach($dados['correspondentes'] as $correspondente)
        <tr>       
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ ($correspondente->dc_categoria_correspondente_cac) ? $correspondente->dc_categoria_correspondente_cac : 'Não informada' }}
            </td>     
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ ($correspondente->nm_cidade_cde) ? $correspondente->nm_cidade_cde : 'Não informado' }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ ($correspondente->nu_identificacao_ide) ? $correspondente->nu_identificacao_ide : 'Não informado' }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ $correspondente->nm_conta_correspondente_ccr }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ (!empty($correspondente->nu_oab_ide)) ? $correspondente->nu_oab_ide : '' }}
            </td>
            <td style="border: 1px hair #000000;vertical-align: center" >
                {{ ($correspondente->email) ? $correspondente->email : 'Não informado' }}
            </td>  
        </tr>
        @endforeach   
    </tbody>
</table>
