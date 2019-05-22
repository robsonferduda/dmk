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
                        <div class="input-group class="col col-2">
                            <span class="input-group-addon">Data de Início</span>
                            <input class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{old('dt_solicitacao_pro')}}">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">Data Fim</span>
                            <input class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{old('dt_solicitacao_pro')}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <select name="relatorio" class="form-control" required>
                                <option value="">Relatório</option>
                                <option value="pagamento-correspondentes-por-processo">Pagamento de Correspondentes (Por Processo)</option>
                                <option value="pagamento-correspondentes-sumarizado">Pagamento de Correspondentes (Sumarizado)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">Correspondente</span>
                            <input type="hidden" name="cd_correspondente_cor" value="{{old('cd_correspondente_cor')}}">
                            <input class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{old('nm_correspondente_cor')}}">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">Processo</span>
                            <input type="text" name="nu_processo_pro" class="form-control" id="nu_processo_pro" placeholder="">
                        </div>
                        <div class="form-group">
                            <select name="cd_banco_ban" class="select2">
                                <option value="">Banco</option>
                                @foreach(\App\Banco::all() as $banco)
                                    <option value="{{ $banco->cd_banco_ban }}">{{ $banco->nm_banco_ban }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
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
    
    });
</script>

@endsection