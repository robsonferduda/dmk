@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Relatórios</li>
    </ol>
</div>
<div id="content">
    <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Relatórios</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('financeiro/relatorios/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <fieldset>
                        <header>
                            <h2> ENTRADAS E SAÍDAS </h2>
                        </header>
                        <div class="row">
                            <section class="col col-md-6 box_busca_data" style="width: 50%; padding-top: 10px">
                                <section class="col col-md-4">
                                    <h3>Prazo Fatal</h3>
                                </section>

                                <section class="col col-md-4">
                                    <label class="label label-black">Data Inicial</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" >
                                </section>
                                <section class="col col-md-4">                           
                                    <label class="label label-black">Data Final</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  >       
                                </section>
                            </section>
                            <section class="col col-md-6 box_busca_data pull-right" style="width: 49%;padding-top: 10px">
                                <section class="col col-md-4">
                                    <h3>Baixa</h3>
                                </section>

                                <section class="col col-md-4">
                                    <label class="label label-black">Data Inicial</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicioBaixa" value="{{ old('dtInicioBaixa') ? old('dtInicioBaixa') : \Session::get('dtInicioBaixa')}}" >
                                </section>
                                <section class="col col-md-4">                           
                                    <label class="label label-black">Data Final</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFimBaixa" value="{{ old('dtFimBaixa') ? old('dtFimBaixa') : \Session::get('dtFimBaixa')}}" >                            
                                </section>

                            </section>
                        </div>
                        <div class="row">                                  
                            <section class="col col-md-4">           
                                <br />                  
                                <label class="label label-black">Cliente</label><br />
                                <div class="input-group" style="width: 100%">
                                <input type="hidden" name="cd_cliente_cli" value="{{(old('cd_cliente_cli') ? old('cd_cliente_cli') : (\Session::get('cliente') ? \Session::get('cliente') : '')) }}">
                                <input style="width: 100%" class="form-control" name="nm_cliente_cli" placeholder="Digite 3 caracteres para busca" type="text" id="cliente_auto_complete" value="{{(old('nm_cliente_cli') ? old('nm_cliente_cli') : (\Session::get('nmCliente') ? \Session::get('nmCliente') : '')) }}"> 
                                 <div style="clear: all;"></div>
                                <span id="limpar-cliente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                                </div>                        
                            </section> 
                            <section class="col col-md-4"> 
                                <br />                          
                                <label class="label label-black">Correspondente</label><br />
                                <div class="input-group" style="width: 100%">
                                <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                                <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                                 <div style="clear: all;"></div>
                                <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                                </div>                        
                            </section>         
                            <section class="col col-md-3">
                                <br />    
                                <br />                                     
                                <input type="checkbox" name="finalizado" id="finalizado" value="S" {{ (\Session::get('finalizado') == 'S'  ? 'checked' : '') }}>
                                <label class="label label-black">Processos Finalizados</label> 
                            </section>

                        </div>
                    </fieldset>
                    <fieldset>
                        <header>
                            <h2> DESPESAS </h2>
                        </header>
                        <div class="row">
                            <section class="col col-md-6 box_busca_data" style="width: 50%; padding-top: 10px">
                                <section class="col col-md-4">
                                    <h3>Vencimento</h3>
                                </section>

                                <section class="col col-md-4">
                                    <label class="label label-black">Data Inicial</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtLancamentoInicio" value="{{ old('dtLancamentoInicio') ? old('dtLancamentoInicio') : \Session::get('dtLancamentoInicio')}}" >
                                </section>
                                <section class="col col-md-4">                           
                                    <label class="label label-black">Data Final</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtLancamentoFim" value="{{ old('dtLancamentoFim') ? old('dtLancamentoFim') : \Session::get('dtLancamentoFim')}}"  >       
                                </section>
                            </section>
                            <section class="col col-md-6 box_busca_data pull-right" style="width: 49%;padding-top: 10px">
                                <section class="col col-md-4">
                                    <h3>Pagamento</h3>
                                </section>

                                <section class="col col-md-4">
                                    <label class="label label-black">Data Inicial</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtPagamentoInicio" value="{{ old('dtPagamentoInicio') ? old('dtPagamentoInicio') : \Session::get('dtPagamentoInicio')}}" >
                                </section>
                                <section class="col col-md-4">                           
                                    <label class="label label-black">Data Final</label><br />
                                    <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtPagamentoFim" value="{{ old('dtPagamentoFim') ? old('dtPagamentoFim') : \Session::get('dtPagamentoFim')}}" >                            
                                </section>

                            </section>
                        </div>
                    </fieldset>

                    <fieldset>
                        <header>
                            <h2>GERAL</h2>
                        </header>
                        <div class="row">
                            <section class="col col-md-4">
                                <label class="label label-black">Relatório<span class="text-danger">*</span></label><br />
                                <select style="width: 100%" name="relatorio" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option {{ (\Session::get('relatorio') == 'relatorio-por-processo'  ? 'selected' : '') }} value="relatorio-por-processo">Relatório Detalhado</option>
                                    <option {{ (\Session::get('relatorio') == 'relatorio-sumarizado'  ? 'selected' : '') }} value="relatorio-sumarizado">Relatório Sumarizado</option>
                                    
                                </select>                            
                            </section>    
                            <section class="col col-md-3">
                                <br />                                     
                                <input type="radio" name="tipo"  value="P" checked {{ (\Session::get('tipo') == 'P'  ? 'checked' : '') }}>
                                <label class="label label-black">Previsto</label> 

                            </section> 
                            <section class="col col-md-2">
                                <br />                                     
                                <input type="radio" name="tipo"  value="R" {{ (\Session::get('tipo') == 'R'  ? 'checked' : '' ) }}>
                                <label class="label label-black">Realizado</label> 

                            </section>                                     
                        </div>                       
                    </fieldset>
                    
                    <fieldset>
                        <br />
                        <header><h4>Gerar relatório para:</h4></header>
                        <div class="row">
                            
                            <section class="col col-md-2">
                                <br />                                     
                                <input type="checkbox" name="despesas" id="despesas" value="S" {{ (\Session::get('despesas') == 'N'  ? '' : 'checked') }}>
                                <label class="label label-black">Despesas</label> 

                            </section> 
                            <section class="col col-md-2">
                                <br />                                     
                                <input type="checkbox" name="entradas" id="entradas" value="S" {{ (\Session::get('entradas') == 'N'  ? '' : 'checked') }}>
                                <label class="label label-black">Entradas</label> 

                            </section> 
                            <section class="col col-md-2">
                                <br />                                     
                                <input type="checkbox" name="saidas" id="saidas" value="S" {{ (\Session::get('saidas') == 'N'  ? '' : 'checked') }}>
                                <label class="label label-black">Saídas</label> 

                            </section>              
                            <section class="col col-md-1">
                                <br />
                                <button style='float: right;' class="btn btn-primary" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar </button>
                            </section>                  
                        </div>
                    </fieldset>
                    
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
                                            <a href="../../financeiro/balanco/reports/{{$arquivo['nome']}}" >{{ $arquivo['nome'] }}</a>
                                        </td>
                                        <td>
                                            {{ $arquivo['tamanho'].'KB' }}
                                        </td>
                                        <td>
                                            <div style="display: block;padding: 1px 1px 1px 1px">
                                                <button title="Excluir" data-url="../../financeiro/balanco/reports/{{$arquivo['nome']}}" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i></button>
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

        $( "#correspondente_auto_complete" ).focusout(function(){
           if($("input[name='cd_correspondente_cor']").val() == ''){
                $("#correspondente_auto_complete").val('');
           }
        });

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_correspondente_cor']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-correspondente').click(function(){
            $("input[name='cd_correspondente_cor']").val('');
            $("input[name='nm_correspondente_cor']").val('');

        });

    
    });
</script>
@endsection