<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revisão de Pagamento</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.10);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #1a7bb9;
            color: white;
            padding: 20px 24px;
        }
        .card-header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .card-header p {
            font-size: 13px;
            opacity: 0.85;
        }
        .card-body {
            padding: 24px;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 8px;
        }
        .summary-item {
            flex: 1;
            min-width: 140px;
            background: #f5f7fa;
            border-radius: 6px;
            padding: 14px 16px;
            text-align: center;
        }
        .summary-item .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 6px;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: 700;
            color: #1a7bb9;
        }
        .summary-item .value.green { color: #27ae60; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        thead th {
            background: #f5f7fa;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #777;
            border-bottom: 2px solid #e0e0e0;
        }
        thead th.text-right { text-align: right; }
        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            vertical-align: middle;
        }
        tbody td.text-right { text-align: right; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafcff; }
        .tfoot-total td {
            padding: 12px;
            font-weight: 700;
            font-size: 14px;
            background: #f5f7fa;
            border-top: 2px solid #e0e0e0;
            text-align: right;
        }
        .actions-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.10);
            padding: 24px;
        }
        .actions-title {
            font-size: 14px;
            font-weight: 700;
            color: #444;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            text-align: center;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.88; }
        .btn-success {
            background: #27ae60;
            color: white;
            width: 100%;
            margin-bottom: 12px;
            font-size: 15px;
            padding: 14px;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
            width: 100%;
            font-size: 15px;
            padding: 14px;
        }
        .motivo-area {
            display: none;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }
        .motivo-area label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #555;
            margin-bottom: 6px;
        }
        .motivo-area textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 13px;
            resize: vertical;
            min-height: 100px;
            font-family: Arial, sans-serif;
        }
        .motivo-area textarea:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 0 2px rgba(231,76,60,0.15);
        }
        .btn-confirmar-recusa {
            background: #e74c3c;
            color: white;
            width: 100%;
            margin-top: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-cancelar-recusa {
            background: #f0f0f0;
            color: #555;
            width: 100%;
            margin-top: 6px;
            padding: 10px;
            font-size: 13px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .alert-recusa {
            background: #fef9f0;
            border: 1px solid #f39c12;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 13px;
            color: #7d6608;
            margin-bottom: 16px;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #bbb;
            margin-top: 20px;
            padding-bottom: 10px;
        }
        @media (max-width: 600px) {
            .summary-grid { flex-direction: column; }
            thead th:nth-child(3), thead th:nth-child(4),
            tbody td:nth-child(3), tbody td:nth-child(4) { display: none; }
        }
    </style>
</head>
<body>
<div class="container">

    {{-- Cabeçalho --}}
    <div class="card">
        <div class="card-header">
            <h1>📄 Demonstrativo de Honorários – {{ $pagamento->nm_mes_ano }}</h1>
            <p>
                {{ $pagamento->correspondente->nm_razao_social_con ?? $pagamento->correspondente->nm_fantasia_con ?? '' }}
                &nbsp;·&nbsp;
                Enviado em {{ $pagamento->dt_envio_aprovacao_pag ? $pagamento->dt_envio_aprovacao_pag->format('d/m/Y \à\s H:i') : '—' }}
            </p>
        </div>
        <div class="card-body">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Competência</div>
                    <div class="value" style="font-size:16px;color:#444;">{{ $pagamento->nm_mes_ano }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Processos</div>
                    <div class="value">{{ $pagamento->itens->count() }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total de Honorários</div>
                    <div class="value green">R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Listagem de processos --}}
    <div class="card">
        <div class="card-body" style="padding:0;">
            <table>
                <thead>
                    <tr>
                        <th>Processo / Descrição</th>
                        <th class="text-right">Honorário</th>
                        <th class="text-right">Despesa</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagamento->itens as $item)
                    <tr>
                        <td>{{ $item->ds_descricao_pai }}</td>
                        <td class="text-right">R$ {{ number_format($item->vl_honorario_pai, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->vl_despesa_pai, 2, ',', '.') }}</td>
                        <td class="text-right"><strong>R$ {{ number_format($item->vl_total, 2, ',', '.') }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#aaa;padding:20px;">Nenhum item encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="tfoot-total">
                        <td colspan="3" style="text-align:right;">Total Geral</td>
                        <td>R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Ações --}}
    <div class="actions-card">
        <div class="actions-title">⚡ Sua resposta</div>

        <div class="alert-recusa">
            Revise a listagem acima e confirme se os valores estão corretos.<br>
            Caso haja alguma divergência, utilize o botão <strong>Recusar</strong> e informe o motivo.
        </div>

        <form id="formRevisao" method="POST" action="{{ url('pagamentos/revisar/' . $token) }}">
            @csrf
            <input type="hidden" name="acao" id="inputAcao" value="">

            {{-- Botão Confirmar --}}
            <button type="button" class="btn btn-success" onclick="confirmarAcao('aprovar')">
                ✅ Confirmar Pagamento
            </button>

            {{-- Botão Recusar --}}
            <button type="button" class="btn btn-danger" onclick="mostrarRecusa()">
                ❌ Recusar
            </button>

            {{-- Área de motivo (aparece ao clicar em Recusar) --}}
            <div class="motivo-area" id="motivoArea">
                <label for="motivo">Informe o motivo da recusa: <span style="color:#e74c3c;">*</span></label>
                <textarea name="motivo" id="motivo" placeholder="Descreva aqui o motivo da recusa ou qual processo está incorreto..."></textarea>
                <button type="button" class="btn-confirmar-recusa" onclick="confirmarRecusa()">
                    Confirmar Recusa
                </button>
                <button type="button" class="btn-cancelar-recusa" onclick="cancelarRecusa()">
                    Cancelar
                </button>
            </div>
        </form>
    </div>

    <p class="footer-note">
        Ao confirmar, o escritório será notificado e efetuará o pagamento em breve.<br>
        Dúvidas? Entre em contato diretamente com o escritório.
    </p>
</div>

<script>
    function confirmarAcao(acao) {
        if (!confirm('Confirmar aprovação do demonstrativo de R$ {{ number_format($pagamento->vl_total_pag, 2, ',', '.') }}?')) return;
        document.getElementById('inputAcao').value = acao;
        document.getElementById('formRevisao').submit();
    }

    function mostrarRecusa() {
        document.getElementById('motivoArea').style.display = 'block';
        document.getElementById('motivo').focus();
    }

    function cancelarRecusa() {
        document.getElementById('motivoArea').style.display = 'none';
        document.getElementById('motivo').value = '';
    }

    function confirmarRecusa() {
        var motivo = document.getElementById('motivo').value.trim();
        if (!motivo) {
            alert('Por favor, informe o motivo da recusa.');
            document.getElementById('motivo').focus();
            return;
        }
        if (!confirm('Tem certeza que deseja RECUSAR este demonstrativo? O escritório será notificado.')) return;
        document.getElementById('inputAcao').value = 'recusar';
        document.getElementById('formRevisao').submit();
    }
</script>
</body>
</html>
