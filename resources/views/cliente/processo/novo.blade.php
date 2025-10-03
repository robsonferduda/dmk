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
        <div class="hidden-xs col-sm-7 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o la-lg"></i> Processos <span>> Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-6 col-lg-6 box-button-xs">
            <div class="boxBtnTopo sub-box-button-xs">
                <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            </div>
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
                        
                        {!! Form::open(['id' => 'frm-add-processo', 'url' => 'processo-cliente', 'class' => 'smart-form']) !!}
                         <header>
                            <i class="fa fa-file-text-o"></i> Dados do Processo <span class="text-danger">(*) Campos Obrigatórios</span>
                        </header>
                        <div class="row">
                            <div  class="col col-xs-12 col-sm-6">
                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-xs-12 col-sm-12">
                                            <input type="hidden" name="cd_cliente_cli" id="cd_cliente_cli" value="{{ $cliente->cd_cliente_cli }}" readonly>
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input required name="nm_cliente_cli" 
                                                value="{{ $cliente->nm_razao_social_cli }}" 
                                                class="form-control ui-autocomplete-input" 
                                                placeholder="Digite 3 caracteres para busca" 
                                                type="text" 
                                                id="client" 
                                                autocomplete="off">
                                            </label>
                                        </section>
                                    </div> 
                                    <div class="row">
                                        <section class="col col-xs-12 col-lg-4">
                                            <label class="label">Código Cliente <a href="#" rel="popover-hover" data-placement="top" data-original-title="Número ou código de acompanhamento externo."><i class="fa fa-question-circle text-primary"></i></a></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_acompanhamento_pro')}}" type="text" name="nu_acompanhamento_pro" maxlength="50">
                                            </label>
                                        </section> 
                                        <section class="col col-xs-12 col-lg-8">                                       
                                            <label class="label" >Advogado Solicitante <a href="#" data-toggle="modal" data-target="#novoAdvogado" style="padding: 1px 8px;"><i class="fa fa-plus-circle"></i> Novo </a></label> 
                                            <label class="select">
                                                <input type="hidden" id="contatoAux"  value="{{old('cd_contato_cot')}}">
                                                <select  id="cd_contato_cot" name="cd_contato_cot" >
                                                    <option value="">Selecione um Advogado Solicitante</option>   
                                                    
                                                </select><i></i>  
                                            </label>         
                                        </section>
                                    </div>
                                    <div class="row">
                                         <section class="col col-xs-12 col-lg-6">
                                            <label class="label">Nº Processo<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_processo_pro')}}" type="text" name="nu_processo_pro" required>
                                            </label>
                                        </section> 
                                         <section class="col col-xs-12 col-lg-6" >                                       
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
                                        <section class="col col-xs-12 col-sm-12">       
                                            <label class="label" >Vara</label>          
                                            <select  name="cd_vara_var" class="select2">
                                                <option selected value="">Selecione uma vara</option>
                                                @foreach($varas as $vara) 
                                                    <option {!! (old('cd_vara_var') == $vara->cd_vara_var ? 'selected' : '' ) !!} value="{{$vara->cd_vara_var}}">{{ $vara->nm_vara_var}}</option>
                                                @endforeach
    
                                            </select> 
                                        </section>
                                    </div> 
                                    
                                    <div class="row">
                    
                                        <section class="col col-xs-12 col-lg-6">
                                           
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione um estado</option>
                                                @foreach($estados as $estado) 
                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach

                                            </select> 
                                        </section>
                                        <section class="col col-xs-12 col-lg-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                           <label class="label" >Cidade<span class="text-danger">*</span></label>          
                                            <select  id="cidade"  name="cd_cidade_cde" class="select2" required>
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div> 

                                   

                                </fieldset>
                            </div>
                        <div class="col col-xs-12 col-sm-6">
                           
                            <fieldset>
                                <div class="row">
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Data da Solicitação</label>
                                        <label class="input">
                                           <input class="dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{old('dt_solicitacao_pro')}}">
                                        </label>
                                    </section> 
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Data Prazo Fatal<span class="text-danger">*</span></label>
                                        <label class="input">
                                           <input class="dt_prazo_fatal_pro" placeholder="___ /___ /___" type="text" name="dt_prazo_fatal_pro" value="{{old('dt_prazo_fatal_pro')}}" required>
                                        </label>
                                    </section> 
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Hora da Audiência</label>
                                        <label class="input">
                                           <input class="hr_audiencia_pro" placeholder="___ : ___" type="text" name="hr_audiencia_pro" value="{{old('hr_audiencia_pro')}}" >
                                        </label>
                                    </section> 
                                </div>    

                                <div class="row">
                                    <section class="col col-xs-12">
                                        <label class="label">Autor<span class="text-danger">*</span></label>
                                        <label class="input">
                                            <input class="form-control" maxlength="500" placeholder="" type="text" name="nm_autor_pro" oninput="this.value = this.value.toUpperCase();" required value="{{old('nm_autor_pro')}}">
                                        </label>
                                    </section> 
                                </div>    

                                <div class="row"> 
                                    <section class="col col-xs-12 col-sm-12">
                                       <label class="label">Réu<span class="text-danger">*</span></label>
                                       <label class="input">
                                          <input class="form-control" placeholder="" maxlength="500" type="text" name="nm_reu_pro" oninput="this.value = this.value.toUpperCase();" required value="{{old('nm_reu_pro')}}" >
                                       </label>
                                   </section> 
                                </div>
                                
                                <div class="row">    
                                    <section class="col col-xs-12 col-sm-12">
                                            <input type="hidden" name="cd_correspondente_cor" id="cd_correspondente_cor" value="{{ $correspondente->cd_conta_con }}" readonly>
                                            <label class="label">Correspondente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input required name="nm_correspondente_cor" 
                                                value="{{ $correspondente->nm_razao_social_con }}" 
                                                class="form-control ui-autocomplete-input" 
                                                placeholder="Digite 3 caracteres para busca" 
                                                type="text" 
                                                id="client" 
                                                autocomplete="off">
                                            </label>
                                        </section>
                                </div> 
                                
                                <div class="row">    
                                    <section class="col col-xs-12 col-sm-12">
                                        <label class="label">Tipo de Serviço<span class="text-danger">*</span></label>
                                        <select id="tipoServico" name="cd_tipo_servico_tse" class="select2" disabled>
                                            <option selected value="">Selecione um cliente e cidade</option>                          
                                        </select>
                                    </section>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <header>
                                <i class="fa fa-legal"></i> Dados da Audiência
                            </header>
                            <fieldset>
                                <div class="row" style=""> 
                                    <section class="col col-md-12 col-lg-12">
                                        <label class="label">Link da Audiência</label>
                                        <label class="input">
                                            <input type="text" class="form-control" placeholder="Link da Audiência" name="ds_link_audiencia_pro" id="ds_link_audiencia_pro">
                                        </label>
                                    </section> 
                                </div>
                                <div class="row" style="margin: 0px 0px 0px -10px !important;"> 
                                    <section class="col col-sm-6">
                                        <label class="label">Advogado</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_advogado_pro" oninput="this.value = this.value.toUpperCase();">{{ old('nm_advogado_pro') }}</textarea>
                                        </label>
                                    </section> 

                                    <section class="col col-sm-6">
                                        <label class="label">Preposto</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_preposto_pro" oninput="this.value = this.value.toUpperCase();">{{ old('nm_preposto_pro') }}</textarea>
                                        </label>
                                    </section>                               
                                </div>
                            </fieldset>
                        </div>
                    </div> 
                     
                        <footer>                            
                            <button type="submit" class="btn btn-success">
                                <i class="fa-fw fa fa-save"></i>Cadastrar
                            </button>
                            <a href="{{ url('processos') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
