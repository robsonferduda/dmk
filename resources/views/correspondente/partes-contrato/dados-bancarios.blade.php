<table class="banco">
    <tr>
        <td colspan="3">
            <div class="banco-rotulo">Favorecido</div>
            <div class="banco-valor">{!! $dadosBancarios['Favorecido'] !!}</div>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;">
            <div class="banco-rotulo">Banco</div>
            <div class="banco-valor">{!! $dadosBancarios['Banco'] !!}</div>
        </td>
        <td style="width: 22%;">
            <div class="banco-rotulo">Agência</div>
            <div class="banco-valor">{!! $dadosBancarios['Agência'] !!}</div>
        </td>
        <td style="width: 28%;">
            <div class="banco-rotulo">Conta</div>
            <div class="banco-valor">{!! $dadosBancarios['Conta'] !!}</div>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="banco-rotulo">PIX</div>
            <div class="banco-valor">{!! $dadosBancarios['PIX'] !!}</div>
        </td>
    </tr>
</table>
