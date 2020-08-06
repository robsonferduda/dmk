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
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Honorários por Tipo de Serviço </span>
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
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                        <h2>Honorários por Tipo de Serviço</h2>             
                    </header>
                    <div class="col-sm-12">
                        <div class="col-md-6">
                            <h5 style="margin-left: 12px;"><strong>Cliente: </strong>{{ $cliente->nm_razao_social_cli }}</h5>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('cliente/honorarios/'.$cliente->cd_cliente_cli) }}" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Honorários</a>

                            <button type="button" class="btn btn-primary pull-right header-btn" style="margin-right: 5px;" data-toggle="modal" data-target="#modalEditar">
                                Editar
                            </button>
                        </div>

                        <div class="col-md-12">
                            <div class="widget-body">                 
                                <ul id="Tabs" class="nav nav-tabs bordered">
                                    <li class="{{ (Session::get('opcao_visualizacao') == 'grupo') ? 'active' : (!Session::has('opcao_visualizacao')) ? 'active' : '' }}">
                                        <a href="#s1" data-toggle="tab">POR GRUPO</a>
                                    </li>
                                    <li class="{{ (Session::get('opcao_visualizacao') == 'cidade') ? 'active' : '' }}">
                                        <a href="#s2" data-toggle="tab">POR CIDADE</a>
                                    </li>
                                </ul>
                                <div id="myTabContent1" class="tab-content padding-10">
                                    <div class="tab-pane fade active in" id="s1">
                                        <form action="{{ url('cliente/buscar-honorarios/'.$cliente->cd_cliente_cli) }}" class="smart-form'" method="GET" role="search">
                                            {{ csrf_field() }} 
                                            <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                                            <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                                            <input type="hidden" name="opcao_visualizacao" value="grupo">
                                                <fieldset>
                                                    <div class="row marginBottom10">
                                                        <section class="col col-md-4">
                                                            <label class="label label-black" >Grupos de Cidade</label> 
                                                            <select name="grupo_cidade" id="grupo_cidade" class="form-control" required="required">
                                                                <option value="0">Selecione um grupo</option>
                                                                @foreach($grupos as $grupo)
                                                                    <option value="{{ $grupo->cd_grupo_cidade_grc }}" {{ (Session::get('grupo_busca_cliente') and Session::get('grupo_busca_cliente') == $grupo->cd_grupo_cidade_grc) ? 'selected' : '' }}>{{ $grupo->nm_grupo_cidade_grc }}</option>
                                                                @endforeach
                                                            </select>
                                                        </section>
                                                        <section class="col col-md-8">
                                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (Session::get('cidade_busca_cliente')) ? Session::get('cidade_busca_cliente') : old('cd_cidade_cde')}}">
                                                           <label class="label label-black" >Cidade</label>          
                                                            <select id="cidade_grupo" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma cidade</option>
                                                            </select> 
                                                        </section> 
                                                    </div>

                                                    <div class="row">
                                                        <section class="col col-md-12">
                                                           <label class="label label-black" >Tipos de Serviço</label><span class="text-info">(Selecione um ou mais Tipos de Serviço)</span>       
                                                            <select multiple name="servico[]" id="servico_grupo" class="select2" required="required">
                                                                <option value="">Selecione um servico</option>
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}" {{ (Session::get('servico_busca_cliente') and in_array($servico->cd_tipo_servico_tse,Session::get('servico_busca_cliente'))) ? 'selected' : '' }}>{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="text-danger box-msg-busca-honorarios"></div>
                                                        </section> 
                                                    </div>
                                                    <div class="row center">
                                                        <section class="col col-md-12">
                                                            <button class="btn btn-primary btnBuscaHonorariosCliente" type="submit" style="margin-top: 18px;"><i class="fa fa-plus"></i> Adicionar</button>
                                                        </section>
                                                    </div>                                                    
                                                </fieldset>                                    
                                        </form>
                                    </div>
                                            <div class="tab-pane fade" id="s2">
                                                <form action="{{ url('cliente/buscar-honorarios/'.$cliente->cd_cliente_cli) }}" class="smart-form'" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                                                <input type="hidden" name="opcao_visualizacao" value="cidade">

                                                <fieldset>
                                                    <div class="row marginBottom10">
                                                        <section class="col col-md-4">                                       
                                                            <label class="label label-black" >Estado</label>          
                                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                                <option selected value="">Selecione um estado</option>
                                                                @foreach(App\Estado::all() as $estado) 
                                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                                @endforeach

                                                            </select> 
                                                        </section>

                                                        <section class="col col-md-8">
                                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                                           <label class="label label-black" >Cidade</label>          
                                                            <select id="cidade" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma Cidade</option>
                                                            </select> 
                                                        </section> 

                                                    </div>

                                                    <div class="row"> 

                                                        <section class="col col-md-12">
                                                           <label class="label label-black" >Tipos de Serviço</label><span class="text-info">(Selecione um ou mais Tipos de Serviço)</span>         
                                                            <select multiple name="servico[]" class="select2" required="required">
                                                                <option value="">Selecione um servico</option>
                                                                <option value="0">Todos</option>
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}" {{ (Session::get('servico_busca_cliente') and in_array($servico->cd_tipo_servico_tse,Session::get('servico_busca_cliente'))) ? 'selected' : '' }}>{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach
                                                            </select>
                                                        </section> 

                                                    </div>
                                                    <div class="row center">                                               
                                                        <section class="col col-md-12">
                                                            <button class="btn btn-primary" type="submit" style="margin-top: 18px;"><i class="fa fa-plus"></i> Adicionar</button>
                                                        </section>
                                                    </div>                                                    

                                                </fieldset><br/>
                                                                                            
                                                
                                            </form>
                                            </div>
                                            
                                        </div>
                
                                    </div>
                    </div>

                    
                    {{ Session::forget('opcao_visualizacao') }}
                    {{ Session::forget('grupo_busca_cliente') }}
                    {{ Session::forget('servico_busca_cliente') }}

                    <div class="col-sm-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">   
                                        @if(count($cidades) > 0)
                                            <div class="col-md-6"> 
                                                <h5>Honorários por Tipo de Serviço</h5> 
                                            </div>
                                            <div class="col-md-6"> 
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorarios"  style="margin-right: -12px; margin-left: 5px;"><i class="fa fa-save fa-lg"></i> Salvar Valores</button>

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
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades as $cidade)
                                                                        <th><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/comarca/excluir/') }}"" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span>  {{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos as $servico)
                                                                    <tr>
                                                                        <td><div style="min-width: 200px;"><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/servico/excluir/') }}"" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                        
                                                                                        <span style="border: none; cursor: pointer;" data-edit="N" data-tipo="cidade" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" class="valor_honorario" data-type="text" data-pk="1" data-placement="bottom" data-placeholder="Valor" data-original-title="Digite o valor do honorário">{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Adicionar' }}</span>                                                                                        
                                                                                        
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
                                                                    @foreach($lista_servicos as $servico)
                                                                        <th><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/servico/excluir/') }}" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades as $cidade)
                                                                    <tr>
                                                                        <td><div style="min-width: 200px;"><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ url('cliente/honorarios/'.$cliente->entidade->cd_entidade_ete.'/comarca/excluir/') }}"" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $cidade->nm_cidade_cde  }}</div></td>
                                                                        @foreach($lista_servicos as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">

                                                                                    <span style="border: none; cursor: pointer;" data-edit="N" data-tipo="servico" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" class="valor_honorario" data-type="text" data-pk="1" data-placement="bottom" data-placeholder="Valor" data-original-title="Digite o valor do honorário" style="display: inline;">{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Adicionar' }}</span>                                                                                     
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
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

<div class="modal fade modal_top_alto" id="modalEditar" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-money"></i> <strong>Adicionar Valores de Honorários</strong></h4>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12">
                                <h4><strong>Comarca</strong>: Florianópolis</h4>
                                <h4><strong>Serviço</strong>: AUDIÊNCIA VARA CÍVEL (ADVOGADO E PREPOSTO)</h4>
                                <div class="form-group" style="margin-top: 8px;">
                                    
                                    <label for="tags">Digite um valor para o honorário</label>
                                    <input type="text" class="form-control taxa-honorario" id="tags" placeholder="Valor">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="">
                                        <input type="checkbox" name="subscription" id="subscription">
                                            <i></i>Aplicar valor à todos os serviços
                                    </label><br/>
                                    <label class="">
                                        <input type="checkbox" name="terms" id="terms">
                                            <i></i>Aplicar valor à todas as comarcas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" id="btn_confirma_exclusao_honorario" class="btn btn-success"><i class="fa fa-user fa-check"></i> Aplicar Valores</a>
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
                        url: '../../../cidades-por-estado/'+estado,
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