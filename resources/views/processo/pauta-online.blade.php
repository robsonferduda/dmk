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
                                <button class="btn btn-primary btn-pesquisar" style="width: 10%; margin-top: 22px;"  type="button"><i class="fa fa-search"></i> Pesquisar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12" id="box-processos-container">
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        carregaProcessos();

        $(".btn-pesquisar").click(function(){
            carregaProcessos();
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
                        container.empty(); // limpa os dados atuais

                        if (response.length === 0) {
                            container.append('<h5 class="center">Nenhum dado para ser exibido</h5>');
                            return;
                        }

                        response.forEach(function(processo) {
                            let html = `
                            <div class="well box-acompanhamento" style="padding: 10px 15px; border: none; border-radius: 10px; background: white; display: block;">
                                <div class="row box-processo">
                                    <div class="col-md-4 box-content">
                                        <h4 style="margin: 0px; font-size: 13px;">NÚMERO ${processo.nu_processo_pro || ''}</h4>
                                        <h6><strong>Prazo Fatal </strong>: ${processo.dt_prazo_fatal_pro || ''} ${processo.hr_audiencia_pro || ''}</h6>
                                        <h6><strong>Correspondente</strong>: ${processo.nm_correspondente || ''}</h6>
                                        <h6><strong>Responsável</strong>: ${processo.nm_responsavel || ''}</h6>
                                        <h6><strong>Parte Adversa</strong>: ${processo.nm_autor_pro || ''}</h6>
                                        <h6><strong>Réu</strong>: ${processo.nm_reu_pro || ''}</h6>
                                    </div>
                                    <div class="col-md-4 box-content">
                                        <h6><strong>Comarca</strong>: ${processo.nm_comarca || ''}</h6>
                                        <h6><strong>Serviço</strong>: ${processo.nm_servico || ''}</h6>
                                        <h6><strong>Cliente</strong>: ${processo.nm_cliente || ''}</h6>
                                        <h6><strong>Foro</strong>: ${processo.nm_foro || ''}</h6>
                                        <h6><strong>Tipo de Processo</strong>: ${processo.nm_tipo_processo || ''}</h6>
                                    </div>
                                    <div class="col-md-4 box-content">
                                        <h6>
                                            <strong>Dados Audiencistas</strong>
                                            <a><i class="fa fa-edit"></i>Editar</a>
                                            <p><strong>Advogado</strong></p>
                                            ${processo.nm_advogado_pro || '<span class="text-danger">Não informado</span>'}
                                            <p><strong>Preposto</strong></p>
                                            ${processo.nm_preposto_pro || '<span class="text-danger">Não informado</span>'}
                                        </h6>
                                    </div>
                                    <div class="col-md-12 col-lg-12 box-content">
                                        <div class="onoffswitch-container" style="margin-left: 0px; font-size: 11px;">
                                            <span class="onoffswitch-title">Documento de Representação Protocolado?</span> 
                                            <span class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" ${processo.fl_documento_representacao_pro ? 'checked' : ''}>
                                                <label class="onoffswitch-label">
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