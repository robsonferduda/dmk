@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Despesas</a></li>
        <li>Cadastrar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-dollar"></i> Despesas <span>> Editar Despesa</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('despesas/lancamentos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Despesas</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="alert fade in none">
                <button class="close" data-dismiss="alert">×</button>
                <i class="fa-fw fa"></i>
                <strong class="msg_titulo"></strong> <span class="msg_mensagem"></span>
            </div>
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Editar Despesa </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm_add_despesas', 'url' => ['despesas',$despesa->cd_despesa_des], 'class' => 'smart-form', 'method' => 'PUT', 'files' => true]) !!}
                            <header>Dados da Despesa <small style="font-size: 12px;"><span class="text-danger">* Campos obrigatórios</span></small></header>
                            <fieldset>

                                <div class="row">
                                    <input type="hidden" name="id_despesa" id="id_despesa" value="{{ (!empty($despesa)) ? $despesa->cd_despesa_des : '' }}">
                                    <section class="col col-6">
                                        <label class="label">Categoria 
                                            <a href="#" rel="popover-hover" data-placement="top" data-html="true" data-original-title="Categoria da Despesa" data-content="Não é obrigatório preencher, utilize ele como filtro para o campo <strong>Tipo de Despesa</strong>. ">
                                            <i class="fa fa-question-circle text-primary"></i>
                                            </a>
                                        </label> 
                                        <label class="select">
                                            <select name="cd_categoria_despesa_cad" class="categoria_despesa">
                                                <option value="0">Selecione uma categoria</option>
                                                @foreach($categorias as $cat)
                                                    <option value="{{ $cat->cd_categoria_despesa_cad }}" {{ ($despesa->tipo->cd_categoria_despesa_cad == $cat->cd_categoria_despesa_cad) ? 'selected' : ''  }}>{{ $cat->nm_categoria_despesa_cad }}</option>
                                                @endforeach
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-6">
                                        <label class="label">Tipo de Despesa <span class="text-danger"> *</span></label>
                                        <label class="select">
                                            <select name="cd_tipo_despesa_tds" class="tipo_despesa">
                                                <option value="">Selecione um tipo</option>
                                                @foreach($despesas as $d)
                                                    <option value="{{ $d->cd_tipo_despesa_tds }}" {{ ($despesa->cd_tipo_despesa_tds == $d->cd_tipo_despesa_tds) ? 'selected' : ''  }}  data-categoria="{{ $d->cd_categoria_despesa_cad }}">{{ $d->nm_tipo_despesa_tds }}</option>
                                                @endforeach
                                            </select> <i></i> </label>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-6">
                                        <label class="label">Descrição </label>
                                        <label class="input"> <i class="icon-append fa fa-font"></i>
                                            <input type="text" name="dc_descricao_des" id="dc_descricao_des" value="{{ $despesa->dc_descricao_des }}">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Data de Vencimento <span class="text-danger"> *</span></label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_vencimento_des" id="dt_vencimento_des" class="date-mask hasDatepicker" value="{{ ($despesa->dt_vencimento_des) ? date('d/m/Y', strtotime($despesa->dt_vencimento_des)) : '' }}" placeholder="__/__/____">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Data de Pagamento</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_pagamento_des" id="dt_pagamento_des" class="date-mask hasDatepicker" value="{{ ($despesa->dt_pagamento_des) ? date('d/m/Y', strtotime($despesa->dt_pagamento_des)) : '' }}" placeholder="__/__/____">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Valor da Despesa <span class="text-danger"> *</span></label>
                                        <label class="input"> <i class="icon-append fa fa-dollar"></i>
                                            <input type="text" name="vl_valor_des" id="vl_valor_des"  value="{{ $despesa->vl_valor_des }}">
                                        </label>
                                    </section>
                                </div>

                                 <div class="row">
                                    <!-- Drop área -->
                                    <section class="col col-6">
                                        <div class="well center dropzone">
                                            <h1 style="font-size: 70px; margin-top: 70px; "><i class="fa fa-cloud-upload"></i></h1>
                                            <h4>Arraste aqui os documentos que deseja enviar</h4>
                                        </div>
                                    </section>

                                    <section class="col col-6">
                                        <div id="filepicker">
                                            <!-- Button Bar -->
                                            <div class="button-bar">
                                                
                                                <div class="btn btn-success btn-upload-plugin fileinput">
                                                    <i class="fa fa-files-o"></i> Buscar Arquivos
                                                    <input type="file" name="files[]" multiple>
                                                </div>   

                                                 <button type="button" class="btn btn-primary start-all btn-upload-plugin">
                                                    <i class="fa fa-upload"></i> Enviar Todos
                                                </button>               

                                                <button type="button" class="btn btn-warning cancel-all btn-upload-plugin">
                                                    <i class="fa fa-ban"></i> Cancelar Todos
                                                </button>

                                                <button type="button" class="btn btn-danger delete-all btn-upload-plugin">
                                                    <i class="fa fa-trash-o"></i> Deletar Todos
                                                </button>
                                            </div>

                                            <!-- Listar Arquivos -->
                                            <div class="table-responsive div-table">
                                                <table class="table table-upload">
                                                    <thead>
                                                        <tr>
                                                            <th class="column-name">Nome</th>
                                                            <th class="column-size center">Tamanho</th>
                                                            <th class="column-date">Data Envio</th>
                                                            <th class="center">Opções</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="files">
                                                        
                                                    </tbody>                        
                                                </table>
                                                <div class="pagination-container text-center"></div>
                                            </div>

                                            <!-- Drop Zone -->
                                            <div class="drop-window">
                                                <div class="drop-window-content">
                                                    <h3><i class="fa fa-upload"></i> Drop files to upload</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </fieldset>
                            
                            <div class="row" style="padding: 5px 20px;">
                                    <header>
                                        <i class="fa  fa-file-text-o"></i> Observações 
                                    </header>
                                    <fieldset>
                                        <div class="row"> 
                                            <section class="col col-sm-12">
                                            <label class="input">
                                                <textarea class="form-control" rows="4" name="obs_des" id="observacao" value="{{old('obs_des')}}" >{{old('obs_des') ? old('obs_des') : ($despesa->obs_des) ? $despesa->obs_des : '' }}</textarea>
                                            </label>
                                            </section> 
                                        </div>
                                    </fieldset>
                                </div>
                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                <button type="button" class="btn btn-danger" onclick="window.history.back();"><i class="fa fa-times"></i> Cancelar</button>
                            </footer>
                        {!! Form::close() !!}                  
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

     $(function() {

        $('#filepicker').filePicker({
                url: '../../filepicker',
                ui: {
                    autoUpload: false
                },
                data: function(){
                   var _token = "{{ csrf_token() }}";
                   var id_despesa = $("#id_despesa").val();

                   return {
                        _token: _token,
                        id_despesa: id_despesa
                   }
                },
                plugins: ['ui', 'drop', 'camera', 'crop']
        })
        .on('done.filepicker', function (e, data) {

            $.ajax({
                url: "../../anexo-despesa-add",
                type: 'POST',
                data: {
                    "_token": $('meta[name="token"]').attr('content'),
                    "id_despesa": $("#id_despesa").val(),
                    "nome_arquivo": data.files[0].name
                },
                success: function(response){   

                    $(".fa").addClass("fa-check");
                    $(".msg_titulo").html("Sucesso");
                    $(".msg_mensagem").html("Arquivo anexado com sucesso");
                    $(".alert").addClass("alert-success");
                    $(".alert").removeClass("none");
                    
                },
                error: function(response){

                    $(".fa").addClass("fa-times");
                    $(".msg_titulo").html("Erro");
                    $(".msg_mensagem").html("Erro ao enviar o arquivo");
                    $(".alert").addClass("alert-danger");
                    $(".alert").removeClass("none");
                }
            });

        })
        .on('delete.filepicker', function (e, data) {

            //Antes de excluir o arquivo, ele remove o registro do banco. Caso ocorra erro no banco, ele não exclui o arquivo e retorna false. Caso exclua do banco, mas não consiga remover o arquivo, ele recupera o arquivo no método deletedone

            $.ajax({
                url: '../../anexo-despesa-delete',
                type: 'POST',
                dataType: "JSON",
                data: {
                    "_method": 'DELETE',
                    "id": $("#id_despesa").val(),                    
                    "nome_arquivo": data.filename,
                    "_token": $('meta[name="token"]').attr('content'),
                },
                success: function(response)
                {
                    $(".fa").addClass("fa-check");
                    $(".msg_titulo").html("Sucesso");
                    $(".msg_mensagem").html("Arquivo excluído com sucesso");
                    $(".alert").addClass("alert-success");
                    $(".alert").removeClass("none");
                },
                error: function(response)
                {
                    $(".fa").addClass("fa-times");
                    $(".msg_titulo").html("Erro");
                    $(".msg_mensagem").html("Erro ao excluir o arquivo");
                    $(".alert").addClass("alert-danger");
                    $(".alert").removeClass("none");

                    return false;
                }
            });

        })
        .on('deletedone.filepicker', function (e, data) {
      
            console.log(data);
            
        });

        $('#dt_vencimento_des').datepicker({
            dateFormat : 'dd/mm/yy',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>'
        }); 

        $("#frm_add_despesas").validate({
            rules : {
                cd_tipo_despesa_tds : {
                    required : true
                },
                dt_vencimento_des : {
                  required : true
                },
                vl_valor_des : {
                    required : true
                }
            },            

            messages : {
                cd_tipo_despesa_tds : {
                    required : 'Preencha o campo para continuar'
                },
                dt_vencimento_des : {
                    required : 'Preencha o campo para continuar'
                },
                vl_valor_des : {
                    required : 'Preencha o campo para continuar'
                }
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
    });

</script>

<script type="text/x-tmpl" id="uploadTemplate">
        <tr class="upload-template">
            <td class="column-name">
                <p class="name">{%= o.file.name %}</p>
                <span class="text-danger error">{%= o.file.error || '' %}</span>
            </td>
            <td colspan="2">
                <p>{%= o.file.sizeFormatted || '' %}</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
            </td>
            <td style="font-size: 150%; text-align: center;">
                {% if (!o.file.autoUpload && !o.file.error) { %}
                    <a href="#" class="action action-primary start" title="Upload">
                        <i class="fa fa-arrow-circle-o-up"></i>
                    </a>
                {% } %}
                <a href="#" class="action action-warning cancel" title="Cancelar">
                    <i class="fa fa-ban"></i>
                </a>
            </td>
        </tr>
    </script>

    <!-- Download Template -->
    <script type="text/x-tmpl" id="downloadTemplate">
        {% o.timestamp = function (src) {
            return (src += (src.indexOf('?') > -1 ? '&' : '?') + new Date().getTime());
        }; %}
        <tr class="download-template">
            <td class="column-name">
                <p class="name">
                    {% if (o.file.url) { %}
                        <a href="{%= o.file.url %}" target="_blank">{%= o.file.name %}</a>
                    {% } else { %}
                        {%= o.file.name %}
                    {% } %}
                </p>
                {% if (o.file.error) { %}
                    <span class="text-danger">{%= o.file.error %}</span>
                {% } %}
            </td>
            <td class="column-size center"><p>{%= o.file.sizeFormatted %}</p></td>
            <td class="column-date">
                {% if (o.file.time) { %}
                    <time datetime="{%= o.file.timeISOString() %}">
                        {%= o.file.timeFormatted %}
                    </time>
                {% } %}
            </td>
            <td class="center">
                {% if (o.file.imageFile && !o.file.error) { %}
                    <a href="#" class="action action-primary crop" title="Crop">
                        <i class="fa fa-crop"></i>
                    </a>
                {% } %}
                {% if (o.file.error) { %}
                    <a href="#" class="action action-warning cancel" title="Cancelar">
                        <i class="fa fa-ban"></i>
                    </a>
                {% } else { %}
                    <a href="#" class="action action-danger delete" title="Delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                {% } %}
            </td>
        </tr>
    </script>
     <!-- Pagination Template -->
    <script type="text/x-tmpl" id="paginationTemplate">
        {% if (o.lastPage > 1) { %}
            <ul class="pagination pagination-sm">
                <li {% if (o.currentPage === 1) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.prevPage %}" data-page="{%= o.prevPage %}" title="Previous">&laquo;</a>
                </li>

                {% if (o.firstAdjacentPage > 1) { %}
                    <li><a href="#!page=1" data-page="1">1</a></li>
                    {% if (o.firstAdjacentPage > 2) { %}
                       <li class="disabled"><a>...</a></li>
                    {% } %}
                {% } %}

                {% for (var i = o.firstAdjacentPage; i <= o.lastAdjacentPage; i++) { %}
                    <li {% if (o.currentPage === i) { %} class="active" {% } %}>
                        <a href="#!page={%= i %}" data-page="{%= i %}">{%= i %}</a>
                    </li>
                {% } %}

                {% if (o.lastAdjacentPage < o.lastPage) { %}
                    {% if (o.lastAdjacentPage < o.lastPage - 1) { %}
                        <li class="disabled"><a>...</a></li>
                    {% } %}
                    <li><a href="#!page={%= o.lastPage %}" data-page="{%= o.lastPage %}">{%= o.lastPage %}</a></li>
                {% } %}

                <li {% if (o.currentPage === o.lastPage) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.nextPage %}" data-page="{%= o.nextPage %}" title="Next">&raquo</a>
                </li>
            </ul>
        {% } %}
    </script><!-- end of #paginationTemplate -->
@endsection