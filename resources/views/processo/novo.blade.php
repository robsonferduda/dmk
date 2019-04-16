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
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Usuários <span>> Novo</span>
            </h1>
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
                    <h2>Cadastro de Usuário </h2>             
                    
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
                        
                        {!! Form::open(['id' => 'frm-add-usuario', 'url' => 'usuarios', 'class' => 'smart-form']) !!}
                        <div class="row">
                            <div  class="col col-6">
                                <header>
                                    <i class="fa fa-user"></i> Dados do Processo
                                </header>

                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-sm-12">
                                            <input type="hidden" name="cd_cliente_cli">
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control ui-autocomplete-input" placeholder="Cliente..." type="text" id="client" autocomplete="off">
                                            </label>
                                        </section>
                                    </div> 
                                    <div class="row">
                                        <section class="col col-sm-12">                                       
                                            <label class="label" >Advogado Solicitante</label> 
                                            <label class="select">
                                                <select  id="cd_contato_cot" name="cd_contato_cot" >
                                                    <option selected value=""></option>            
                                                </select><i></i>  
                                            </label>         
                                        </section>
                                    </div>
                                    <div class="row">
                                         <section class="col col-6">
                                            <label class="label">Nº Processo<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control" placeholder="" type="text" id="client" >
                                            </label>
                                        </section> 
                                         <section class="col col-6">                                       
                                            <label class="label" >Tipo de Processo</label>          
                                            <select  id="" name="" class="select2">
                                                <option selected value=""></option>            
                                            </select> 
                                        </section>
                                    </div>                                                     <div class="row">
                                        <section class="col col-sm-12">
                                            <label class="label">Autor</label>
                                            <label class="input">
                                                <input class="form-control" placeholder="" type="text" id="client" >
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
                                           <label class="label" >Cidade</label>          
                                            <select  id="cidade" disabled name="cd_cidade_cde" class="select2">
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div>         
                                     <div class="row">                        
                                        <section class="col col-sm-12">
                                            <label class="label">Correspondente</label>
                                            <label class="input">
                                                <input class="form-control" placeholder="Correspondente..." type="text" id="client">
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
                                        <label class="label">Data da Audiência</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text" id="client" >
                                        </label>
                                    </section> 
                                    <section class="col col-4">
                                        <label class="label">Hora da Audiência</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text" id="client" >
                                        </label>
                                    </section> 
                                     <section class="col col-4">
                                        <label class="label">Data Prazo Fatal</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text" id="client" >
                                        </label>
                                    </section> 
                                </div>    
                                <div class="row"> 
                                     <section class="col col-sm-12">
                                        <label class="label">Réu</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text" id="client" >
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
                                               <input class="form-control" placeholder="" type="text" id="client" >
                                            </label>
                                        </section> 
                                    </div>
                                    <div class="row"> 
                                         <section class="col col-sm-12">
                                            <label class="label">Advogado</label>
                                            <label class="input">
                                               <input class="form-control" placeholder="" type="text" id="client" >
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
                                        <textarea class="form-control" rows="4"></textarea>
                                    </label>
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
        $( "#client" ).autocomplete({
          source: path,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);

                $.ajax({
                    url: '../advogados-por-cliente/'+ui.item.id,
                    type: 'GET',
                    dataType: "JSON",
                    beforeSend: function(){
                        // $('#cidade').empty();
                        // $('#cidade').append('<option selected value="">Carregando...</option>');
                        // $('#cidade').prop( "disabled", true );

                    },
                    success: function(response)
                    {                   

                        console.log(response); 
                        // $('#cidade').empty();
                        // $('#cidade').append('<option selected value="">Selecione</option>');
                        $.each(response,function(index,element){

                            if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                            }else{
                                    $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                            }
                                
                            });       
                           
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                });


          }
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

        buscaCidade();

        $("#estado").change(function(){
           
            buscaCidade(); 

        });


    });
   

    
</script>

@endsection

