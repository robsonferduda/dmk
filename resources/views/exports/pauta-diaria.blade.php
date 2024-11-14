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
    table {border: none;}
</style>
<table cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="16" style="font-size: 7px; vertical-align: center">
            <p>Pauta Diária - Período de {{ $dt_inicio }} a {{ $dt_fim }}</p>
        </td>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: left; vertical-align: center; padding: 5px 3px; width: 120px;">Documento de Representação</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: left; vertical-align: center; padding: 5px 3px; width: 100px;">Dados Audiencistas</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Correspondente</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; padding: 5px 3px;">Responsável</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Prazo Fatal</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Hora</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Parte Adversa</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Réu</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Comarca</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center">Serviço Cliente</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Cliente</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Foro</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Nº dos Autos</th>        
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Tipo de Processo</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 150px;">Situação</th>
        <th style="background-color:#2c699d; color:white; height:20px;font-size: 7px;border: 1px hair #000000;text-align: center; vertical-align: center; width: 120px;">Observações</th>
    </tr>
    </thead>
    <tbody>

        @foreach($dados['processos'] as $dado)
        <tr style="padding: 5px 3px;">
            <td style="font-size: 7px; border: 1px hair #000000; vertical-align: center">
                @if($dado->fl_documento_representacao_pro == 'S')
                    <p style="color: #739e73;">Protocolado</p>
                @else
                    <p style="color: #a90329;">Pendente</p>
                @endif
            </td>
            <td style="font-size: 7px; border: 1px hair #000000; vertical-align: center; height: 100px;" >
                <p style="font-weight: bold;">Advogado</p>
                <p>{{ $dado->nm_advogado_pro ? $dado->nm_advogado_pro  : 'Não informado' }}</p>
                <p>------------------------------------------</p>
                <p>Preposto</p>
                <p>{{ $dado->nm_preposto_pro ? $dado->nm_preposto_pro  : 'Não informado' }}</p>
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->correspondente ? $dado->correspondente->contaCorrespondente->nm_conta_correspondente_ccr  : '' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center">
                {{ $dado->responsavel ? $dado->responsavel->name : ''}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center; text-align: center;">
                 {{ $dado->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($dado->dt_prazo_fatal_pro)) : ' '}} 
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center; text-align: center;" >
                {{ $dado->hr_audiencia_pro ? date('H:i', strtotime($dado->hr_audiencia_pro)) : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->nm_autor_pro ? $dado->nm_autor_pro  : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
               {{ $dado->nm_reu_pro ? $dado->nm_reu_pro : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->cidade ? $dado->cidade->nm_cidade_cde : ' '}}-{{ $dado->cidade->estado ? $dado->cidade->estado->sg_estado_est : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->honorario ? $dado->honorario->tipoServico->nm_tipo_servico_tse : ' '}}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->cliente->nm_razao_social_cli ? $dado->cliente->nm_razao_social_cli :'' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->vara ? $dado->vara->nm_vara_var : ' '}} 
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->nu_processo_pro ? $dado->nu_processo_pro : ' '}} 
            </td>            
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
               {{ $dado->tipoProcesso ? $dado->tipoProcesso->nm_tipo_processo_tpo : '' }}
            </td>
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
               {{ $dado->status ? $dado->status->nm_status_processo_conta_stp : '' }}
            </td>   
            <td style="font-size: 7px;border: 1px hair #000000; vertical-align: center" >
                {{ $dado->dc_observacao_processo_pro ? $dado->dc_observacao_processo_pro : '' }}
            </td>
        </tr>
        @endforeach
   
    </tbody>
</table>
<table>
    <tr>
        <td colspan="16" style="font-size: 7px; vertical-align: center" >
            <p>Gerada em {{ date("d/m/Y H:i:s") }}</p>
        </td>
    </tr>
</table>