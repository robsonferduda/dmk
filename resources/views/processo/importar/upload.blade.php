@extends('layouts.admin')
@section('content')
<style>
    .badge-default { background-color: #999; color: #fff; }
    .badge-primary { background-color: #337ab7; color: #fff; }
    .badge-success { background-color: #5cb85c; color: #fff; }
    .badge-info { background-color: #5bc0de; color: #fff; }
    .badge-warning { background-color: #f0ad4e; color: #fff; }
    .badge-danger { background-color: #d9534f; color: #fff; }
    .table th { background-color: #f5f5f5; font-weight: bold; }
    .table-hover tbody tr:hover { background-color: #f0f8ff; cursor: pointer; }
</style>
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Importar</li>
        <li>Upload</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-6 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Processos <span> > Importar </span>
            </h1>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 box-button-xs">
            <div class="boxBtnTopo sub-box-button-xs">
                <a data-toggle="modal" data-target="#modal_layout_processo" class="btn btn-default pull-right"><i class="fa fa-file-excel-o fa-lg"></i><span> Layout de Importação</span></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
            @if($failures)
                <div class="alert alert-danger" role="alert">
                    <strong>Erros:</strong>
                  
                    <ul>
                        @foreach ($failures as $failure)
                            @foreach ($failure->errors() as $error)
                                <li>Na linha {{ $failure->row() }} - {{ $error }}</li>
                            @endforeach
                        @endforeach
                  </ul>
                </div>
            @endif
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <div class="row"> 
                    {{ Form::open(array('url' => 'processos/importar', 'method' => 'post', 'id' => 'form-importar-processos', 'enctype' => 'multipart/form-data')) }}               
                        <section class="col col-xs-12 col-md-5 smart-form">
                            <div class="input input-file">
                                <span class="button"><input type="file" id="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Procurar Arquivo</span><input type="text" placeholder="Arquivo" readonly="">
                            </div>
                        </section>
                        <section class="col col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <button type="submit" class="btn btn-success btn-importar"><i class="fa fa-file-excel-o fa-lg"></i><span> Importar Planilha</span></button>
                        </section>
                    {{ Form::close() }}
                </div>
                
                <!-- Área de Feedback de Importação -->
                <div class="row" id="feedback-importacao" style="display: none; margin-top: 20px;">
                    <section class="col col-xs-12 col-md-12">
                        <div class="alert alert-info">
                            <h4 id="titulo-importacao"><i class="fa fa-spinner fa-spin" id="icone-importacao"></i> Importando Processos...</h4>
                            <div class="progress" style="height: 30px; margin-top: 15px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                     id="barra-progresso" 
                                     role="progressbar" 
                                     style="width: 0%; font-size: 14px; line-height: 30px;" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">0%</div>
                            </div>
                            <div style="margin-top: 15px;">
                                <p><strong>Total de linhas:</strong> <span id="total-linhas">0</span></p>
                                <p><strong>Linhas processadas:</strong> <span id="linhas-processadas">0</span></p>
                                <p><strong>Linhas com sucesso:</strong> <span id="linhas-sucesso" class="text-success">0</span></p>
                                <p><strong>Linhas com erro:</strong> <span id="linhas-erro" class="text-danger">0</span></p>
                            </div>
                            <div id="log-importacao" style="max-height: 200px; overflow-y: auto; margin-top: 10px; background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px;">
                            </div>
                        </div>
                    </section>
                </div>
                
                <div class="row">
                    <section class="col col-xs-12 col-md-5">
                        <div class="col-md-12 upload-arquivo-processo" style="display: none; margin-top: 10px;">           
                            <div class="progress">
                                <div class="progress-bar-upload-arquivo-processo progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </article>
        
        <!-- Listagem de Processos Importados Hoje -->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <h4><i class="fa fa-list"></i> Processos Importados Hoje</h4>
                <hr>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Buscar por Número do Processo</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="buscar-processo" placeholder="Digite o número do processo...">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" id="btn-buscar-processo">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-default btn-block" type="button" id="btn-limpar-busca">
                                <i class="fa fa-refresh"></i> Limpar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <span class="badge badge-info" style="font-size: 14px; padding: 8px 12px;">
                                    Total: <span id="total-processos-hoje">0</span> processo(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div id="loading-processos" style="display: none; text-align: center; padding: 20px;">
                            <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                            <p>Carregando processos...</p>
                        </div>
                        
                        <div id="lista-processos-hoje">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Carregando lista de processos importados hoje...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>      
<div class="modal fade in modal_top_alto" id="modal_layout_processo" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Layout de Importação</h4>
                     </div>
                    <div class="modal-body">
                        <form method="POST" class="smart-form" id="frm-importar-processo" action="{{ url('layout/importar/processo') }}">
                        @csrf
                            <fieldset>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <div class="form-group">
                                            <label class="label label-black">Cliente<span class="text-danger">*</span></label></label>          
                                            <select required class="select2" name="cliente" data-input="#codigo-cliente" >
                                                 <option selected value="" >Selecione um Cliente</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->cd_cliente_cli }}">{{ $cliente->nm_razao_social_cli }}</option>                              
                                                @endforeach
                                            </select>                               
                                        </div>  
                                    </section>      
                                </div>                                          
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Gerar Layout</button>
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
</div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            
            var progressInterval = null;
            
            // Carregar processos importados hoje ao carregar a página
            carregarProcessosHoje();
            
            // Buscar processo por número
            $('#btn-buscar-processo').on('click', function() {
                var numerProcesso = $('#buscar-processo').val();
                carregarProcessosHoje(numerProcesso);
            });
            
            // Buscar ao pressionar Enter
            $('#buscar-processo').on('keypress', function(e) {
                if (e.which === 13) {
                    var numerProcesso = $('#buscar-processo').val();
                    carregarProcessosHoje(numerProcesso);
                }
            });
            
            // Limpar busca
            $('#btn-limpar-busca').on('click', function() {
                $('#buscar-processo').val('');
                carregarProcessosHoje();
            });
            
            // Função para carregar processos importados hoje
            function carregarProcessosHoje(busca) {
                $('#loading-processos').show();
                $('#lista-processos-hoje').html('');
                
                $.ajax({
                    url: '{{ url("processos/importados-hoje") }}',
                    type: 'GET',
                    data: { busca: busca },
                    success: function(response) {
                        $('#loading-processos').hide();
                        
                        if (response.success) {
                            $('#total-processos-hoje').text(response.total);
                            
                            if (response.processos.length === 0) {
                                $('#lista-processos-hoje').html(
                                    '<div class="alert alert-warning">' +
                                    '<i class="fa fa-exclamation-triangle"></i> Nenhum processo importado encontrado hoje.' +
                                    '</div>'
                                );
                            } else {
                                var html = '<div class="table-responsive"><table class="table table-bordered table-striped table-hover">';
                                html += '<thead><tr>';
                                html += '<th width="5%">#</th>';
                                html += '<th width="15%">Nº Processo</th>';
                                html += '<th width="15%">Cliente</th>';
                                html += '<th width="15%">Autor</th>';
                                html += '<th width="15%">Réu</th>';
                                html += '<th width="10%">Comarca</th>';
                                html += '<th width="10%">Status</th>';
                                html += '<th width="10%">Data Cadastro</th>';
                                html += '<th width="5%">Ações</th>';
                                html += '</tr></thead><tbody>';
                                
                                response.processos.forEach(function(processo, index) {
                                    var statusClass = processo.status_class || 'default';
                                    var statusLabel = processo.status_label || 'N/A';
                                    
                                    html += '<tr>';
                                    html += '<td>' + (index + 1) + '</td>';
                                    html += '<td><strong>' + (processo.nu_processo_pro || 'N/A') + '</strong></td>';
                                    html += '<td>' + (processo.cliente || 'N/A') + '</td>';
                                    html += '<td>' + (processo.nm_autor_pro || 'N/A') + '</td>';
                                    html += '<td>' + (processo.nm_reu_pro || 'N/A') + '</td>';
                                    html += '<td>' + (processo.cidade || 'N/A') + '</td>';
                                    html += '<td><span class="badge badge-' + statusClass + '">' + statusLabel + '</span></td>';
                                    html += '<td>' + processo.dt_cadastro + '</td>';
                                    html += '<td class="text-center" style="min-width: 100px;">';
                                    html += '<a href="{{ url("processos/acompanhamento") }}/' + processo.hash + '" class="btn btn-xs btn-primary" title="Visualizar" style="margin-right: 3px;">';
                                    html += '<i class="fa fa-eye"></i>';
                                    html += '</a>';
                                    html += '<a href="{{ url("processos/editar") }}/' + processo.hash + '" class="btn btn-xs btn-warning" title="Editar">';
                                    html += '<i class="fa fa-edit"></i>';
                                    html += '</a>';
                                    html += '</td>';
                                    html += '</tr>';
                                });
                                
                                html += '</tbody></table></div>';
                                
                                $('#lista-processos-hoje').html(html);
                            }
                        } else {
                            $('#lista-processos-hoje').html(
                                '<div class="alert alert-danger">' +
                                '<i class="fa fa-exclamation-circle"></i> Erro ao carregar processos: ' + response.message +
                                '</div>'
                            );
                        }
                    },
                    error: function() {
                        $('#loading-processos').hide();
                        $('#lista-processos-hoje').html(
                            '<div class="alert alert-danger">' +
                            '<i class="fa fa-exclamation-circle"></i> Erro ao carregar processos. Tente novamente.' +
                            '</div>'
                        );
                    }
                });
            }
            
            // Função para atualizar progresso
            function atualizarProgresso() {
                $.ajax({
                    url: '{{ url("processos/importar/progresso") }}',
                    type: 'GET',
                    success: function(progress) {
                        if (progress.total > 0) {
                            var percentual = Math.round((progress.processadas / progress.total) * 100);
                            
                            $('#total-linhas').text(progress.total);
                            $('#linhas-processadas').text(progress.processadas);
                            $('#linhas-sucesso').text(progress.sucesso);
                            $('#linhas-erro').text(progress.erros);
                            $('#barra-progresso').css('width', percentual + '%').text(percentual + '%');
                            
                            // Adicionar log
                            if (progress.processadas > 0 && progress.processadas % 5 === 0) {
                                var timestamp = new Date().toLocaleTimeString();
                                $('#log-importacao').append('<div class="text-muted">[' + timestamp + '] Processando linha ' + progress.processadas + ' de ' + progress.total + '</div>');
                                
                                // Auto-scroll para o final
                                var logDiv = document.getElementById('log-importacao');
                                logDiv.scrollTop = logDiv.scrollHeight;
                            }
                        }
                    }
                });
            }
            
            // Interceptar submit do formulário de importação
            $('#form-importar-processos').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                var fileInput = $('#file')[0];
                
                if (!fileInput.files.length) {
                    alert('Por favor, selecione um arquivo para importar.');
                    return false;
                }
                
                // Resetar feedback
                $('#feedback-importacao').show();
                
                // Resetar título e classes do alert
                $('#titulo-importacao').html('<i class="fa fa-spinner fa-spin" id="icone-importacao"></i> Importando Processos...');
                $('#feedback-importacao .alert').removeClass('alert-success alert-danger').addClass('alert-info');
                
                $('#total-linhas').text('Carregando...');
                $('#linhas-processadas').text('0');
                $('#linhas-sucesso').text('0');
                $('#linhas-erro').text('0');
                $('#barra-progresso').css('width', '0%').text('0%').addClass('progress-bar-animated').removeClass('bg-danger').addClass('bg-success');
                $('#log-importacao').html('<div class="text-info">Iniciando importação...</div>');
                
                // Desabilitar botão
                $('.btn-importar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importando...');
                
                // Iniciar polling de progresso
                progressInterval = setInterval(atualizarProgresso, 1000); // Atualizar a cada 1 segundo
                
                $.ajax({
                    url: '{{ url("processos/importar") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        return xhr;
                    },
                    success: function(response) {
                        // Parar polling
                        clearInterval(progressInterval);
                        
                        if (response.success) {
                            // Atualizar título e ícone para sucesso
                            $('#titulo-importacao').html('<i class="fa fa-check-circle text-success" id="icone-importacao"></i> Importação Concluída!');
                            $('.alert-info').removeClass('alert-info').addClass('alert-success');
                            
                            $('#log-importacao').append('<div class="text-success"><strong>✓ Importação concluída!</strong></div>');
                            $('#log-importacao').append('<div class="text-success">' + response.message + '</div>');
                            
                            // Atualizar contadores finais
                            $('#total-linhas').text(response.total);
                            $('#linhas-processadas').text(response.processadas);
                            $('#linhas-sucesso').text(response.sucesso);
                            $('#linhas-erro').text(response.erros);
                            $('#barra-progresso').css('width', '100%').text('100%').removeClass('progress-bar-animated').addClass('bg-success');
                            
                            // Reabilitar botão
                            $('.btn-importar').prop('disabled', false).html('<i class="fa fa-file-excel-o fa-lg"></i><span> Importar</span>');
                            
                            // Recarregar lista de processos
                            carregarProcessosHoje();
                            
                            // Scroll suave para a lista de processos
                            $('html, body').animate({
                                scrollTop: $('#lista-processos-hoje').offset().top - 100
                            }, 1000);
                        } else {
                            // Atualizar título e ícone para erro
                            $('#titulo-importacao').html('<i class="fa fa-times-circle text-danger" id="icone-importacao"></i> Erro na Importação');
                            $('.alert-info').removeClass('alert-info').addClass('alert-danger');
                            
                            $('#log-importacao').append('<div class="text-danger"><strong>✗ Erro na importação</strong></div>');
                            if (response.errors) {
                                response.errors.forEach(function(error) {
                                    $('#log-importacao').append('<div class="text-danger">• ' + error + '</div>');
                                });
                            }
                            $('#barra-progresso').removeClass('bg-success').addClass('bg-danger');
                        }
                        
                        // Reabilitar botão
                        $('.btn-importar').prop('disabled', false).html('<i class="fa fa-file-excel-o fa-lg"></i><span> Importar</span>');
                    },
                    error: function(xhr) {
                        // Parar polling
                        clearInterval(progressInterval);
                        
                        // Atualizar título e ícone para erro
                        $('#titulo-importacao').html('<i class="fa fa-times-circle text-danger" id="icone-importacao"></i> Erro na Importação');
                        $('.alert-info').removeClass('alert-info').addClass('alert-danger');
                        
                        $('#log-importacao').append('<div class="text-danger"><strong>✗ Erro ao processar arquivo</strong></div>');
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, errors) {
                                if (Array.isArray(errors)) {
                                    errors.forEach(function(error) {
                                        $('#log-importacao').append('<div class="text-danger">• ' + error + '</div>');
                                    });
                                } else {
                                    $('#log-importacao').append('<div class="text-danger">• ' + errors + '</div>');
                                }
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            $('#log-importacao').append('<div class="text-danger">' + xhr.responseJSON.message + '</div>');
                        } else {
                            $('#log-importacao').append('<div class="text-danger">Erro desconhecido. Tente novamente.</div>');
                        }
                        
                        $('#barra-progresso').removeClass('bg-success').addClass('bg-danger');
                        
                        // Reabilitar botão
                        $('.btn-importar').prop('disabled', false).html('<i class="fa fa-file-excel-o fa-lg"></i><span> Importar</span>');
                    }
                });
            });
            
            $("select[name='cliente']").change(function(){               
                buscaAdvogado();              
            });  


            var buscaAdvogado = function(){
        
                var cliente = $("select[name='cliente'] option:selected").val();

                $.ajax({
                        url: '../../advogados-por-cliente/'+cliente,
                        type: 'GET',
                        dataType: "JSON",                        
                        success: function(response)
                        {                   
                            console.log(response);

                            $('#advogado').empty();
                            $('#advogado').append('<option value="">Selecione um Advogado Solicitante</option>');
                            $.each(response,function(index,element){
                                $('#advogado').append('<option value="'+element.cd_contato_cot+'">'+element.nm_contato_cot+'</option>');                                                            
                            });       
                            $('#advogado').trigger('change');     
                        },
                            error: function(response)
                            {
                                //console.log(response);
                    }
                });

            }
        });  

        $(function() {
                // Validation
            var validobj = $("#frm-importar-processo").validate({

                    ignore: 'input[type=hidden], .select2-input, .select2-focusser',
                    rules : {
                        cliente : {
                            required: true,
                        },                      
                    },

                    // Messages for form validation
                    messages : {
                        cliente : {
                            required : 'Campo Cliente é Obrigatório'
                        },
                                               
                    },

                    errorPlacement: function (error, element) {
                        var elem = $(element);
                        console.log(elem);
                        if(element.attr("name") == "cliente") {
                            error.appendTo( element.next("span") );
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
                        } else {
                            elem.addClass(errorClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
                        } else {
                            elem.removeClass(errorClass);
                        }
                    }    
                });

        });
   
    </script>
@endsection