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
                        <h5><strong>Cliente: </strong>{{ $cliente->nm_razao_social_cli }}</h5>
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-12">  
                                            <form action="{{ url('cliente/buscar-honorarios/'.$cliente->cd_cliente_cli) }}" class="smart-form'" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">

                                                <fieldset>
                                                    <div class="row"> 

                                                        <section class="col col-md-3">
                                                            <label class="label label-black" >Grupos de Cidade</label> 
                                                            <select name="grupo_cidade" id="grupo_cidade" class="form-control">
                                                                <option value="0">Selecione um grupo</option>
                                                                @foreach($grupos as $grupo)
                                                                    <option value="{{ $grupo->cd_grupo_cidade_grc }}">{{ $grupo->nm_grupo_cidade_grc }}</option>
                                                                @endforeach
                                                            </select>
                                                        </section>

                                                        <section class="col col-md-4">                                       
                                                            <label class="label label-black" >Estado</label>          
                                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                                <option selected value="">Selecione um estado</option>
                                                                @foreach(App\Estado::all() as $estado) 
                                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                                @endforeach

                                                            </select> 
                                                        </section>

                                                        <section class="col col-md-5">
                                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                                           <label class="label label-black" >Cidade</label>          
                                                            <select id="cidade" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma Cidade</option>
                                                            </select> 
                                                        </section> 

                                                    </div>

                                                    <div class="row"> 

                                                        <section class="col col-md-3">
                                                           <label class="label label-black" >Tipos de Serviço</label>          
                                                            <select name="servico" class="form-control">
                                                                <option value="">Selecione um servico</option>
                                                                <option value="0">Todos</option>
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}">{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach
                                                            </select>
                                                        </section> 

                                                       

                                                        <section class="col col-md-2">
                                                            <button class="btn btn-primary" type="submit" style="margin-top: 18px;"><i class="fa fa-plus"></i> Adicionar</button>
                                                        </section>

                                                    </div>                                                    

                                                </fieldset><br/>
                                                                                            
                                                
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="#" rel="popover-hover" data-placement="top" data-original-title="Organizar Tabela" data-content="Organiza os dados da tabela por cidades ou pelos tipos de serviço. O valor selecionado será disposto em ordem alfabética na primeira coluna da tabela. Você pode alterar a organização quando desejar.">
                            <i class="fa fa-question-circle text-primary"></i>
                        </a> 
                        <strong> ORGANIZAR POR:</strong> 
                        <div class="well">
                            <div class="row"> 
                                <form action="{{ url('cliente/honorarios/organizar') }}" class="smart-form'" method="POST" role="search">
                                    {{ csrf_field() }} 
                                    <section class="col col-md-2">  
                                        <input type="hidden" name="cd_cliente" id="cd_cliente" value="{{ $cliente->cd_cliente_cli }}">   
                                        <select  id="organizar" name="organizar" class="form-control">
                                            <option value="0">Selecione uma opção</option>
                                                <option value="1" {{ ($organizar == 1) ? 'selected' : '' }}>Tipos de Serviço</option>
                                                <option value="2" {{ ($organizar == 2) ? 'selected' : '' }}>Cidades</option>
                                        </select> 
                                    </section> 
                                    <section class="col col-md-2">
                                        <button class="btn btn-warning" type="submit"><i class="fa fa-sort-amount-asc"></i> Reorganizar</button>
                                    </section>
                                </form>
                            </div>
                        </div>

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
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorarios" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Atualizar Valores</button>

                                                <a href="{{ url('cliente/limpar-selecao/'.$cliente->cd_cliente_cli) }}" class="btn btn-warning pull-right header-btn" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Seleção</a>
                                            </div>                                             
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades as $cidade)
                                                                        <th><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ $cliente->entidade->cd_entidade_ete }}/comarca/excluir/" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span>  {{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos as $servico)
                                                                    <tr>
                                                                        <td><div style="min-width: 200px;"><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ $cliente->entidade->cd_entidade_ete }}/servico/excluir/" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                        
                                                                                        <a href="form-x-editable.html#" data-tipo="cidade" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" class="valor_honorario" data-type="text" data-pk="1" data-placement="bottom" data-placeholder="Valor" data-original-title="Digite o valor do honorário">
                                                                                            {{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Adicionar' }}
                                                                                        </a>                                                                                        
                                                                                        
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
                                                                        <th><span style="cursor: pointer;" data-id="{{ $servico->cd_tipo_servico_tse }}" data-url="{{ $cliente->entidade->cd_entidade_ete }}/servico/excluir/" data-texto="do serviço <strong>{{ $servico->nm_tipo_servico_tse }}</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades as $cidade)
                                                                    <tr>
                                                                        <td><div style="min-width: 200px;"><span style="cursor: pointer;" data-id="{{ $cidade->cd_cidade_cde  }}" data-url="{{ $cliente->entidade->cd_entidade_ete }}/comarca/excluir/" data-texto="da comarca <strong>{{ $cidade->nm_cidade_cde  }}</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> {{ $cidade->nm_cidade_cde  }}</div></td>
                                                                        @foreach($lista_servicos as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">

                                                                                    <a href="form-x-editable.html#" data-tipo="servico" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" class="valor_honorario" data-type="text" data-pk="1" data-placement="bottom" data-placeholder="Valor" data-original-title="Digite o valor do honorário" style="display: inline;">
                                                                                        
                                                                                        {{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : 'Adicionar' }}
                                                                                    </a>                                                                                     
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
                                                <h6>Nenhum honorário cadastrado</h6>
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
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        $('.valor_honorario').editable({
            validate: function (value) {
                if ($.trim(value) == '')
                    return 'Valor obrigatório';
            },
            tpl: '<input type="text" style="width: 20px;" class="form-control taxa-honorario">'
        });

        $('.valor_honorario').on('shown', function (e, editable) {

            var cidade = $(this).data('cidade');
            var servico = $(this).data('servico');
            var tipo = $(this).data('tipo');

            editable.input.$input.closest('.control-group').find('.editable-buttons').append('<div style="display: inline; margin-left: 8px;" class="input-group-btn"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu" data-cidade="'+cidade+'" data-servico="'+servico+'"><li><a class="atualizaValores" data-tipo="cidade">Repetir valor para todas as cidades</a></li><li><a class="atualizaValores" data-tipo="servico">Repetir valor para todos os serviços</a></li><li><a class="atualizaValores" data-tipo="tabela">Repetir valor para toda a tabela</a></li></ul></div>');

        });

        $(document).on("focus", ".taxa-honorario", function () {
            $(this).mask('#####000,00', {reverse: true});
        });

        $(document).on("click", ".atualizaValores", function () {

            var cidade = $(this).closest("ul").data("cidade");
            var servico = $(this).closest("ul").data("servico");
            var tipo = $(this).data("tipo");
            var valor = $(".taxa-honorario").val().replace(".", ",");

            
            $(".valor_honorario").each(function(){


                if(tipo === "cidade"){

                    var valor_cidade = $(this).data("cidade");

                    if(valor_cidade === cidade){
                        $(this).text(valor);
                    }
                }

                if(tipo === "servico"){

                    var valor_servico = $(this).data("servico");

                    if(valor_servico === servico){
                        $(this).text(valor);
                    }
                }

                if(tipo === "tabela"){
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