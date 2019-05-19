@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o"></i> Processos <span>> Detalhes </span> <span>> {{ $processo->nu_processo_pro }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 boxBtnTopo">

            <a title="Relatório" class="btn btn-default pull-right header-btn btnMargin" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-info fa-lg"></i>Relatório</a>
            <a title="Despesas" class="btn btn-warning pull-right header-btn" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i>Despesas</a>
            <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>     
            <a data-toggle="modal" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-edit fa-lg"></i> Editar</a> 
            
           
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
                        <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                        <h2>Dados do Processo </h2>             
                    </header>                
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-file-text-o"></i> <strong>Dados Básicos</strong></legend>
                                    <div class="row" style="margin-left: 5px;">
                                        
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset style="margin-bottom: 15px;">
                                    <legend><i class="fa fa-files-o"></i> <strong>Arquivos do Processo</strong><span class="btn-upload" data-toggle="modal" data-target="#modalUpload"><i class="fa fa-plus-circle"></i> Novo </span></legend>
                                    @foreach($processo->anexos as $anexo)
                                        
                                            <div class="row" style="width:100%; background-color: #fff; margin-bottom: 10px; border-bottom: 1px solid #eaeaea;">
                                                <div style="float: left; width: 8%; text-align: center;">
                                                    <label class="text-default" style="margin-top: 8px;"><i class="fa fa-file-text-o fa-2x"></i></label>
                                                </div>
                                                <div style="float: left; width: 84%">
                                                    <h4><a href="{{ url('files/'.$anexo->cd_anexo_processo_apr) }}">{{ $anexo->nm_anexo_processo_apr }}</a></h4>
                                                    <h6 style="margin: 0px; font-weight: 200;"><strong>{{ date('d/m/Y H:i:s', strtotime($anexo->created_at)) }}</strong> por <strong>{{ $anexo->entidade->usuario->name }}</strong></h6>   
                                                </div>
                                                <div style="float: left; width: 8% text-align: center;">
                                                    <label class="text-danger" style="margin-top: 8px; cursor: pointer;"><i title="Excluir" data-id="{{ $anexo->cd_anexo_processo_apr }}" data-url="../../files/" class="fa fa-trash fa-2x pull-right excluir_registro"></i></label>
                                                </div>    
                                            </div>
                                        
                                    @endforeach
                                </fieldset>
                            </div>
                        </div>             
                </div>
            </article>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ url('file-upload') }}" enctype="multipart/form-data">
            @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-files-o"></i> Adicionar Arquivos</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 upload-msg marginBottom5"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id_processo" id="id_processo" value="{{ $processo->cd_processo_pro }}">
                            <div class="form-group">
                                <input type="text" name="arquivo" id="arquivo" class="form-control" placeholder="Nome do arquivo" required="">
                            </div>
                            <div class="form-group">
                                <input name="file" id="poster" type="file" class="form-control"><br/>
                                <div class="progress progress-striped active">
                                    <div class="progress-bar bg-color-darken" role="progressbar" style="width: 0%"><span class="percent"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-success btn-enviar-arquivo"><i class="fa fa-upload"></i> Enviar Arquivo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {
        $('#modalUpload').on('show.bs.modal', function () {
            $("#arquivo").empty();
            $("#poster").empty();
            $(".upload-msg").empty();
        });
    });

    function validate(formData, jqForm, options) {
        var form = jqForm[0];
        var fileExtension = ['exe', 'rar', 'php', 'js', 'zip'];

        if(!form.file.value) {
            $(".upload-msg").html('<span class="text-danger">Obrigatório selecionar um arquivo para envio</span>');
            return false;
        }
        
        if($.inArray(form.file.value.split('.').pop().toLowerCase(), fileExtension) != -1) {
            $(".upload-msg").html('<span class="text-danger">Formato do arquivo não permitido</span>');
            return false;
        }
    }
 
    (function() {
 
    var bar = $('.progress-bar');
    var percent = $('.percent');
    var status = $('#status');
 
    $('form').ajaxForm({
        beforeSubmit: validate,
        beforeSend: function(){
            $(".upload-msg").empty();
            status.empty();
            var percentVal = '0%';
            var posterValue = $('.btn-enviar-arquivo').fieldValue();
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        success: function(response) {
            var percentVal = 'Aguarde, estamos gravando o arquivo...';
            bar.width(percentVal)
            percent.html(percentVal);
            bar.width(0)
            percent.html("");
            $(".upload-msg").html('<span class="text-success">Arquivo enviado com sucesso, atualizando dados...</span>');
            location.reload();
        },
        error: function(response){

            bar.width(0);
            percent.html("");

            switch (response.status) {
                case 413:
                    $(".upload-msg").html('<span class="text-danger">O arquivo excede o tamanho máximo permitido pelo sistema</span>');
                break;
                case 500:
                    $(".upload-msg").html('<span class="text-danger">Envio cancelado, o arquvio enviado causou erro do servidor</span>');
                break;
                default:
                    $(".upload-msg").html('<span class="text-danger">Erro na gravação do arquivo. Código do erro: '+response.status+' </span>');
            }
        },
        complete: function(xhr) {
            
        }
    });
     
    })();
</script>
@endsection