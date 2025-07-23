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
                                <label class="label-padrao">Código Cliente</label>
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
                            <section class="col col-md-12" style="margin-top: 8px;">                                       
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
                <div class="col-md-12">
                    <h5 style="font-size: 12px;"><strong>Total de Processos</strong>: <span id="total-processos">0</span></h5>
                </div>
                <div class="col-md-12" id="box-processos-container">
                    
                </div>
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

        function carregaProcessos()
        {

            data = $("#dt_prazo_fatal_pro").val();
            processo = $("#nu_processo_pro").val();
            numero_acompanhamento = $("#nu_acompanhamento_pro").val();
            nm_cliente = $("#nm_cliente").val();
            nm_correspondente = $("#nm_correspondente").val();
            responsavel = $("#cd_responsavel_pro").val();            

            $.ajax({
                
                url: host+'/api/processo/pauta',
                type: 'POST',
                data: {"data": data, 
                       "processo": processo, 
                       "numero_acompanhamento": numero_acompanhamento,
                       "nm_cliente": nm_cliente, 
                       "nm_correspondente": nm_correspondente,                         
                       "responsavel": responsavel
                },
                dataType: "JSON",
                beforeSend: function(){
                    //$(".box-acompanhamento").empty();            
                },
                success: function(response){

                    let container = $("#box-processos-container");
                    let totalElement = $("#total-processos");

                    container.empty(); // limpa os dados atuais
                    totalElement.text(response.length); // atualiza o total

                    if (response.length === 0) {
                        container.append('<h5 class="center">Nenhum dado para ser exibido</h5>');
                        return;
                    }

                        response.forEach(function(processo) {

                            let html = `
                            <div class="well box-acompanhamento" style="padding: 10px 15px; border: none; border-radius: 10px; background: white; display: block;">
                                <div class="row box-processo">
                                    <div class="col-lg-12 box-content">
                                        <h6 style="margin: 0px; font-size: 13px;">
                                            <strong>
                                                <span style="background-color: ${ processo.background }" class="label label-default pull-right">${ processo.nm_status_processo_conta_stp }</span>
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
                                        <h4 style="margin: 0px; font-size: 13px;">NÚMERO ${processo.nu_processo_pro || ''}</h4>
                                        <h6><strong>Prazo Fatal </strong>: ${processo.dt_prazo_fatal_pro || ''} ${processo.hr_audiencia_pro || ''}</h6>
                                        <h6><strong>Correspondente</strong>: ${processo.nm_conta_correspondente_ccr || ''}</h6>
                                        <h6><strong>Responsável</strong>: ${processo.name || ''}</h6>
                                        <h6><strong>Parte Adversa</strong>: ${processo.nm_autor_pro || ''}</h6>
                                        <h6><strong>Réu</strong>: ${processo.nm_reu_pro || ''}</h6>
                                    </div>
                                    <div class="col-md-4 box-content">
                                        <h6><strong>Comarca</strong>: ${processo.nm_cidade_cde || ''}/${processo.sg_estado_est || ''}</h6>
                                        <h6><strong>Serviço</strong>: ${processo.nm_tipo_servico_tse || ''}</h6>
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
                                                <input type="checkbox" name="fl_documento_representacao_pro" class="onoffswitch-checkbox" id="fl_documento_representacao_pro">
                                                <label class="onoffswitch-label" for="fl_documento_representacao_pro"> 
                                                    <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                                    <span class="onoffswitch-switch"></span>
                                                </label> 
                                            </span> 
                                        </div>
                                    </div>

                                </div>
                            </div>`;

                            container.append(html);
                        });

                }
            });
        }

    });
</script>
@endsection