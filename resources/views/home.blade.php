@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li>Início</li>
    </ol>
</div>
<div id="content">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-home"></i>Painel Administrativo 
            </h1>
        </div>
        @role('administrator')
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 folder_settings">
                <ul id="sparks" class="">
                    <li class="sparks-info">
                        <h5>TAMANHO DA PASTA
                            <span class="txt-color-purple driver_tamanho">
                                <i class="fa fa-spinner fa-spin"></i> Calculando...
                            </span>
                        </h5>
                    </li>
                    <li class="sparks-info">
                        <h5>UTILIZAÇÃO
                            <span class="txt-color-blue driver_percentual">
                                <i class="fa fa-spinner fa-spin"></i> Calculando...
                            </span>
                        </h5>
                    </li>
                </ul>
            </div>
        @endrole
    </div>

    @role('cliente')
    {{-- ===== DASHBOARD DO CLIENTE ===== --}}

    {{-- Linha 1: Contadores --}}
    <div class="row" id="cliente-contadores">
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <div style="background:#fff; border-left:4px solid #5b9bd1; border-radius:4px; padding:15px 20px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:12px; text-transform:uppercase; color:#888; letter-spacing:.5px;"><i class="fa fa-folder-open txt-color-blue"></i> Processos Ativos</span>
                <div style="margin:6px 0 4px; font-size:34px; font-weight:700; color:#2c3e50;" id="cnt-ativos"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>
                <a href="{{ url('cliente/processos/acompanhamento') }}" style="font-size:12px;">Ver todos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <div style="background:#fff; border-left:4px solid #e67e22; border-radius:4px; padding:15px 20px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:12px; text-transform:uppercase; color:#888; letter-spacing:.5px;"><i class="fa fa-calendar txt-color-orange"></i> Audiências Hoje</span>
                <div style="margin:6px 0 4px; font-size:34px; font-weight:700; color:#2c3e50;" id="cnt-hoje"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>
                <a href="{{ url('cliente/pauta') }}" style="font-size:12px;">Ver pauta &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <div style="background:#fff; border-left:4px solid #f1c40f; border-radius:4px; padding:15px 20px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:12px; text-transform:uppercase; color:#888; letter-spacing:.5px;"><i class="fa fa-clock-o" style="color:#f1c40f;"></i> Próximos 7 dias</span>
                <div style="margin:6px 0 4px; font-size:34px; font-weight:700; color:#2c3e50;" id="cnt-7dias"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>
                <a href="{{ url('cliente/processos/acompanhamento') }}" style="font-size:12px;">Ver processos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3" id="cnt-msg-card">
            <div style="background:#fff; border-left:4px solid #ccc; border-radius:4px; padding:15px 20px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);" id="cnt-msg-inner">
                <span style="font-size:12px; text-transform:uppercase; color:#888; letter-spacing:.5px;"><i class="fa fa-envelope"></i> Mensagens Não Lidas</span>
                <div style="margin:6px 0 4px; font-size:34px; font-weight:700; color:#2c3e50;" id="cnt-mensagens"><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i></div>
                <a href="{{ url('cliente/processos/acompanhamento') }}" style="font-size:12px;">Ver processos &rsaquo;</a>
            </div>
        </div>
    </div>

    {{-- Linha 2: Pauta de hoje --}}
    <div class="row" style="margin-top:4px;">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-calendar-check-o txt-color-orange"></i>
                    <strong class="pauta-hoje-titulo" style="margin-left:6px;">Pauta de Hoje</strong> <span class="text-muted">— {{ date('d/m/Y') }}</span>
                    <a href="{{ url('cliente/pauta') }}" class="btn btn-xs btn-default pull-right"><i class="fa fa-external-link"></i> Pauta completa</a>
                </div>
                <div class="panel-body" id="box-pauta-hoje">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 3: Distribuição por status + Mensagens não lidas --}}
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1); min-height:260px;">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-pie-chart txt-color-blue"></i>
                    <strong style="margin-left:6px;">Distribuição por Situação</strong>
                </div>
                <div class="panel-body" id="box-status">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1); min-height:260px;">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-envelope txt-color-red"></i>
                    <strong style="margin-left:6px;">Mensagens Pendentes</strong>
                </div>
                <div class="panel-body" id="box-mensagens">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 4: Próximas audiências --}}
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-clock-o txt-color-blue"></i>
                    <strong style="margin-left:6px;">Próximas Audiências</strong> <small class="text-muted">(próximos 10 processos)</small>
                </div>
                <div class="panel-body" id="box-proximas">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    @endrole

    @role('administrator')

    {{-- ===== DASHBOARD DO ESCRITÓRIO ===== --}}

    {{-- Linha 1: Contadores --}}
    <div class="row" id="escritorio-contadores">
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <div style="background:#fff; border-left:4px solid #5b9bd1; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-folder-open txt-color-blue"></i> Ativos</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-ativos"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('processos') }}" style="font-size:11px;">Ver todos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <div style="background:#fff; border-left:4px solid #e67e22; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-calendar txt-color-orange"></i> Audiências Hoje</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-hoje"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('processos/pauta/online') }}" style="font-size:11px;">Ver pauta &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <div style="background:#fff; border-left:4px solid #f1c40f; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-clock-o" style="color:#f1c40f;"></i> Próx. 7 dias</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-7dias"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('processos') }}" style="font-size:11px;">Ver processos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2" id="esc-cnt-msg-card">
            <div style="background:#fff; border-left:4px solid #ccc; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);" id="esc-cnt-msg-inner">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-envelope"></i> Msgs N. Lidas</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-mensagens"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('processos') }}" style="font-size:11px;">Ver processos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2" id="esc-cnt-pendente-card">
            <div style="background:#fff; border-left:4px solid #ccc; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);" id="esc-cnt-pendente-inner">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-exclamation-circle"></i> Pendentes</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-pendentes"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('processos') }}" style="font-size:11px;">Ver processos &rsaquo;</a>
            </div>
        </div>
        <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
            <div style="background:#fff; border-left:4px solid #27ae60; border-radius:4px; padding:12px 16px; margin-bottom:10px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <span style="font-size:11px; text-transform:uppercase; color:#888;"><i class="fa fa-users" style="color:#27ae60;"></i> Correspondentes</span>
                <div style="margin:5px 0 3px; font-size:30px; font-weight:700; color:#2c3e50;" id="esc-cnt-correspondentes"><i class="fa fa-spinner fa-spin" style="font-size:18px;"></i></div>
                <a href="{{ url('correspondentes') }}" style="font-size:11px;">Ver todos &rsaquo;</a>
            </div>
        </div>
    </div>

    {{-- Linha 2: Pauta de hoje --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-calendar-check-o txt-color-orange"></i>
                    <strong class="esc-pauta-hoje-titulo" style="margin-left:6px;">Pauta de Hoje</strong> <span class="text-muted">&mdash; {{ date('d/m/Y') }}</span>
                    <a href="{{ url('processos/pauta/online') }}" class="btn btn-xs btn-default pull-right"><i class="fa fa-external-link"></i> Pauta completa</a>
                </div>
                <div class="panel-body" id="esc-box-pauta-hoje">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 3: Status + Área do Direito + Tipo de Processo --}}
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1); min-height:280px;">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-tasks txt-color-blue"></i>
                    <strong style="margin-left:6px;">Situação dos Processos</strong>
                </div>
                <div class="panel-body" id="esc-box-status">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1); min-height:280px;">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-balance-scale txt-color-green"></i>
                    <strong style="margin-left:6px;">Por Área do Direito</strong>
                </div>
                <div class="panel-body" id="esc-box-area">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1); min-height:280px;">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-sitemap txt-color-purple"></i>
                    <strong style="margin-left:6px;">Por Tipo de Processo</strong>
                </div>
                <div class="panel-body" id="esc-box-tipo">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 4: Próximas audiências --}}
    <div class="row" style="margin-bottom:20px;">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <div class="panel-heading" style="border-radius:4px 4px 0 0; padding:10px 15px;">
                    <i class="fa fa-clock-o txt-color-blue"></i>
                    <strong style="margin-left:6px;">Próximas Audiências</strong> <small class="text-muted">(próximos 10 processos)</small>
                </div>
                <div class="panel-body" id="esc-box-proximas">
                    <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>
                </div>
            </div>
        </div>
    </div>

    @endrole
