@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Usuários</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o la-lg"></i> Processos <span>> Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
         <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
              <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                    
                    data-widget-colorbutton="false" 
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true" 
                    data-widget-sortable="false"
                    
                -->
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Processo </h2>             
                    
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <!-- widget div-->
                <div role="content">
                    
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                        
                    </div>
                    <!-- end widget edit box -->
                    
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        
                        {!! Form::open(['id' => 'frm-add-processo', 'url' => 'processos', 'class' => 'smart-form']) !!}
                        <div class="row">
                            <div  class="col col-6">
                                <header>
                                    <i class="fa fa-file-text-o"></i> Dados do Processo
                                </header>

                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-sm-12">
                                            <input type="hidden" name="cd_cliente_cli" value="{{old('cd_cliente_cli')}}" >
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input required name="nm_cliente_cli" value="{{old('nm_cliente_cli')}}" class="form-control ui-autocomplete-input" placeholder="Digite 3 caracteres para busca" type="text" id="client" autocomplete="off">
                                            </label>
                                        </section>
                                    </div> 
                                    <div class="row">
                                        <section class="col col-4">
                                            <label class="label">Nº Externo <a href="#" rel="popover-hover" data-placement="top" data-original-title="Número ou código de acompanhamento externo."><i class="fa fa-question-circle text-primary"></i></a></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_acompanhamento_pro')}}" type="text" name="nu_acompanhamento_pro" maxlength="50">
                                            </label>
                                        </section> 
                                        <section class="col col-8">                                       
                                            <label class="label" >Advogado Solicitante</label> 
                                            <label class="select">
                                                <input type="hidden" id="contatoAux"  value="{{old('cd_contato_cot')}}">
                                                <select  id="cd_contato_cot" name="cd_contato_cot" >
                                                    <option selected value="">Selecione um Advogado Solicitante</option>            
                                                </select><i></i>  
                                            </label>         
                                        </section>
                                    </div>
                                    <div class="row">
                                         <section class="col col-6">
                                            <label class="label">Nº Processo<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_processo_pro')}}" type="text" name="nu_processo_pro" required>
                                            </label>
                                        </section> 
                                         <section class="col col-6" >                                       
                                            <label class="label" >Tipo de Processo<span class="text-danger">*</span></label>          
                                            <label class="select">
                                                <select  name="cd_tipo_processo_tpo" required>
                                                    <option selected value="">Selecione o Tipo de Processo</option>     
                                                     @foreach($tiposProcesso as $tipo) 
                                                        <option {!! (old('cd_tipo_processo_tpo') == $tipo->cd_tipo_processo_tpo ? 'selected' : '' ) !!} value="{{$tipo->cd_tipo_processo_tpo}}">{{ $tipo->nm_tipo_processo_tpo}}</option>
                                                     @endforeach       
                                                </select><i></i>   
                                            </label>
                                        </section>
                                    </div>                                                     
                                    <div class="row">
                                        <section class="col col-sm-12">
                                            <label class="label">Autor</label>
                                            <label class="input">
                                                <input class="form-control" placeholder="" type="text" name="nm_autor_pro" value="{{old('nm_autor_pro')}}">
                                            </label>
                                        </section> 
                                    </div>    
                                    <div class="row">
                    
                                        <section class="col col-6">
                                           
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione um estado</option>
                                                @foreach($estados as $estado) 
                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach

                                            </select> 
                                        </section>
                                        <section class="col col-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                           <label class="label" >Cidade<span class="text-danger">*</span></label>          
                                            <select  id="cidade"  name="cd_cidade_cde" class="select2" required>
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div>         
                                     <div class="row">    
                                        <input type="hidden" name="cd_correspondente_cor" value="{{old('cd_correspondente_cor')}}">           
                                        <section class="col col-sm-12">
                                            <label class="label">Correspondente</label>
                                            <label class="input">
                                                <input class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{old('nm_correspondente_cor')}}">
                                            </label>
                                        </section>
                                        
                                    </div> 

                                </fieldset>
                        </div>
                        <div  class="col col-6">
                            <header>
                                <i class="">&nbsp;</i> 
                            </header>
                            <fieldset>
                                <div class="row">
                                    <section class="col col-4">
                                        <label class="label">Data da Solicitação</label>
                                        <label class="input">
                                           <input class="dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{old('dt_solicitacao_pro')}}">
                                        </label>
                                    </section> 
                                    <section class="col col-4">
                                        <label class="label">Hora da Audiência</label>
                                        <label class="input">
                                           <input class="hr_audiencia_pro" placeholder="___ : ___" type="text" name="hr_audiencia_pro" value="{{old('hr_audiencia_pro')}}" >
                                        </label>
                                    </section> 
                                     <section class="col col-4">
                                        <label class="label">Data Prazo Fatal</label>
                                        <label class="input">
                                           <input class="dt_prazo_fatal_pro" placeholder="___ /___ /___" type="text" name="dt_prazo_fatal_pro" value="{{old('dt_prazo_fatal_pro')}}">
                                        </label>
                                    </section> 
                                </div>    
                                <div class="row"> 
                                     <section class="col col-sm-12">
                                        <label class="label">Réu</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text" name="nm_reu_pro" value="{{old('nm_reu_pro')}}" >
                                        </label>
                                    </section> 
                                </div>
                                <div class="row">
                                    <section class="col col-sm-12">       
                                        <label class="label" >Vara</label>          
                                        <select  name="cd_vara_var" class="select2">
                                            <option selected value="">Selecione uma vara</option>
                                            @foreach($varas as $vara) 
                                                <option {!! (old('cd_vara_var') == $vara->cd_vara_var ? 'selected' : '' ) !!} value="{{$vara->cd_vara_var}}">{{ $vara->nm_vara_var}}</option>
                                            @endforeach

                                        </select> 
                                    </section>
                                </div> 
                                <header>
                                    <i class="fa">Audiência com:</i> 
                                </header>
                                <fieldset>
                                    <div class="row"> 
                                         <section class="col col-sm-12">
                                            <label class="label">Preposto</label>
                                            <label class="input">
                                               <input class="form-control" placeholder="" type="text" name="nm_preposto_pro" value="{{old('nm_preposto_pro')}}">
                                            </label>
                                        </section> 
                                    </div>
                                    <div class="row"> 
                                         <section class="col col-sm-12">
                                            <label class="label">Advogado</label>
                                            <label class="input">
                                               <input class="form-control" placeholder="" type="text" name="nm_advogado_pro" value="{{old('nm_advogado_pro')}}" >
                                            </label>
                                        </section> 
                                    </div>
                                </fieldset>
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <fieldset style="padding-top: 0px">
                                <div class="row"> 
                                    <section class="col col-sm-12">
                                    <label class="label">Observações</label>
                                    <label class="input">
                                        <textarea class="form-control" rows="4" id="observacao" name="dc_observacao_pro" value="{{old('dc_observacao_pro')}}" >{{old('dc_observacao_pro')}}</textarea>
                                    </label>
                                    </section> 
                                </div>
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <header>
                                <i class="fa fa-money"></i> Honorários
                            </header>
                            <br />
                            <fieldset style="padding-top: 0px">
                                <div class="row"> 
                                    <section class="col col-sm-12">
                                        
                                                <div class="alert alert-info" role="alert">
                                                    <i class="fa-fw fa fa-info"></i>
                                                    <strong>Informação!</strong> Ao selecionar o tipo de serviço, os campos de valor serão preenchidos com os valores padrões cadastrados no Cliente e/ou Correspondente na cidade selecionada, caso os valores existam. Sendo permitida sua mudança.                          
                                                </div>
                                                <div class="">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th style="width: 50%">Tipos de Serviços<span class="text-danger">*</span></th>
                                                            <th style="">Valor Cliente</th>
                                                            <th style="">Valor Correspondente</th>
                                                            <th style="">Nota Fiscal Cliente</th>
                                                        </thead>
                                                        <tbody>  
                                                            <tr>  
                                                                <td>                                       
                                                                    <select id="tipoServico" name="cd_tipo_servico_tse" class="select2">
                                                                        <option selected value="">Selecione um tipo de serviço
                                                                        </option>      
                                                                        @foreach($tiposDeServico as $tipoDeServico)
                                                                            <option  value="{{$tipoDeServico->cd_tipo_servico_tse}}">     {{$tipoDeServico->nm_tipo_servico_tse}}
                                                                            </option>  
                                                                        @endforeach                 
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input style="width: 100px; padding-left: 12px" name="taxa_honorario_cliente"  id="taxa-honorario-cliente" type="text" class="form-control taxa-honorario" value="{{old('taxa_honorario_cliente')}}" >
                                                                        </div>
                                                                        </div>
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">$</span>
                                                                            <input name="taxa_honorario_correspondente" style="width: 100px;padding-left: 12px" id="taxa-honorario-correspondente" type="text" class="form-control taxa-honorario"  value="{{old('taxa_honorario_correspondente')}}" >
                                                                    </div>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">$</span>
                                                                            <input disabled name="nota_fiscal_cliente" style="width: 100px;padding-left: 12px" id="nota_fiscal_cliente" type="text" class="form-control taxa-honorario"  value="{{old('nota_fiscal_cliente')}}" title="Aguardando seleção do Cliente" >
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                           
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                     </section> 
                                </div>
                            </fieldset>
                        </div>

                    </div> 
                     
                          
                        <footer>
                            <button type="submit" class="btn btn-primary">
                                Cadastrar
                            </button>
                        </footer>
                        {!! Form::close() !!}                      
                        
                    </div>
                    <!-- end widget content -->
                    
                </div>
                <!-- end widget div -->
                
            </div>
            </article>
        </div>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {
        var path = "{{ url('autocompleteCliente') }}";
        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_correspondente_cor']").val(ui.item.id);
            $("#taxa-honorario-correspondente").val('');

          },
          open: function(event, ui){
            
          }
        });

        $( "#correspondente_auto_complete" ).focusout(function(){
           if($("input[name='cd_correspondente_cor']").val() == ''){
                $("#correspondente_auto_complete").val('');
           }
        });


        $( "#client" ).focusout(function(){
           if($("input[name='cd_cliente_cli']").val() == ''){
                $("#client").val('');
           }
        });

        $( "#client" ).autocomplete({
          source: path,
          minLength: 2,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);
            $("input[name='nota_fiscal_cliente']").val(ui.item.nota);
            $("input[name='nota_fiscal_cliente']").prop('disabled', false);
            $("#taxa-honorario-cliente").val('');

            buscaAdvogado();
        
          },
          open: function(event, ui){
            
          }
        });
   
        $('#tipoServico').change(function(){

            $("#taxa-honorario-cliente").val('');  
            $("#taxa-honorario-correspondente").val('');  

            var cliente = $("input[name='cd_cliente_cli']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();
            var tipoServico = $(this).val();
            if(cliente != '' && cidade != '' && tipoServico != ''){
                $.ajax({
                        
                        url: '../busca-valor-cliente/'+cliente+'/'+cidade+'/'+tipoServico,
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function(){
                            // $('#cidade').empty();
                            // $('#cidade').append('<option selected value="">Carregando...</option>');
                            // $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                 
                            if(response){
                                var response = JSON.parse(response);  
                                $("#taxa-honorario-cliente").val(response.nu_taxa_the);       
                            }
                        },
                        error: function(response)
                        {
                                //console.log(response);
                        }
                });
            }

            var correspondente = $("input[name='cd_correspondente_cor']").val();
            if(correspondente != '' && cidade != '' && tipoServico != ''){
                
                $.ajax({
                        
                        url: '../busca-valor-correspondente/'+correspondente+'/'+cidade+'/'+tipoServico,
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function(){
                            // $('#cidade').empty();
                            // $('#cidade').append('<option selected value="">Carregando...</option>');
                            // $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                 
                            if(response){
                                var response = JSON.parse(response);  
                                $("#taxa-honorario-correspondente").val(response.nu_taxa_the);       
                            }
                        },
                        error: function(response)
                        {
                                //console.log(response);
                        }
                });         
            }


        });
        
        var buscaAdvogado = function(){

            var cliente = $("input[name='cd_cliente_cli']").val();

            $.ajax({
                    url: '../advogados-por-cliente/'+cliente,
                    type: 'GET',
                    dataType: "JSON",
                    beforeSend: function(){
                        // $('#cidade').empty();
                        // $('#cidade').append('<option selected value="">Carregando...</option>');
                        // $('#cidade').prop( "disabled", true );

                    },
                    success: function(response)
                    {                   
                        
                        $('#cd_contato_cot').empty();
                        $('#cd_contato_cot').append('<option selected value="">Selecione um Advogado Solicitante</option>');
                        $.each(response,function(index,element){

                            if($("#contatoAux").val() != element.cd_contato_cot){
                                $('#cd_contato_cot').append('<option value="'+element.cd_contato_cot+'">'+element.nm_contato_cot+'</option>');                            
                            }else{
                                $('#cd_contato_cot').append('<option selected value="'+element.cd_contato_cot+'">'+element.nm_contato_cot+'</option>');      
                            }
                                
                            });       
                           
                        },
                        error: function(response)
                        {
                            //console.log(response);
                    }
            });

        }

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

        if($("input[name='cd_cliente_cli']").val() != '' && $("input[name='cd_cliente_cli']").val() != null){

            buscaAdvogado();
            $("input[name='nota_fiscal_cliente']").prop('disabled', false);
        }

        buscaCidade();

        $("#estado").change(function(){
           
            buscaCidade(); 

        });

        $(function() {
                // Validation
            var validobj = $("#frm-add-processo").validate({

                    ignore: 'input[type=hidden], .select2-input, .select2-focusser',
                    rules : {
                        nm_cliente_cli : {
                            required: true,
                        },
                        nu_processo_pro : {
                            required: true
                        },
                        cd_tipo_processo_tpo : {
                            required: true
                        },
                        cd_cidade_cde: {
                            required: true
                        }
                       
                        
                    },

                    // Messages for form validation
                    messages : {
                        nm_cliente_cli : {
                            required : 'Campo Cliente é Obrigatório'
                        },
                        nu_processo_pro : {
                            required : 'Campo Nº Processo é Obrigatório'
                        },
                        cd_tipo_processo_tpo : {
                            required : 'Campo Tipo de Processo é Obrigatório'
                        },
                        cd_cidade_cde: {
                            required : 'Campo Cidade é Obrigatório'
                        }
                       
                        
                    },

                    errorPlacement: function (error, element) {
                        var elem = $(element);
                        console.log(elem);
                        if(element.attr("name") == "cd_cidade_cde") {
                            error.appendTo( element.next("span") );
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
                        } else {
                            elem.addClass(errorClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
                        } else {
                            elem.removeClass(errorClass);
                        }
                    }    
                });

            $(document).on("change", ".select2", function () {
                if (!$.isEmptyObject(validobj.submitted)) {
                    validobj.form();
                }
            });

        });

        $('#cidade').select2({}).focus(function () {
            $(this).select2('focus');
        });

    });
   

    
</script>

@endsection

