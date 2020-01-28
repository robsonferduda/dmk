@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Processos <span> > Relatórios</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('processo/relatorios/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data prazo fatal início<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" required >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data prazo fatal fim<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  required >                            
                        </section>
                        <section class="col col-md-2">                                                        
                            <label class="label label-black"></label><br />
                            <select style="width: 100%" name="relatorio" class="form-control" required>
                                <option value="">Relatório</option>
                                <option {{ (\Session::get('relatorio') == 'para-cliente'  ? 'selected' : '') }} value="para-cliente">Para o cliente</option>
                                <option {{ (\Session::get('relatorio') == 'para-todos-clientes'  ? 'selected' : '') }} value="para-todos-clientes">Todos clientes</option>
                            </select>                            
                        </section>     
                        <section class="col col-md-3">                           
                            <label class="label label-black">Cliente<span class="text-danger label-cliente-danger"></span></label></label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_cliente_cli" value="{{(old('cd_cliente_cli') ? old('cd_cliente_cli') : (\Session::get('cliente') ? \Session::get('cliente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_cliente_cli" placeholder="Digite 3 caracteres para busca" type="text" id="cliente_auto_complete" value="{{(old('nm_cliente_cli') ? old('nm_cliente_cli') : (\Session::get('nmCliente') ? \Session::get('nmCliente') : '')) }}" > 
                             <div style="clear: all;"></div>
                            <span id="limpar-cliente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>

                        <section class="col col-md-2">
                            <br />              
                            <input type="checkbox" name="finalizado" id="finalizado" value="S" {{ (\Session::get('finalizado') == 'S'  ? 'checked' : '') }}>  
                            <label class="label label-black">Processos Finalizados</label> 

                        </section> 
                        <section class="col col-md-1">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar </button>
                        </section> 
                                                           
                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Arquivos</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th>Data</th>                    
                                    <th>Nome</th>
                                    <th>Tamanho</th>                                                                                      
                                    <th data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($arquivos as $arquivo)
                                    <tr>
                                        <td>
                                            {{ $arquivo['data'] }}
                                        </td>
                                        <td>
                                            <a href="arquivo/{{$arquivo['nome']}}" >{{ $arquivo['nome'] }}</a>
                                        </td>
                                        <td>
                                            {{ $arquivo['tamanho'].'KB' }}
                                        </td>
                                        <td>
                                            <div style="display: block;padding: 1px 1px 1px 1px">
                                                <button title="Excluir" data-url="reports/{{$arquivo['nome']}}" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i></button>
                                            </div>    
                                        </td>
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

        $("select[name='relatorio']").change(function(){
            if($(this).val() == 'para-cliente'){
                $('.label-cliente-danger').text('*');
                $("input[name='nm_cliente_cli']").attr('required','required');
            }else{
                $('.label-cliente-danger').text(' ');
                $("input[name='nm_cliente_cli']").prop('required',false);
            }
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