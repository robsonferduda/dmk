<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualização cadastral – DMK Advogados</title>
</head>
<body style="margin:0; padding:0; background:#f2f4f7; font-family:Arial, Helvetica, sans-serif; color:#1f2328;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f4f7;">
    <tr>
        <td align="center" style="padding:24px 12px;">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%; max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e3e7ed;">
                <tr>
                    <td style="background:#1b2f4b; padding:22px 32px;">
                        <span style="font-size:20px; font-weight:bold; letter-spacing:3px; color:#ffffff;">DMK</span>
                        <span style="font-size:12px; letter-spacing:4px; color:#c9a96a; padding-left:6px;">ADVOGADOS</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px 32px 8px; font-size:15px; line-height:1.6;">
                        <p style="margin:0 0 16px;">Prezado(a) <strong>{{ $nome }}</strong>,</p>
                        <p style="margin:0 0 16px;">
                            O DMK ADVOGADOS está atualizando o cadastro de sua rede de correspondentes. Essa iniciativa é muito importante para o escritório:
                            permite manter uma comunicação eficiente, realizar os pagamentos corretamente e dar continuidade às nossas parcerias,
                            com a assinatura eletrônica do contrato de prestação de serviços.
                        </p>
                        <p style="margin:0 0 10px;">Pedimos que atualize seu cadastro até <strong>{{ $prazo }}</strong>, informando no formulário:</p>
                        <ul style="margin:0 0 20px; padding-left:22px;">
                            <li style="margin-bottom:4px;">Nome completo, CPF e RG;</li>
                            <li style="margin-bottom:4px;">Endereço completo com CEP;</li>
                            <li style="margin-bottom:4px;">Telefone com DDD e e-mail;</li>
                            <li style="margin-bottom:4px;">Número da OAB e seccional (ex.: OAB/ES 12.345);</li>
                            <li style="margin-bottom:4px;">Dados bancários e chave Pix;</li>
                            <li style="margin-bottom:4px;">Comarca/Estado de origem e as demais comarcas em que poderá nos atender pelos valores já acordados na parceria.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:4px 32px 24px;">
                        <a href="{{ $link }}" target="_blank"
                           style="display:inline-block; background:#a8834a; color:#ffffff; text-decoration:none; font-weight:bold; font-size:15px; letter-spacing:0.5px; padding:14px 28px; border-radius:6px;">
                            CLIQUE AQUI PARA ATUALIZAR SEU CADASTRO
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 8px; font-size:15px; line-height:1.6;">
                        <p style="margin:0 0 16px;">
                            Acesse o sistema com seu login e senha e atualize seus dados cadastrais dentro do sistema.
                            Seu login é o e-mail <strong>{{ $login }}</strong>.
                        </p>
                        <p style="margin:0 0 16px;">
                            Mesmo que seus dados não tenham mudado, acesse o formulário e confirme as informações,
                            para que possamos seguir com a formalização da parceria.
                        </p>
                        <p style="margin:0 0 16px;">
                            Agradecemos pela parceria e pela colaboração nesta etapa tão importante para o DMK.
                            Em caso de dúvidas, nossa equipe está à disposição pelo retorno deste e-mail.
                        </p>
                        <p style="margin:0 0 28px;">Atenciosamente,<br><strong>Equipe DMK ADVOGADOS</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f7f8fa; border-top:1px solid #e3e7ed; padding:16px 32px; font-size:12px; line-height:1.5; color:#6b7480;">
                        Se o botão não funcionar, copie e cole este endereço no navegador:<br>
                        <a href="{{ $link }}" style="color:#1b2f4b; word-break:break-all;">{{ $link }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@if($pixel)
    <img src="{{ $pixel }}" width="1" height="1" alt="" style="display:block; width:1px; height:1px; border:0;">
@endif
</body>
</html>