<div class="modal fade modal_top_alto" id="novoAdvogado" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-legal"></i> Adicionar Advogado Solicitante
                </h4>
            </div>
            <div class="modal-body">
                <div class="row box-cadastro">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Nome Completo</strong></label>
                            <input type="text" class="form-control" placeholder="Nome Completo" oninput="this.value = this.value.toUpperCase();" required="required" name="nome_advogado_solicitante" id="nome_advogado_solicitante">
                            <div id="msg_error_advogado" class="text-danger"></div>
                        </div>    
                    </div>
                    <div style="padding: 5px 15px;">
                        <p>O Advogado será adicinado para o Escritório responsável pelo processo.</p>
                    </div>
                </div>
                <div class="row box-erro-cadastro" style="display: none">
                    <div class="col-md-12 center">
                        <p class="text-danger">Obrigatório selecionar o cliente para habilitar o cadastro de Advogado Solicitante.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                <button type="button" class="btn btn-success" id="btnSalvarAdvogadoSolicitante"><i class="fa-fw fa fa-save"></i> Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');

            buscaAdvogado();

            $("#estado").change(function(){           
                buscaCidade(); 
            });

            $('#cidade').change(function(){
            
                var cliente = $("input[name='cd_cliente_cli']").val();
                var cidade = $("select[name='cd_cidade_cde']").val();
                var estado = $("select[name='cd_cidade_cde']").val();

                if(cliente != '' && cidade != ''){
                    buscaTiposServico(cliente,cidade);
                }                

            });

            $('#novoAdvogado').on('shown.bs.modal', function () {

                if($("#cd_cliente_cli").val()){

                    $(".box-cadastro").css('display','block');
                    $(".box-erro-cadastro").css('display','none');
                    
                }else{
                    $(".box-erro-cadastro").css('display','block');
                    $(".box-cadastro").css('display','none');
                }

                $("#msg_error_advogado").html("");
                $("#nome_advogado_solicitante").trigger('focus');
            })

            $("#btnSalvarAdvogadoSolicitante").click(function(){

                var nome = $("#nome_advogado_solicitante").val();
                var cliente = $("#cd_cliente_cli").val();

                if(nome == ""){
                    $("#msg_error_advogado").html("<span>Campo nome completo é obrigatório.</span>");
                    $("#nome_advogado_solicitante").trigger('focus');
                }else{
                    $("#msg_error_advogado span").remove();
                
                    $.ajax(
                    {
                        type: "POST",
                        url: host+"/cliente/advogado",
                        data: {
                            "_token": $('meta[name="token"]').attr('content'),
                            "cliente": cliente,
                            "nome_advogado_solicitante": nome
                        },
                        beforeSend: function()
                        {
                            //$("#processamento").modal('show');
                        },
                        success: function(response)
                        {
                            buscaAdvogado(); 
                            $("#cd_contato_cot").val(response.id);
                            $("#novoAdvogado").modal('hide');
                        },
                        error: function(response)
                        {
                            
                            //location.reload();
                        }
                    });
                }

            });

            function buscaTiposServico (cliente,cidade){

                $.ajax(
                {
                    url: host+"/tipos-de-servico/cliente/"+cliente+"/cidade/"+cidade,
                    type: 'GET',
                    dataType: "JSON",
                    success: function(response)
                    {              
                        
                        $('#tipoServico').empty();
                        $('#tipoServico').append('<option selected value="">Selecione um tipo de serviço</option>');
                        $.each(response,function(index,element){
                            if($("#cd_tipo_servico_tse_aux").val() != element.tipo_servico.cd_tipo_servico_tse){
                                $('#tipoServico').append('<option value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');                            
                            }else{
                                $('#tipoServico').append('<option selected value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');      
                            }
                                        
                        });       
              
                        $('#tipoServico').prop( "disabled", false );        

                    },
                    error: function(response)
                    {
                    }
                });
            }

            function buscaAdvogado(){

                var cliente = $("input[name='cd_cliente_cli']").val();

                $.ajax({
                        url: host+'/advogados-por-cliente/'+cliente,
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
                            $('#cd_contato_cot').append('<option value="">Selecione um Advogado Solicitante</option>');
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
                            url: host+'/cidades-por-estado/'+estado,
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

        });
    </script>
@endsection