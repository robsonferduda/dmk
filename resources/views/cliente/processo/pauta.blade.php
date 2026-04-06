@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Pauta Online</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i>Processos <span> > Pauta Online</span>
            </h1>
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="well" style="margin-left: 1px; margin-right: 1px; border-radius: 10px; background: #f5f5f5;">
                <fieldset>
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label-padrao">Prazo Fatal</label>
                            <input style="width: 100%" class="form-control datepicker date-mask" type="text" data-dateformat="dd/mm/yy" name="dt_prazo_fatal_pro" id="dt_prazo_fatal_pro" placeholder="___/___/____" value="{{ !empty($prazo_fatal) ? date('d/m/Y', strtotime($prazo_fatal)) : '' }}">
                        </section>
                        <section class="col col-md-3 col-lg-3">
                            <label class="label-padrao">Número do Processo</label>
                            <input style="width: 100%" class="form-control" type="text" id="nu_processo_pro" placeholder="Nº Processo" value="">
                        </section>
                        <section class="col col-md-3 col-lg-3 box-select2">
                            <label class="label-padrao">Tipos de Processo</label>
                            <select name="cd_tipo_processo_tpo" id="cd_tipo_processo_tpo" class="select2">
                                <option value="">Tipos de Processo</option>
                                @foreach($tiposProcesso as $tipo)
                                    <option value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                @endforeach
                            </select>
                        </section>
                        <section class="col col-md-4 col-lg-4" style="margin-top: 8px;">
                            <label class="label-padrao">Situação</label>
                            <select id="cd_status_processo_stp" name="cd_status_processo_stp" class="select2">
                                <option selected value="">Status do Processo</option>
                                @foreach($status as $st)
                                    <option value="{{ $st->cd_status_processo_stp }}">{{ $st->nm_status_processo_conta_stp }}</option>
                                @endforeach
                            </select>
                        </section>
                        <section class="col col-md-12 center">
                            <button class="btn btn-primary btn-pesquisar" style="width: 10%; margin-top: 22px;" type="button"><i class="fa fa-search"></i> Pesquisar</button>
                        </section>
                    </div>
                </fieldset>
            </div>
        </article>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <h5 style="font-size: 12px;"><strong>Total de Processos</strong>: <span id="total-processos">0</span></h5>
                </div>
                <div class="col-md-12" id="box-processos-container"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');

        carregaProcessos();

        $(".btn-pesquisar").click(function () {
            carregaProcessos();
        });

        function carregaProcessos() {

            var data                 = $("#dt_prazo_fatal_pro").val();
            var processo             = $("#nu_processo_pro").val();
            var situacao             = $("#cd_status_processo_stp").val();
            var tipo                 = $("#cd_tipo_processo_tpo").val();

            $.ajax({
                url: host + '/api/cliente/processo/pauta',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: data,
                    processo: processo,
                    statusProcesso: situacao,
                    tipo: tipo
                },
                dataType: "JSON",
                success: function (response) {

                    var container    = $("#box-processos-container");
                    var totalElement = $("#total-processos");

                    container.empty();
                    totalElement.text(response.length);

                    if (response.length === 0) {
                        container.append('<h5 class="center">Nenhum dado para ser exibido</h5>');
                        return;
                    }

                    response.forEach(function (processo) {

                        var cor_fundo = (processo.fl_audiencia_confirmada_pro) ? '#c9ffcb' : 'white';
                        var cor_borda = (processo.fl_audiencia_confirmada_pro) ? '#95ff9a' : 'white';

                        cor_fundo = (processo.fl_checkin_pro) ? '#c8e7ff' : cor_fundo;
                        cor_borda = (processo.fl_checkin_pro) ? '#a7d9ff' : cor_borda;

                        var html = `
                        <div class="well box-acompanhamento" style="padding: 10px 15px; border: 1px solid ${cor_borda}; border-radius: 10px; background: ${cor_fundo}; display: block;">
                            <div class="row box-processo">
                                <div class="col-lg-12 box-content">
                                    <h6 style="margin: 0px; font-size: 13px;">
                                        <strong>
                                            <span style="background-color: ${ processo.ds_color_stp }" class="label label-default pull-right">${ processo.nm_status_processo_conta_stp }</span>
                                        </strong>
                                        <strong>NÚMERO ${ processo.nu_processo_pro || '' }</strong>
                                    </h6>
                                </div>
                                <div class="col-md-6 box-content" style="margin-top: 5px;">
                                    <h6><strong>Prazo Fatal</strong>: ${ processo.dt_prazo_fatal_pro || '' } ${ processo.hr_audiencia_pro || '' }</h6>
                                    <h6><strong>Correspondente</strong>: ${ processo.nm_conta_correspondente_ccr || '' }</h6>
                                    <h6><strong>Parte Adversa</strong>: ${ processo.nm_autor_pro || '' }</h6>
                                    <h6><strong>Réu</strong>: ${ processo.nm_reu_pro || '' }</h6>
                                    <h6><strong>Área do Direito</strong>: ${ processo.dc_area_direito_ado || '' }</h6>
                                </div>
                                <div class="col-md-6 box-content">
                                    <h6><strong>Comarca</strong>: ${ processo.nm_cidade_cde || '' }/${ processo.sg_estado_est || '' }</h6>
                                    <h6><strong>Serviço</strong>: ${ processo.nm_tipo_servico_tse || '' }</h6>
                                    <h6><strong>Código do Cliente</strong>: ${ processo.nu_acompanhamento_pro || '' }</h6>
                                    <h6><strong>Foro</strong>: ${ processo.nm_vara_var || '' }</h6>
                                    <h6><strong>Tipo de Processo</strong>: ${ processo.nm_tipo_processo_tpo || '' }</h6>
                                </div>
                            </div>
                        </div>`;

                        container.append(html);
                    });
                },
                error: function (xhr) {
                    console.error('Erro ao carregar processos:', xhr.responseText);
                }
            });
        }

    });
</script>
@endsection