@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Importar</li>
        <li>Códigos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Processos <span> > Importar </span><span> > Códigos </span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
               <div class="row">
                    <section class="col col-md-5">
                        <div class="form-group">
                            <label class="label label-black" >Cliente</label>          
                            <select class="select2" name="cliente" data-input="#codigo-cliente" >
                                 <option selected value="" >Selecione um Cliente</option>
                                @foreach($clientes as $cliente)
                                    <option data-id="{{ $cliente->cd_cliente_cli }}" value="{{ $cliente->nu_cliente_cli }}">{{ $cliente->nm_razao_social_cli }}</option>                              
                                @endforeach
                            </select>                               
                        </div>  
                    </section>      
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código do Cliente</label>  
                            <input type="text" id="codigo-cliente"  name="codigo" class="form-control codigo-cliente" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-cliente"><i class="fa fa-copy"></i> Copiar</button>                    
                </div>  
                <div class="row">
                    <section class="col col-md-5">
                        <div class="form-group">
                            <label class="label label-black" >Advogado Solicitante</label>          
                            <select class="select2"  id="advogado" name="advogado" data-input="#codigo-advogado">
                                <option value="">Selecione um Advogado Solicitante</option>            
                            </select><i></i>                            
                        </div>  
                    </section>      
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código do Advogado</label>  
                            <input type="text" id="codigo-advogado"  name="codigo" class="form-control codigo-advogado" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-advogado"><i class="fa fa-copy"></i> Copiar</button>                    
                </div>  
                <div class="row">
                    <section class="col col-md-5">
                        <div class="form-group">
                            <label class="label label-black" >Vara</label>          
                            <select class="select2" name="vara" data-input="#codigo-vara">
                                 <option selected value="" >Selecione uma Vara</option>
                                @foreach($varas as $vara)
                                    <option value="{{ $vara->nu_vara_var }}">{{ $vara->nm_vara_var }}</option>                              
                                @endforeach
                            </select>                               
                        </div>  
                    </section>      
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código da Vara</label>  
                            <input type="text" id="codigo-vara"  name="codigo" class="form-control codigo" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-vara"><i class="fa fa-copy"></i> Copiar</button>                    
                </div> 
                <div class="row">
                    <section class="col col-md-5">
                        <div class="form-group">
                            <label class="label label-black" >Tipo de Serviço</label>          
                            <select class="select2" name="tipo" data-input="#codigo-tipo">
                                 <option selected value="" >Selecione um Tipo de Serviço</option>
                                @foreach($tiposServico as $tipoServico)
                                    <option value="{{ $tipoServico->nu_tipo_servico_tse }}">{{ $tipoServico->nm_tipo_servico_tse }}</option>                          
                                @endforeach
                            </select>                               
                        </div>  
                    </section>      
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código do Tipo de Serviço</label>  
                            <input type="text" id="codigo-tipo"  name="codigo" class="form-control codigo" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-tipo"><i class="fa fa-copy"></i> Copiar</button>                    
                </div>  
                <div class="row">
                    <section class="col col-md-5">
                        <div class="form-group">
                            <label class="label label-black" >Tipo de Processo</label>          
                            <select class="select2" name="tipo-processo" data-input="#codigo-tipo-processo">
                                 <option selected value="" >Selecione um Tipo de Processo</option>
                                @foreach($tiposProcesso as $tipoProcesso)
                                    <option value="{{ $tipoProcesso->nu_tipo_processo_tpo }}">{{ $tipoProcesso->nm_tipo_processo_tpo }}</option>                          
                                @endforeach
                            </select>                               
                        </div>  
                    </section>      
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código do Tipo de Processo</label>  
                            <input type="text" id="codigo-tipo-processo"  name="codigo" class="form-control codigo" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-tipo-processo"><i class="fa fa-copy"></i> Copiar</button>                    
                </div>
                <div class="row">
                    <section class="col col-md-3">
                        <div class="form-group">
                            <label class="label label-black" >Estado</label>           
                            <select  id="estado" name="estado" class="select2">
                                <option selected value="">Selecione um estado</option>
                                @foreach($estados as $estado) 
                                    <option value="{{ $estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                @endforeach
                            </select>                          
                        </div>  
                    </section>      
                    <section class="col col-md-3">
                        <div class="form-group">
                            <label class="label label-black" >Cidade</label>             
                            <select  id="cidade"  name="cidade" class="select2" data-input="#codigo-comarca">
                                <option selected value="">Selecione uma Cidade</option>
                            </select>                  
                        </div>  
                    </section>     
                    <section class="col col-md-2">
                        <div class="form-group">
                            <label class="label label-black" >Código da Comarca</label>  
                            <input type="text" id="codigo-comarca"  name="codigo" class="form-control codigo" style="text-align: center;font-weight: bold">                               
                        </div>                                                
                    </section>
                    <br/>
                    <button class="btn btn-primary btn-sm btn-codigo" type="button" data-id="#codigo-comarca"><i class="fa fa-copy"></i> Copiar</button>                    
                </div>           
            </div>
        </article>
    </div>
</div>      
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $("select").change(function(){               
                $($(this).data('input')).val($(this).val());                
            });            

            $(".btn-codigo").click(function(){               
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($($(this).data('id')).val());
                $($(this).data('id')).select();
                document.execCommand("copy");
                $temp.remove();
            });

            $("select[name='cliente']").change(function(){               
                buscaAdvogado();              
            });  


            var buscaAdvogado = function(){
        
                var cliente = $("select[name='cliente'] option:selected").data('id');

                $.ajax({
                        url: '../../advogados-por-cliente/'+cliente,
                        type: 'GET',
                        dataType: "JSON",                        
                        success: function(response)
                        {                   
                            console.log(response);

                            $('#advogado').empty();
                            $('#advogado').append('<option value="">Selecione um Advogado Solicitante</option>');
                            $.each(response,function(index,element){
                                $('#advogado').append('<option value="'+element.nu_contato_cot+'">'+element.nm_contato_cot+'</option>');                                                            
                            });       
                            $('#advogado').trigger('change');     
                        },
                            error: function(response)
                            {
                                //console.log(response);
                    }
                });

            }

            $("#estado").change(function(){    
                buscaCidade(); 
            });

            var buscaCidade = function(){

                estado = $("#estado").val();

                if(estado != ''){

                    $.ajax(
                        {
                            url: '../../cidades-por-estado/'+estado,
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
        });        
    </script>
@endsection