</div>
@endsection
@section('script')
    <script type="text/javascript">

        var host =  $('meta[name="base-url"]').attr('content');

        // -----------------------------------------------------------------------
        // Dashboard do cliente
        // -----------------------------------------------------------------------
        @role('cliente')

        function carregarContadores() {
            $.getJSON(host + '/api/cliente/dashboard/contadores', function (d) {
                $('#cnt-ativos').text(d.total_ativos);
                $('#cnt-hoje').text(d.audiencias_hoje);
                $('#cnt-7dias').text(d.proximos_7_dias);
                $('#cnt-mensagens').text(d.mensagens_nao_lidas);

                if (d.mensagens_nao_lidas > 0) {
                    $('#cnt-msg-inner').css({'background':'#fdecea','border-left-color':'#c0392b'});
                    $('#cnt-mensagens').css('color','#c0392b');
                }
            });
        }

        function carregarPautaHoje() {
            $.getJSON(host + '/api/cliente/dashboard/pauta-hoje', function (data) {
                var box   = $('#box-pauta-hoje');
                var lista = data.processos;
                var total = data.total;

                // atualiza badge no título
                $('#badge-pauta-hoje').remove();
                if (total > 0) {
                    var badgeHtml = ' <span id="badge-pauta-hoje" class="badge" style="background:#e67e22;">' + total + '</span>';
                    if (total > 10) {
                        badgeHtml += ' <small class="text-muted" style="font-size:11px;">exibindo 10 de ' + total + '</small>';
                    }
                    $('.pauta-hoje-titulo').append(badgeHtml);
                }

                if (!lista.length) {
                    box.html('<p class="text-center text-muted">Nenhuma audiência hoje.</p>');
                    return;
                }
                var html = '<div class="table-responsive"><table class="table table-condensed table-hover" style="font-size:12px;">'
                         + '<thead><tr><th>Horário</th><th>Nº Processo</th><th>Serviço</th><th>Comarca/UF</th><th>Correspondente</th><th>Situação</th><th></th></tr></thead><tbody>';
                lista.forEach(function (p) {
                    html += '<tr>'
                          + '<td>' + (p.hr_audiencia_pro || '—') + '</td>'
                          + '<td>' + (p.nu_processo_pro || '—') + '</td>'
                          + '<td>' + (p.nm_tipo_servico_tse || '—') + '</td>'
                          + '<td>' + (p.nm_cidade_cde || '—') + '/' + (p.sg_estado_est || '') + '</td>'
                          + '<td>' + (p.nm_conta_correspondente_ccr || '—') + '</td>'
                          + '<td><span class="label" style="background:' + (p.ds_color_stp || '#999') + '">' + (p.nm_status_processo_conta_stp || '') + '</span></td>'
                          + '<td><a href="' + host + '/cliente/processos/acompanhamento/' + (p.hash || p.cd_processo_pro) + '" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a></td>'
                          + '</tr>';
                });
                html += '</tbody></table></div>';
                box.html(html);
            }).fail(function () {
                $('#box-pauta-hoje').html('<p class="text-danger text-center">Erro ao carregar pauta.</p>');
            });
        }

        function carregarStatus() {
            $.getJSON(host + '/api/cliente/dashboard/status', function (lista) {
                var box = $('#box-status');
                if (!lista.length) {
                    box.html('<p class="text-center text-muted">Sem processos ativos.</p>');
                    return;
                }
                var total = 0;
                lista.forEach(function(i){ total += parseInt(i.total); });
                var html = '';
                lista.forEach(function (s) {
                    var pct = total > 0 ? Math.round(parseInt(s.total) / total * 100) : 0;
                    var cor = s.ds_color_stp || '#aaa';
                    html += '<div style="margin-bottom:8px;">'
                          + '<div style="display:flex; justify-content:space-between; font-size:12px;"><span>' + s.nm_status_processo_conta_stp + '</span><strong>' + s.total + '</strong></div>'
                          + '<div style="background:#eee; border-radius:4px; height:8px;">'
                          + '<div style="width:' + pct + '%; background:' + cor + '; border-radius:4px; height:8px;"></div>'
                          + '</div></div>';
                });
                box.html(html);
            }).fail(function () {
                $('#box-status').html('<p class="text-danger text-center">Erro ao carregar.</p>');
            });
        }

        function carregarMensagens() {
            $.getJSON(host + '/api/cliente/dashboard/mensagens', function (lista) {
                var box = $('#box-mensagens');
                if (!lista.length) {
                    box.html('<p class="text-center text-muted">Nenhuma mensagem pendente.</p>');
                    return;
                }
                var html = '<ul class="list-group" style="margin:0;">';
                lista.forEach(function (m) {
                    html += '<li class="list-group-item" style="padding:8px 10px; font-size:12px;">'
                          + '<a href="' + host + '/cliente/processos/acompanhamento/' + m.token + '" target="_blank" style="font-weight:bold;">Proc. ' + m.nu_processo + '</a>'
                          + '<span class="pull-right text-muted">' + m.data + '</span>'
                          + '<p style="margin:3px 0 0; color:#555;">' + m.texto + '</p>'
                          + '</li>';
                });
                html += '</ul>';
                box.html(html);
            }).fail(function () {
                $('#box-mensagens').html('<p class="text-danger text-center">Erro ao carregar.</p>');
            });
        }

        function carregarProximas() {
            $.getJSON(host + '/api/cliente/dashboard/proximas', function (lista) {
                var box = $('#box-proximas');
                if (!lista.length) {
                    box.html('<p class="text-center text-muted">Nenhuma audiência futura nos próximos dias.</p>');
                    return;
                }
                var hoje = new Date(); hoje.setHours(0,0,0,0);
                var html = '<div class="table-responsive"><table class="table table-condensed table-hover" style="font-size:12px;">'
                         + '<thead><tr><th>Prazo Fatal</th><th>Horário</th><th>Nº Processo</th><th>Tipo de Serviço</th><th>Comarca/UF</th><th>Situação</th></tr></thead><tbody>';
                lista.forEach(function (p) {
                    var prazo = new Date(p.dt_prazo_fatal_pro + 'T00:00:00');
                    var diff  = Math.round((prazo - hoje) / 86400000);
                    var rowStyle = diff <= 3 ? 'background:#fdecea;' : (diff <= 7 ? 'background:#fffde7;' : '');
                    var prazoFmt = p.dt_prazo_fatal_pro
                        ? p.dt_prazo_fatal_pro.split('-').reverse().join('/')
                        : '—';
                    html += '<tr style="' + rowStyle + '">'
                          + '<td>' + prazoFmt + '</td>'
                          + '<td>' + (p.hr_audiencia_pro || '—') + '</td>'
                          + '<td>' + (p.nu_processo_pro || '—') + '</td>'
                          + '<td>' + (p.nm_tipo_servico_tse || '—') + '</td>'
                          + '<td>' + (p.nm_cidade_cde || '—') + '/' + (p.sg_estado_est || '') + '</td>'
                          + '<td><span class="label" style="background:' + (p.ds_color_stp || '#999') + '">' + (p.nm_status_processo_conta_stp || '') + '</span></td>'
                          + '</tr>';
                });
                html += '</tbody></table></div><small class="text-muted" style="font-size:11px;">🔴 ≤ 3 dias &nbsp; 🟡 ≤ 7 dias</small>';
                box.html(html);
            }).fail(function () {
                $('#box-proximas').html('<p class="text-danger text-center">Erro ao carregar.</p>');
            });
        }

        $(document).ready(function () {
            carregarContadores();
            carregarPautaHoje();
            carregarStatus();
            carregarMensagens();
            carregarProximas();
        });

        @endrole

        @role('administrator')

        function escritorioCarregarContadores() {
            $.ajax({
                url: host + '/escritorio/dashboard/contadores',
                type: 'GET',
                success: function (data) {
                    $('#esc-cnt-ativos').text(data.total_ativos);
                    $('#esc-cnt-hoje').text(data.audiencias_hoje);
                    $('#esc-cnt-7dias').text(data.proximos_7_dias);
                    $('#esc-cnt-mensagens').text(data.mensagens_nao_lidas);
                    $('#esc-cnt-pendentes').text(data.pendentes_analise);
                    $('#esc-cnt-correspondentes').text(data.correspondentes_ativos);

                    if (parseInt(data.mensagens_nao_lidas) > 0) {
                        $('#esc-cnt-msg-inner').css('border-left-color', '#e74c3c');
                        $('#esc-cnt-msg-inner i').css('color', '#e74c3c');
                    }
                    if (parseInt(data.pendentes_analise) > 0) {
                        $('#esc-cnt-pendente-inner').css('border-left-color', '#8e44ad');
                        $('#esc-cnt-pendente-inner i').css('color', '#8e44ad');
                    }
                },
                error: function () {
                    $('#escritorio-contadores .fa-spinner').replaceWith('<span class="text-danger">!</span>');
                }
            });
        }

        function escritorioCarregarPautaHoje() {
            $.ajax({
                url: host + '/escritorio/dashboard/pauta-hoje',
                type: 'GET',
                success: function (data) {
                    var total = data.total;
                    var lista = data.processos;
                    var $titulo = $('.esc-pauta-hoje-titulo');

                    $titulo.find('.badge').remove();
                    if (total > 0) {
                        $titulo.append(' <span class="badge" id="esc-badge-pauta-hoje" style="background:#e67e22;">' + total + '</span>');
                    }

                    if (lista.length === 0) {
                        $('#esc-box-pauta-hoje').html('<p class="text-center text-muted">Nenhum processo com prazo fatal hoje.</p>');
                        return;
                    }

                    var aviso = (total > 10)
                        ? '<p class="text-muted" style="font-size:11px; margin-bottom:6px;">Exibindo 10 de ' + total + ' processos. <a href="{{ url("processos/pauta/online") }}">Ver pauta completa</a>.</p>'
                        : '';

                    var html = aviso + '<div class="table-responsive"><table class="table table-condensed table-hover" style="margin-bottom:0;"><thead><tr><th>Nº Processo</th><th>Cliente</th><th>Tipo</th><th>Situação</th><th>Prazo Fatal</th></tr></thead><tbody>';
                    lista.forEach(function (p) {
                        var hoje = new Date(); hoje.setHours(0,0,0,0);
                        var prazo = new Date(p.dt_prazo_fatal_pro + 'T00:00:00');
                        var diff = Math.round((prazo - hoje) / 86400000);
                        var rowStyle = diff <= 0 ? 'background:#fde8e8;' : (diff <= 3 ? 'background:#fff3cd;' : '');
                        html += '<tr style="' + rowStyle + '">';
                        html += '<td><a href="{{ url("processos") }}/' + p.token_pro + '">' + (p.nu_processo_pro || 'S/N') + '</a></td>';
                        html += '<td>' + (p.nm_cliente || '') + '</td>';
                        html += '<td>' + (p.nm_tipo_processo || '') + '</td>';
                        html += '<td>' + (p.nm_status || '') + '</td>';
                        html += '<td>' + (p.dt_prazo_fatal_pro ? p.dt_prazo_fatal_pro.split('-').reverse().join('/') : '') + '</td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table></div>';
                    $('#esc-box-pauta-hoje').html(html);
                },
                error: function () {
                    $('#esc-box-pauta-hoje').html('<p class="text-center text-danger">Erro ao carregar a pauta de hoje.</p>');
                }
            });
        }

        function escritorioCarregarStatus() {
            $.ajax({
                url: host + '/escritorio/dashboard/status',
                type: 'GET',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#esc-box-status').html('<p class="text-center text-muted">Sem dados.</p>');
                        return;
                    }
                    var total = data.reduce(function(s, i) { return s + parseInt(i.total); }, 0);
                    var html = '';
                    data.forEach(function (item) {
                        var pct = total > 0 ? Math.round(parseInt(item.total) / total * 100) : 0;
                        var cor = item.ds_color_stp || '#5b9bd1';
                        html += '<div style="margin-bottom:8px;">';
                        html += '<div style="display:flex; justify-content:space-between; font-size:12px;">';
                        html += '<span>' + item.nm_status_processo_conta_stp + '</span>';
                        html += '<span><b>' + item.total + '</b></span>';
                        html += '</div>';
                        html += '<div style="background:#f0f0f0; border-radius:3px; height:10px; overflow:hidden;">';
                        html += '<div style="width:' + pct + '%; background:' + cor + '; height:100%; border-radius:3px;"></div>';
                        html += '</div></div>';
                    });
                    $('#esc-box-status').html(html);
                },
                error: function () {
                    $('#esc-box-status').html('<p class="text-center text-danger">Erro ao carregar.</p>');
                }
            });
        }

        function escritorioCarregarPorArea() {
            $.ajax({
                url: host + '/escritorio/dashboard/por-area',
                type: 'GET',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#esc-box-area').html('<p class="text-center text-muted">Sem dados.</p>');
                        return;
                    }
                    var total = data.reduce(function(s, i) { return s + parseInt(i.total); }, 0);
                    var cores = ['#5b9bd1','#27ae60','#e67e22','#8e44ad','#e74c3c','#16a085','#2980b9','#f39c12'];
                    var html = '';
                    data.forEach(function (item, idx) {
                        var pct = total > 0 ? Math.round(parseInt(item.total) / total * 100) : 0;
                        var cor = cores[idx % cores.length];
                        html += '<div style="margin-bottom:8px;">';
                        html += '<div style="display:flex; justify-content:space-between; font-size:12px;">';
                        html += '<span>' + item.area + '</span>';
                        html += '<span><b>' + item.total + '</b></span>';
                        html += '</div>';
                        html += '<div style="background:#f0f0f0; border-radius:3px; height:10px; overflow:hidden;">';
                        html += '<div style="width:' + pct + '%; background:' + cor + '; height:100%; border-radius:3px;"></div>';
                        html += '</div></div>';
                    });
                    $('#esc-box-area').html(html);
                },
                error: function () {
                    $('#esc-box-area').html('<p class="text-center text-danger">Erro ao carregar.</p>');
                }
            });
        }

        function escritorioCarregarPorTipo() {
            $.ajax({
                url: host + '/escritorio/dashboard/por-tipo-processo',
                type: 'GET',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#esc-box-tipo').html('<p class="text-center text-muted">Sem dados.</p>');
                        return;
                    }
                    var total = data.reduce(function(s, i) { return s + parseInt(i.total); }, 0);
                    var cores = ['#5b9bd1','#e67e22','#27ae60','#8e44ad','#e74c3c','#16a085','#2980b9','#f39c12'];
                    var html = '';
                    data.forEach(function (item, idx) {
                        var pct = total > 0 ? Math.round(parseInt(item.total) / total * 100) : 0;
                        var cor = cores[idx % cores.length];
                        html += '<div style="margin-bottom:8px;">';
                        html += '<div style="display:flex; justify-content:space-between; font-size:12px;">';
                        html += '<span>' + item.tipo + '</span>';
                        html += '<span><b>' + item.total + '</b></span>';
                        html += '</div>';
                        html += '<div style="background:#f0f0f0; border-radius:3px; height:10px; overflow:hidden;">';
                        html += '<div style="width:' + pct + '%; background:' + cor + '; height:100%; border-radius:3px;"></div>';
                        html += '</div></div>';
                    });
                    $('#esc-box-tipo').html(html);
                },
                error: function () {
                    $('#esc-box-tipo').html('<p class="text-center text-danger">Erro ao carregar.</p>');
                }
            });
        }

        function escritorioCarregarProximas() {
            $.ajax({
                url: host + '/escritorio/dashboard/proximas',
                type: 'GET',
                success: function (data) {
                    if (!data || data.length === 0) {
                        $('#esc-box-proximas').html('<p class="text-center text-muted">Nenhuma audiência futura encontrada.</p>');
                        return;
                    }
                    var html = '<div class="table-responsive"><table class="table table-condensed table-hover" style="margin-bottom:0;"><thead><tr><th>Nº Processo</th><th>Cliente</th><th>Tipo</th><th>Situação</th><th>Prazo Fatal</th></tr></thead><tbody>';
                    data.forEach(function (p) {
                        var hoje = new Date(); hoje.setHours(0,0,0,0);
                        var prazo = new Date(p.dt_prazo_fatal_pro + 'T00:00:00');
                        var diff = Math.round((prazo - hoje) / 86400000);
                        var rowStyle = diff <= 3 ? 'background:#fde8e8;' : (diff <= 7 ? 'background:#fff3cd;' : '');
                        html += '<tr style="' + rowStyle + '">';
                        html += '<td><a href="{{ url("processos") }}/' + p.token_pro + '">' + (p.nu_processo_pro || 'S/N') + '</a></td>';
                        html += '<td>' + (p.nm_cliente || '') + '</td>';
                        html += '<td>' + (p.nm_tipo_processo || '') + '</td>';
                        html += '<td>' + (p.nm_status || '') + '</td>';
                        html += '<td>' + (diff <= 3 ? '<span class="text-danger"><b>' : (diff <= 7 ? '<span class="text-warning"><b>' : '<span>')) + (p.dt_prazo_fatal_pro ? p.dt_prazo_fatal_pro.split('-').reverse().join('/') : '') + (diff <= 7 ? '</b></span>' : '</span>') + '</td>';
                        html += '</tr>';
                    });
                    html += '</tbody></table></div>';
                    if (data.length >= 10) {
                        html += '<p class="text-muted text-right" style="font-size:11px; margin-top:4px;">Exibindo os 10 próximos processos.</p>';
                    }
                    $('#esc-box-proximas').html(html);
                },
                error: function () {
                    $('#esc-box-proximas').html('<p class="text-center text-danger">Erro ao carregar próximas audiências.</p>');
                }
            });
        }

        $(document).ready(function () {
            escritorioCarregarContadores();
            escritorioCarregarPautaHoje();
            escritorioCarregarStatus();
            escritorioCarregarPorArea();
            escritorioCarregarPorTipo();
            escritorioCarregarProximas();
        });

        @endrole

        function carregarEspacoPasta() {
            $.ajax({
                url: host+"/dashboard/espaco-pasta",
                type: 'GET',
                success: function (data) {
                    $('.driver_tamanho').html(data.tamanho_pasta);
                    $('.driver_percentual').html(
                        data.tamanho_pasta + ' / ' + data.limite_definido + 
                        ' (' + data.percentual_uso + '%)'
                    );
                },
                error: function () {
                    $('.driver_tamanho').html('<span class="text-danger">Erro ao calcular</span>');
                    $('.driver_percentual').html('<span class="text-danger">Erro ao calcular</span>');
                }
            });
        }

        function carregarAcessosRecentes() {
            $.ajax({
                url: host+"/dashboard/acessos-recentes",
                type: 'GET',
                beforeSend: function () {
                    $("#acessos-recentes").html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>');
                },
                success: function (html) {
                    $("#acessos-recentes").html(html);
                },
                error: function () {
                    $("#acessos-recentes").html('<p class="text-danger text-center">Erro ao carregar os dados.</p>');
                }
            });
        }

        function carregarTop5Correspondentes(data_inicio, data_fim) {
            $.ajax({
                url: host+"/dashboard/correspondentes",
                type: 'GET',
                data: {
                    data_inicio: data_inicio,
                    data_fim: data_fim
                },
                beforeSend: function () {
                    $("#top5-correspondentes").html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando...</p>');
                },
                success: function (html) {
                    $("#top5-correspondentes").html(html);
                },
                error: function () {
                    $("#top5-correspondentes").html('<p class="text-danger text-center">Erro ao carregar os dados.</p>');
                }
            });
        }

        $(document).ready(function() {

            // Carregar espaço da pasta via AJAX
            carregarEspacoPasta();

            // Carregar acessos recentes
            carregarAcessosRecentes();

            // Função para formatar data como yyyy-mm-dd
            function formatarData(data) {
                return data.toISOString().split('T')[0];
            }

            // Define datas iniciais: últimos 7 dias
            const hoje = new Date();
            const fim = formatarData(hoje);
            const inicio = formatarData(new Date(hoje.setDate(hoje.getDate() - 7)));

            $('#data_inicio').val(inicio);
            $('#data_fim').val(fim);

            if (inicio && fim) {
                carregarTop5Correspondentes(inicio, fim);
            }

            // Trigger on date change
            $('#data_inicio, #data_fim').on('change', function () {
                const di = $('#data_inicio').val();
                const df = $('#data_fim').val();
                if (di && df) {
                    carregarTop5Correspondentes(di, df);
                }
            });

            $(function() {
                $('.periodo-btn').on('click', function() {
                    var dias = parseInt($(this).data('dias'));
                    var hoje = new Date();
                    var dataFim = hoje.toISOString().split('T')[0];

                    var dataInicio = new Date();
                    dataInicio.setDate(dataInicio.getDate() - dias);
                    var dataInicioStr = dataInicio.toISOString().split('T')[0];

                    $('#data_inicio').val(dataInicioStr);
                    $('#data_fim').val(dataFim);

                    carregarTop5Correspondentes(dataInicioStr, dataFim);
                });
            });

        });
    </script>
@endsection