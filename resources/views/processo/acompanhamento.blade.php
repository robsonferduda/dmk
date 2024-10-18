@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Acompanhamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Acompanhamento</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 box-button-xs" >
            <div class="sub-box-button-xs">
                <a  title="Novo" data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
                <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
            </div>
           
        </div>
    </div>
    <div class="row container-acompanhamento">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('processos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <input type="hidden" name="acompanhamento" value="S">
                    <div class="row" style="margin-bottom: 10px;">
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Prazo Fatal</label><br />
                            <input style="width: 100%" class="form-control date-mask" type="text" id="dt_prazo_fatal_pro" id="dt_prazo_fatal_pro" placeholder="___/___/____" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Número do Processo</label><br />
                            <input style="width: 100%" class="form-control" type="text" id="nu_processo_pro" placeholder="Nº Processo" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Réu</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="reu" class="form-control" id="reu" placeholder="Réu" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section> 
                        <section class="col col-md-4 col-lg-3">
                            <label class="label label-black">Autor</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="autor" class="form-control" id="autor" placeholder="Autor" value="{{ !empty($autor) ? $autor : '' }}" >                            
                        </section>

                        <section class="col col-md-4 col-lg-3 box-select2"> 
                            <select name="cd_tipo_processo_tpo" id="cd_tipo_processo_tpo" class="select2">
                                <option value="">Tipos de Processo</option>
                                @foreach($tiposProcesso as $tipo)
                                    <option {{ (!empty($tipoProcesso) && $tipoProcesso == $tipo->cd_tipo_processo_tpo) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                @endforeach
                            </select>
                        </section> 
                        <section class="col col-md-4 col-lg-3 box-select2"> 
                            <select name="cd_tipo_servico_tse" id="cd_tipo_servico_tse" class="select2">
                                <option value="">Tipos de Serviço</option>
                                @foreach($tiposServico as $tipo)
                                    <option {{ (!empty($tipoServico) && $tipoServico == $tipo->cd_tipo_servico_tse) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</option>
                                @endforeach
                            </select>
                        </section> 
                        <section class="col col-md-4 col-lg-3 box-select2">       
                            <select id="cd_responsavel_pro" name="cd_responsavel_pro" class="select2">
                                <option selected value="">Responsável</option>
                                @foreach($responsaveis as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </select> 
                        </section> 

                        <section class="col col-md-4 col-lg-3 box-select2"> 
                            <select name="status" id="status" class="select2">
                                <option value="">Status do Acompanhamento</option>
                                <option value="dentro-prazo">Dentro do Prazo</option>
                                <option value="data-limite">Data Limite</option>
                                <option value="atrasado">Atrasado</option>
                            </select>
                        </section> 
                        <section class="col col-md-4 col-lg-3 box-select2">         
                            <select  id="estado" name="cd_estado_est" class="select2">
                                <option selected value="">Estado</option>
                                    @foreach(App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                    @endforeach
                            </select> 
                        </section>
                        <section class="col col-md-4 col-lg-3 box-select2">         
                            <select  id="cidade"  name="cd_cidade_cde" class="select2" required>
                                <option selected value="">Comarca</option>
                            </select> 
                        </section>  
                        <section class="col col-md-4 col-lg-3 box-select2">       
                            <select id="cd_status_processo_stp" name="cd_status_processo_stp" class="select2">
                                <option selected value="">Status do Processo</option>
                                @foreach($status as $st)
                                    <option value="{{ $st->cd_status_processo_stp }}">{{ $st->nm_status_processo_conta_stp }}</option>
                                @endforeach
                            </select> 
                        </section> 
                    </div><hr>
                    <div class="row center">
                        <button class="btn btn-primary btn-sm" type="button" id="btnBuscarProcessosAndamento"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: -15px;">
            <div class="col-sm-12 col-md-3" style="padding: 5px 0px;">
                <div class="input-group input-group-md">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-filter"></i></span>
                    <div>
                        <input type="text" class="form-control" id="filter" name="filter" class="filter"/>                                          
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-3" style="padding: 5px 8px; margin-top: 12px;">
                <h4 style="font-size: 13px;" id="label-total-processos"></h4>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6" style=" margin-top: 12px;">
                <section class="pull-right">
                    <select id="filtro-pagination">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>  <label class="hidden-xs">Registros por página</label>
                </section>
            </div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 body-acompanhamento">

        </article>
    </div>
</div>
<div id="dialog_clone_text" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
    <p>
        Ao clicar em "Continuar" uma cópia do processo será realizada.
    </p>
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
                                            <input type="hidden" id="contatoAux" value="">
                                            <select id="cd_contato_cot" name="cd_contato_cot">
                                                <option value="">Selecione o tipo de intervalo</option>    
                                                <option value="">Data de Solicitação</option> 
                                                <option value="">Prazo Fatal</option>         
                                            </select><i></i>  
                                        </label>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-4">
                                        <label>Data prazo fatal início</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_inicio" id="dt_inicio" placeholder="___/___/____" class="mascara_data">
                                        </label>
                                    </section>
                                    <section class="col col-4">
                                        <label>Data prazo fatal fim</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_fim" id="dt_fim" placeholder="___/___/____" class="mascara_data" >
                                        </label>
                                    </section>
                               
                                    <section class="col col-2">
                                        <br />              
                                        <input type="radio"  required name="tipo" id="tipo" value="excel" >  
                                        <label>Excel</label> 
                                    </section> 
                                     <section class="col col-2">
                                        <br />              
                                        <input type="radio" required name="tipo" id="tipo" value="pdf" >  
                                        <label>PDF</label> 
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
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');

        function formataNulo(valor){

            return valor != null ? valor : "";
        }

        function formataNuloResposta(valor){

            return valor != null ? valor : "Não informado";
        }

        if (!RegExp.escape) {
            RegExp.escape = function (value) {
                return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&")
            };
        }

        var $medias = $('.box-acompanhamento'), $h4s = $medias.find('> .box-processo > .box-content > h6');

        $('#filter').keyup(function () {
            var filter = this.value,
                regex;

            if (filter && $medias) {

                regex = new RegExp(RegExp.escape(this.value), 'i')

                var $found = $h4s.filter(function () {
                    return regex.test($(this).text())
                }).closest('.box-acompanhamento').show();

                $medias.not($found).hide()
            } else {
                $medias.show();
            }
        });

        $.ajax({
                
                url: host+'/api/processo/andamento',
                type: 'GET',
                contentType: 'application/json',
                dataType: "JSON",
                beforeSend: function(){
                    $("#label-total-processos").html("");
                    $('.container-acompanhamento').loader('show'); 
                    $('.pagination').empty();                      
                },
                success: function(response){ 

                    $("#label-total-processos").html("<strong>TOTAL DE PROCESSOS</strong>: "+response.length);   

                    $.each(response,function(index,data){

                        $(".body-acompanhamento")
                        .append('<div class="well box-acompanhamento" style="padding: 10px 15px; border: none; background: '+data.background+';">'+
                            
                            '<div class="row box-processo">'+
                                '<div class="hidden-xs hidden-sm hidden-md col-lg-12 box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+' <strong>'+data.nm_status_processo_conta_stp+'</strong> <strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+

                                '<div class="col-xs-12 col-sm-12 col-md-12 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+'</h6></div>'+
                                '<div class="col-xs-12 col-sm-8 col-md-8 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong>'+data.nm_status_processo_conta_stp+'</strong></h6></div>'+
                                '<div class="col-xs-12  col-sm-4 col-md-4 hidden-lg box-content"><h6 style="margin: 0px; font-size: 13px;"><strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h6></div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Prazo Fatal</strong>: '+data.dt_prazo_fatal_pro+' '+formataNulo(data.hr_audiencia_pro)+'</h6>'+
                                    '<h6><strong>Código Cliente</strong>: '+formataNuloResposta(data.nu_acompanhamento_pro)+'</h6>'+ 
                                 
                                    '<h6><strong>Tipo de Serviço</strong>: '+data.nm_tipo_servico_tse+'</h6>'+                   
                                    '<h6><strong>Vara/Cidade</strong>: '+formataNuloResposta(data.nm_vara_var)+'/'+data.nm_cidade_cde+'-'+data.sg_estado_est+'</h6>'+    
                                    '<h6><strong>Responsável</strong>: '+formataNuloResposta(data.name)+'</h6>'+                                             
                                '</div>'+
                                '<div class="col-md-6 box-content">'+
                                    '<h6><strong>Cliente</strong>: '+formataNuloResposta(data.nm_razao_social_cli)+'  </h6>'+
                                    '<h6><strong>Correspondente</strong>: '+formataNuloResposta(data.nm_conta_correspondente_ccr)+'</h6>'+
                                    '<h6><strong>Autor</strong>: '+formataNuloResposta(data.nm_autor_pro)+'</h6>'+
                                    '<h6><strong>Réu</strong>: '+formataNuloResposta(data.nm_reu_pro)+'</h6>'+ 
                                    
                                '</div>'+
                                '<div class="hidden-xs col-sm-12 col-md-12 pull-right">'+
                                    '<a title="Despesas" class="icone-acompanhamento" href="../processos/despesas/'+data.hash+'"><i class="fa fa-money"></i> Despesas</a> '+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> Acompanhamento</a> '+
                                    '<a title="Clonar" class="dialog_clone icone-acompanhamento" href="../processos/clonar/'+data.hash+'"><i class="fa fa-clone"></i> Clonar</a> '+
                                    '<a title="Relatório" class="icone-acompanhamento" href="../processos/relatorio/'+data.hash+'"><i class="fa fa-usd"></i> Relatório Financeiro</a> '+
                                    '<a title="Editar" class="icone-acompanhamento" class="editar_vara" href="../processos/editar/'+data.hash+'"><i class="fa fa-edit"></i> Editar </a> '+
                                    '<a title="Excluir" data-id="'+data.cd_processo_pro+'" data-url="../processos/" class="excluir_registro icone-acompanhamento" href="#"><i class="fa fa-trash"></i> Excluir</a>'+
                                '</div>'+
                                '<div class="hidden-md hidden-sm hidden-lg col-md-6">'+
                                    '<a title="Despesas" class="icone-acompanhamento" href=""><i class="fa fa-money"></i> </a>'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> </a>'+
                                    '<a title="Clonar" class="icone-acompanhamento" class="dialog_clone" href=""><i class="fa fa-clone"></i> </a>'+
                                    '<a title="Relatório" class="icone-acompanhamento" href=""><i class="fa fa-usd"></i> </a>'+
                                    '<a title="Editar" class="icone-acompanhamento" class="editar_vara" href=""><i class="fa fa-edit"></i> </a>'+
                                    '<a title="Excluir" data-id="'+data.cd_processo_pro+'" data-url="../processos/" class="excluir_registro icone-acompanhamento" href="#"><i class="fa fa-trash"></i> </a>'+
                                '</div>'+
                            '</div>'+
                        '</div>');

                    });

                    $('.container-acompanhamento').loader('hide'); 
                    $(".body-acompanhamento").pagify(10, ".box-acompanhamento");
                    $medias = $('.box-acompanhamento');
                    $h4s = $medias.find('> .box-processo > .box-content > h6');
                                                      
                },
                error: function(response)
                {
                    alert("Erro ao processar requisição");
                    $('.container-acompanhamento').loader('hide'); 
                }
        });

        $("#btnBuscarProcessosAndamento").click(function(){

            processo = $("#nu_processo_pro").val();
            responsavel = $("#cd_responsavel_pro").val();
            tipo = $("#cd_tipo_processo_tpo").val();
            servico = $("#cd_tipo_servico_tse").val();
            status = $("#status").val();
            reu = $("#reu").val();
            autor = $("#autor").val();
            data = $("#dt_prazo_fatal_pro").val();
            comarca = $("#cidade").val();
            statusProcesso = $("#cd_status_processo_stp").val();


            $.ajax({
                
                url: host+'/processos/buscar/andamento',
                type: 'POST',
                data: {"processo": processo, "responsavel": responsavel, "tipo": tipo, "servico": servico, "status": status, "reu": reu, "autor": autor, "data": data, "comarca": comarca, "statusProcesso": statusProcesso ,"flag": false },
                dataType: "JSON",
                beforeSend: function(){
                    $("#label-total-processos").html("");
                    $('.container-acompanhamento').loader('show');  
                    $('.pagination').empty();                     
                },
                success: function(response){ 

                    $(".body-acompanhamento").empty();
                    $("#label-total-processos").html("<strong>TOTAL DE PROCESSOS</strong>: "+response.length);    

                    $.each(response,function(index,data){

                        $(".body-acompanhamento")
                        .append('<div class="well box-acompanhamento" style="padding: 10px 15px; border: none; background: '+data.background+';">'+
                            '<h4 style="margin: 0px; font-size: 13px;">NÚMERO '+data.nu_processo_pro+' <strong>'+data.nm_status_processo_conta_stp+'</strong> <strong class="pull-right" style="color: '+data.fonte+'">'+data.situacao+'</strong></h4>'+
                            '<div class="row box-processo">'+
                                '<div class="col-md-6">'+
                                    '<h6><strong>Prazo Fatal</strong>: '+data.dt_prazo_fatal_pro+' '+formataNulo(data.hr_audiencia_pro)+'</h6>'+
                                    '<h6><strong>Status</strong>: '+data.nm_status_processo_conta_stp+'</h6>'+
                                    '<h6><strong>Tipo de Serviço</strong>: '+data.nm_tipo_servico_tse+'</h6>'+                   
                                    '<h6><strong>Vara/Cidade</strong>: '+formataNuloResposta(data.nm_vara_var)+'/'+data.nm_cidade_cde+'-'+data.sg_estado_est+'</h6>'+    
                                    '<h6><strong>Responsável</strong>: '+formataNuloResposta(data.name)+'</h6>'+                                             
                                '</div>'+
                                '<div class="col-md-6">'+
                                    '<h6><strong>Cliente</strong>: '+formataNuloResposta(data.nm_razao_social_cli)+'  </h6>'+
                                    '<h6><strong>Correspondente</strong>: '+formataNuloResposta(data.nm_conta_correspondente_ccr)+'</h6>'+
                                    '<h6><strong>Autor</strong>: '+formataNuloResposta(data.nm_autor_pro)+'</h6>'+
                                    '<h6><strong>Réu</strong>: '+formataNuloResposta(data.nm_reu_pro)+'</h6>'+ 
                                '</div>'+
                                '<div class="hidden-xs hidden-sm col-md-12 pull-right">'+
                                    '<a title="Despesas" class="icone-acompanhamento" href="../processos/despesas/'+data.hash+'"><i class="fa fa-money"></i> Despesas</a> '+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> Acompanhamento</a> '+
                                    '<a title="Clonar" class="dialog_clone icone-acompanhamento" href="../processos/clonar/'+data.hash+'"><i class="fa fa-clone"></i> Clonar</a> '+
                                    '<a title="Relatório" class="icone-acompanhamento" href="../processos/relatorio/'+data.hash+'"><i class="fa fa-usd"></i> Relatório Financeiro</a> '+
                                    '<a title="Editar" class="icone-acompanhamento" class="editar_vara" href="../processos/editar/'+data.hash+'"><i class="fa fa-edit"></i> Editar </a> '+
                                    '<a title="Excluir" data-id="'+data.cd_processo_pro+'" data-url="../processos/" class="excluir_registro icone-acompanhamento" href="#"><i class="fa fa-trash"></i> Excluir</a>'+
                                '</div>'+
                                '<div class="hidden-md hidden-lg col-md-6">'+
                                    '<a title="Despesas" class="icone-acompanhamento" href=""><i class="fa fa-money"></i> </a>'+
                                    '<a title="Acompanhamento" class="icone-acompanhamento" href="../processos/acompanhamento/'+data.hash+'"><i class="fa fa-calendar"></i> </a>'+
                                    '<a title="Clonar" class="icone-acompanhamento" class="dialog_clone" href=""><i class="fa fa-clone"></i> </a>'+
                                    '<a title="Relatório" class="icone-acompanhamento" href=""><i class="fa fa-usd"></i> </a>'+
                                    '<a title="Editar" class="icone-acompanhamento" class="editar_vara" href=""><i class="fa fa-edit"></i> </a>'+
                                    '<a title="Excluir" data-id="'+data.cd_processo_pro+'" data-url="../processos/" class="excluir_registro icone-acompanhamento" href="#"><i class="fa fa-trash"></i> </a>'+
                                '</div>'+
                            '</div>'+
                        '</div>');

                    });

                    $('.container-acompanhamento').loader('hide'); 
                    //$(".body-acompanhamento").pagify(10, ".box-acompanhamento");
                                                      
                },
                error: function(response)
                {
                    alert("Erro ao processar requisição");
                    $('.container-acompanhamento').loader('hide'); 
                }
            });

        });    

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete_pauta" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          beforeSend: function(){
           
          },
          search: function(event, ui){
            
            $("input[name='cdCorrespondente']").val('');
          },
          select: function(event, ui) {

            $("input[name='cdCorrespondente']").val(ui.item.id);
            

          },
          open: function(event, ui){
            
          },
          appendTo: "#modal_pauta",
          
        });

        $("#correspondente_auto_complete_pauta" ).focusout(function(){
           if($("input[name='cdCorrespondente']").val() == ''){
                $("#correspondente_auto_complete_pauta").val('');
                $('.ui-autocomplete').attr('style', 'z-index: 905 !important');

           }
        });

        $('select').on('select2:open', function(e){
             $('.custom-dropdown').parent().css('z-index', 99999);
        });

        $('#modal_pauta').on('shown.bs.modal', function () {
          $('#dt_pauta').trigger('focus');
        });

       
    });

    $("#filtro-pagination").change(function(){
        $(".body-acompanhamento").pagify($(this).val(), ".box-acompanhamento");
    });

    var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Carregando...</option>');
                            $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Selecione</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('#cidade').trigger('change');     
                            $('#cidade').prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                });
            }
        }

    $("#estado").change(function(){
        buscaCidade(); 
    });

