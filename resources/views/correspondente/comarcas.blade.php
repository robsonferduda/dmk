@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Comarcas de Atuação</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-map-marker"></i> Correspondentes <span> > Comarcas de Atuação</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i></span><h2>Cadastro de Correspondente </h2>             
                </header>
                <div role="content">
                    <div class="widget-body no-padding">
                        @if(Auth::user()->cd_nivel_niv == 3)
                        {!! Form::open(['id' => 'frm-edit-usuario', 'url' => ['correspondente/editar',$correspondente->cd_entidade_ete], 'class' => 'smart-form','method' => 'PUT']) !!}
                            <input type="hidden" name="conta" id="conta" value="{{ $correspondente->cd_conta_con }}">

                        @else
                            {!! Form::open(['id' => 'frm-edit-usuario', 'url' => ['correspondente/editar',$correspondente->correspondente->cd_entidade_ete], 'class' => 'smart-form','method' => 'PUT']) !!}
                            <input type="hidden" name="conta" id="conta" value="{{ $correspondente->correspondente->cd_conta_con }}">
                        @endif

                            <input type="hidden" name="entidade" id="entidade" value="{{ $correspondente->entidade->cd_entidade_ete }}">
                            <input type="hidden" name="telefones" id="telefones">
                            <input type="hidden" name="emails" id="emails">
                            <input type="hidden" name="registrosBancarios" id="registrosBancarios">
                            

                            <header>
                                <i class="fa fa-map-marker"></i> Comarca de Origem
                                <a href="#" rel="popover-hover" data-placement="top" data-original-title="Comarca de Origem" data-content="Informe a comarca de origem do correspondente. Caso deseje alterar o valor informado, clique sobre ela para excluir e adicione novamente.">
                                <i class="fa fa-question-circle text-primary"></i>
                                </a> 
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select  id="pai_cidade_origem" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (old('cd_cidade_cde') ? old('cd_cidade_cde') : $correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label label-pai_cidade_origem">Cidade</label>          
                                        <select id="cidade_origem" disabled name="cd_cidade_cde" class="select2 pai_cidade_origem">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button data-atuacao="S" type="button" class="btn btn-success adicionar-origem" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</a>
                                    </section>
                                </div> 
                            </fieldset>
                            <div class="row"> 
                                <div class="box_btn_origem" style="margin: 5px 30px;">
                                    @if(count($correspondente->entidade->origem()->get()) > 0)
                                        @foreach($correspondente->entidade->origem()->get() as $atuacao) 
                                            <button type="button" class="btn btn-warning btn-atuacao" style="padding: 3px 8px;" data-id="{{ $atuacao->cd_cidade_atuacao_cat }}">{{ $atuacao->cidade()->first()->nm_cidade_cde }} <i class="fa fa-times"></i></button>
                                        @endforeach
                                    @else
                                        <span class="text-warning erro-origem-vazia"><i class="fa fa-warning"></i> Comarca de origem não informada</span>
                                    @endif
                                </div>
                            </div>
                            <hr/>

                            <header>
                                <i class="fa fa-check"></i> Comarcas de Atuação 
                                <a href="#" rel="popover-hover" data-placement="top" data-original-title="Comarcas de Atuação" data-html="true" data-content="Para informar somente uma cidade, selecione o estado e em seguida a cidade desejada. Para inserir todas as cidades de um estado, selecione o estado e na opção Cidade selecione a opção: <strong>Todas as cidades <strong>">
                                <i class="fa fa-question-circle text-primary"></i>
                                </a> 
                            </header>
                            <fieldset>
                                <div class="row">                     
                                    <section class="col col-4">
                                        <label class="label" >Estado</label>          
                                        <select id="pai_cidade_atuacao" name="cd_estado_est" class="select2 estado">
                                            <option selected value="">Selecione</option>
                                                @foreach(\App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                    <option value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
                                        </select> 
                                    </section>
                                    <section class="col col-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (old('cd_cidade_cde') ? old('cd_cidade_cde') : $correspondente->entidade->endereco) ? $correspondente->entidade->endereco->cd_cidade_cde : '' }}">
                                        <label class="label label-pai_cidade_atuacao">Cidade</label>          
                                        <select id="cidade_atuacao" disabled name="cd_cidade_cde" class="select2 pai_cidade_atuacao">
                                            <option selected value="">Selecione a cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-2">
                                        <label class="label" style="color: white;">Adicionar</label>
                                        <button data-atuacao="N" type="button" class="btn btn-success adicionar-atuacao" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</a>
                                    </section>
                                </div> 
                            </fieldset>
                        {!! Form::close() !!}                      
                    </div>
                </div>                
            </div>

            <div id="box-msg" class="col col-12 none">
                <div class="alert alert-info fade in">
                    <i class="fa fa-gear fa-spin"></i> <span>Aguarde, buscando comarcas de atuação...</span>
                </div>
            </div>

            <div id="box-error" class="col col-12 none">
                <div class="alert alert-danger fade in">
                    <i class="fa fa-times"></i> <span></span>
                </div>
            </div>

            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-map-marker"></i> </span>
                    <h2>Comarcas de Atuação</h2>
                </header>
                <div class="row">
                    <div>
                        <div class="widget-body no-padding">
                            <table id="" class="table table-striped table-bordered table-hover table-comarcas" width="100%">
                                <thead>                         
                                    <tr>   
                                        <th style="">Comarca</th>                                  
                                        <th style="width:100px;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                                                                             
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modal_erro_atuacao" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> Erro de Processamento<strong></strong></h4>
            </div>
                <div class="modal-body" style="text-align: center;">
                        <h4 class="text-danger"><i class="fa fa-times"></i> Ops...</h4>
                        <h4>Ocorreu um erro ao processar sua operação.</h4>
                        <h4 class="msg_erro_adicao"></h4>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        var entidade = $("#entidade").val();
        loadAtuacao(entidade);

        $('.adicionar-atuacao').click(function(){

            var entidade = $("#entidade").val();
            var estado = $("#pai_cidade_atuacao").val();
            var cidade = $("#cidade_atuacao").val();
            var atuacao = $(this).data("atuacao");
            $("#box-error").css("display","none");

            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/atuacao/adicionar",
                dataType: "json",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "entidade": entidade,
                    "cidade": cidade,
                    "estado": estado,
                    "atuacao": atuacao
                },
                beforeSend: function()
                {
                    $("#processamento").modal('show');
                },
                success: function(response)
                {
                    $(".box_btn_atuacao button").remove();
                    $(".erro-atuacao-vazia").remove();
                    loadAtuacao(entidade);
                    loadOrigem(entidade);    
                    $("#processamento").modal('hide');                
                },
                error: function(response)
                {
                    $("#box-error").css("display","block");
                    $("#box-error span").html(response.responseJSON.msg);
                    $("#processamento").modal('hide');
                }
            });


        });

        $('.adicionar-origem').click(function(){

            var entidade = $("#entidade").val();
            var cidade = $("#cidade_origem").val();
            var atuacao = $(this).data("atuacao");
            $("#box-error").css("display","none");

            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/atuacao/adicionar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "entidade": entidade,
                    "cidade": cidade,
                    "atuacao": atuacao
                },
                beforeSend: function()
                {
                    $("#processamento").modal('show');
                },
                success: function(response)
                {
                    $(".box_btn_atuacao button").remove();
                    $(".erro-atuacao-vazia").remove();
                    loadOrigem(entidade);
                    loadAtuacao(entidade);
                    $("#processamento").modal('hide');
                    
                },
                error: function(response)
                {
                    console.log(response.responseJSON.msg);
                    $("#processamento").modal('hide');
                    $("#box-error").css("display","block");
                    $("#box-error span").html(response.responseJSON.msg);
                }
            });


        });

        function loadAtuacao(entidade){

            $.ajax({

                url: "../../correspondente/atuacao/"+entidade,
                type: 'GET',
                dataType: "JSON",
                beforeSend: function(){
                    $("#box-msg").css("display","block");
                },
                success: function(response)
                {             
                    $('.table-comarcas').dataTable().fnDestroy();
                    $('.table-comarcas > tbody > tr').remove(); 
                    
                    if(response.length){

                        $("#box-msg span").html("Foram encontradas "+response.length+" comarcas. Aguarde o carregamento dos dados na tabela");

                        $.each(response, function(index, value){
                            $('.table-comarcas > tbody').append('<tr><td>'+value.cidade.nm_cidade_cde+'</td><td class="center"><button type="button" class="btn btn-danger btn-atuacao" style="padding: 3px 8px;" data-id="'+value.cd_cidade_atuacao_cat+'"><i class="fa fa-times"></i> Excluir</button></td></tr>');
                        });

                    }
                    
                    $('.btn-atuacao').on('click', function(){

                        atuacao = $(this).data("id");
                        entidade = $("#entidade").val();
                        $("#box-error").css("display","none");

                        $.ajax({
                                url: '../../correspondente/atuacao/excluir/'+atuacao,
                                type: 'GET',
                                dataType: "JSON",
                            success: function(response)
                            {                
                                $(".box_btn_atuacao button").remove();       
                                loadAtuacao(entidade);
                                loadOrigem(entidade);
                            },
                            error: function(response)
                            {
                                $("#box-error").css("display","block");
                                $("#box-error span").html(response.responseJSON.message);
                            }
                        });

                    }); 
                    
                    $('.table-comarcas').dataTable({"sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                        "t"+
                        "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
                    "autoWidth" : true,
                    "ordering": true,
                    "oLanguage": {
                        "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-filter"></i></span>',
                        "sEmptyTable": "Nenhum registro encontrado",
                        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sInfoThousands": ".",
                        "sLengthMenu": "_MENU_ resultados por página",
                        "sLoadingRecords": "Carregando...",
                        "sProcessing": "Processando...",
                        "sZeroRecords": "Nenhum registro encontrado",
                        
                        "oPaginate": {
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                            "sFirst": "Primeiro",
                            "sLast": "Último"
                        },
                        "oAria": {
                            "sSortAscending": ": Ordenar colunas de forma ascendente",
                            "sSortDescending": ": Ordenar colunas de forma descendente"
                        },
                    }});

                    $("#box-msg").css("display","none");
                },
                error: function(response)
                {

                }
            });
        }

        function loadOrigem(entidade){

            $.ajax({

                url: "../../correspondente/origem/"+entidade,
                type: 'GET',
                dataType: "JSON",

                success: function(response)
                {                   
                    $(".box_btn_origem button").remove();
                    $('.erro-origem-vazia').html("");

                    $.each(response, function(index, value){
                        $('.box_btn_origem').append('<button type="button" class="btn btn-warning btn-atuacao" style="padding: 3px 8px;" data-id="'+value.cd_cidade_atuacao_cat+'">'+value.cidade.nm_cidade_cde+' <i class="fa fa-times"></i> </button>');
                    });

                    $('.btn-atuacao').on('click', function(){

                        atuacao = $(this).data("id");
                        entidade = $("#entidade").val();

                        $.ajax({
                                url: '../../correspondente/atuacao/excluir/'+atuacao,
                                type: 'GET',
                                dataType: "JSON",
                            success: function(response)
                            {                
                                $(".box_btn_atuacao button").remove();       
                                loadAtuacao(entidade);
                                loadOrigem(entidade);
                            },
                            error: function(response)
                            {
                                $("#box-error").css("display","block");
                                $("#box-error span").html(response.responseJSON.message);
                            }
                        });

                    });   
                },
                error: function(response)
                {

                }
            });
        }

        var buscaCidade = function(estado,target){

            if(estado != ''){

                //Limpar mensagens de erro
                $('.label-'+target).html("Cidade");

                $.ajax(
                    {
                        url: '../../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Aguarde, carregando cidades...</option>');
                            $('.'+target).trigger('change'); 
                            $('.'+target).prop( "disabled", true); 
                        },
                        success: function(response)
                        {                    
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Selecione a cidade</option>');
                            $('.'+target).append('<option value="0">Todas as cidades</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('.'+target).append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('.'+target).append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('.'+target).trigger('change');     
                            $('.'+target).prop( "disabled", false );        
                        },
                        error: function(response)
                        {   
                            $('.label-'+target).append('<span class="text-danger"> Erro ao carregar lista de cidades. Atualize a página e tente novamente <span>');
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Selecione a cidade</option>');
                            $('.'+target).trigger('change'); 
                        }
                    });
            }
        }

        $(".estado").change(function(){
            buscaCidade($(this).val(),$(this).attr('id')); 
        });

        $(".estado").trigger('change');

    });
</script>
@endsection