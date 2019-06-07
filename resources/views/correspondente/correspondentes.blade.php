@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Listar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondente/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('correspondente/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="row"> 
                            <section class="col col-md-3">                                       
                                <label class="label label-black" >Estado</label>          
                                <select  id="estado" name="cd_estado_est" class="select2">
                                    <option selected value="">Selecione um estado</option>
                                    @foreach(App\Estado::all() as $estado) 
                                        <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                    @endforeach
                                </select>
                            </section>
                            <section class="col col-md-3">
                                <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                <label class="label label-black" >Cidade</label>          
                                <select id="cidade" name="cd_cidade_cde" class="select2">
                                    <option selected value="">Selecione uma cidade</option>
                                </select> 
                            </section> 
                            <section class="col col-md-2">
                                <label class="label label-black">CPF/CNPJ</label>
                                <input type="text" style="width: 100%;" name="identificacao" class="form-control" id="Nome" placeholder="CPF/CNPJ">
                            </section>
                            <section class="col col-md-3">
                                <label class="label label-black" >Nome</label><br>
                                <input type="text" style="width: 100%;" name="nome" class="form-control" id="Nome" placeholder="Nome">
                            </section>
                            <section class="col col-md-1">
                                <label class="label" >Buscar</label>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                            </section>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Correspondentes</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if(isset($correspondetes))
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>    
                                    <th style="width: 20%;">Comarca de Origem</th> 
                                    <th style="width: 15%;">CPF/CNPJ</th>                                                                   
                                    <th style="width: 30%;">Nome</th>
                                    <th style="width: 20%;" class="center">Email</th>                                  
                                    <th style="width: 15%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($correspondetes as $correspondente)
                                    <tr>
                                        <td>{!! ($correspondente->entidade->atuacao) ? $correspondente->entidade->atuacao->cidade->nm_cidade_cde : '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td>{!! ($correspondente->entidade->identificacao) ? $correspondente->entidade->identificacao->nu_identificacao_ide : '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td>{{ $correspondente->nm_razao_social_con }}</td>
                                        <td>{!! ($correspondente->entidade->usuario) ? $correspondente->entidade->usuario->email: '<span class="text-danger">Não informado</span>' !!}</td>
                                        <td class="center">
                                            <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('correspondente/detalhes/'.$correspondente->contaCorrespondente->cd_correspondente_cor) }}"><i class="fa fa-file-text-o"></i> </a>

                                            <a title="Honorários" class="btn btn-warning btn-xs"  href="{{ url('correspondente/honorarios/'.$correspondente->contaCorrespondente->cd_correspondente_cor) }}"><i class="fa fa-money"></i> </a>

                                            <a title="Despesas" class="btn btn-info btn-xs" href="{{ url('correspondente/despesas/'.$correspondente->contaCorrespondente->cd_correspondente_cor) }}"><i class="fa fa-dollar"></i> </a>

                                            <button title="Excluir" class="btn btn-danger btn-xs remover_registro" data-url="{{ url('correspondente/excluir/'.$correspondente->cd_conta_correspondente_ccr) }}" data-id="{{ $correspondente->contaCorrespondente->cd_conta_correspondente_ccr }}"><i class="fa fa-trash"></i> </button> 
                                        </td>
                                    </tr>
                                @endforeach                                                           
                            </tbody>
                        </table>
                        @else
                            <h5 class="center marginTop20"><i class="fa fa-info-circle"></i> Selecione os termos da sua busca e clique em <strong>Buscar</strong></h5>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {

        var _location = document.location.toString();
        var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
        var applicationName = _location.substring(0, applicationNameIndex) + '/';
        var webFolderIndex = _location.indexOf('/', _location.indexOf(applicationName) + applicationName.length);
        var pathname = _location.substring(0, webFolderIndex);

        var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: pathname+'/cidades-por-estado/'+estado,
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