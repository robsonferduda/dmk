<style>
    @font-face {
        font-family: 'Varela';
        font-style: normal;
        font-weight: normal;
        src: url("https://fonts.googleapis.com/css2?family=Varela+Round") format('truetype');
    }
    
    /* Specify the elements to style */
    html {
        font-family: 'Varela', sans-serif;
    }
</style>
<h2>Pauta Diária</h2>
<span>Teste</span>
<table>
    <thead>
    <tr>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">RESPONSÁVEL</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">PRAZO FATAL</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">HORA</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">PARTE ADVERSA</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">RÉU</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">COMARCA</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">TIPO DE SERVIÇO CLIENTE</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">CLIENTE</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">FORO</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">Nº DOS AUTOS</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">CORRESPONDENTE</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">TIPO DE PROCESSO</th>
        <th style="background-color:#D99594;height:20px;font-size: 7px;border: 1px hair #000000;text-align: center;vertical-align: center">STATUS</th>
    </tr>
    </thead>
    <tbody>

        @foreach($dados['processos'] as $dado)
        <tr style="padding: 5px 3px;">
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->responsavel ? $dado->responsavel->name : ''}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center">
                 {{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : ' '}} 
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->hr_audiencia_pro ? date('H:i', strtotime($dado->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nm_autor_pro ? $dado->nm_autor_pro  : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
               {{ $dado->nm_reu_pro ? $dado->nm_reu_pro : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->cidade ? $dado->cidade->nm_cidade_cde : ' '}}-{{ $dado->cidade->estado ? $dado->cidade->estado->sg_estado_est : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->honorario ? $dado->honorario->tipoServico->nm_tipo_servico_tse : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->cliente->nm_razao_social_cli ? $dado->cliente->nm_razao_social_cli :'' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->vara ? $dado->vara->nm_vara_var : ' '}} 
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->nu_processo_pro ? $dado->nu_processo_pro : ' '}} 
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
                {{ $dado->correspondente ? $dado->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  : '' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
               {{ $dado->tipoProcesso ? $dado->tipoProcesso->nm_tipo_processo_tpo : '' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000;vertical-align: center" >
               {{ $dado->status ? $dado->status->nm_status_processo_conta_stp : '' }}
            </td>   
            
        </tr>
        @endforeach
   
    </tbody>
</table>