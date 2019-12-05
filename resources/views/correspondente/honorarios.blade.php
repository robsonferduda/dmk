@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Honorários por Tipo de Serviço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span>> Honorários por Tipo de Serviço </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondentes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Correspondentes</a>
            <a data-toggle="modal" href="{{ url('correspondente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
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
                        <h5><strong>Correspondente: </strong>{{ $cliente->nm_razao_social_con }}</h5>
                        <span class="text-primary"><i class="fa fa-info-circle"></i> Selecione cidades e os serviços para adicionar valores aos honorários</span>
                        <div class="well">
                            <div class="row">

                                <div class="col-sm-12">
                                    
                                    <div class="row">
                                        <div class="col-md-12">  
                                            <form action="{{ url('correspondente/buscar-honorarios/'.$cliente->cd_conta_con) }}" class="smart-form'" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_correspondente" id="cd_correspondente" value="{{ $cliente->cd_correspondente_cor }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">

                                                <fieldset>
                                                    <div class="row"> 

                                                        <section class="col col-md-4">                                       
                                                            <label class="label label-black" >Estado</label>          
                                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                                <option selected value="">Estado</option>
                                                                @foreach(App\Estado::all() as $estado) 
                                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                                @endforeach
                                                            </select> 
                                                        </section>

                                                        <section class="col col-md-8">
                                                           <label class="label label-black" >Cidades de Atuação</label>          
                                                            <select id="cidade" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma cidade</option>
                                                               <option value="0">Todas</option>
                                                            </select> 
                                                        </section> 
                                                        <hr/>
                                                        <section class="col col-md-12">
                                                            <select  multiple size="8" id="lista_servicos" name="lista_servicos[]" >
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}">{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach                              
                                                            </select>    
                                                        </section> 

                                                        <section class="col col-md-1">
                                                            <label class="label">Adicionar</label>          
                                                            <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Adicionar</button>
                                                        </section> 
                                                    </div>
                                                </fieldset>                                                                       
                                            </form>
                                        </div>
                                    </div>
                                </div>
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

                                                <a class="btn btn-danger pull-right header-btn remover_honorarios" data-url="{{ url('correspondente/honorarios/excluir/'.$cliente->cd_correspondente_cor ) }}" data-id="{{ $cliente->entidade->cd_entidade_ete }}" style="margin-right: -12px; margin-left: 5px;"><i class="fa fa-times fa-lg"></i> Excluir Todos</a>

                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorariosCorrespondente" style="margin-left: 5px;"><i class="fa fa-save fa-lg"></i> Salvar Valores</button>

                                                <div class="btn-group pull-right header-btn">
                                                    <a class="btn btn-default" href="javascript:void(0);"><i class="fa fa-sort-amount-asc"></i> Ordenar Por</a>
                                                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="{{ url('correspondente/honorarios/organizar/2') }}">Cidades</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ url('correspondente/honorarios/organizar/1') }}">Serviços</a>
                                                            </li>
                                                        </ul>
                                                </div>

                                            </div> 
                                            
                                                @if(Session::get('organizar') == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered" style="margin-bottom: 150px;">
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
                                                        <table class="table table-bordered" style="margin-bottom: 150px;">
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
<div class="modal fade modal_top_alto" id="modal_excluir_honorarios" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-times"></i> <strong> Excluir Honorários</strong></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 center">
                                {!! Form::open(['id' => 'frm_envio_convite', 'url' => 'correspondente/honorarios/remover', 'class' => 'form-inline']) !!}
                                    <p style="font-size: 14px;">
                                        Essa operação irá remover todos os valores de honorários cadastrados para esse correspondente.
                                    </p>
                                    <h6>Confirma a remoção de todos os valores?</h6>
                                    <input type="hidden" name="entidade_correspondente_excluir" id="entidade_correspondente_excluir">
                                    <input type="hidden" name="cd_correspondente_excluir" id="cd_correspondente_excluir">
                                    
                                    <input type="hidden" name="url" id="url">
                                    <div class="msg_retorno"></div>

                                    <div class="center marginTop20">
                                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function(){

        var duallistbox = $('select[name="lista_servicos[]"]').bootstrapDualListbox({
            nonSelectedListLabel: 'Serviços Disponíveis',
            selectedListLabel: 'Serviços Selecionados',
            infoText: 'Mostrando {0} registros',
            filterTextClear: 'Mostrar Todos',
            infoTextFiltered: '<span class="label label-warning">Filtrados</span> {0} de {1}',
            infoTextEmpty: 'Não há registros',
            filterPlaceHolder: 'Filtrar',
            moveSelectedLabel: 'Mover Serviços Selecionados',
            moveAllLabel: 'Mover Todos',
            removeSelectedLabel: 'Remover Selecionados',
            removeAllLabel: 'Remover Todos',
            moveOnSelect: false
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
            entidade = $('#cd_entidade').val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '../../correspondente/'+entidade+'/cidades-por-estado/'+estado,
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
                            $('#cidade').append('<option selected value="">Selecione uma cidade</option>');
                            $('#cidade').append('<option selected value="0">Todas</option>');
                            $.each(response,function(index,element){

                                $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                
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