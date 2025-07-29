@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes 
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div style="margin-left: 15px; margin-right: 15px;">
                <div class="alert alert-danger fade in">
                    <i class="fa-fw fa fa-times"></i>
                    <strong>Atenção!</strong> Os dados apresentados aqui são somente teste. Essa tela depende do cadastro de correspondentes para ser utilizada.
                </div>
            </div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well" style="margin-left: 15px; margin-right: 15px;">
                <form action="{{ url('correspondente/todos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-3">                                       
                                <label class="label label-black" >Estado</label>          
                                <select  id="pai_cidade_atuacao" name="cd_estado_est" class="select2 estado">
                                    <option selected value="">Selecione um estado</option>
                                    @foreach(App\Estado::orderBy('nm_estado_est')->get() as $estado) 
                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                    @endforeach
                                </select>
                            </section>
                            <section class="col col-md-4">
                                <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                <label class="label label-black" >Cidade</label>          
                                <select id="cidade" name="cd_cidade_cde" class="select2 pai_cidade_atuacao">
                                    <option selected value="">Selecione uma cidade</option>
                                </select> 
                            </section> 
                            <section class="col col-md-5">
                                <label class="label label-black">Nome</label><br>
                                <input type="text" name="nome" style="width: 75%" class="form-control" id="Nome" placeholder="Nome">
                            
                                <button class="btn btn-primary" style="width: 20%"  type="submit"><i class="fa fa-search"></i> Buscar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
        z
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() { 

        var buscaCidade = function(estado,target){

            if(estado != ''){

                $.ajax(
                    {
                        url: '../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Carregando...</option>');
                            $('.'+target).prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('.'+target).empty();
                            $('.'+target).append('<option selected value="">Selecione</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('.'+target).append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('.'+target).append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('.'+target).trigger('change');     
                            $('.'+target).prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                    });
            }
        }

        $(".estado").change(function(){
            buscaCidade($(this).val(),$(this).attr('id')); 
        });     

    });
</script>
@endsection