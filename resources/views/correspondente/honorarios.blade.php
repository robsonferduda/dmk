@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Honorários</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span>> Honorários </span> <span>> <strong>{{ $cliente->nm_conta_correspondente_ccr }}</strong></span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondentes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Correspondentes</a>
            <button class="btn btn-primary pull-right header-btn marginLeft5" id="showAllHonorariosCorrespondente"><i class="fa fa-table fa-lg"></i> Mostrar Todos</button>  
            <a class="btn btn-danger pull-right header-btn remover_honorarios marginLeft5" data-url="{{ url('correspondente/honorarios/excluir/'.$cliente->cd_correspondente_cor ) }}" data-id="{{ $cliente->entidade->cd_entidade_ete }}"><i class="fa fa-times fa-lg"></i> Excluir Todos os Honorários</a>                                               
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-12">                                    
                                    <div class="row">
                                        <div class="col-md-12">  
                                            <form action="{{ url('correspondente/buscar-honorarios/'.$cliente->cd_conta_con) }}" class="smart-form'" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_correspondente" id="cd_correspondente" value="{{ $cliente->cd_correspondente_cor }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}" data-token="{{ \Crypt::encrypt($cliente->entidade->cd_entidade_ete) }}">
                                                <input type="hidden" name="ordem" id="ordem" value="comarca">

                                                <fieldset>
                                                    <div class="row"> 

                                                        <section class="col col-md-4">                                       
                                                            <label class="label label-black" >Estado</label>          
                                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                                <option selected value="">Estado</option>
                                                                @foreach(App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                                @endforeach
                                                            </select> 
                                                        </section>

                                                        <section class="col col-md-8">
                                                           <label class="label label-black">
                                                                <a href="#" rel="popover-hover" data-placement="top" data-html="true" data-original-title="Cidades de Atuação" data-content="São listadas somente as comercas onde o correpondente atua. Para cadastrar uma cidade de atuação">
                                                                    <i class="fa fa-question-circle text-primary"></i>
                                                                </a>
                                                                Comarca de Atuação
                                                           </label>  
                                                            <a href="{{ url('correspondente/comarcas/'.\Crypt::encrypt($cliente->cd_correspondente_cor)) }}" target="_blank" style="padding: 1px 8px;"><i class="fa fa-plus-circle"></i> Cadastrar Comarca de Atuação </a>        
                                                            <select id="cidade" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma comarca</option>
                                                            </select> 
                                                        </section>

                                                        <div class="col-sm-12" id="msg_busca_cidade_honorario" style="margin-top: 5px;"></div>

                                                        <hr/>
                                                        <section class="col col-md-12">
                                                            <select  multiple size="8" id="lista_servicos" name="lista_servicos[]" >
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}">{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach                              
                                                            </select>    
                                                        </section> 
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12" id="msg_valida_busca" style="margin: 5px 0px;"></div> 
                                                        <section class="col col-md-12"> 
                                                            <button class="btn btn-primary btn-buscar-honorarios" type="button"><i class="fa fa-search"></i> Buscar</button>
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
                                        <div class="col-md-6"> 
                                            <h4><strong>Valores de Honorários por Serviços</strong></h4> 
                                        </div>
                                        <div class="col-md-6">

                                            <div class="btn-group pull-right header-btn marginLeft5">
                                                <a class="btn btn-default" href="javascript:void(0);"><i class="fa fa-sort-amount-asc"></i> Ordenar Por</a>
                                                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="#" id="showHonorariosComarca"><span>Comarca</span></a>
                                                    </li>
                                                    <li>
                                                        <a href="#" id="showHonorariosServico"><span>Serviços</span></a>
                                                    </li>
                                                </ul>
                                            </div>                                        

                                        </div>
                                        <div class="col-md-12 progresso" style="display: none; margin-top: 10px;">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 container-honorarios">
                                            <div class="col-md-12 box-loader-honorarios"></div>
                                            <div class="box-loader-honorarios-error none">
                                                <h4 class="text-danger"><i class="fa fa-times-circle"></i> Erro ao enviar requisição, tente novamente</h4>
                                            </div>
                                            <div class="tabelah none">
                                                <table class="table table-bordered table-load-honorarios">
                                                    <thead>
                                                        <tr>
                                                            <th>Comarca</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
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
                                {!! Form::open(['id' => 'frm_excluir_honorarios_correspondente', 'url' => 'correspondente/honorarios/remover', 'class' => 'form-inline']) !!}
                                    <p style="font-size: 14px;">
                                        Essa operação irá excluir todos os valores de honorários por serviço para todas as comarcas cadastradas para esse correspondente.
                                    </p>
                                    <h6>Confirma a exclusão de todos os valores?</h6>
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


    <div class="modal fade modal_top_alto" id="modalEditarHonorarios" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-money"></i> <strong>Adicionar Valores de Honorários</strong></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="modal_id_comarca" id="modal_id_comarca">
                        <input type="hidden" name="modal_id_servico" id="modal_id_servico">
                        <div class="row">
                            <div class="col-md-12">
                                <h4><strong>Comarca</strong>: <span id="nmcomarca">Florianópolis</span></h4>
                                <h4><strong>Serviço</strong>: <span id="nmservico">AUDIÊNCIA VARA CÍVEL (ADVOGADO E PREPOSTO)</span></h4>
                                <p>Vocẽ pode excluir o honorário selecionado ou editar seu valor conforme opções abaixo</p>                                
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top: 8px;">                                    
                                    <label for="tags">Digite um valor para o honorário</label>
                                    <input type="text" class="form-control taxa-honorario" id="input-honorario" placeholder="Valor">
                                </div>
                            </div>
                            <div class="col-md-9" style="margin-left: 20px;">
                                <div class="form-group">
                                    <label class="checkbox">
                                        <input type="checkbox" name="all_services" id="all_services" value="true">
                                        <i></i>Aplicar o mesmo valor para todos os serviços desta comarca 
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="checkbox">
                                        <input type="checkbox" name="all_comarcas" id="all_comarcas" value="true">
                                        <i></i>Aplicar o mesmo valor para este serviço em todas as comarcas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" id="btn_excluir_registro_honorario" data-tipo="unico" data-id="" data-texto=" somente o valor do honorário selecionado"><i class="fa fa-remove"></i> Excluir Honorário</button>
                        <button class="btn btn-success" id="btn_confirma_adicao_honorario"><i class="fa fa-user fa-check"></i> Salvar Valores</button>
                        <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</button>
                    </div>
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
                    <h4>
                        Essa operação irá excluir <span id="txt_exclusao_honorario"></span>. 
                    </h4>
                    <h4>Deseja continuar?</h4>
                    <input type="hidden" name="id_exclusao_honorario" id="id_exclusao_honorario">
                    <input type="hidden" name="tipo_honorario" id="tipo_honorario">
                    <div class="msg_retorno_honorario"></div>
                </div>
                <div class="modal-footer">
                    <a type="button" id="btn_confirma_exclusao_honorario_correspondente" class="btn btn-primary"><i class="fa fa-user fa-check"></i> Confirmar</a>
                    <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal_top_alto" id="modal_btn_confirma_add_honorarios" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modal_exclusao" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-trash"></i> <strong>Atualização de Registros</strong></h4>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <div id="status_terefa"></div>
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

    $(document).ready(function(){

        $('.box-loader-honorarios').addClass('none');

        $(document).on("focus", ".taxa-honorario", function () {
            $(this).mask('#####000,00', {reverse: true});
        });

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

        $("#showHonorariosComarca").click(function(){

            $("#ordem").val("comarca");

            lista_servicos = $("#lista_servicos").val();
            lista_cidades = $("#cidade").val();

            if(lista_cidades && lista_servicos != null){

                $(".btn-buscar-honorarios").trigger("click");

            }else{

                $("#showAllHonorariosCorrespondente").trigger("click");
            }

        });

        $("#showHonorariosServico").click(function(){

            $("#ordem").val("servico");
            
            lista_servicos = $("#lista_servicos").val();
            lista_cidades = $("#cidade").val();

            if(lista_cidades && lista_servicos != null){

                $(".btn-buscar-honorarios").trigger("click");

            }else{

                $("#showAllHonorariosCorrespondente").trigger("click");
            }

        });

        $('.btn-add-honorarios').on('click', function (e, editable) {

            //Botão de adicinar valores de honorários
            //Validar seleção de comarca
            //Validar seleção de serviço
            //Inserir valores, sem atualizar a tela
            cidades = $("#cidade").val();
            servicos = $("#lista_servicos").val();

            if(!cidade){
                $("#msg_valida_busca").html('<span class="text-danger">Selecione uma ou mais cidades para continuar</span>');
                return false;
            }

            if(servicos === null){
                $("#msg_valida_busca").html('<span class="text-danger">Selecione um ou mais serviços para continuar</span>');
                return false;
            }

            $("#msg_valida_busca").empty();

            $("#modalAddHonorarios").modal('show');

        });

        $(document).on("click", "#btn_confirma_add_honorarios", function () {

            var valor = $("#input-honorario").val();
            var comarca = $("#modal_id_comarca").val();
            var servico = $("#modal_id_servico").val();
            var entidade = $("#cd_entidade").data("token");
            var all_services = $('#all_services').is(":checked");
            var all_comarcas = $('#all_comarcas').is(":checked");
            var all_table = $('#all_table').is(":checked");

            cidades = $("#cidade").val();
            servicos = $("#lista_servicos").val();
            $("#status_terefa").html('');

            var valores = new Array();
            var entidade = $("#cd_entidade").val();
            var correspondente = $("#cd_correspondente").val();

            var valor = $.trim($("#input-honorario").val().replace(/[\t\n]+/g,' '));

            for(var i=0; i< servicos.length; i++) {
            
                if(cidades == 0){

                    $("#cidade > option").each(function(){

                        if($(this).val() != "" && $(this).val() != "0"){
                            var dados = {servico: servicos[i], cidade: $(this).val(), valor: valor};
                            valores.push(dados);
                        }

                    });

                }else{

                    var dados = {servico: servicos[i], cidade: cidades, valor: valor};
                    valores.push(dados);
                }

            }

            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/honorarios/salvar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "valores": JSON.stringify(valores),
                    "entidade": entidade
                },
                beforeSend: function()
                {
                    $("#modalAddHonorarios").modal('hide');
                    $("#modal_btn_confirma_add_honorarios").modal('show');
                    $("#status_terefa").html('<i class="fa fa-gear fa-spin"></i> Aguarde, processando requisição...');
                },
                success: function(response)
                {
                    $("#status_terefa").html('<i class="fa fa-info"></i> Operação realizada com sucesso!');
                    //$('.btn-buscar-honorarios').trigger('click');
                },
                error: function(response)
                {
                    $("#status_terefa").empty('<i class="fa fa-times"></i> Houve um erro ao processar sua requisição');
                }
            });

        });

        $('.btn-buscar-honorarios').on('click', function (e, editable) {

            estado = $("#estado").val();
            cidade = $("#cidade").val();

            if(!cidade){
                $("#msg_valida_busca").html('<span class="text-danger">Selecione uma ou mais cidades para continuar</span>');
                return false;
            }

            if($("#lista_servicos").val() === null){
                $("#msg_valida_busca").html('<span class="text-danger">Selecione um ou mais serviços para continuar</span>');
                return false;
            }

            $("#msg_valida_busca").empty();

            id = $("#cd_entidade").data("token");
            ordem = $("#ordem").val();
            lista_servicos = $("#lista_servicos").val();
            cidades = $("#cidade").val();
            correspondente = $("#cd_correspondente").val();

            $.ajax({
                url: '../../correspondente/buscar-honorarios/'+id,
                type: 'GET',
                data: {"id":id, "ordem": ordem, "lista_servicos": lista_servicos, "estado": estado, "lista_cidades": cidades, "correspondente": correspondente },
                dataType: "JSON",
                beforeSend: function(){
                    
                    $('.box-loader-honorarios').loader('show');
                    $('.box-loader-honorarios').removeClass('none');
                    $(".tabelah").addClass('none');
                    $(".honorarios-empty").addClass('none');

                },
                success: function(response)
                {    

                    $(".tabelah").addClass('none');
                    $(".table-load-honorarios thead tr th").remove();
                    $(".table-load-honorarios tbody tr td").remove();

                    var total_honorarios = Object.keys(response.honorarios).length;

                    if(ordem == 'comarca'){

                            $(".table-load-honorarios").find("thead").find("tr").append('<th>Comarcas</th>');
                            $.each(response.servicos,function(index,value){

                                $(".table-load-honorarios").find("thead").find("tr").append('<th class="center"><span data-tipo="servico" data-id="'+value.cd_tipo_servico_tse+'" data-texto=" todas as ocorrências do serviço <strong>'+value.nm_tipo_servico_tse+'</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_tipo_servico_tse+'</th>');
                            });

                            $.each(response.comarcas,function(index,value){

                                $(".table-load-honorarios").find("tbody").append('<tr><td><span data-tipo="comarca" data-id="'+value.cd_cidade_cde+'" data-texto=" todas as ocorrências da comarca <strong>'+value.nm_cidade_cde+'</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_cidade_cde+'</td></tr>');
                                $.each(response.servicos,function(index,data){

                                    indice = "key-"+value.cd_cidade_cde+"-"+data.cd_tipo_servico_tse;
                                    label_valor = "Adicionar";
                                    valor = null;
                                    if(response.honorarios[indice]){
                                        label_valor = response.honorarios[indice].replace(".", ",");
                                        valor = response.honorarios[indice].replace(".", ",");
                                    }

                                    $(".table-load-honorarios").find("tbody")
                                                               .find("tr:last")
                                                               .append('<td class="center"><span class="add-valor-honorario" data-edit="N" data-comarca="'+value.cd_cidade_cde+'" data-nmcomarca="'+value.nm_cidade_cde+'" data-servico="'+data.cd_tipo_servico_tse+'" data-nmservico="'+data.nm_tipo_servico_tse+'" data-valor="'+valor+'">'+label_valor+'</span></td>');


                                });

                            });

                    }else if(ordem == 'servico'){

                            
                            $(".table-load-honorarios").find("thead").find("tr").append('<th>Serviços</th>');
                            $.each(response.comarcas,function(index,value){

                                $(".table-load-honorarios").find("thead").find("tr").append('<th class="center"><span data-tipo="comerca" data-id="'+value.cd_cidade_cde+'" data-texto="da comarca <strong>'+value.nm_cidade_cde+'</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_cidade_cde+'</th>');
                            });

                            $.each(response.servicos,function(index,value){

                                $(".table-load-honorarios").find("tbody").append('<tr><td><span data-tipo="servico" data-id="'+value.cd_tipo_servico_tse+'" data-texto="do serviço <strong>'+value.nm_tipo_servico_tse+'</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_tipo_servico_tse+'</td></tr>');
                                $.each(response.comarcas,function(index,data){

                                    indice = "key-"+data.cd_cidade_cde+"-"+value.cd_tipo_servico_tse;
                                    label_valor = "Adicionar";
                                    valor = null;
                                    if(response.honorarios[indice]){
                                        label_valor = response.honorarios[indice].replace(".", ",");
                                        valor = response.honorarios[indice].replace(".", ",");
                                    }

                                    $(".table-load-honorarios").find("tbody")
                                                               .find("tr:last")
                                                               .append('<td class="center"><span class="add-valor-honorario" data-edit="N" data-comarca="'+data.cd_cidade_cde+'" data-nmcomarca="'+data.nm_cidade_cde+'" data-servico="'+value.cd_tipo_servico_tse+'" data-nmservico="'+value.nm_tipo_servico_tse+'" data-valor="'+valor+'">'+label_valor+'</span></td>');

                                });
                            });
                    }

                    $("#modal_exclusao_honorario").modal('hide');
                    $("#modalEditarHonorarios").modal('hide');

                    $('.box-loader-honorarios-error').addClass('none');             
                    $('.box-loader-honorarios').addClass('none');
                    $(".tabelah").removeClass('none');

                },
                error: function(response)
                {   
                    $('.box-loader-honorarios-error').removeClass('none');
                    $('.box-loader-honorarios').addClass('none');
                }
            });

        });

        $("#showAllHonorariosCorrespondente").click(function(){

            id = $("#cd_entidade").data("token");
            ordem = $("#ordem").val();

            $.ajax({
                url: '../../correspondente/honorarios',
                type: 'GET',
                data: {"id":id, "ordem": ordem},
                dataType: "JSON",
                beforeSend: function(){
                    
                    $('.box-loader-honorarios').loader('show');
                    $('.box-loader-honorarios').removeClass('none');
                    $(".tabelah").addClass('none');
                    $(".honorarios-empty").addClass('none');

                },
                success: function(response)
                {       
                    $(".tabelah").addClass('none');
                    $(".table-load-honorarios thead tr th").remove();
                    $(".table-load-honorarios tbody tr td").remove();
                    var total_honorarios = Object.keys(response.honorarios).length;

                    if(total_honorarios > 0){

                        if(ordem == 'comarca'){

                            $(".table-load-honorarios").find("thead").find("tr").append('<th>Comarcas</th>');
                            $.each(response.servicos,function(index,value){

                                $(".table-load-honorarios").find("thead").find("tr").append('<th class="center"><span data-tipo="servico" data-id="'+value.cd_tipo_servico_tse+'" data-texto=" todas as ocorrências do serviço <strong>'+value.nm_tipo_servico_tse+'</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_tipo_servico_tse+'</th>');
                            });

                            $.each(response.comarcas,function(index,value){

                                $(".table-load-honorarios").find("tbody").append('<tr><td><span data-tipo="comarca" data-id="'+value.cd_cidade_cde+'" data-texto=" todas as ocorrências da comarca <strong>'+value.nm_cidade_cde+'</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_cidade_cde+'</td></tr>');
                                $.each(response.servicos,function(index,data){

                                    indice = "key-"+value.cd_cidade_cde+"-"+data.cd_tipo_servico_tse;
                                    label_valor = "Adicionar";
                                    valor = null;
                                    if(response.honorarios[indice]){
                                        label_valor = response.honorarios[indice].replace(".", ",");
                                        valor = response.honorarios[indice].replace(".", ",");
                                    }

                                    $(".table-load-honorarios").find("tbody")
                                                               .find("tr:last")
                                                               .append('<td class="center"><span class="add-valor-honorario" data-edit="N" data-comarca="'+value.cd_cidade_cde+'" data-nmcomarca="'+value.nm_cidade_cde+'" data-servico="'+data.cd_tipo_servico_tse+'" data-nmservico="'+data.nm_tipo_servico_tse+'" data-valor="'+valor+'">'+label_valor+'</span></td>');


                                });

                            });

                        }else if(ordem == 'servico'){

                            
                            $(".table-load-honorarios").find("thead").find("tr").append('<th>Serviços</th>');
                            $.each(response.comarcas,function(index,value){

                                $(".table-load-honorarios").find("thead").find("tr").append('<th class="center"><span data-tipo="comerca" data-id="'+value.cd_cidade_cde+'" data-texto="da comarca <strong>'+value.nm_cidade_cde+'</strong> para todos os serviços"class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_cidade_cde+'</th>');
                            });

                            $.each(response.servicos,function(index,value){

                                $(".table-load-honorarios").find("tbody").append('<tr><td><span data-tipo="servico" data-id="'+value.cd_tipo_servico_tse+'" data-texto="do serviço <strong>'+value.nm_tipo_servico_tse+'</strong> para todas as comarcas" class="text-danger excluir_registro_honorario"><i class="fa fa-times-circle"></i></span> '+value.nm_tipo_servico_tse+'</td></tr>');
                                $.each(response.comarcas,function(index,data){

                                    indice = "key-"+data.cd_cidade_cde+"-"+value.cd_tipo_servico_tse;
                                    label_valor = "Adicionar";
                                    valor = null;
                                    if(response.honorarios[indice]){
                                        label_valor = response.honorarios[indice].replace(".", ",");
                                        valor = response.honorarios[indice].replace(".", ",");
                                    }

                                    $(".table-load-honorarios").find("tbody")
                                                               .find("tr:last")
                                                               .append('<td class="center"><span class="add-valor-honorario" data-edit="N" data-comarca="'+data.cd_cidade_cde+'" data-nmcomarca="'+data.nm_cidade_cde+'" data-servico="'+value.cd_tipo_servico_tse+'" data-nmservico="'+value.nm_tipo_servico_tse+'" data-valor="'+valor+'">'+label_valor+'</span></td>');

                                });
                            });
                        }

                    }else{

                        $(".container-honorarios").html('<div class="alert alert-info fade in marginTop10"><button class="close" data-dismiss="alert">×</button><i class="fa-fw fa fa-info"></i><strong>Informação!</strong> Não existem registros de honorários cadastrados para o correspondente</div><h2 class="center"></h2>');

                    }

                    $("#modal_exclusao_honorario").modal('hide');
                    $("#modalEditarHonorarios").modal('hide');

                    $('.box-loader-honorarios-error').addClass('none');             
                    $('.box-loader-honorarios').addClass('none');
                    $(".tabelah").removeClass('none');
    
                },
                error: function(response)
                {   
                    $('.box-loader-honorarios-error').removeClass('none');
                    $('.box-loader-honorarios').addClass('none');
                }
            });

        });

        $(document).on("click", ".add-valor-honorario", function () {

            valor = $(this).data("valor");
            comarca = $(this).data("comarca");
            nmcomarca = $(this).data("nmcomarca");
            servico = $(this).data("servico");
            nmservico = $(this).data("nmservico");

            $("#nmcomarca").html(nmcomarca);
            $("#nmservico").html(nmservico);
            $("#input-honorario").val(valor);
            $("#btn_excluir_registro_honorario").attr('data-id', comarca+'-'+servico);
            $("#modal_id_comarca").val(comarca);
            $("#modal_id_servico").val(servico);

            $("#modal_exclusao_honorario #id_exclusao_honorario").val(comarca+'-'+servico);

            $("#modalEditarHonorarios").modal('show');

        });

        $(document).on("click", "#btn_excluir_registro_honorario", function () {

            var tipo = $(this).data('tipo');
            var texto = $(this).data('texto');

            $("#modal_exclusao_honorario #txt_exclusao_honorario").html(texto);
            $("#modal_exclusao_honorario #tipo_honorario").val(tipo);
            $("#modal_exclusao_honorario").modal('show');

        });

        $(document).on("click", ".excluir_registro_honorario", function () {

            var id = $(this).data('id');
            var tipo = $(this).data('tipo');
            var texto = $(this).data('texto');

            $("#modal_exclusao_honorario #txt_exclusao_honorario").html(texto);
            $("#modal_exclusao_honorario #tipo_honorario").val(tipo);
            $("#modal_exclusao_honorario #id_exclusao_honorario").val(id);
            $("#modal_exclusao_honorario").modal('show');

        });

        $('#modal_exclusao_honorario').on('show.bs.modal', function () {
            $(".msg_retorno_honorario").html("");
        });   

        $('#modalEditarHonorarios').on('shown.bs.modal', function () {
            $("#input-honorario").focus();
            $(".erro_atualiza_valores").html("");
            $(".status_atualiza_valores").html("");            
        });    

        $(document).on("click", "#btn_confirma_adicao_honorario", function () {

            var estado = $("#estado").val();
            var comarca = $("#modal_id_comarca").val();
            var servico = $("#modal_id_servico").val();
            var entidade = $("#cd_entidade").data("token");
            var all_service = $("#all_services").is(":checked");
            var all_comarca = $("#all_comarcas").is(":checked");
            var servicos = $("#lista_servicos").val();
           
            var entidade = $("#cd_entidade").val();
            var correspondente = $("#cd_correspondente").val();
            var valor = $.trim($("#input-honorario").val().replace(/[\t\n]+/g,' '));
            
            $.ajax(
            {
                type: "POST",
                url: "../../correspondente/honorarios/salvar",
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "valor": valor,
                    "estado": estado,
                    "comarca": comarca,
                    "servico": servico,
                    "entidade": entidade,
                    "all_service": all_service,
                    "all_comarca": all_comarca,
                    "servicos": servicos
                },
                beforeSend: function()
                {
                    $(".status_atualiza_valores").html('<i class="fa fa-gear fa-spin"></i> Processando requisição...');
                },
                success: function(response)
                {
                    $(".status_atualiza_valores").html('Operação realizada com sucesso! Aguarde, atualizando dados...');
                    $(".btn-buscar-honorarios").trigger("click");
                },
                error: function(response)
                {
                    $(".status_atualiza_valores").empty();
                    $(".erro_atualiza_valores").html('Houve um erro ao processar sua requisição.');
                }
            });


        });
        

        $(document).on("click", "#btn_confirma_exclusao_honorario_correspondente", function () {

            var id = $("#id_exclusao_honorario").val();
            var tipo= $("#tipo_honorario").val();
            var token = $("#cd_entidade").data("token");

            $.ajax({

                url: '../../correspondente/'+token+'/honorarios/'+tipo+'/excluir/'+id,
                type: 'DELETE',
                dataType: "JSON",
                data: {
                    "id": id,
                    "_method": 'DELETE',
                    "_token": $('meta[name="token"]').attr('content'),
                },
                beforeSend: function()
                {
                    $(".msg_retorno_honorario").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');
                },
                success: function(response)
                {
                    $(".msg_retorno_honorario").html('<h4 class="text-success marginTop10"><strong>Registro excluído com sucesso. Atualizando dados...</strong></h4>');
                    $(".btn-buscar-honorarios").trigger("click");
                },
                error: function(response)
                {
                    $(".msg_retorno_honorario").html('<h4 class="text-danger marginTop10"><strong>Ocorreu um erro ao processar sua requisição.</strong></h4>')
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

                            if(response.length > 0)
                            {
                                $('#cidade').append('<option selected value="0">Todas</option>');
                                if(response.length == 1)
                                    $("#msg_busca_cidade_honorario").html('<span class="text-primary"> '+response.length+' comarca encontrada</span>');
                                else
                                    $("#msg_busca_cidade_honorario").html('<span class="text-primary"> '+response.length+' comarcas encontradas</span>');

                            }else{
                                $("#msg_busca_cidade_honorario").html('<span class="text-danger">Nenhuma comarca encontrada. Cadastre as comarcas de atuação para vincular os honorários</span>');
                            }

                            $.each(response,function(index,element){

                                $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                
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