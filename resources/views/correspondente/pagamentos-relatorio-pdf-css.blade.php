* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }

.header { border-bottom: 3px solid #1a7bb9; padding-bottom: 10px; margin-bottom: 16px; }
.header-tbl { width: 100%; border-collapse: collapse; }
.header-tbl td { vertical-align: middle; }
.header-escritorio { font-size: 15px; font-weight: bold; color: #1a7bb9; }
.header-sub { font-size: 9px; color: #888; margin-top: 2px; }
.header-right { text-align: right; }
.header-titulo { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1a7bb9; }
.header-competencia { font-size: 20px; font-weight: bold; color: #333; }
.header-gerado { font-size: 8px; color: #aaa; margin-top: 2px; }

.section-title {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: bold;
    color: #1a7bb9;
    border-left: 3px solid #1a7bb9;
    padding-left: 8px;
    margin-bottom: 12px;
    margin-top: 18px;
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    color: #fff;
    border-radius: 2px;
}
.badge-gerado   { background: #95a5a6; }
.badge-enviado  { background: #e67e22; }
.badge-aprovado { background: #2980b9; }
.badge-pago     { background: #27ae60; }
.badge-recusado { background: #e74c3c; }

/* ── Resumo em cards ── */
.resumo-lista { width: 100%; margin-bottom: 12px; }

.resumo-card {
    width: 100%;
    border: 1px solid #d0dce8;
    border-bottom: none;
    padding: 12px 14px;
    background: #fff;
    page-break-inside: avoid;
}
.resumo-card:last-child { border-bottom: 1px solid #d0dce8; }
.resumo-card--zebra { background: #f4f8fc; }

.resumo-card__tbl { width: 100%; border-collapse: collapse; }
.resumo-card__tbl td { vertical-align: middle; padding: 0; }

.resumo-card__nome {
    font-size: 12px;
    font-weight: bold;
    color: #222;
    line-height: 1.4;
    padding-right: 12px;
}
.resumo-card__valor {
    font-size: 14px;
    font-weight: bold;
    color: #1a7bb9;
    text-align: right;
    white-space: nowrap;
}
.resumo-card__linha2 td {
    padding-top: 8px;
    vertical-align: middle;
}
.resumo-card__dado {
    font-size: 11px;
    color: #444;
    margin-left: 10px;
    line-height: 1.4;
}
.resumo-card__dado-label {
    font-weight: bold;
    color: #1a7bb9;
}

.resumo-total {
    width: 100%;
    border: 2px solid #1a7bb9;
    background: #eef4fa;
    padding: 12px 14px;
    margin-top: 0;
}
.resumo-total__tbl { width: 100%; border-collapse: collapse; }
.resumo-total__label {
    font-size: 12px;
    font-weight: bold;
    color: #333;
    text-transform: uppercase;
}
.resumo-total__valor {
    font-size: 16px;
    font-weight: bold;
    color: #1a7bb9;
    text-align: right;
}

.resumo-vazio {
    text-align: center;
    color: #aaa;
    padding: 20px;
    font-size: 11px;
    border: 1px solid #d0dce8;
}

.footer { border-top: 1px solid #ddd; padding-top: 6px; margin-top: 20px; font-size: 8px; color: #aaa; text-align: center; }
