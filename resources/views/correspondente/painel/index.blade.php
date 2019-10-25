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
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('correspondente/painel/relatorios/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data prazo fatal inicial<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" required >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data prazo fatal final<span class="text-danger">*</span></label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  required >                            
                        </section>
                        <section class="col col-md-4">                                                        
                            <label class="label label-black">Relatório<span class="text-danger">*</span></label><br />
                            <select style="width: 100%" name="relatorio" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option {{ (\Session::get('relatorio') == 'relacao-processos'  ? 'selected' : '') }} value="relacao-processos">Relação de processos</option>
                            </select>                            
                        </section> 
                        <section class="col col-md-4">                           
                            <label class="label label-black">Cliente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_conta_con" value="{{(old('cd_conta_con') ? old('cd_conta_con') : (\Session::get('conta') ? \Session::get('conta') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_conta_con" placeholder="Digite 3 caracteres para busca" type="text" id="conta_auto_complete" value="{{(old('nm_conta_con') ? old('nm_conta_con') : (\Session::get('nmConta') ? \Session::get('nmConta') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-conta" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>                                    
                    </div>
                    <div class="row">

                      
                        
                        {{--<section class="col col-md-3">
                            <label class="label label-black">Processo</label><br />
                            <input style="width: 100%" type="text" name="nu_processo_pro" id="nu_processo_pro" placeholder="">    
                        </section> --}}    
                        <section class="col col-md-9">
                            <br />           
                            {{--<input type="radio" name="extensao" id="extensao" value="pdf" {{ (\Session::get('extensao') != 'xlsx'  ? 'checked' : '') }} >  
                            <label class="label label-black">PDF</label>  
                            <input type="radio" name="extensao" id="extensao" value="xlsx" {{ (\Session::get('extensao') == 'xlsx'  ? 'checked' : '') }} >  
                            <label class="label label-black">Excel</label> --}}
                            <input type="checkbox" name="finalizado" id="finalizado" value="S" {{ (\Session::get('finalizado') == 'S'  ? 'checked' : '') }}>
                            <label class="label label-black">Processos Finalizados</label> 

                        </section> 
                        <section class="col col-md-3">
                            <br />
                            <button style='float: right;' class="btn btn-primary" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar </button>
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
                                            <a href="../../correspondente/painel/reports/{{$arquivo['nome']}}" >{{ $arquivo['nome'] }}</a>
                                        </td>
                                        <td>
                                            {{ $arquivo['tamanho'].'KB' }}
                                        </td>
                                        <td>
                                            <div style="display: block;padding: 1px 1px 1px 1px">
                                                <button title="Excluir" data-url="../../correspondente/painel/reports/{{$arquivo['nome']}}" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i></button>
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

        $( "#conta_auto_complete" ).focusout(function(){
           if($("input[name='cd_conta_con']").val() == ''){
                $("#conta_auto_complete").val('');
           }
        });

        var pathConta = "{{ url('autocompleteConta') }}";

        $( "#conta_auto_complete" ).autocomplete({
          source: pathConta,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_conta_con']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-conta').click(function(){
            $("input[name='cd_conta_con']").val('');
            $("input[name='nm_conta_con']").val('');

        });
    
    });
</script>

@endsection