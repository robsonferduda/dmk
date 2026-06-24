<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
@include('correspondente.pagamentos-relatorio-pdf-css')
</style>
</head>
<body>

@include('correspondente.pagamentos-relatorio-pdf-cabecalho', compact('pagamentos', 'bancoPorPag', 'escritorio', 'mesAnoFmt'))

<div class="footer">
    Documento gerado automaticamente em {{ now()->format('d/m/Y \à\s H:i') }}
    — {{ $escritorio->nm_razao_social_con ?? $escritorio->nm_fantasia_con ?? '' }}
    — Uso interno. Não possui validade como comprovante.
</div>

</body>
</html>
