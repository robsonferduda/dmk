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
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 box-button-xs" >
            <div class="sub-box-button-xs">
                <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right"><i class ="fa fa-list fa-lg"></i> Listar Processos</a>
                <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
            </div>
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="well" style="margin-left: 1px; margin-right: 1px; border-radius: 10px; background: #f5f5f5;">
                <form action="{{ url('processos/pauta/online') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-2">                                       
                                <label class="label-padrao">Prazo Fatal</label>          
                                <input style="width: 100%" class="form-control datepicker date-mask" type="text" data-dateformat="dd/mm/yy" name="dt_prazo_fatal_pro" id="dt_prazo_fatal_pro" placeholder="___/___/____" value="{{ !empty($prazo_fatal) ? date('d/m/Y', strtotime($prazo_fatal)) : '' }}" > 
                            </section>
                            <section class="col col-md-2 col-lg-2">
                                <label class="label-padrao">Número do Processo</label>
                                <input style="width: 100%" class="form-control" type="text" id="nu_processo_pro" placeholder="Nº Processo" value="{{ !empty($reu) ? $reu : '' }}" >
                            </section> 
                            <section class="col col-md-2 col-lg-2">
                                <label class="label-padrao">Código do Cliente</label>
                                <input style="width: 100%" class="form-control" type="text" id="nu_acompanhamento_pro" placeholder="Código Cliente" value="{{ !empty($nu_acompanhamento_pro) ? $nu_acompanhamento_pro : '' }}" >         
                            </section>
                            <section class="col col-md-3 col-lg-3">
                                <label class="label-padrao">Nome Cliente</label><br />
                                <input style="width: 100%" minlength=3 type="text" name="nm_cliente" class="form-control" id="nm_cliente" placeholder="Nome Cliente" value="{{ !empty($nm_cliente) ? $nm_cliente : '' }}" >         
                            </section> 
                            <section class="col col-md-3 col-lg-3">
                                <label class="label-padrao">Correspondente</label><br />
                                <input style="width: 100%" minlength=3 type="text" name="nm_correspondente" class="form-control" id="nm_correspondente" placeholder="Correspondente" value="{{ !empty($nm_correspondente) ? $nm_correspondente : '' }}" >         
                            </section> 
                            <section class="col col-md-3 col-lg-3 box-select2"> 
                                <label class="label-padrao">Tipos de Processo</label> 
                                <select name="cd_tipo_processo_tpo" id="cd_tipo_processo_tpo" class="select2">
                                    <option value="">Tipos de Processo</option>
                                    @foreach($tiposProcesso as $tipo)
                                        <option {{ (!empty($tipoProcesso) && $tipoProcesso == $tipo->cd_tipo_processo_tpo) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                    @endforeach
                                </select>
                            </section> 
                            <section class="col col-md-3 col-lg-3" style="margin-top: 8px;">  
                                <label class="label-padrao">Situação</label>       
                                <select id="cd_status_processo_stp" name="cd_status_processo_stp" class="select2">
                                    <option selected value="">Status do Processo</option>
                                    @foreach($status as $st)
                                        <option value="{{ $st->cd_status_processo_stp }}">{{ $st->nm_status_processo_conta_stp }}</option>
                                    @endforeach
                                </select> 
                            </section> 
                            <section class="col col-xs-3 col-lg-3" style="margin-top: 8px;">
                                           
                                            <label class="label-padrao" >Área do Direito</label>          
                                            <select  id="cd_area_direito_ado" name="cd_area_direito_ado" class="select2">
                                                <option selected value="">Selecione um área</option>
                                                @foreach($areas as $area) 
                                                    <option {!! (old('cd_area_direito_ado') == $area->cd_area_direito_ado ? 'selected' : '' ) !!} value="{{ $area->cd_area_direito_ado }}">{{ $area->dc_area_direito_ado}}</option>
                                                @endforeach

                                            </select> 
                            </section>
                            <section class="col col-md-3 col-lg-3" style="margin-top: 8px;">                                       
                                <label class="label-padrao">Responsável</label>          
                                <select id="cd_responsavel_pro" name="cd_responsavel_pro" class="select2">
                                    <option selected value="">Selecione um responsável</option>
                                    @foreach($responsaveis as $usuario)
                                        <option value="{{ $usuario->id }}" {{ ($responsavel == $usuario->id or Auth::user()->id == $usuario->id) ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                    @endforeach
                                </select> 
                            </section>
                            <section class="col col-md-12 center"> 
                                <button class="btn btn-primary btn-pesquisar" style="width: 10%; margin-top: 22px;" type="button"><i class="fa fa-search"></i> Pesquisar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12" id="timeline-processos-wrapper" style="display: none; margin-bottom: 15px;">
                    <div class="well" style="margin-left: 1px; margin-right: 1px; border-radius: 10px; background: #fff; padding: 15px 20px;">
                        <h5 style="margin: 0 0 12px 0; font-size: 13px;">
                            <i class="fa fa-clock-o"></i> <strong>Distribuição de Processos por Horário</strong>
                            <span id="timeline-pico-horario" style="font-weight: normal; color: #666; font-size: 12px;"></span>
                        </h5>
                        <div id="timeline-processos" style="display: flex; align-items: flex-end; gap: 4px; min-height: 120px; padding: 10px 0 5px; overflow-x: auto;"></div>
                        <div id="timeline-processos-legenda" style="margin-top: 8px; font-size: 11px; color: #888;"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h5 style="font-size: 12px;"><strong>Total de Processos</strong>: <span id="total-processos">0</span></h5>
                </div>
                <div class="col-md-12" id="box-processos-container">
                    
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<div class="modal fade in modal_top_alto" id="modal_pauta" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Pauta Diária</h4>
                     </div>
                    <div class="modal-body">
                        <form method="POST" class="smart-form" id="frm-pauta" action="{{ url('processo/pauta-diaria') }}">
                        @csrf
                            <fieldset>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Tipo de Intervalo de Data</label>
                                        <label class="select">
                                            <select id="tipo_data" name="tipo_data">
                                                <option value="">Selecione o tipo de intervalo</option>    
                                                <option value="solicitacao">Data de Solicitação</option> 
                                                <option value="prazo_fatal" selected>Prazo Fatal</option>         
                                            </select><i></i>  
                                        </label>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-4">
                                        <label>Data início</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_inicio" id="dt_inicio" value="{{ date('d/m/Y') }}" data-dateformat="dd/mm/yy" placeholder="___/___/____" class="datepicker mascara_data">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label>Data fim</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_fim" id="dt_fim" value="{{ date('d/m/Y') }}" data-dateformat="dd/mm/yy" placeholder="___/___/____" class="datepicker mascara_data" >
                                        </label>
                                    </section>
                               
                                    <section class="col col-2">
                                        <label class="radio-inline" style="margin-top: 22px; margin-left: 10px;">
                                            <input type="radio" name="tipo" id="excel" value="excel" required> Excel
                                        </label>
                                    </section> 
                                    <section class="col col-2">     
                                        <label class="radio-inline" style="margin-top: 22px; margin-left: 10px;">
                                            <input type="radio" name="tipo" id="pdf" value="pdf" required> PDF
                                        </label>
                                    </section> 
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Responsável</label>
                                        <select style="width: 100%"  class="select2" name="responsavel" >
                                            <option value="">Todos</option>
                                            @foreach($responsaveis as $user)
                                                 <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Tipos de Processo</label>
                                        <select style="width: 100%"  class="select2" name="tipoProcesso" >
                                            <option value="">Todos</option>
                                            @foreach($tiposProcesso as $tipo)
                                                 <option value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">    
                                    <input type="hidden" name="cdCorrespondente" value="">           
                                    <section class="col col-sm-12">
                                        <label>Correspondente</label>
                                        <label class="input">
                                            <input class="form-control" name="nmCorrespondente" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete_pauta" value="">
                                        </label>
                                    </section>
                                </div> 
                                <div class="row">
                                    <section class="col col-sm-12"> 
                                        <label>Status do Processo</label> 
                                        <select id="cd_status_processo_stp" name="cd_status_processo_stp" class="select2">
                                            <option selected value="">Todos</option>
                                            @foreach($status as $st)
                                                <option value="{{ $st->cd_status_processo_stp }}">{{ $st->nm_status_processo_conta_stp }}</option>
                                            @endforeach
                                        </select> 
                                    </section>                                     
                                </div>
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Gerar Pauta</button>
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
</div>
  <div class="modal fade modal_top_alto" id="informarPreposto" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="frmInformarPreposto" action="{{ url('processo/pauta/atualizar-dados') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cd_processo_pro" id="cd_processo_pro" value="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title">
                                <i class="icon-append fa fa-legal"></i> Dados da Audiência - Informar Advogado e/ou Preposto
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row box-cadastro">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Advogado</strong><span class="text-info"> Informe NOME COMPLETO - OAB - TELEFONE (Separados por traço)</span></label>
                                        <p class="text-danger">Digite cada sequencia de dados em uma linha</p>
                                        <textarea class="form-control texto-processo" rows="8" name="dados_advogado" id="dados_advogado" 
                                        placeholder="NOME COMPLETO - OAB - TELEFONE"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();"></textarea>
                                        <div id="msg_error_advogado" class="text-danger"></div>
                                    </div>    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Preposto</strong><span class="text-info"> Informe NOME COMPLETO - CPF - RG - TELEFONE (Separados por traço)</span></label>
                                        <p class="text-danger">Digite cada sequencia de dados em uma linha</p>
                                        <textarea class="form-control texto-processo" rows="8" name="dados_preposto" id="dados_preposto" 
                                        placeholder="NOME COMPLETO - CPF - RG - TELEFONE"
                                        style="text-transform: uppercase;"
                                        oninput="this.value = this.value.toUpperCase();"></textarea>
                                        <div id="msg_error_preposto" class="text-danger"></div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                            <button type="button" class="btn btn-success" id="btnSalvarAdvogadoSolicitante"><i class="fa-fw fa fa-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function flagAtivo(val) {
        if (val === true || val === 1) return true;
        if (val === false || val === 0 || val == null) return false;
        var s = String(val).toLowerCase();
        if (s === '0' || s === 'f' || s === 'false' || s === 'n' || s === 'no' || s === 'off') return false;
        return (s === '1' || s === 't' || s === 'true' || s === 's' || s === 'yes');
    }

    function extrairHoraAudiencia(hr) {
        if (!hr) return null;
        var texto = String(hr).trim();
        if (!texto || texto.toLowerCase() === 'não informada') return null;
        var match = texto.match(/(\d{1,2}):(\d{2})/);
        if (!match) return null;
        var hora = parseInt(match[1], 10);
        return (hora >= 0 && hora <= 23) ? hora : null;
    }

    function formatarHoraLabel(hora) {
        return String(hora).padStart(2, '0') + 'h';
    }

    function corBarraTimeline(qtd, maxQtd, ehPico) {
        if (ehPico) return '#3276b1';
        var intensidade = maxQtd > 0 ? qtd / maxQtd : 0;
        if (intensidade >= 0.75) return '#5a9fd4';
        if (intensidade >= 0.5) return '#7eb3de';
        if (intensidade >= 0.25) return '#a8cce8';
        return '#d4e6f5';
    }

    function renderTimelineProcessos(processos) {
        var wrapper = $("#timeline-processos-wrapper");
        var container = $("#timeline-processos");
        var legenda = $("#timeline-processos-legenda");
        var picoElement = $("#timeline-pico-horario");

        container.empty();
        legenda.empty();
        picoElement.text('');

        if (!processos || processos.length === 0) {
            wrapper.hide();
            return;
        }

        var porHora = {};
        var semHorario = 0;

        processos.forEach(function(processo) {
            var hora = extrairHoraAudiencia(processo.hr_audiencia_pro);
            if (hora === null) {
                semHorario++;
            } else {
                porHora[hora] = (porHora[hora] || 0) + 1;
            }
        });

        var horasComProcessos = Object.keys(porHora).map(Number).sort(function(a, b) { return a - b; });

        if (horasComProcessos.length === 0) {
            wrapper.hide();
            return;
        }

        var maxQtd = Math.max.apply(null, Object.values(porHora));
        var horasPico = horasComProcessos.filter(function(h) { return porHora[h] === maxQtd; });
        var horaInicio = Math.max(0, horasComProcessos[0] - 1);
        var horaFim = Math.min(23, horasComProcessos[horasComProcessos.length - 1] + 1);

        var picoTexto = horasPico.map(formatarHoraLabel).join(', ');
        picoElement.html(' — Pico: <strong style="color: #3276b1;">' + picoTexto + '</strong> (' + maxQtd + ' processo' + (maxQtd > 1 ? 's' : '') + ')');

        for (var h = horaInicio; h <= horaFim; h++) {
            var qtd = porHora[h] || 0;
            var ehPico = qtd > 0 && qtd === maxQtd;
            var alturaBarra = qtd > 0 ? Math.max(24, Math.round((qtd / maxQtd) * 80)) : 4;
            var cor = qtd > 0 ? corBarraTimeline(qtd, maxQtd, ehPico) : '#eee';

            var barraHtml = `
                <div class="timeline-hora-item" style="flex: 1; min-width: 36px; display: flex; flex-direction: column; align-items: center; cursor: default;"
                     title="${formatarHoraLabel(h)}: ${qtd} processo${qtd !== 1 ? 's' : ''}">
                    <span style="font-size: 11px; font-weight: ${ehPico ? 'bold' : 'normal'}; color: ${ehPico ? '#3276b1' : '#666'}; margin-bottom: 4px; min-height: 14px;">
                        ${qtd > 0 ? qtd : ''}
                    </span>
                    <div style="width: 100%; max-width: 40px; height: ${alturaBarra}px; background: ${cor}; border-radius: 4px 4px 0 0; border: 1px solid ${qtd > 0 ? (ehPico ? '#2c6999' : '#b8d4ea') : '#ddd'}; transition: height 0.3s;"></div>
                    <span style="font-size: 10px; color: #888; margin-top: 5px; white-space: nowrap;">${formatarHoraLabel(h)}</span>
                </div>`;

            container.append(barraHtml);
        }

        var partesLegenda = [];
        if (semHorario > 0) {
            partesLegenda.push('<i class="fa fa-info-circle"></i> ' + semHorario + ' processo' + (semHorario > 1 ? 's' : '') + ' sem horário informado');
        }
        partesLegenda.push('Período exibido: ' + formatarHoraLabel(horaInicio) + ' às ' + formatarHoraLabel(horaFim));
        legenda.html(partesLegenda.join(' &nbsp;|&nbsp; '));

        wrapper.show();
    }

    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        carregaProcessos();

        $(".btn-pesquisar").click(function(){
            carregaProcessos();
        });

        $("#btnSalvarAdvogadoSolicitante").click(function(e) {
            e.preventDefault(); // impede o comportamento padrão

            var form = $("#frmInformarPreposto");
            var formData = form.serialize(); // ou new FormData(form[0]) se houver arquivos

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                success: function(response) {
                    // Aqui você trata o retorno do controlador Laravel
                    console.log("✅ Sucesso:", response);
                    $("#informarPreposto").modal("hide");
                    $(".btn-pesquisar").trigger('click');
                },
                error: function(xhr) {
                    console.error("❌ Erro:", xhr.responseText);
                    // Ex: exibir erro de validação ou alerta
                }
            });

            return false; // redundante com e.preventDefault(), mas pode manter por segurança
        });

        $(document).on('change','.status',function(){

            let select = $(this);
            let novoStatus = select.val();
            let processoId = select.data('processo');

            if (novoStatus == 0) return; // Evita envio se for a opção "Alterar Situação"

            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja realmente alterar a situação deste processo?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, alterar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar via AJAX
                    $.ajax({
                        url: host+'/processos/alterar-status',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            cd_processo_pro: processoId,
                            novo_status: novoStatus
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Status alterado!',
                                text: 'O status do processo foi atualizado com sucesso.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $(".btn-pesquisar").trigger('click');
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Não foi possível alterar o status.',
                                icon: 'error'
                            });
                        }
                    });
                } else {
                    // Volta o select para a opção original (se quiser)
                    select.val(0); // ou guardar a antiga e restaurar
                }
            });

        });

        $(document).on('click','.dados-audiencistas',function(){

            let id = $(this).data("id");

            $.ajax({
                
                url: host+'/api/processo/'+id,
                type: 'GET',
                beforeSend: function(){
                               
                },
                success: function(response){
                    $("#cd_processo_pro").val(id);
                    $("#dados_advogado").val(response.nm_advogado_pro);
                    $("#dados_preposto").val(response.nm_preposto_pro); 

                    $("#informarPreposto").modal("show");                
                },
                complete: function(){
                     
                }
            });
        });

        $(document).on('change', 'input[name="fl_audiencia_confirmada_pro"]', function () {
            let confirmado = $(this).is(':checked'); // true ou false
            let processoId = $(this).data('processo'); // vamos adicionar isso no HTML

            // Enviar via AJAX
            $.ajax({
                url: host+'/processo/atualizar-audiencia',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    cd_processo_pro: processoId,
                    fl_audiencia_confirmada_pro: confirmado ? 'S' : 'N'
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atualizado',
                        text: 'Audiência confirmada foi atualizada com sucesso!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    $(".btn-pesquisar").trigger('click');
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao atualizar a audiência.'
                    });
                }
            });
        });

        $(document).on('change', 'input[name="fl_checkin_pro"]', function () {
            let confirmado = $(this).is(':checked');
            let processoId = $(this).data('processo');

            $.ajax({
                url: host+'/processo/atualizar-checkin',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    cd_processo_pro: processoId,
                    fl_checkin_pro: confirmado ? 'S' : 'N'
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atualizado',
                        text: 'Check-in do correspondente atualizado com sucesso!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    $(".btn-pesquisar").trigger('click');
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao atualizar o check-in do correspondente.'
                    });
                }
            });
        });

        $(document).on('change', 'input[name="fl_checkin_escritorio_pro"]', function () {
            let confirmado = $(this).is(':checked');
            let processoId = $(this).data('processo');

            $.ajax({
                url: host+'/processo/atualizar-checkin-escritorio',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    cd_processo_pro: processoId,
                    fl_checkin_escritorio_pro: confirmado ? 'S' : 'N'
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atualizado',
                        text: 'Check-in do escritório atualizado com sucesso!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    $(".btn-pesquisar").trigger('click');
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao atualizar o check-in do escritório.'
                    });
                }
            });
        });

        $(document).on('change', 'input[name="fl_documento_representacao_pro"]', function () {
            let confirmado = $(this).is(':checked'); // true ou false
            let processoId = $(this).data('processo'); // vamos adicionar isso no HTML

            // Enviar via AJAX
            $.ajax({
                url: host+'/processo/atualizar-documento-representacao',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    cd_processo_pro: processoId,
                    fl_documento_representacao_pro: confirmado ? 'S' : 'N'
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Atualizado',
                        text: 'Documento de Representação Protocolado atualizado com sucesso!',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    $(".btn-pesquisar").trigger('click');
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao atualizar o processo.'
                    });
                }
            });
        });

        function carregaProcessos()
        {

            data = $("#dt_prazo_fatal_pro").val();
            processo = $("#nu_processo_pro").val();
            numero_acompanhamento = $("#nu_acompanhamento_pro").val();
            nm_cliente = $("#nm_cliente").val();
            nm_correspondente = $("#nm_correspondente").val();
            responsavel = $("#cd_responsavel_pro").val();    
            situacao = $("#cd_status_processo_stp").val();  
            tipo = $("#cd_tipo_processo_tpo").val();     
            area = $("#cd_area_direito_ado").val(); 

            $.ajax({
                
                url: host+'/api/processo/pauta',
                type: 'POST',
                data: {"data": data, 
                       "processo": processo, 
                       "numero_acompanhamento": numero_acompanhamento,
                       "nm_cliente": nm_cliente, 
                       "nm_correspondente": nm_correspondente,                         
                       "responsavel": responsavel,
                       "statusProcesso": situacao,
                       "area": area,
                       "tipo": tipo
                },
                dataType: "JSON",
                beforeSend: function(){
                    //$(".box-acompanhamento").empty();            
                },
                success: function(response){

                    let container = $("#box-processos-container");
                    let totalElement = $("#total-processos");

                    container.empty();
                    totalElement.text(response.length);
                    renderTimelineProcessos(response);

                    if (response.length === 0) {
                        container.append('<h5 class="center">Nenhum dado para ser exibido</h5>');
                        return;
                    }

                        response.forEach(function(processo) {

                            let audienciaConfirmada = flagAtivo(processo.fl_audiencia_confirmada_pro);
                            let checkinEscritorio = flagAtivo(processo.fl_checkin_escritorio_pro);
                            let checkinCorrespondente = flagAtivo(processo.fl_checkin_pro);

                            let cor_fundo = 'white';
                            let cor_borda = 'white';

                            // Sequência: audiência → escritório → correspondente
                            if (checkinCorrespondente) {
                                cor_fundo = '#c9ffcb';
                                cor_borda = '#95ff9a';
                            } else if (checkinEscritorio) {
                                cor_fundo = '#c8e7ff';
                                cor_borda = '#a7d9ff';
                            } else if (audienciaConfirmada) {
                                cor_fundo = '#fff3cd';
                                cor_borda = '#ffc107';
                            }

                            let html = `
                            <div class="well box-acompanhamento" style="position: relative; padding: 10px 15px; border: 1px solid ${cor_borda}; border-radius: 10px; background: ${cor_fundo}; display: block;">
                                <div class="row box-processo">
                                    <div class="col-lg-12 box-content">
                                        <h6 style="margin: 0px; font-size: 13px;">
                                            <strong>
                                                <span style="background-color: ${ processo.ds_color_stp }" class="label label-default pull-right">${ processo.nm_status_processo_conta_stp }</span>
                                            </strong> 
                                            <select class="status" name="status" data-processo="${ processo.cd_processo_pro }" class="pull-left">
                                                <option selected value="0">Alterar Situação</option>
                                                <option value="3" ${processo.cd_status_processo_stp == 3 ? 'selected' : ''}>ACEITO PELO CORRESPONDENTE</option>
                                                <option value="2" ${processo.cd_status_processo_stp == 2 ? 'selected' : ''}>AGUARDANDO CONFIRMAÇÃO DE CONTRATAÇÃO</option>
                                                <option value="10" ${processo.cd_status_processo_stp == 10 ? 'selected' : ''}>AGUARDANDO CONFIRMAÇÃO DE RECEBIMENTO DE DOCUMENTOS</option>
                                                <option value="11" ${processo.cd_status_processo_stp == 11 ? 'selected' : ''}>AGUARDANDO CUMPRIMENTO</option>
                                                <option value="12" ${processo.cd_status_processo_stp == 12 ? 'selected' : ''}>AGUARDANDO DADOS</option>
                                                <option value="5" ${processo.cd_status_processo_stp == 5 ? 'selected' : ''}>AGUARDANDO RECEBIMENTO DOS DOCUMENTOS DO CLIENTE</option>
                                                <option value="16" ${processo.cd_status_processo_stp == 16 ? 'selected' : ''}>ALTERADO PELO CLIENTE</option>
                                                <option value="15" ${processo.cd_status_processo_stp == 15 ? 'selected' : ''}>CADASTRADO PELO CLIENTE</option>
                                                <option value="7" ${processo.cd_status_processo_stp == 7 ? 'selected' : ''}>CANCELADO PELO CLIENTE</option>
                                                <option value="9" ${processo.cd_status_processo_stp == 9 ? 'selected' : ''}>CONTRATAR CORRESPONDENTE</option>
                                                <option value="13" ${processo.cd_status_processo_stp == 13 ? 'selected' : ''}>DADOS ATUALIZADOS</option>
                                                <option value="1" ${processo.cd_status_processo_stp == 1 ? 'selected' : ''}>ELABORAR DOCUMENTOS DE REPRESENTAÇÃO</option>
                                                <option value="6" ${processo.cd_status_processo_stp == 6 ? 'selected' : ''}>FINALIZADO</option>
                                                <option value="8" ${processo.cd_status_processo_stp == 8 ? 'selected' : ''}>FINALIZADO PELO CORRESPONDENTE</option>
                                                <option value="14" ${processo.cd_status_processo_stp == 14 ? 'selected' : ''}>PENDENTE DE ANÁLISE</option>
                                                <option value="4" ${processo.cd_status_processo_stp == 4 ? 'selected' : ''}>RECUSADO PELO CORRESPONDENTE</option>
                                            </select>                 
                                        </h6>
                                    </div>
                                    <div class="col-md-4 box-content" style="margin-top: 5px;">
                                        <h4 style="margin: 0px; font-size: 13px;"><a title="Acompanhamento" target="_BLANK" class="icone-acompanhamento" href="../../processos/acompanhamento/${processo.hash || ''}">NÚMERO ${processo.nu_processo_pro || ''}</a></h4>
                                        <h6><strong>Prazo Fatal </strong>: ${processo.dt_prazo_fatal_pro || ''} ${processo.hr_audiencia_pro || ''}</h6>
                                        <h6><strong>Correspondente</strong>: ${processo.nm_conta_correspondente_ccr || ''}</h6>
                                        <h6><strong>Responsável</strong>: ${processo.name || ''}</h6>
                                        <h6><strong>Parte Adversa</strong>: ${processo.nm_autor_pro || ''}</h6>
                                        <h6><strong>Réu</strong>: ${processo.nm_reu_pro || ''}</h6>
                                        <h6><strong>Área do Direito</strong>: ${processo.dc_area_direito_ado || ''}</h6>
                                    </div>
                                    <div class="col-md-4 box-content">
                                        <h6><strong>Comarca</strong>: ${processo.nm_cidade_cde || ''}/${processo.sg_estado_est || ''}</h6>
                                        <h6><strong>Serviço</strong>: ${processo.nm_tipo_servico_tse || ''}</h6>
                                        <h6><strong>Código do Cliente</strong>: ${processo.nu_acompanhamento_pro || ''}</h6>
                                        <h6><strong>Cliente</strong>: ${processo.nm_razao_social_cli || ''}</h6>
                                        <h6><strong>Foro</strong>: ${processo.nm_vara_var || ''}</h6>
                                        <h6><strong>Tipo de Processo</strong>: ${processo.nm_tipo_processo_tpo || ''}</h6>
                                    </div>
                                    <div class="col-md-4 box-content">
                                        <h6>
                                            <strong>Dados Audiencistas</strong>
                                            <a class="dados-audiencistas" data-id="${processo.cd_processo_pro}"><i class="fa fa-edit"></i>Editar</a>
                                            <p style="color: #3F51B5; margin-bottom: 0px;"><strong>Advogado</strong></p>
                                            <div style="margin-bottom: 5px;">
                                                ${processo.nm_advogado_pro || '<span class="text-danger">Não informado</span>'}
                                            </div>
                                            <p style="color: #3F51B5; margin-bottom: 0px;"><strong>Preposto</strong></p>
                                            <div>
                                                ${processo.nm_preposto_pro || '<span class="text-danger">Não informado</span>'}
                                            </div
                                        </h6>
                                    </div>
                                    <div class="col-md-12 col-lg-12">
                                        
                                        <div class="onoffswitch-container" style="margin-left: 0px; font-size: 11px;">
                                            <span class="onoffswitch-title">Documento de Representação Protocolado?</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" 
                                                        ${processo.fl_documento_representacao_pro === 'S' ? 'checked' : ''}
                                                        name="fl_documento_representacao_pro" 
                                                        class="onoffswitch-checkbox" 
                                                        id="fl_documento_representacao_pro_${processo.cd_processo_pro}"
                                                        data-processo="${processo.cd_processo_pro}">
                                                <label class="onoffswitch-label" for="fl_documento_representacao_pro_${processo.cd_processo_pro}"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>

                                        <div class="onoffswitch-container" style="margin-left: 8px; font-size: 11px;">
                                            <span class="onoffswitch-title">Audiência Confirmada</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" 
                                                       ${flagAtivo(processo.fl_audiencia_confirmada_pro) ? 'checked' : ''}
                                                       name="fl_audiencia_confirmada_pro" 
                                                       class="onoffswitch-checkbox" 
                                                       id="fl_audiencia_confirmada_pro_${processo.cd_processo_pro}"
                                                       data-processo="${processo.cd_processo_pro}">
                                                <label class="onoffswitch-label" for="fl_audiencia_confirmada_pro_${processo.cd_processo_pro}"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>

                                        <div class="onoffswitch-container" style="margin-left: 8px; font-size: 11px;">
                                            <span class="onoffswitch-title">Check-in Escritório</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" 
                                                       ${flagAtivo(processo.fl_checkin_escritorio_pro) ? 'checked' : ''}
                                                       name="fl_checkin_escritorio_pro" 
                                                       class="onoffswitch-checkbox" 
                                                       id="fl_checkin_escritorio_pro_${processo.cd_processo_pro}"
                                                       data-processo="${processo.cd_processo_pro}">
                                                <label class="onoffswitch-label" for="fl_checkin_escritorio_pro_${processo.cd_processo_pro}"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>

                                        <div class="onoffswitch-container" style="margin-left: 8px; font-size: 11px;">
                                            <span class="onoffswitch-title">Check-in Correspondente</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" 
                                                       ${flagAtivo(processo.fl_checkin_pro) ? 'checked' : ''}
                                                       name="fl_checkin_pro" 
                                                       class="onoffswitch-checkbox" 
                                                       id="fl_checkin_pro_${processo.cd_processo_pro}"
                                                       data-processo="${processo.cd_processo_pro}">
                                                <label class="onoffswitch-label" for="fl_checkin_pro_${processo.cd_processo_pro}"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>
                                    </div>
                                </div>
                                ${parseInt(processo.nu_total_arquivos_pro) > 0 ? `<span style="position:absolute; bottom:10px; right:14px; font-size:11px; color:#666; font-weight: bold;"><i class="fa fa-paperclip"></i> ${processo.nu_total_arquivos_pro} arquivo(s) - ${parseFloat(processo.nu_tamanho_anexos_pro).toFixed(2)} MB</span>` : ''}
                            </div>`;

                            container.append(html);
                        });

                }
            });
        }

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $("#correspondente_auto_complete_pauta").autocomplete({
            source: pathCorrespondente,
            minLength: 3,
            search: function(event, ui) {
                $("input[name='cdCorrespondente']").val('');
            },
            select: function(event, ui) {
                $("input[name='cdCorrespondente']").val(ui.item.id);
            },
            appendTo: "#modal_pauta",
        });

        $("#correspondente_auto_complete_pauta").focusout(function() {
            if ($("input[name='cdCorrespondente']").val() == '') {
                $("#correspondente_auto_complete_pauta").val('');
                $('.ui-autocomplete').attr('style', 'z-index: 905 !important');
            }
        });

    });
</script>
@endsection