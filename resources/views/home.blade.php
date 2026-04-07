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
    <div class="row" id="filtro-periodo" style="margin-bottom: 8px;">
        <div class="col-md-12 col-sm-12 mb-3">
            <form id="formFiltroPeriodo" class="form-inline" style="text-align: right;" method="GET" action="{{ url()->current() }}">
                <div class="form-group">
                    <input type="date" class="form-control" name="data_inicio" id="data_inicio" value="{{ date('d/m/Y') }}">
                </div>
                <div class="form-group">
                    <input type="date" class="form-control" name="data_fim" id="data_fim" value="{{ date('d/m/Y') }}">
                </div>

                <div class="btn-group" role="group" aria-label="Períodos Rápidos">
                    <button type="button" class="btn btn-default periodo-btn" data-dias="7">Última semana</button>
                    <button type="button" class="btn btn-default periodo-btn" data-dias="15">Últimos 15 dias</button>
                    <button type="button" class="btn btn-default periodo-btn" data-dias="30">Últimos 30 dias</button>
                </div>
            </form>
        </div>
    </div>

        <div class="row">           



            <!--
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="alert alert-warning fade in">
                    <button class="close" data-dismiss="alert">×</button>
                    <i class="fa-fw fa fa-warning"></i>
                    <strong>Atenção</strong> Sua conta não foi ativada. Acesse seu email e ative sua conta. Não recebeu o email? <a href="{{ url("/") }}">Clique aqui</a>!
                </div>
            </div>
            -->

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center connect box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        @if(file_exists('public/img/users/ent'.Auth::user()->cd_entidade_ete.'.png')) 
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/ent'.Auth::user()->cd_entidade_ete.'.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @else
                            <a href="" data-toggle="modal" data-target="#upload-image"><img src="{{ asset('img/users/user.png') }}" alt="" style="width: 100%; margin: 0 auto;" class="img-circle img-responsive"></a>
                        @endif
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4><span>Olá <b>{{ (Auth::user()) ? Auth::user()->name : "Usuário não logado!" }}</b>!</span></h4>
                        <h5>
                            @if(Auth::user()->cd_nivel_niv == 2)
                                <a href="{{ url("usuarios/".\Crypt::encrypt(Auth::user()->id)) }}" class="margin-top-5 margin-bottom-5"> <span>Meu Perfil</span></a>
                            @endif

                            @if(Auth::user()->cd_nivel_niv == 1) 
                                <a href="{{ url("conta/detalhes/".\Crypt::encrypt(Auth::user()->cd_conta_con)) }}"> Minha Conta</a>  
                            @endif
                        </h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>  

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <a href="{{ url('processos') }}"><img src="{{ asset('img/processo.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4>
                            <span><b>Processos</b></span>
                        </h4>
                        
                        <h5>
                            @if(count($processos) > 0)
                                <span>({{ count($processos) }})</span>
                            @endif
                            <a href="{{ url('processos') }}">Meus Processos</a>
                        </h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div> 

            <div class="col-sm-12 col-md-6 col-lg-4">                
                <div class="well text-center box-home" style="min-height: 110px;">
                    <div class="col-sm-12 col-md-6 col-lg-3">
                        <a href="{{ url('processos') }}"><img src="{{ asset('img/legal.png') }}" alt="" style="width: 90%; margin: 0 auto;" ></a>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-9" style="text-align: left;">
                        <h4><span><b>Correspondentes</b></span></h4>                    
                        <h5><a href="{{ url('correspondentes') }}">Meus Correspondentes</a></h5>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

        </div>
        <div class="row">
            <!--
            <div class="col-md-4 " id="top5-correspondentes">
            
            </div>
            
            <div class="col-md-4" id="acessos-recentes">
            
            </div>
        -->
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