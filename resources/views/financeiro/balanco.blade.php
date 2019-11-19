@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Financeiro</li>
        <li>Balanço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-money"></i> Financeiro <span> > Balanço</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">              
                <form action="{{ url('financeiro/balanco/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data prazo fatal inicial</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data prazo fatal final</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  >                            
                        </section>
                        <section class="col col-md-4">                                                        
                            <label class="label label-black">Relatório<span class="text-danger">*</span></label><br />
                            <select style="width: 100%" name="relatorio" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option {{ (\Session::get('relatorio') == 'relatorio-por-processo'  ? 'selected' : '') }} value="relatorio-por-processo">Relatório por processo</option>
                                <option {{ (\Session::get('relatorio') == 'relatorio-sumarizado'  ? 'selected' : '') }} value="relatorio-sumarizado">Relatório Sumarizado</option>
                                
                            </select>                            
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

                         <section class="col col-md-2">
                            <label class="label label-black">Data da baixa inicial</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicioBaixa" value="{{ old('dtInicioBaixa') ? old('dtInicioBaixa') : \Session::get('dtInicioBaixa')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data da baixa final</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFimBaixa" value="{{ old('dtFimBaixa') ? old('dtFimBaixa') : \Session::get('dtFimBaixa')}}" >                            
                        </section>

                        <section class="col col-md-4">                           
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>                                                
                    </div>
                    <div class="row">
                        <section class="col col-md-3">
                            <br />                                     
                            <input type="checkbox" name="finalizado" id="finalizado" value="S" {{ (\Session::get('finalizado') == 'S'  ? 'checked' : '') }}>
                            <label class="label label-black">Processos Finalizados</label> 

                        </section> 
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
                                    
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-money"></i> </span>
                    <h2>Balanço</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                      <table class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th></th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>                               
                                <tr>                                    
                                    <td style="font-weight: bold">Despesas</td>
                                    <td>{{ 'R$ '.number_format(\Session::get('despesaTotal'),2,',','') }}</td>
                                </tr>   
                                <tr>                                    
                                    <td style="font-weight: bold">Saídas</td>
                                    <td>{{ 'R$ '.number_format(\Session::get('saidaTotal'),2,',','') }}</td>
                                </tr>   
                                <tr>                                    
                                    <td style="font-weight: bold">Entradas</td>
                                    <td>{{ 'R$ '.number_format(\Session::get('entradaTotal'),2,',','') }}</td>
                                </tr> 
                                <tr>                                    
                                    <td style="font-weight: bold">Saldo</td>
                                    <td>{{ 'R$ '.number_format(\Session::get('total'),2,',','') }}</td>
                                </tr>                               
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