@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Painel</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Painel</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondente/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <div class="row"> 
                    <section class="col col-md-12"> 
                        <span>PAINEL DE CORRESPONDENTES POR ESTADO</span><hr/>
                    </section>
                    <section class="col col-md-3">                                               
                        <select  id="estado" name="cd_estado_est" class="select2">
                            <option selected value="">SELECIONE UM ESTADO</option>
                                @foreach(App\Estado::all() as $estado) 
                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                @endforeach
                        </select>
                    </section>
                </div>
                <div class="row msg-total" style="margin-top: 15px; padding-left: 15px;"> 
                    
                </div>
                <div class="row cidades-honorarios" style="margin-top: 15px;"> 
                    
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        var buscaCidade = function(){

            estado = $("#estado").val();
            total = 0;

            if(estado != ''){

                $.ajax(
                    {
                        url: '../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $(".cidades-honorarios").empty();
                            $(".cidades-honorarios").append('<div class="center" style="width: 100%; margin: 50px auto;"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>');
                        },
                        success: function(response)
                        {   
                            total = 0;            
                            $(".cidades-honorarios").empty();
                            $.each(response,function(index,element){

                                $(".cidades-honorarios").append('<div class="col-md-2 demo-icon-font"><i class="fa fa-map-marker"></i> '+element.nm_cidade_cde+'</div>');
                                total ++;
                               
                                
                            });
                            $(".msg-total").html('<span>Você possui correspondentes em <strong>'+total+'</strong> cidades deste estado</span>');     
                                 
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