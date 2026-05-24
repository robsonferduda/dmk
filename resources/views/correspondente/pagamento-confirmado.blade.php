<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmação de Pagamento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon { font-size: 56px; margin-bottom: 20px; }
        .card h1 { font-size: 22px; font-weight: bold; margin-bottom: 10px; }
        .card p  { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 6px; }
        .info-box {
            background: #f5f7fa;
            border-radius: 6px;
            padding: 16px;
            margin: 20px 0;
            text-align: left;
        }
        .info-box dl { display: flex; flex-wrap: wrap; gap: 6px 0; }
        .info-box dt { width: 120px; font-size: 12px; color: #888; font-weight: bold; }
        .info-box dd { width: calc(100% - 120px); font-size: 12px; color: #333; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #e8f8f0; color: #27ae60; }
        .badge-info    { background: #e8f2fc; color: #2980b9; }
        .badge-warning { background: #fef9e7; color: #d68910; }
        /* status variants */
        .ok h1    { color: #27ae60; }
        .ok .icon::before { content: '✅'; }
        .ja-aprovado h1 { color: #2980b9; }
        .ja-aprovado .icon::before { content: 'ℹ️'; }
        .ja-pago h1 { color: #27ae60; }
        .ja-pago .icon::before { content: '💰'; }
        .invalido h1  { color: #e74c3c; }
        .invalido .icon::before { content: '❌'; }
        .nao-encontrado h1 { color: #e74c3c; }
        .nao-encontrado .icon::before { content: '🔍'; }
        .footer-note { font-size: 11px; color: #aaa; margin-top: 24px; }
    </style>
</head>
<body>

@if($status === 'ok')
<div class="card ok">
    <div class="icon"></div>
    <h1>Pagamento Aprovado!</h1>
    <p>O demonstrativo de honorários foi confirmado com sucesso.</p>
    @if(isset($pagamento))
    <div class="info-box">
        <dl>
            <dt>Correspondente</dt>
            <dd>{{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}</dd>
            <dt>Competência</dt>
            <dd>{{ $pagamento->nm_mes_ano }}</dd>
            <dt>Valor</dt>
            <dd><strong>R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</strong></dd>
            <dt>Status</dt>
            <dd><span class="badge badge-success">Aprovado</span></dd>
            <dt>Aprovado em</dt>
            <dd>{{ $pagamento->dt_aprovacao_pag->format('d/m/Y \à\s H:i') }}</dd>
        </dl>
    </div>
    @endif
    <p>O escritório será notificado e efetuará o pagamento nos próximos dias.</p>
    <p class="footer-note">Você pode fechar esta página.</p>
</div>

@elseif($status === 'ja_aprovado')
<div class="card ja-aprovado">
    <div class="icon"></div>
    <h1>Já Aprovado</h1>
    <p>Este demonstrativo já foi aprovado anteriormente.</p>
    @if(isset($pagamento))
    <div class="info-box">
        <dl>
            <dt>Correspondente</dt>
            <dd>{{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '—' }}</dd>
            <dt>Competência</dt>
            <dd>{{ $pagamento->nm_mes_ano }}</dd>
            <dt>Valor</dt>
            <dd><strong>R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</strong></dd>
            <dt>Status</dt>
            <dd><span class="badge badge-info">Aprovado</span></dd>
        </dl>
    </div>
    @endif
    <p class="footer-note">Você pode fechar esta página.</p>
</div>

@elseif($status === 'ja_pago')
<div class="card ja-pago">
    <div class="icon"></div>
    <h1>Pagamento Realizado</h1>
    <p>Este demonstrativo já foi aprovado e o pagamento foi efetuado.</p>
    <p class="footer-note">Você pode fechar esta página.</p>
</div>

@elseif($status === 'invalido')
<div class="card invalido">
    <div class="icon"></div>
    <h1>Link Inválido</h1>
    <p>Este link de confirmação não está disponível no momento.</p>
    <p>O pagamento pode já ter sido processado ou o link pode ter sido alterado.</p>
    <p class="footer-note">Em caso de dúvidas, entre em contato com o escritório.</p>
</div>

@else {{-- nao_encontrado --}}
<div class="card nao-encontrado">
    <div class="icon"></div>
    <h1>Link Não Encontrado</h1>
    <p>Este link de confirmação é inválido ou expirou.</p>
    <p class="footer-note">Em caso de dúvidas, entre em contato com o escritório.</p>
</div>
@endif

</body>
</html>