(function($) {
    var pagify = {
        items: {},
        container: null,
        totalPages: 1,
        perPage: 3,
        currentPage: 0,
        createNavigation: function() {
            this.totalPages = Math.ceil(this.items.length / this.perPage);

            $('.pagination', this.container.parent()).remove();
            var pagination = $('<ul class="pagination"></ul>').append('<li><a class="nav prev disabled" data-next="false"><</a></li>');

            for (var i = 0; i < this.totalPages; i++) {
                var pageElClass = "page";
                if (!i)
                    pageElClass = "page current";
                var pageEl = '<li><a class="' + pageElClass + '" data-page="' + (
                i + 1) + '">' + (
                i + 1) + "</a></li>";
                pagination.append(pageEl);
            }
            pagination.append('<li><a class="nav next" data-next="true">></a></li>');

            this.container.after(pagination);

            var that = this;
            $("body").off("click", ".nav");
            this.navigator = $("body").on("click", ".nav", function() {
                var el = $(this);
                that.navigate(el.data("next"));
            });

            $("body").off("click", ".page");
            this.pageNavigator = $("body").on("click", ".page", function() {
                var el = $(this);
                that.goToPage(el.data("page"));
            });
        },
        navigate: function(next) {
            // default perPage to 5
            if (isNaN(next) || next === undefined) {
                next = true;
            }
            $(".pagination .nav").removeClass("disabled");
            if (next) {
                this.currentPage++;
                if (this.currentPage > (this.totalPages - 1))
                    this.currentPage = (this.totalPages - 1);
                if (this.currentPage == (this.totalPages - 1))
                    $(".pagination .nav.next").addClass("disabled");
                }
            else {
                this.currentPage--;
                if (this.currentPage < 0)
                    this.currentPage = 0;
                if (this.currentPage == 0)
                    $(".pagination .nav.prev").addClass("disabled");
                }

            this.showItems();
        },
        updateNavigation: function() {

            var pages = $(".pagination .page");
            pages.removeClass("current");
            $('.pagination .page[data-page="' + (
            this.currentPage + 1) + '"]').addClass("current");
        },
        goToPage: function(page) {

            this.currentPage = page - 1;

            $(".pagination .nav").removeClass("disabled");
            if (this.currentPage == (this.totalPages - 1))
                $(".pagination .nav.next").addClass("disabled");

            if (this.currentPage == 0)
                $(".pagination .nav.prev").addClass("disabled");
            this.showItems();
        },
        showItems: function() {
            this.items.hide();
            var base = this.perPage * this.currentPage;
            this.items.slice(base, base + this.perPage).show();

            this.updateNavigation();
        },
        init: function(container, items, perPage) {
            this.container = container;
            this.currentPage = 0;
            this.totalPages = 1;
            this.perPage = perPage;
            this.items = items;
            this.createNavigation();
            this.showItems();
        }
    };

    // stuff it all into a jQuery method!
    $.fn.pagify = function(perPage, itemSelector) {
        var el = $(this);
        var items = $(itemSelector, el);

        // default perPage to 5
        if (isNaN(perPage) || perPage === undefined) {
            perPage = 3;
        }

        // don't fire if fewer items than perPage
        if (items.length <= perPage) {
            return true;
        }

        pagify.init(el, items, perPage);
    };
})(jQuery);


</script>
@endsection