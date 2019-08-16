@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Correspondentes <span> > Relatórios</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('financeiro/entrada/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data de Início</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" required >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data Fim</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  required >                            
                        </section>

                         <section class="col col-md-4">                           
                            <label class="label label-black">Cliente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_cliente_cli" value="{{(old('cd_cliente_cli') ? old('cd_cliente_cli') : (\Session::get('cliente') ? \Session::get('cliente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_cliente_cli" placeholder="Digite 3 caracteres para busca" type="text" id="cliente_auto_complete" value="{{(old('nm_cliente_cli') ? old('nm_cliente_cli') : (\Session::get('nmCliente') ? \Session::get('nmCliente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-cliente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                                                    
                    </div>
                    <div class="row">                                   
                        <section class="col col-md-3">
                            <br />
                            <button class="btn btn-default" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar </button>
                        </section>                                   
                    </div>
                    <div class="row">

                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Entradas</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th>Nº Processo</th>                    
                                    <th>Prazo Fatal</th>
                                    <th>Tipo de Serviço</th>    
                                    <th>Cliente</th>
                                    <th>Valor</th>  
                                    <th></th>                                                                                                
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($entradas as $entrada)
                                <tr {{ ($entrada->fl_pago_cliente_pth == 'N') ? 'style=background-color:#fb8e7e' : '' }} >
                                    <td>{{ $entrada->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($entrada->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($entrada->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $entrada->tipoServico->nm_tipo_servico_tse }}</td>
                                    <td>{{ $entrada->processo->cliente->nm_razao_social_cli }}</td>
                                    <td>{{ $entrada->vl_taxa_honorario_cliente_pth }}</td>
                                    <td><input type="checkbox" class="check-pagamento-cliente" data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}' ></td>                              
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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


        $(".check-pagamento-cliente").click(function(){

            var input = $(this);
            var id = $(this).data('id');
            if ($(this).is(':checked') ) {
                var checked = 'S';            
            }else {
                var checked = 'N';
            }

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/cliente/baixa') }}",
                data:{id:id,checked:checked},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            input.closest('tr').css('background-color','#8ec9bb');
                        }else{
                            input.closest('tr').css('background-color','#fb8e7e');
                        }
                    }                                               
                }
            });
            
        });

        $( "#cliente_auto_complete" ).focusout(function(){
           if($("input[name='cd_cliente_cli']").val() == ''){
                $("#cliente_auto_complete").val('');
           }
        });

        var pathCliente = "{{ url('autocompleteCliente') }}";

        $( "#cliente_auto_complete" ).autocomplete({
          source: pathCliente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-cliente').click(function(){
            $("input[name='cd_cliente_cli']").val('');
            $("input[name='nm_cliente_cli']").val('');

        });
    
    });
</script>

@endsection