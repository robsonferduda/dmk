{{-- Contrato de correspondência gerado em PDF pelo mPDF (ContratoCorrespondenteGenerator::renderizarPdf) --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Contrato de Correspondência</title>
<style>
    @page {
        header: html_cabecalho;
        footer: html_rodape;
    }
    @page :first {
        header: html_cabecalhoVazio;
    }
    body {
        font-family: sourceserif;
        font-size: 10.2pt;
        line-height: 1.5;
        color: #1f2328;
    }
    p { margin: 0 0 2.4mm; text-align: justify; }
    strong { font-weight: bold; color: #0f1b2d; }

    .cab-tabela, .rod-tabela { width: 100%; border-collapse: collapse; font-family: nunitosans; }
    .cab-tabela td { padding-bottom: 2mm; border-bottom: 0.5pt solid #d9dee5; vertical-align: bottom; }
    .cab-marca { font-family: nunitosansxb; font-size: 8.5pt; letter-spacing: 1.5pt; color: #1b2f4b; }
    .cab-marca span { color: #a8834a; }
    .cab-titulo { font-size: 7pt; color: #7a8594; text-align: right; }
    .rod-tabela td { padding-top: 2mm; border-top: 0.5pt solid #d9dee5; font-size: 7pt; color: #7a8594; vertical-align: top; }
    .rod-pagina { text-align: right; font-family: nunitosans; font-weight: bold; color: #1b2f4b; }

    .timbre { width: 100%; border-collapse: collapse; margin-bottom: 7mm; }
    .timbre td { vertical-align: bottom; padding-bottom: 3mm; border-bottom: 1.6pt solid #1b2f4b; }
    .timbre-marca { font-family: nunitosansxb; font-size: 24pt; color: #1b2f4b; letter-spacing: 2pt; line-height: 1; }
    .timbre-sub { font-family: nunitosans; font-weight: bold; font-size: 7.5pt; letter-spacing: 3.2pt; color: #a8834a; }
    .timbre-dados { font-family: nunitosans; font-size: 7.3pt; line-height: 1.45; color: #5d6877; text-align: right; }

    .sobretitulo { font-family: nunitosans; font-weight: bold; font-size: 7.5pt; letter-spacing: 2pt; color: #a8834a; text-transform: uppercase; margin: 0 0 1.5mm; text-align: left; }
    h1 { font-family: nunitosansxb; font-size: 14.5pt; line-height: 1.25; color: #1b2f4b; margin: 0 0 4mm; text-align: left; font-weight: normal; }
    .meta { width: 100%; border-collapse: collapse; margin: 0 0 6mm; font-family: nunitosans; }
    .meta td { padding: 2.2mm 3mm; background-color: #f4f6f9; border-left: 2pt solid #a8834a; vertical-align: top; }
    .meta td.sep { background-color: #ffffff; border-left: 0; width: 3mm; padding: 0; }
    .meta-rotulo { font-size: 6.8pt; letter-spacing: 1pt; text-transform: uppercase; color: #7a8594; font-weight: bold; }
    .meta-valor { font-size: 9pt; color: #1b2f4b; font-weight: bold; }

    .partes { background-color: #f4f6f9; border-left: 2pt solid #1b2f4b; padding: 4mm 5mm 2mm; margin: 0 0 5mm; }
    .partes-texto { font-size: 10pt; }

    .considerando-p { padding-left: 4mm; border-left: 0.8pt solid #d9dee5; }
    .considerando { font-weight: bold; color: #1b2f4b; }
    .resolvem { margin: 5mm 0 2mm; padding: 3mm 0; border-top: 0.5pt solid #d9dee5; border-bottom: 0.5pt solid #d9dee5; font-weight: bold; color: #1b2f4b; text-align: center; }

    .clausula { margin: 7mm 0 3mm; padding-bottom: 1.6mm; border-bottom: 0.5pt solid #d9dee5; page-break-after: avoid; }
    .clausula-num { font-family: nunitosans; font-weight: bold; font-size: 7.5pt; letter-spacing: 1.8pt; color: #a8834a; }
    .clausula-titulo { font-family: nunitosansxb; font-size: 10.5pt; color: #1b2f4b; letter-spacing: .3pt; }
    .subtitulo { font-family: nunitosans; font-weight: bold; font-size: 8.8pt; letter-spacing: .8pt; color: #1b2f4b; margin: 4.5mm 0 2mm; text-align: left; }

    .numerado .n, .texto .n { font-family: nunitosans; font-weight: bold; color: #1b2f4b; }
    .item { margin-left: 9mm; text-indent: -5mm; margin-bottom: 1.8mm; }
    .item .letra { font-family: nunitosans; font-weight: bold; color: #a8834a; }
    .lista { margin: 0 0 3mm 4mm; padding-left: 5mm; }
    .lista li { margin-bottom: .8mm; }
    .destaque { background-color: #fbf7f0; border-left: 2pt solid #a8834a; padding: 3mm 4mm; font-size: 9.3pt; }

    .banco { width: 100%; border-collapse: collapse; margin: 1mm 0 4mm; font-family: nunitosans; }
    .banco td { padding: 2mm 3mm; border: 0.5pt solid #d9dee5; vertical-align: top; }
    .banco-rotulo { font-size: 6.5pt; letter-spacing: 1pt; text-transform: uppercase; color: #7a8594; font-weight: bold; }
    .banco-valor { font-size: 9pt; color: #1f2328; }
    .banco-valor strong { color: #0f1b2d; }

    .manter-junto { page-break-inside: avoid; }
    .assinaturas { width: 100%; border-collapse: collapse; page-break-inside: avoid; margin-top: 2mm; }
    .assinaturas td { vertical-align: top; padding: 0; }
    .assinaturas td.fecho { text-align: justify; padding: 4mm 0 0; }
    .assinaturas td.local-data { text-align: center; font-weight: bold; color: #1b2f4b; padding: 3mm 0 10mm; }
    .assinaturas td.ass-papel { width: 47%; font-family: nunitosans; font-weight: bold; font-size: 7pt; letter-spacing: 1.8pt; color: #a8834a; text-transform: uppercase; height: 16mm; }
    .assinaturas td.ass-papel-testemunha { height: 22mm; padding-top: 10mm; }
    .assinaturas td.ass-vao { width: 6%; }
    .assinaturas td.ass-dados { border-top: 0.8pt solid #1f2328; padding-top: 2mm; }
    .ass-nome { font-family: nunitosans; font-weight: bold; font-size: 8.5pt; color: #1b2f4b; line-height: 1.3; }
    .ass-campo { font-family: nunitosans; font-size: 8pt; color: #5d6877; line-height: 1.5; }

    .anexo-titulo { font-family: nunitosansxb; font-size: 13pt; color: #1b2f4b; margin: 0 0 1mm; text-align: left; }
    .anexo-resumo { font-family: nunitosans; font-size: 8pt; color: #5d6877; margin: 0 0 5mm; text-align: left; }
    .uf { margin: 0 0 3.5mm; }
    .uf-titulo { font-family: nunitosans; font-weight: bold; font-size: 8pt; color: #1b2f4b; letter-spacing: .6pt; text-transform: uppercase; border-bottom: 0.5pt solid #d9dee5; padding-bottom: .8mm; margin-bottom: 1.2mm; }
    .uf-titulo span { color: #a8834a; font-weight: normal; letter-spacing: 0; text-transform: none; }
    .uf-lista { font-family: nunitosans; font-size: 7.2pt; line-height: 1.45; color: #3a4452; margin: 0; }
</style>
</head>
<body>

<htmlpageheader name="cabecalhoVazio"></htmlpageheader>

<htmlpageheader name="cabecalho">
    <table class="cab-tabela">
        <tr>
            <td class="cab-marca">DMK <span>ADVOGADOS</span></td>
            <td class="cab-titulo">Contrato de Prestação de Serviços de Advocacia em Regime de Correspondência</td>
        </tr>
    </table>
</htmlpageheader>

<htmlpagefooter name="rodape">
    <table class="rod-tabela">
        <tr>
            <td style="width: 70%;">{{ $nomeContratado ?: 'Contratado (a)' }}</td>
            <td class="rod-pagina" style="width: 30%;">Página {PAGENO} de {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>

<table class="timbre">
    <tr>
        <td>
            <div class="timbre-marca">DMK</div>
            <div class="timbre-sub">ADVOGADOS</div>
        </td>
        <td class="timbre-dados">
            Deborah Mekacheski Pereira Sociedade Individual de Advocacia<br>
            CNPJ 19.439.096/0001-17 · OAB/SC 2178/2013<br>
            Rua Saldanha Marinho, 374, sala 806 · Centro · Florianópolis/SC · 88053-300
        </td>
    </tr>
</table>

<p class="sobretitulo">Instrumento particular</p>
<h1>CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE ADVOCACIA EM REGIME DE CORRESPONDÊNCIA</h1>

<table class="meta">
    <tr>
        <td style="width: 70%;">
            <div class="meta-rotulo">Contratado (a)</div>
            <div class="meta-valor">{{ $nomeContratado ?: '—' }}</div>
        </td>
        <td class="sep"></td>
        <td>
            <div class="meta-rotulo">Emissão</div>
            <div class="meta-valor">{{ $dataEmissao }}</div>
        </td>
    </tr>
</table>

<div class="partes">
    <p class="partes-texto">{!! $textoPartes !!}</p>
</div>
<p class="considerando-p"><span class="considerando">Considerando</span> que a CONTRATANTE patrocina clientes que demandam ou são demandados em todos os Estados do Brasil.</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que o (a) CONTRATADO (A) declara, sob fé de grau, dispor de condições de pessoal, técnicas, operacionais, financeiras, administrativas e de logística para o integral, tempestivo e correto atendimento dos serviços de advocacia indicados no objeto do presente instrumento, {!! $trechoComarcas !!}, de acordo com as exigências descritas neste instrumento;</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que, por este instrumento, independentemente de qualquer formalidade adicional, a CONTRATANTE credencia e autoriza o (a) CONTRATADO (A) para a prestação dos serviços constantes do objeto, em regime de correspondência, nos termos e limites deste instrumento;</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que, por regime de correspondência, se entende o credenciamento outorgado pela CONTRATANTE para a prática, pelo (a) CONTRATADO (A), de todas as atividades de advocacia indicadas no presente contrato, consideradas de meio em relação às atividades descritas no art. 1º, I, do Estatuto da Advocacia;</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que cada uma das partes contratantes conta com carteira própria de clientes e que as sociedades signatárias expressamente manifestam seu desinteresse no compartilhamento de clientela eis que o presente instrumento está limitado ao ajuste ora firmado de regime de correspondência, e que assim restam inalterados os direitos, os deveres e as responsabilidades de cada parte contratante;</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que o presente instrumento não é firmado em regime de exclusividade e que sua formalização não gera vínculos entre as partes contratantes de natureza societária, fiscal, trabalhista ou de qualquer outra natureza excedente do regime de correspondência.</p>
<p class="considerando-p"><span class="considerando">Considerando</span> que as obrigações e direitos decorrentes do presente instrumento têm por finalidade única e exclusiva a execução adequada dos serviços indicados no objeto contratual em favor dos clientes da CONTRATANTE, conforme solicitação específica por ato;</p>
<p class="resolvem">RESOLVEM, em comum acordo, celebrar o presente CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE ADVOCACIA EM REGIME DE CORRESPONDÊNCIA, sob as cláusulas seguintes.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA PRIMEIRA</div>
    <div class="clausula-titulo">DO OBJETO</div>
</div>
<p class="numerado"><span class="n">1.1.</span> Constitui objeto deste contrato o credenciamento do (a) CONTRATADO (A), pela CONTRATANTE, para a finalidade única e exclusiva de prestação de serviços de advocacia em regime de correspondência, de caráter judicial e extrajudicial, em especial a realização de audiências, sustentação oral e acompanhamento de julgamentos, protocolo de petições, efetivação de diligências, extração de fotocópias, distribuição de ações, solicitação de certidões, despachos com magistrados, negociações e acordos, gestão da expedição e levantamento de alvarás e todos e quaisquer atos e diligências necessários ao acompanhamento dos procedimentos judiciais e extrajudiciais sob responsabilidade técnica e patrocínio da CONTRATANTE nas Comarcas descritas acima, tudo mediante pedido expresso por ato e de acordo com o estabelecido no Manual de Procedimentos para Advogados Correspondentes, no presente instrumento e com demais diretrizes, instruções e autorizações prévias da CONTRATANTE.</p>
</div>
<p class="texto"><span class="n">Parágrafo único.</span> O (A) CONTRATADO (A) declara ciência e concordância de que todas as demandas deverão ser obrigatoriamente recebidas, aceitas, acompanhadas, executadas e finalizadas através do sistema operacional da CONTRATANTE (“Sistema DMK”), comprometendo-se a cumprir integralmente o fluxo operacional estabelecido pela CONTRATANTE, incluindo, mas não se limitando:</p>
<p class="item"><span class="letra">a)</span> ao aceite formal do ato dentro do prazo estipulado pela CONTRATANTE;</p>
<p class="item"><span class="letra">b)</span> à indicação do advogado e/ou preposto responsável pela realização do ato no prazo máximo de 48 (quarenta e oito) horas após o aceite da demanda, ressalvadas as hipóteses de urgência, nas quais os dados deverão ser informados imediatamente ou dentro do prazo especificamente solicitado pela CONTRATANTE;</p>
<p class="item"><span class="letra">c)</span> ao acompanhamento, preparação e realização integral do ato contratado, bem como ao imediato envio de confirmação de recebimento de todos os documentos, orientações, links e informações necessárias para a realização do ato;</p>
<p class="item"><span class="letra">d)</span> ao envio de relatórios, atas de audiência, comprovantes, certidões e demais documentos relacionados ao ato realizado;</p>
<p class="item"><span class="letra">e)</span> à atualização constante e correta das informações no Sistema DMK durante todas as etapas da demanda;</p>
<p class="item"><span class="letra">f)</span> à confirmação obrigatória da audiência, tanto pelo Sistema DMK quanto via WhatsApp, com antecedência mínima de 01 (um) dia da realização do ato;</p>
<p class="item"><span class="letra">g)</span> à realização obrigatória de check-in no Sistema DMK no dia da audiência, com antecedência mínima de 01 (uma) hora antes do horário designado para o ato;</p>
<p class="item"><span class="letra">h)</span> à finalização completa do ato no Sistema DMK imediatamente após sua conclusão, com inserção de todas as informações e documentos pertinentes, sob pena de responsabilização contratual, bloqueio e/ou suspensão de pagamento, descredenciamento e demais penalidades previstas neste instrumento.</p>
<p class="numerado"><span class="n">1.2.</span> A presente contratação se dá em regime de não exclusividade, isto é, não cria direitos e obrigações a nenhuma das partes de prestar ou tomar serviços apenas uma da outra, ficando livres as partes para contratar com outros tomadores/prestadores durante a vigência do presente contrato.</p>
<p class="numerado"><span class="n">1.3</span> A presente contratação se dá em regime de não exclusividade, isto é, não cria direitos e obrigações a nenhuma das partes de prestar ou tomar serviços apenas uma da outra, ficando livres as partes para contratar com outros tomadores/prestadores durante a vigência do presente contrato.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA SEGUNDA</div>
    <div class="clausula-titulo">DA VIGÊNCIA</div>
</div>
<p class="texto">O contrato ora firmado terá vigência por prazo indeterminado, a contar da data de sua assinatura pelas partes contratantes.</p>
</div>
<p class="texto">As partes signatárias, a qualquer tempo e independentemente de motivação, poderão pronunciar a rescisão unilateral mediante mera comunicação escrita com aviso de recebimento, hipótese em que o contrato vigerá por 30 (trinta) dias, contados do recebimento do ato de rescisão.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA TERCEIRA</div>
    <div class="clausula-titulo">DA REMUNERAÇÃO DOS SERVIÇOS</div>
</div>
<p class="texto">Pela execução dos serviços de correspondência indicados no item 1.1 da ‘CLÁUSULA PRIMEIRA – DO OBJETO’ do presente instrumento, o (a) CONTRATADO (A), promoverá pagamento ao (a)  CONTRATADO (A) por ato executado os valores constantes na Tabela de Valores de Atos ajustados por e-mail e cadastrados no sistema da CONTRATANTE.</p>
</div>
<p class="texto">Os atos não previstos na tabela acima terão seu valor ajustado caso a caso pelas partes contratantes.</p>
<p class="texto">Os pagamentos serão realizados mensalmente mediante apresentação de relatório de atividades executadas no mês de referência que poderá ser gerado através do painel de atividades do parceiro e respectivo recibo simples de Prestação de Serviços a ser emitido pelo (a) CONTRATADO (A), nominal à CONTRATANTE, deduzindo-se eventual tributação de retenção legal obrigatória, assim como valores correspondentes a eventual agravamento de risco em processos patrocinados pela CONTRATANTE decorrente de falhas cometidas pelo (a) CONTRATADO (A).</p>
<p class="texto">O recibo de pagamento realizado pela CONTRATANTE ao (a) CONTRATADO (A) servirá de recibo para todos os efeitos legais. Este recibo de pagamento será anexado no sistema no parceiro que poderá visualizá-lo a qualquer tempo.</p>
<p class="texto">Correrão contra o (a) CONTRATADO (A), todos os demais encargos tributários eventualmente devidos relativos a impostos federais, estaduais ou municipais, taxas federais, estaduais ou municipais, contribuições sociais, FGTS, e bem assim quaisquer eventuais encargos previdenciários ou trabalhistas incidentes ou relacionados à atividade objeto de contratação.</p>
<p class="texto">O recibo simples de Prestação de Serviços será emitido pelo (a) CONTRATADO (A), e enviado pela CONTRATANTE através de e-mail financeiro@dmkavogados.com.br junto com o relatório mensal até o 5º (quinto) dia útil de cada mês, para pagamento em 30 dias subsequentes ao mês dos serviços prestados, concordando o (a) CONTRATADO (A), que a entrega do relatório e Recibo de Prestação de Serviços após o 5º (quinto) dia útil implicará pagamento somente no mês subsequente ao da entrega.</p>
<p class="texto">O adimplemento das obrigações constantes desta cláusula será realizado por meio de depósito bancário, diretamente na conta corrente de titularidade do (a) CONTRATADO (A):</p>
@include('correspondente.partes-contrato.dados-bancarios')
<p class="texto">O pagamento acordado e referido nos itens 3.1 e 3.3 poderá vir a ser ajustado em função de contingências imputáveis à CONTRATANTE em virtude de fatos, ações, omissões do (a) CONTRATADO (A):</p>
<div class="manter-junto">
<p class="subtitulo">DO REEMBOLSO DE CUSTAS PROCESSUAIS E DO ADIANTAMENTO</p>
<p class="texto">As custas processuais reembolsáveis serão objeto de reembolso pela CONTRATANTE, mediante entrega de relatório de prestação de contas e dos respectivos comprovantes de pagamento das custas processuais, pelo (a) CONTRATADO (A), na forma e termos indicados pela CONTRATANTE no manual de correspondentes.</p>
</div>
<p class="texto">Entende-se por custa processual reembolsável aquela direta ou indiretamente recolhida ao Poder Judiciário ou a órgão julgador de processo ou procedimento administrativo, em correspondência única e exclusivamente a custas processuais (tais como, exemplificativamente, custas de correio para fins de protocolo postal), CD para gravação de áudio ou documentos.</p>
<p class="texto">O reembolso das custas indicadas na presente cláusula será feito por meio de depósito bancário diretamente na conta corrente de titularidade do (a) CONTRATADO (A): conforme dados bancários indicados no item 3.6 deste instrumento no prazo estipulado neste contrato.</p>
<p class="texto">Faculta-se à CONTRATANTE adiantar valores ao CONTRATADO (A), a título de adiantamento para pagamento de custas reembolsáveis, ficando tais valores sujeitos a prestação de contas mencionada no item 4.1 acima, bem como sua devolução integral na hipótese de rescisão contratual.</p>
<p class="texto">As despesas com impressões para realização dos atos não serão reembolsadas, exceto com autorização expressa do Dmk ou do cliente.</p>
<p class="texto">Despesas não reembolsáveis correrão por conta do (a) contratado (a), salvo se previamente autorizadas pela CONTRATANTE.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA QUINTA</div>
    <div class="clausula-titulo">DAS DESPESAS EXTRAPROCESSUAIS</div>
</div>
<p class="texto">Concordam as partes que se incluem na remuneração do serviço contratado e que não serão reembolsadas as despesas decorrentes de fotocópias, deslocamento, alimentação, transporte, hospedagem, correspondências, contratação de prepostos, telefonia ou qualquer outra de natureza extraprocessual.</p>
</div>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA SEXTA</div>
    <div class="clausula-titulo">DAS OBRIGAÇÕES DA CONTRATANTE</div>
</div>
<p class="texto">Constituem obrigações da CONTRATANTE:</p>
</div>
<p class="texto">Realizar tempestivamente o pagamento do reembolso e remuneração decorrentes dos serviços prestados pelo (a) CONTRATADO (A), bem como o devido reembolso das custas processuais reembolsáveis objeto de prestação de contas semanal;</p>
<p class="texto">Remeter, tempestivamente, por meio do sistema de informática indicado pela CONTRATANTE ou, na sua falta, via e-mail, correio ou outro meio idôneo as manifestações processuais (petições iniciais, defesas, recursos e petições incidentais) relativas aos processos de seus clientes para a realização dos atos;;</p>
<p class="texto">Indicar ao (à) CONTRATADO (A), em formulário próprio ou por outro meio escrito, físico ou digital, o prazo para a realização do protocolo das manifestações processuais;</p>
<p class="texto">Prestar as informações e instruções técnicas para adequado cumprimento das tarefas a cargo do (a) CONTRATADO (A);</p>
<p class="texto">Entregar ao (à) CONTRATADO (A), manual de procedimentos referente a todos os encargos que deverão ser desempenhados pelo (a) CONTRATADO (A),  no período de vigência do contrato;</p>
<p class="texto">As demais previstas neste instrumento, as próprias da função de contratante em regime de correspondência e as estatuídas na legislação de regência.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA SÉTIMA</div>
    <div class="clausula-titulo">DAS OBRIGAÇÕES E RESPONSABILIDADES DO(A) CONTRATADO (A)</div>
</div>
<p class="texto">Constituem obrigações do (a) CONTRATADO (A):</p>
</div>
<p class="texto">Cumprir correta e tempestivamente todos os atos encaminhados pela CONTRATANTE;</p>
<p class="texto">Cumprir todas as solicitações de diligências e fotocópias dentro do prazo requerido constante no cadastro em sistema da solicitação no sistema LawyerExpress. Cumprir em 24 (vinte e quatro) horas, no caso de solicitação de Urgência.</p>
<p class="texto">Obter certidões judiciais que comprovem a impossibilidade material de cumprimento das diligências e fotocópias solicitadas pela CONTRATANTE, sempre que referidas diligências e fotocópias forem necessárias ao atendimento de prazos processuais e/ou judiciais em curso, isso para resguardar direitos dos clientes da CONTRATANTE e viabilizar a minuta de requerimento de devolução dos referidos prazos processuais e/ou judiciais. Em caso de impossibilidade de certidão de indisponibilidade anexar a tela da movimentação do processo assinado pelo cartorário ou foto do local da realização do ato, sob pena de não pagamento.</p>
<p class="texto">Aceitar o ato contratado através do sistema no máximo com 24 (vinte e quatro).</p>
<p class="texto">Realizar indicação de advogado e preposto que comparecerá em audiência, após a aceitação do ato contratado máximo com 24 (vinte e quatro) após o aceite.</p>
<p class="texto">Entrar em contato com o preposto e com as testemunhas para repassar o caso e orientá-los para que a Audiência seja realizada da melhor forma possível, com qualidade e ótimo desempenho.</p>
<p class="texto">Naqueles casos em que não houver indicação de preposto pelo cliente, contratar e treinar pessoas de sua confiança para exercer a função de preposto em audiências, tendo em vista sua responsabilidade sob a contratação.</p>
<p class="texto">Estar presente com pelo menos 30 (trinta) minutos de antecedência aos atos processuais orais (audiências e julgamentos) indicados ou solicitados pela CONTRATANTE e promover nas audiências e julgamentos todos os atos postulatórios e cabíveis na defesa dos clientes patrocinados assim como requerimento de que as publicações não sejam feitas em seu nome, mas dos signatários das peças dos respectivos clientes.</p>
<p class="texto">Ser sempre cordial com todos os nossos clientes, juízes e conciliadores, pois é o nome da CONTRATANTE que está sendo avaliado em todos os atos através do (a) CONTRATADO (A).</p>
<p class="texto">Seguir todas as orientações repassadas pelo cliente, sob pena de ter que arcar com valores dos processos, por falta de responsabilidade e zelo.</p>
<p class="texto">Acompanhar em cartório, a requerimento da CONTRATANTE, o andamento dos processos ativos em carteira;</p>
<p class="texto">Informar via sistema e via e-mail à CONTRATANTE as alterações relevantes dos processos, em especial as decorrentes de publicação que venham a ocorrer em nome de seus advogados na Imprensa Oficial ou de informação obtida diretamente nos autos do processo, na forma e termos solicitados pela CONTRATANTE;</p>
<p class="texto">Manter os dados atualizados em sistema e cumprir fielmente as determinações do Manual  de correspondentes.</p>
<p class="texto">Digitalizar todos os atos processuais, contrafés, recibos, guias de pagamento e demais documentos e anexá-los ao sistema operacional indicado pela CONTRATANTE;</p>
<p class="texto">Seguir as orientações da CONTRATANTE quanto ao tratamento das demandas e seus atos orais, especialmente quanto a propostas de acordos;</p>
<p class="texto">Prestar contas, via sistema operacional da CONTRATANTE, dos atos praticados, no prazo máximo de 12 (doze horas) horas após a realização do ato. Ex: Deverá incluir a ata da audiência realizada no sistema, finalizando o ato contratado.</p>
<p class="texto">Obter autorização prévia e expressa de advogado categorizado da CONTRATANTE para a prática de intervenções diretas nos autos, tais como juntada de instrumentos de procuração ou de petições; Jamais deverá se manifestar em qualquer processo, sem autorização expressa do cliente.</p>
<p class="texto">Comunicar à CONTRATANTE, via sistema operacional e e-mail, cumulativamente, qualquer ato praticado que possa desencadear o início do curso de prazo processual, e bem assim abster-se de atos que, de forma desnecessária, possam implicar o início do curso de prazo processual;</p>
<p class="texto">Manter as condições declaradas e comunicar à CONTRATANTE alterações relevantes de estado que possam prejudicar a execução do objeto contratual;</p>
<p class="texto">Abster-se de qualquer comunicação direta ou indireta com cliente da CONTRATANTE em relação a qualquer aspecto relacionado ao presente instrumento, exceto quando expressamente autorizada a tanto pela CONTRATANTE;</p>
<p class="texto">Assegurar à CONTRATANTE e a seus colaboradores tratamento cooperativo e respeitoso;</p>
<p class="texto">Realizar com naturalidade e desenvoltura os atos necessários ao adequado cumprimento do objeto deste contrato.</p>
<p class="destaque">ABSTER-SE DE PATROCINAR CAUSAS CONTRA CLIENTES ESPECÍFICOS INTEGRANTES DA CARTEIRA DE CLIENTES DA CONTRATANTE, DENTRO DO PERÍODO DE VIGÊNCIA DESTE CONTRATO. QUANDO RECEBER A SOLICITAÇÃO DE AUDIÊNCIA, CONFIRMAR PRIMEIRAMENTE SE HÁ ALGUM IMPEDIMENTO PARA ATUAÇÃO EM FAVOR DO CLIENTE QUE REPRESENTARÁ NESTE ATO. O REPRESENTANTE CORRESPONDENTE NÃO PODERÁ DE FORMA ALGUMA TER AÇÕES EM FACE DA EMPRESA QUE REPRESENTAMOS, SOB PENA DE INFRINGIR O CÓDIGO DE ÉTICA DA OAB. SE HOUVER IMPEDIMENTO, FAVOR COMUNICAR IMEDIATAMENTE, PARA QUE POSSAMOS FAZER A CONTRATAÇÃO DE OUTRO PARCEIRO QUE POSSA ATUAR.</p>
<p class="texto">O (A) CONTRATADO (A) declara que está ciente de que as ORIENTAÇÕES DE AUDIÊNCIAS enviadas deverão ser lidas, atendidas e consignadas em ata, bem como que é expressamente vedado o aceite de qualquer obrigação que não esteja expressamente definida nestas orientações, sob pena de comprometer o pagamento do ato e de responsabilização pelo ato realizado sem expressa autorização. Atenção, o correspondente não é o patrono do processo e sim o advogado do cliente. Assim, não poderá realizar qualquer ato dentro do processo que não esteja expressamente autorizado pelo cliente final.</p>
<p class="texto">O (A) CONTRATADO (A) declara, sob as penas da lei, que ela mesma e os executores que indicar (advogados e/ou prepostos), para a realização dos atos contratados não tem qualquer relação pessoal ou profissional, ou ainda, que estão reunidos em qualquer forma de associação com caráter de cooperação recíproca, com a parte adversa ou seus advogados, bem como não possuem processos, findo ou em andamento e/ou interesse contra a carteira de clientes da CONTRATANTE, sob pena de descumprimento ao que aduz o Código de Ética e Disciplina da Ordem dos Advogados do Brasil: Art. 2º O advogado, indispensável à administração da Justiça, é defensor do Estado Democrático de Direito, dos direitos humanos e garantias fundamentais, da cidadania, da moralidade, da Justiça e da paz social, cumprindo-lhe exercer o seu ministério em consonância com a sua elevada função pública e com os valores que lhe são inerentes. Parágrafo único. São deveres do advogado: II - atuar com destemor, independência, honestidade, decoro, veracidade, lealdade, dignidade e boa-fé;</p>
<p class="texto">As demais previstas neste instrumento, as próprias da função de contratado em regime de correspondência e as estatuídas na legislação de regência.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA OITAVA</div>
    <div class="clausula-titulo">DAS RESPONSABILIDADES</div>
</div>
<p class="numerado"><span class="n">8.1.</span> Cada parte responderá individualmente por suas obrigações tributárias, previdenciárias, trabalhistas, cíveis, profissionais, bancárias, societárias e demais encargos decorrentes de sua atuação, bem como pelos danos que der causa em razão de ação, omissão, negligência, imprudência, imperícia ou descumprimento contratual.</p>
</div>
<p class="numerado"><span class="n">8.2.</span> Caso qualquer das partes venha a sofrer cobrança, condenação, prejuízo financeiro, penalidade processual, indenização ou qualquer ônus decorrente de obrigação atribuível à outra parte, ficará assegurado o direito de regresso integral contra a parte responsável, incluindo perdas e danos, honorários advocatícios, custas processuais e demais despesas suportadas.</p>
<p class="numerado"><span class="n">8.3.</span> O (A) CONTRATADO (A) responderá integralmente por quaisquer prejuízos causados à CONTRATANTE ou aos clientes por ela representados em razão de:</p>
<p class="item"><span class="letra">a)</span> não comparecimento injustificado à audiência, ato processual ou diligência previamente aceita;</p>
<p class="item"><span class="letra">b)</span> atraso que gere revelia, confissão, arquivamento, aplicação de multa, preclusão, perda de prazo ou qualquer prejuízo processual;</p>
<p class="item"><span class="letra">c)</span> realização de acordo sem autorização prévia e expressa da CONTRATANTE ou do cliente;</p>
<p class="item"><span class="letra">d)</span> descumprimento das orientações processuais encaminhadas pela CONTRATANTE;</p>
<p class="item"><span class="letra">e)</span> dispensa de testemunha, depoimento pessoal, oitiva, produção de provas, protestos em ata, requerimentos ou demais atos processuais sem autorização expressa da CONTRATANTE;</p>
<p class="item"><span class="letra">f)</span> manifestação processual, juntada de documentos, petições, procurações ou prática de qualquer ato não autorizado expressamente;</p>
<p class="item"><span class="letra">g)</span> conduta incompatível com as orientações repassadas pela CONTRATANTE, pelo cliente ou com os deveres éticos e profissionais da advocacia.</p>
<p class="numerado"><span class="n">8.4.</span> Na hipótese de ocorrência de quaisquer das situações previstas nesta cláusula, o (A) CONTRATADO (A) ficará responsável pelo ressarcimento integral dos prejuízos suportados pela CONTRATANTE e/ou pelo cliente, incluindo, mas não se limitando a:</p>
<ul class="lista">
    <li>condenações judiciais;</li>
    <li>acordos realizados;</li>
    <li>multas;</li>
    <li>custas;</li>
    <li>indenizações;</li>
    <li>despesas processuais;</li>
    <li>honorários advocatícios;</li>
    <li>perdas financeiras;</li>
    <li>prejuízos operacionais;</li>
    <li>danos à imagem e à relação comercial da CONTRATANTE perante seus clientes.</li>
</ul>
<p class="numerado"><span class="n">8.5.</span> A responsabilidade prevista nesta cláusula permanecerá íntegra mesmo após eventual rescisão do presente contrato, aplicando-se a atos praticados durante sua vigência.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA DÉCIMA</div>
    <div class="clausula-titulo">DA VEDAÇÃO A ATOS DE CONCORRÊNCIA</div>
</div>
<p class="texto">O (A) CONTRATADO (A) compromete-se a não prospectar, nem desviar ou contratar direta ou indiretamente, os clientes que compõem as carteiras de processos patrocinadas pela CONTRATANTE durante a vigência do presente contrato.</p>
</div>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA ONZE</div>
    <div class="clausula-titulo">DA CONFIDENCIALIDADE</div>
</div>
<p class="texto">As informações operacionais ou jurídicas conhecidas por força da execução desse contrato serão tratadas como confidenciais e não poderão ser divulgadas a terceiros.</p>
</div>
<div class="manter-junto">
<p class="subtitulo">11. PROTEÇÃO DE DADOS PESSOAIS</p>
<p class="numerado"><span class="n">11.1</span> O (A) CONTRATADO (A), obriga-se, sempre que aplicável, a atuar no presente Contrato em conformidade com a legislação vigente sobre Proteção de Dados Pessoais e as determinações de órgãos reguladores/fiscalizadores sobre a matéria, em especial, a Lei nº 13.709/2018 - Lei Geral de Proteção de Dados Pessoais (“LGPD”).</p>
</div>
<p class="numerado"><span class="n">11.2</span> Caso exista modificação dos textos legais acima indicados ou de qualquer outro, de forma que exija modificações na estrutura do escopo deste Contrato ou na execução das atividades ligadas a este Contrato, O (A) CONTRATADO (A) deverá adequar-se às condições vigentes. Se houver alguma disposição que impeça a continuidade do Contrato conforme as disposições acordadas, a CONTRATANTE poderá resolve-lo sem qualquer penalidade, apurando-se os serviços prestados e/ou produtos fornecidos até a data da rescisão e consequentemente os valores devidos correspondentes.</p>
<p class="numerado"><span class="n">11.3</span> O (A) CONTRATADO (A), seguirá as instruções recebidas da CONTRATANTE em relação ao tratamento dos Dados Pessoais, além de observar e cumprir as normas legais vigentes aplicáveis, sob pena de arcar com as perdas e danos que eventualmente possa causar à CONTRATANTE, aos seus colaboradores, clientes e fornecedores, sem prejuízo das demais sanções aplicáveis.</p>
<p class="numerado"><span class="n">11.4</span> O (A) CONTRATADO (A), deverá manter registro das operações de tratamento de Dados Pessoais que realizar, bem como implementar medidas técnicas e organizativas necessárias para proteger os dados contra a destruição, acidental ou ilícita, a perda, a alteração, a comunicação ou difusão ou o acesso não autorizado, além de garantir que o ambiente (seja ele físico ou lógico) utilizado por ela para o tratamento de Dados Pessoais é estruturado de forma a atender os requisitos de segurança, aos padrões de boas práticas de governança e aos princípios gerais previstos na legislação e nas demais normas regulamentares aplicáveis.</p>
<p class="numerado"><span class="n">11.5</span> O (A) CONTRATADO (A) deverá notificar a CONTRATANTE sobre reclamações e solicitações dos titulares de Dados Pessoais que venha a receber (por exemplo, sobre a correção, exclusão, complementação e bloqueio de dados) e sobre as ordens de tribunais, autoridade pública e reguladores competentes, e quaisquer outras exposições ou ameaças em relação à conformidade com a proteção de dados identificadas pelo mesmo.</p>
<p class="numerado"><span class="n">11.6</span> O (A) CONTRATADO (A) deverá notificar a CONTRATANTE em 24 (vinte e quatro) horas de (i) qualquer não cumprimento (ainda que suspeito) das disposições legais relativas à proteção de Dados Pessoais; (ii) qualquer descumprimento das obrigações contratuais relativas ao processamento e tratamento dos Dados Pessoais; e (iii) qualquer violação de segurança no âmbito das atividades da O (A) CONTRATADO (A).</p>
<p class="numerado"><span class="n">11.7</span> O (A) CONTRATADO (A) compromete-se a auxiliar a CONTRATANTE com a suas obrigações judiciais ou administrativas, de acordo com a Lei de Proteção de Dados aplicável, fornecendo informações relevantes disponíveis e qualquer outra assistência para documentar e eliminar a causa e os riscos impostos por quaisquer violações de segurança.</p>
<p class="numerado"><span class="n">11.8</span> A CONTRATANTE terá o direito de acompanhar, monitorar, auditar e fiscalizar a conformidade DO (A) CONTRATADO (A) com as obrigações de Proteção de Dados Pessoais, sem que isso implique em qualquer diminuição da responsabilidade que a CONTRATANTE possui perante a LGPD e este Contrato.</p>
<p class="numerado"><span class="n">11.9</span> A ANUNCIANTE não autoriza a O (A) CONTRATADO (A) a usar, compartilhar ou comercializar quaisquer eventuais elementos de dados, que se originem ou sejam criados a partir do tratamento de Dados Pessoais, estabelecido por este Contrato.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA TREZE</div>
    <div class="clausula-titulo">DA RESCISÃO DO CONTRATO</div>
</div>
<p class="texto">As partes poderão rescindir o contrato durante a sua vigência, seja de forma conjunta ou unilateralmente, mediante notificação prévia por escrito. A rescisão será efetivada após a assinatura do respectivo termo.</p>
</div>
<p class="texto">Mesmo após a rescisão, o Contratado será responsável por quaisquer prejuízos sofridos pelo cliente decorrentes de revelia, acordos feitos incorretamente ou orientações não seguidas, ocorridos durante a execução do contrato.</p>
<p class="texto">A rescisão contratual não afasta, limita ou extingue a responsabilidade do (a) CONTRATADO (A) por atos praticados durante a vigência deste contrato, permanecendo este responsável por todos os prejuízos decorrentes de descumprimento de orientações, revelia, confissão, perda de prazo, acordos não autorizados, dispensa indevida de provas ou qualquer atuação culposa ou dolosa que cause danos à CONTRATANTE ou aos seus clientes.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA QUATORZE</div>
    <div class="clausula-titulo">DA ARBITRAGEM</div>
</div>
<p class="texto">Em caso de dúvida, divergência ou eventual litígio que decorra das relações contratuais estabelecidas por meio do presente instrumento, as partes comprometem-se a envidar todos os esforços para, de boa-fé, compor amigavelmente.</p>
</div>
<p class="texto">Caracterizada a dúvida, divergência ou litígio, as partes buscarão solução de mediação e composição consensual junto ao Tribunal de Ética e Disciplina da OAB/SC, em prazo que não supere 30 (trinta) dias contados da notificação de divergência recebida por qualquer das partes e assinada pela outra.</p>
<p class="texto">Esgotado esse prazo sem que se tenha chegado a uma solução consensual, ou caso qualquer das partes envie antes de tal prazo notificação informando o encerramento das negociações para a obtenção de uma solução consensual, o litígio, dúvida ou divergência entre as partes, oriundos e/ou relativos ao presente instrumento, será definitivamente resolvido por meio de Arbitragem, conforme previsto pela Lei 9.307/96, valendo esta disposição tanto como Cláusula Compromissória quanto como Compromisso Arbitral.</p>
<div class="manter-junto">
<div class="clausula">
    <div class="clausula-num">CLÁUSULA QUINZE</div>
    <div class="clausula-titulo">DAS DISPOSIÇÕES FINAIS</div>
</div>
<p class="texto">As partes contratantes ainda resolvem que:</p>
</div>
<p class="texto">Os direitos e obrigações decorrentes deste contrato não poderão ser cedidos a terceiros nem terceirizados sem a anuência da parte CONTRATANTE, permitindo-se ao (à) CONTRATADO (A) substabelecer, com reservas de poderes, as atividades indicadas no item 7 do presente instrumento;</p>
<p class="texto">Qualquer ato realizado pelo (a) CONTRATADO (A), fora das orientações enviadas pelo CONTRATANTE, ou ainda em caso de revelia aplicada ao cliente devido ao não comparecimento ao ato designado, os valores a título de condenação ou indenização deverão ser arcados pelo (a) advogado (a) correspondente, ou seja, pelo (a) CONTRATADO (A).</p>
<p class="texto">Cabe ao (à) CONTRATADO (A), apenas os honorários indicados na clausula terceira, sendo certo que os honorários de sucumbência são devidos apenas aos patronos titulares do processo.</p>


<table class="assinaturas">
    <tr>
        <td colspan="3" class="fecho">E, por estarem em pleno acordo quanto aos termos do presente contrato, as partes firmam o presente em 03 (três) vias de igual teor na presença de duas testemunhas para que este contrato surta os seus jurídicos e legais efeitos.</td>
    </tr>
    <tr>
        <td colspan="3" class="local-data">{{ $localData }}</td>
    </tr>
    <tr>
        <td class="ass-papel">Contratante</td>
        <td class="ass-vao"></td>
        <td class="ass-papel">Contratado (A)</td>
    </tr>
    <tr>
        <td class="ass-dados">
            <div class="ass-nome">DEBORAH MEKACHESKI PEREIRA SOCIEDADE INDIVIDUAL DE ADVOCACIA</div>
            <div class="ass-campo">CNPJ: 19.439.096/0001-17</div>
            <div class="ass-campo">Representante: Deborah Mekacheski Pereira — OAB/SC 33.565B</div>
        </td>
        <td class="ass-vao"></td>
        <td class="ass-dados">
            <div class="ass-nome">{!! $contratadaNome !!}</div>
            <div class="ass-campo">OAB: {!! $contratadaOab !!}</div>
            <div class="ass-campo">CPF/CNPJ: {!! $contratadaCpf !!}</div>
        </td>
    </tr>
    <tr>
        <td class="ass-papel ass-papel-testemunha">Testemunha 1</td>
        <td class="ass-vao"></td>
        <td class="ass-papel ass-papel-testemunha">Testemunha 2</td>
    </tr>
    <tr>
        <td class="ass-dados">
            <div class="ass-campo">Nome: ________________________________</div>
            <div class="ass-campo">CPF: _________________________________</div>
        </td>
        <td class="ass-vao"></td>
        <td class="ass-dados">
            <div class="ass-campo">Nome: ________________________________</div>
            <div class="ass-campo">CPF: _________________________________</div>
        </td>
    </tr>
</table>

@if(!empty($anexoComarcas))
    <pagebreak />
    <p class="sobretitulo">Parte integrante do contrato</p>
    <p class="anexo-titulo">Anexo I – Comarcas de Atuação</p>
    <p class="anexo-resumo">
        {{ number_format(array_sum(array_map('count', $anexoComarcas)), 0, ',', '.') }} comarcas em {{ count($anexoComarcas) }} {{ count($anexoComarcas) === 1 ? 'estado' : 'estados' }}
    </p>
    @foreach($anexoComarcas as $estado => $cidades)
        <div class="uf">
            <div class="uf-titulo">{{ $estado }} <span>· {{ number_format(count($cidades), 0, ',', '.') }} {{ count($cidades) === 1 ? 'comarca' : 'comarcas' }}</span></div>
            <p class="uf-lista">{{ implode(' · ', $cidades) }}</p>
        </div>
    @endforeach
@endif

</body>
</html>
