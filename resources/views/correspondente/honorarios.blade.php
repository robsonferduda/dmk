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
                        <span class="text-primary"><i class="fa fa-info-circle"></i> Selecione a cidade e os serviços para adicionar um valor dos honorários</span>
                        <div class="well">
                            <div class="row">

                                <div class="col-sm-12">
                                    
                                    <div class="row">
                                        <div class="col-md-12">  
                                            <form action="{{ url('correspondente/buscar-honorarios/'.$cliente->cd_conta_con) }}" class="smart-form'" method="GET" role="search">
                                                {{ csrf_field() }} 
                                                <input type="hidden" name="cd_correspondente" id="cd_correspondente" value="{{ $cliente->cd_conta_con }}">
                                                <input type="hidden" name="cd_entidade" id="cd_entidade" value="{{ $cliente->entidade->cd_entidade_ete }}">

                                                <fieldset>
                                                    <div class="row"> 

                                                        <section class="col col-md-2">                                       
                                                            <label class="label label-black" >Estado</label>          
                                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                                <option selected value="">Estado</option>
                                                                @foreach(App\Estado::all() as $estado) 
                                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                                @endforeach

                                                            </select> 
                                                        </section>

                                                        <section class="col col-md-4">
                                                           <label class="label label-black" >Cidades (informadas pelo correspondente)</label>          
                                                            <select id="cidade" name="cd_cidade_cde" class="select2">
                                                               <option selected value="">Selecione uma cidade</option>
                                                            </select> 
                                                        </section> 

                                                        <section class="col col-md-3">
                                                           <label class="label label-black" >Tipos de Serviço</label>          
                                                            <select name="servico" class="form-control">
                                                                <option value="">Selecione um serviço</option>
                                                                <option value="0">Todos</option>
                                                                @foreach($servicos as $servico)
                                                                    <option value="{{ $servico->cd_tipo_servico_tse }}">{{ $servico->nm_tipo_servico_tse }}</option>
                                                                @endforeach
                                                            </select>
                                                        </section> 

                                                        <section class="col col-md-2">
                                                            <label class="label label-black">Ordem</label>          
                                                            <select  id="cidade" name="organizar" class="form-control">
                                                               <option value="0">Selecione</option>
                                                               <option value="1">Serviços</option>
                                                               <option value="2">Cidades</option>
                                                            </select> 
                                                        </section> 

                                                        <section class="col col-md-1">
                                                            <label class="label">Adicionar</label>          
                                                            <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> </button>
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
                                                <button class="btn btn-success pull-right header-btn" id="btnSalvarHonorariosCorrespondente" style="margin-right: -12px;"><i class="fa fa-save fa-lg"></i> Salvar Alterações</button>

                                                <a href="{{ url('correspondente/limpar-selecao/'.$cliente->cd_conta_con) }}" class="btn btn-warning pull-right header-btn" style="margin-right: 5px;"><i class="fa fa-eraser fa-lg"></i> Limpar Seleção</a>
                                            </div> 
                                            
                                                @if($organizar == 1)
                                                    <div class="tabelah">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tipo de Serviço</th>
                                                                    @foreach($cidades as $cidade)
                                                                        <th>{{ $cidade->nm_cidade_cde }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($lista_servicos as $servico)
                                                                    <tr>
                                                                        <td>{{ $servico->nm_tipo_servico_tse }}</td>
                                                                        @foreach($cidades as $cidade)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                    <div class="input-group">
                                                                                        <span class="input-group-addon">$</span>
                                                                                        <input type="text" class="form-control taxa-honorario" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" value="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">
                                                                                        <div class="input-group-btn">
                                                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false">
                                                                                                <span class="caret"></span>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                                                 <li><a class="toda-cidade" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}"  data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor para todas as cidades</a></li>
                                                                                                <li><a class="todo-servico" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}"  data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor para todos os serviços</a></li>
                                                                                                <li><a class="toda-tabela" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}"  data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor em toda tabela</a></li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
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
                                                                        <th>{{ $servico->nm_tipo_servico_tse }}</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($cidades as $cidade)
                                                                    <tr>
                                                                        <td>{{ $cidade->nm_cidade_cde  }}</td>
                                                                        @foreach($lista_servicos as $servico)
                                                                            <td>
                                                                                <div class="col-sm-12">
                                                                                    <div class="input-group">
                                                                                        <span class="input-group-addon">$</span>
                                                                                        <input type="text" class="form-control taxa-honorario" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" value="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">
                                                                                        <div class="input-group-btn">
                                                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false">
                                                                                                <span class="caret"></span>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                                                <li><a class="toda-cidade" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}" data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor para todas as cidades</a></li>
                                                                                                <li><a class="todo-servico" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}"  data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor para todos os serviços</a></li>
                                                                                                <li><a class="toda-tabela" data-cidade="{{ $cidade->cd_cidade_cde }}" data-servico="{{ $servico->cd_tipo_servico_tse }}"  data-valor="{{ (!empty($valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse])) ? $valores[$cidade->cd_cidade_cde][$servico->cd_tipo_servico_tse] : '' }}">Repetir valor em toda tabela</a></li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
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