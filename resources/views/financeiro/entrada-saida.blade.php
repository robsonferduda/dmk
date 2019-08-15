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
                <form action="{{ url('correspondente/relatorios/buscar') }}" class="form-inline" method="POST" role="search">
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
                            <label class="label label-black"></label><br />
                            <select style="width: 100%" name="relatorio" class="form-control" required>
                                <option value="">Relatório</option>
                                <option {{ (\Session::get('relatorio') == 'pagamento-correspondentes-por-processo'  ? 'selected' : '') }} value="pagamento-correspondentes-por-processo">Pagamento de Correspondentes (Por Processo)</option>
                                <option {{ (\Session::get('relatorio') == 'pagamento-correspondentes-sumarizado'  ? 'selected' : '' ) }} value="pagamento-correspondentes-sumarizado">Pagamento de Correspondentes (Sumarizado)</option>
                            </select>                            
                        </section>     
                        <section class="col col-md-4">      
                            <label class="label label-black"></label><br />     
                            <select name="cd_banco_ban" class="select2">
                                <option value="">Banco</option>
                                @foreach(\App\Banco::all() as $banco)
                                    <option {{ (\Session::get('banco') == $banco->cd_banco_ban  ? 'selected' : '' ) }}  value="{{ $banco->cd_banco_ban }}">{{ $banco->nm_banco_ban }}</option>
                                @endforeach
                            </select>                            
                        </section>                                         
                    </div>
                    <div class="row">

                        <section class="col col-md-4">                           
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                        
                        {{--<section class="col col-md-3">
                            <label class="label label-black">Processo</label><br />
                            <input style="width: 100%" type="text" name="nu_processo_pro" id="nu_processo_pro" placeholder="">    
                        </section> --}}    
                        <section class="col col-md-4">
                            <br />           
                            <input type="radio" name="extensao" id="extensao" value="pdf" {{ (\Session::get('extensao') != 'xlsx'  ? 'checked' : '') }} >  
                            <label class="label label-black">PDF</label>  
                            <input type="radio" name="extensao" id="extensao" value="xlsx" {{ (\Session::get('extensao') == 'xlsx'  ? 'checked' : '') }} >  
                            <label class="label label-black">Excel</label>      
                            <input type="checkbox" name="finalizado" id="finalizado" value="S" {{ (\Session::get('finalizado') == 'S'  ? 'checked' : '') }}>  
                            <label class="label label-black">Processos Finalizados</label> 

                        </section> 
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
                    <h2>Entradas e Saídas</h2>
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
                                    <th></th>  
                                    <th></th>                                     
                                    <th>Correspondente</th>   
                                    <th></th>  
                                    <th></th>                                                                                                             
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($entrasSaidas as $entradaSaida)
                                <tr>
                                    <td>{{ $entradaSaida->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($entradaSaida->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($entradaSaida->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $entradaSaida->tipoServico->nm_tipo_servico_tse }}</td>
                                    <td>{{ $entradaSaida->processo->cliente->nm_razao_social_cli }}</td>
                                    <td>{{ $entradaSaida->vl_taxa_honorario_cliente_pth }}</td>
                                    <td><input type="checkbox" name="teste"></td>
                                    <td>
                                        @if(!empty($entradaSaida->processo->correspondente->contaCorrespondente))
                                            {{ $entradaSaida->processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr }} 
                                        @endif
                                    </td>
                                    <td>{{ $entradaSaida->vl_taxa_honorario_correspondente_pth }}</td>
                                    <td><input type="checkbox" name="teste"></td>                                    
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