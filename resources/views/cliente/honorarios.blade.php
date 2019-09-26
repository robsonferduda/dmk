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
                <div class="col-sm-12">
                    <div class="well">
                        <form action="{{ url('cliente/honorarios/buscar/'.$cliente->cd_cliente_cli) }}" class="smart-form'" method="GET" role="search">
                            <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                                            <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">
                                            <input type="hidden" name="opcao_visualizacao" value="grupo">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="row"> 
                                    <section class="col col-md-4">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (Session::get('cidade_busca_cliente')) ? Session::get('cidade_busca_cliente') : old('cd_cidade_cde')}}">       
                                        <select id="cidade_grupo" name="cd_cidade_cde" class="select2">
                                            <option selected value="">Selecione uma cidade</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-md-6">
                                        <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ (Session::get('cidade_busca_cliente')) ? Session::get('cidade_busca_cliente') : old('cd_cidade_cde')}}">        
                                        <select id="cidade_grupo" name="cd_cidade_cde" class="select2">
                                            <option selected value="">Selecione uma cidade</option>
                                        </select> 
                                    </section>
                                    <section class="col col-md-2">
                                        <button class="btn btn-primary" style="width: 100%" type="submit"><i class="fa fa-search"></i> Buscar</button>
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
                                        @if(count($cidades) > 0)
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
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades as $cidade)
                                                                        <th> {{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos as $servico)
                                                                    <tr>
                                                                        <td><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ $cliente->entidade->cd_entidade_ete }}/servico/excluir/" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                        
                                                                                        {{ $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] }}                                                                                      
                                                                                        
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
                                                                        <th>{{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades as $cidade)
                                                                    <tr>
                                                                        <td>{{ $cidade->nm_cidade_cde  }}</td>
                                                                        @foreach($lista_servicos as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">

                                                                                    {{  $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] }}</span>                                                                                     
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
                
            </article>
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