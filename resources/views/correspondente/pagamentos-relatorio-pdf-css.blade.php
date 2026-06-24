* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }

.header { border-bottom: 3px solid #1a7bb9; padding-bottom: 10px; margin-bottom: 16px; }
.header-tbl { width: 100%; border-collapse: collapse; }
.header-tbl td { vertical-align: middle; }
.header-escritorio { font-size: 15px; font-weight: bold; color: #1a7bb9; }
.header-sub { font-size: 9px; color: #888; margin-top: 2px; }
.header-right { text-align: right; }
.header-titulo { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1a7bb9; }
.header-competencia { font-size: 20px; font-weight: bold; color: #333; }
.header-gerado { font-size: 8px; color: #aaa; margin-top: 2px; }

.section-title { font-size: 9px; text-transform: uppercase; font-weight: bold; color: #1a7bb9;
    border-left: 3px solid #1a7bb9; padding-left: 7px; margin-bottom: 8px; margin-top: 18px; }
.section-title--detalhe { margin-top: 24px; font-size: 10px; }

.badge { padding: 2px 6px; font-size: 8px; font-weight: bold; text-transform: uppercase; color: #fff; }
.badge-gerado   { background: #95a5a6; }
.badge-enviado  { background: #e67e22; }
.badge-aprovado { background: #2980b9; }
.badge-pago     { background: #27ae60; }
.badge-recusado { background: #e74c3c; }

.resumo-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
.resumo-table thead tr { background: #1a7bb9; color: white; }
.resumo-table thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: bold; }
.resumo-table tbody td { padding: 6px 8px; font-size: 9px; vertical-align: top; border-bottom: 1px solid #eef0f3; }
.resumo-table tfoot td { padding: 8px; font-size: 10px; font-weight: bold; background: #f0f4f9; border-top: 2px solid #1a7bb9; }

/* ── Bloco de cada pagamento ── */
.pagamento-bloco {
    width: 100%;
    margin-top: 16px;
    margin-bottom: 4px;
    border: 1px solid #d0dce8;
    border-radius: 4px;
    overflow: hidden;
    page-break-inside: avoid;
}
.pagamento-bloco--zebra {
    background: #f8fafc;
    border-color: #c8d6e5;
}
.pagamento-bloco__header {
    width: 100%;
    background: #1a7bb9;
    color: #fff;
    padding: 10px 12px;
}
.pagamento-bloco__header-tbl { width: 100%; border-collapse: collapse; }
.pagamento-bloco__header-tbl td { vertical-align: middle; }
.pagamento-bloco__nome { font-size: 13px; font-weight: bold; line-height: 1.35; }
.pagamento-bloco__meta { font-size: 10px; color: #cce0f5; margin-top: 3px; }
.pagamento-bloco__header-right { text-align: right; white-space: nowrap; }
.pagamento-bloco__valor { font-size: 14px; font-weight: bold; margin-left: 8px; }

.pagamento-bloco__banco {
    width: 100%;
    background: #f7fbff;
    border-bottom: 1px solid #e0e8f0;
}
.pagamento-bloco__banco-tbl { width: 100%; border-collapse: collapse; }
.pagamento-bloco__banco-tbl td {
    padding: 8px 12px;
    vertical-align: top;
    border-right: 1px solid #e8eef4;
    font-size: 11px;
    color: #222;
}
.pagamento-bloco__banco-tbl td:last-child { border-right: none; }
.pagamento-bloco__banco-tbl td.pix { background: #eef6ff; }
.pagamento-bloco__label {
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    color: #888;
    margin-bottom: 3px;
}
.pagamento-bloco__label--pix { color: #1a7bb9; }
.pagamento-bloco__valor-texto { font-size: 11px; color: #222; }
.pagamento-bloco__valor-texto--pix { font-weight: bold; }
.pagamento-bloco__sem-banco {
    padding: 8px 12px;
    color: #aaa;
    font-style: italic;
    font-size: 10px;
}

/* ── Tabela de processos (detalhe) ── */
.proc-table { width: 100%; border-collapse: collapse; }
.proc-table--detalhe thead tr { background: #e8f0f8; }
.proc-table--detalhe thead th {
    padding: 8px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: bold;
    color: #444;
    border-bottom: 1px solid #c5d4e3;
}
.proc-table--detalhe tbody td {
    padding: 8px 10px;
    font-size: 11px;
    vertical-align: middle;
    border-bottom: 1px solid #e8eef4;
    line-height: 1.4;
}
.proc-table--detalhe tbody tr.proc-zebra td { background: #f3f7fb; }
.proc-table--detalhe tbody tr.proc-excluido td {
    color: #999;
    text-decoration: line-through;
    background: #fafafa;
}
.proc-table--detalhe tfoot td {
    padding: 9px 10px;
    font-size: 11px;
    font-weight: bold;
    background: #eef4fa;
    border-top: 2px solid #1a7bb9;
}
.proc-numero { color: #1a7bb9; font-weight: bold; }
.proc-descricao { color: #333; }

.footer { border-top: 1px solid #ddd; padding-top: 6px; margin-top: 20px; font-size: 8px; color: #aaa; text-align: center; }
