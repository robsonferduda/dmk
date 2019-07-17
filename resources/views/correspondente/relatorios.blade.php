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
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('cliente/novo') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{old('dtInicio')}}" required >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data Fim</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{old('dtFim')}}" required >                            
                        </section>
                        <section class="col col-md-4">                                                        
                            <label class="label label-black"></label><br />
                            <select style="width: 100%" name="relatorio" class="form-control" required>
                                <option value="">Relatório</option>
                                <option value="pagamento-correspondentes-por-processo">Pagamento de Correspondentes (Por Processo)</option>
                                <option value="pagamento-correspondentes-sumarizado">Pagamento de Correspondentes (Sumarizado)</option>
                            </select>                            
                        </section>     
                        <section class="col col-md-4">      
                            <label class="label label-black"></label><br />     
                            <select name="cd_banco_ban" class="select2">
                                <option value="">Banco</option>
                                @foreach(\App\Banco::all() as $banco)
                                    <option value="{{ $banco->cd_banco_ban }}">{{ $banco->nm_banco_ban }}</option>
                                @endforeach
                            </select>                            
                        </section>                                         
                    </div>
                    <div class="row">

                        <section class="col col-md-4">                           
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{old('cd_correspondente_cor')}}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{old('nm_correspondente_cor')}}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                        
                        <section class="col col-md-3">
                            <label class="label label-black">Processo</label><br />
                            <input style="width: 100%" type="text" name="nu_processo_pro" class="form-control" id="nu_processo_pro" placeholder="">    
                        </section>     
                         <section class="col col-md-3">
                            <br />
                            <button class="btn btn-default" type="submit"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        </section>                                   
                    </div>
                    <div class="row">

                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
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