@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Honorários por Tipo de Serviço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Honorários por Tipo de Serviço </span> <span> > {{ $cliente->nm_razao_social_cli }} </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('clientes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('cliente/editar/'.$cliente->cd_cliente_cli) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="col-sm-12">
                    @if(isset($msg))
                    <div class="alert alert-info fade in">
                                <button class="close" data-dismiss="alert">
                                    ×
                                </button>
                                <i class="fa-fw fa fa-info"></i>
                                <strong>Informação!</strong> {!! $msg !!}
                            </div>
                    @endif
                    <div class="well">
                        <form action="{{ url('cliente/honorarios/buscar/'.$cliente->cd_cliente_cli) }}" class="smart-form'" method="GET" role="search">
                            <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                            <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                            <input type="hidden" name="opcao_visualizacao" value="grupo">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="row"> 
                                    <section class="col col-md-4">
                                        <label class="label label-black">Cidade</label>
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (Session::get('cidade_busca_cliente')) ? Session::get('cidade_busca_cliente') : old('cd_cidade_cde')}}">       
                                        <select id="cidade_grupo" name="cd_cidade_cde" class="select2">
                                            <option value="">Selecione uma cidade</option>
                                            @foreach($cidades as $cidade)                                                
                                                <option value="{{ $cidade->cd_cidade_cde }}">{{ $cidade->nm_cidade_cde }}</option>                                                
                                            @endforeach
                                        </select> 
                                    </section> 
                                    <section class="col col-md-4">
                                        <label class="label label-black" >Tipos de Serviço</label>     
                                        <select multiple name="servico[]" id="servico_grupo" class="select2">
                                            <option value="">Selecione um servico</option>
                                                @foreach($lista_servicos as $servico)
                                                    <option value="{{ $servico->cd_tipo_servico_tse }}" {{ (Session::get('servico_busca_cliente') and in_array($servico->cd_tipo_servico_tse,Session::get('servico_busca_cliente'))) ? 'selected' : '' }}>{{ $servico->nm_tipo_servico_tse }}</option>
                                                @endforeach
                                        </select>
                                    </section> 
                                    <section class="col col-md-2">
                                        <label class="label">Buscar</label>
                                        <button class="btn btn-primary" style="width: 100%" type="submit" data-toggle="modal" data-target="#processamento"><i class="fa fa-search"></i> Buscar</button>
                                    </section>
                                    <section class="col col-md-2">
                                        <label class="label">Mostrar Todos</label>
                                        <button class="btn btn-default" style="width: 100%" type="submit" name="mostrar_todos" data-toggle="modal" data-target="#processamento"><i class="fa fa-list-ul"></i> Mostrar Todos</button>
                                    </section>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>

                    <div class="col-sm-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        
                                            <div class="col-md-6"> 
                                                <h5>Honorários por Tipo de Serviço</h5> 
                                            </div>
                                            <div class="col-md-6">                                               
                                                <a href="{{ url('cliente/honorarios/adicionar/'.$cliente->cd_cliente_cli) }}" class="btn btn-success pull-right header-btn" style="margin-right: -12px; margin-left: 5px;"><i class="fa fa-plus fa-lg"></i> Adicionar Valores</a>

                                                <div class="btn-group pull-right header-btn">
                                                    <a class="btn btn-default" href="javascript:void(0);"><i class="fa fa-sort-amount-asc"></i> Ordenar Por</a>
                                                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="{{ url('cliente/honorarios/organizar/2') }}">Cidades</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('cliente/honorarios/organizar/1') }}">Serviços</a>
                                                            </li>
                                                        </ul>
                                                </div>
                                            </div> 
                                            @if(!empty($cidades_tabela))                                            
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades_tabela as $cidade)
                                                                        <th><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/comarca/excluir/') }}" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span>  {{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos_tabela as $servico)
                                                                    <tr>
                                                                        <td dlass="center"><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/servico/excluir/') }}" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades_tabela as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                        
                                                                                    {{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Sem valor'}}                                                                                      
                                                                                        
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cidade</th>
                                                                    @foreach($lista_servicos_tabela as $servico)
                                                                        <th><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/servico/excluir/') }}" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades_tabela as $cidade)
                                                                    <tr>
                                                                        <td><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/comarca/excluir/') }}" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $cidade->nm_cidade_cde  }}</td>
                                                                        @foreach($lista_servicos_tabela as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">

                                                                                    {{  (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Sem valor' }}</span>                                                                                     
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="col-md-12 center"> 
                                                    <hr/>
                                                    <h4>Nenhum honorário para ser exibido</h4>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
            </article>
        </div>
    </div>
</div>
 <div class="modal fade modal_top_alto" id="modal_exclusao_honorario" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-trash"></i> <strong>Excluir Registro</strong></h4>
                    </div>
                    <div class="modal-body" style="text-align: center;">
                        <h4>Essa operação irá excluir todas as ocorrências <span id="txt_exclusao_honorario"></span>. Para excluir somente um valor, apague o valor numérico e pressione o botão <strong>Atualizar Valores</strong></h4>
                        <h4>Deseja continuar?</h4>
                        <input type="hidden" name="id" id="id_exclusao_honorario">
                        <input type="hidden" name="url" id="url_honorario">
                        <div class="msg_retorno_honorario"></div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" id="btn_confirma_exclusao_honorario" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</a>
                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        $(".btnBuscaHonorariosCliente").click(function(){

            var tipo_servico = $("#servico_grupo").val();

            if(tipo_servico == null){
                $(".box-msg-busca-honorarios").html("Selecione um tipo de serviço para realizar a consulta");
                $("#servico_grupo").focus();
                return false;
            }else{
                $(".box-msg-busca-honorarios").html("");
            }

        });


        $(".excluir_registro_honorario").click(function(){

            var id = $(this).data('id');
            var url = $(this).data('url');
            var texto = $(this).data('texto');

            $("#modal_exclusao_honorario #txt_exclusao_honorario").html(texto);
            $("#modal_exclusao_honorario #url_honorario").val(url);
            $("#modal_exclusao_honorario #id_exclusao_honorario").val(id);
            $("#modal_exclusao_honorario").modal('show');
        });

        $('.valor_honorario').editable({
            validate: function (value) {
                if ($.trim(value) == '')
                    return 'Valor obrigatório';
            },
            tpl: '<input type="text" style="width: 20px;" class="form-control taxa-honorario">',
            success: function(){
                $(this).attr("data-edit","S");
            }
        });

        $('.valor_honorario').on('shown', function (e, editable) {

            var cidade = $(this).data('cidade');
            var servico = $(this).data('servico');
            var tipo = $(this).data('tipo');

            editable.input.$input.closest('.control-group')
                                 .find('.editable-buttons')
                                 .append('<button type="button" title="Repetir valor para todas as cidades" class="btn btn-warning btn-sm btn-honorario atualizaValores" data-tipo="cidade" data-cidade="'+cidade+'" data-servico="'+servico+'"><strong class="icon-honorario">C</strong></button><button type="button" title="Repetir valor para todos os serviços" class="btn btn-info btn-sm btn-honorario atualizaValores" data-tipo="servico" data-cidade="'+cidade+'" data-servico="'+servico+'"><strong class="icon-honorario">S</strong></button><button type="button" title="Repetir valor para toda a tabela" class="btn btn-success btn-sm btn-honorario atualizaValores" data-tipo="tabela" data-cidade="'+cidade+'" data-servico="'+servico+'"><strong class="icon-honorario">T</strong></button>');

        });

        $(document).on("focus", ".taxa-honorario", function () {
            $(this).mask('#####000,00', {reverse: true});
        });

        $(document).on("click", ".atualizaValores", function () {

            var cidade = $(this).data("cidade");
            var servico = $(this).data("servico");
            var tipo = $(this).data("tipo");
            var valor = $(".taxa-honorario").val().replace(".", ",");

            
            $(".valor_honorario").each(function(){


                if(tipo === "servico"){

                    var valor_cidade = $(this).data("cidade");

                    if(valor_cidade === cidade){
                        $(this).attr("data-edit","S");
                        $(this).text(valor);
                    }
                }

                if(tipo === "cidade"){

                    var valor_servico = $(this).data("servico");

                    if(valor_servico === servico){
                        $(this).attr("data-edit","S");
                        $(this).text(valor);
                    }
                }

                if(tipo === "tabela"){
                    $(this).attr("data-edit","S");
                    $(this).text(valor);
                }
     
            });

        });

        var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '/cidades-por-estado/'+estado,
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

        buscaCidade();

        $("#estado").change(function(){
            
            buscaCidade(); 

        });

    });

    
</script>
@endsection