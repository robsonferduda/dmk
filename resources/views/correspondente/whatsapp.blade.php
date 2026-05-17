@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente') }}">Correspondentes</a></li>
        <li>WhatsApp</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-whatsapp" style="color: #25D366;"></i> Correspondentes <span> > WhatsApp</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget" id="wid-id-whatsapp" data-widget-editbutton="false">
                <header>
                    <span class="widget-icon"><i class="fa fa-whatsapp" style="color: #25D366;"></i></span>
                    <h2>
                        Status WhatsApp dos Correspondentes
                        <span id="total-badge" class="badge" style="margin-left: 8px; display: none;"></span>
                    </h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_whatsapp" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th class="center" style="width: 180px;">
                                        <i class="fa fa-whatsapp" style="color: #25D366;"></i> Número WhatsApp
                                    </th>
                                    <th class="center" style="width: 200px;">Envio Automático</th>
                                    <th class="center" style="width: 80px;"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>

@section('script')
<script>
    $(document).ready(function () {
        var table = $('#dt_whatsapp').DataTable({
            "ajax": {
                "url": "{{ url('correspondente/whatsapp/data') }}",
                "type": "GET",
                "dataSrc": "data"
            },
            "columns": [
                { "data": "nm_razao_social_con" },
                {
                    "data": "nu_telefone_whatsapp_con",
                    "render": function (data) {
                        if (data) {
                            return '<a href="https://wa.me/55' + data + '" target="_blank" class="text-success">'
                                 + '<i class="fa fa-whatsapp"></i> ' + data + '</a>';
                        }
                        return '<span class="text-danger"><i class="fa fa-times-circle"></i> Não informado</span>';
                    }
                },
                {
                    "data": "fl_chatpro_ativo_con",
                    "render": function (data) {
                        var ativo = (data === true || data === 't' || data === 'true' || data === '1');
                        if (ativo) {
                            return '<span class="label label-success"><i class="fa fa-check-circle"></i> Ativo</span>';
                        }
                        return '<span class="label label-default"><i class="fa fa-times-circle"></i> Inativo</span>';
                    }
                },
                {
                    "data": "edit_url",
                    "orderable": false,
                    "searchable": false,
                    "render": function (data) {
                        return '<a title="Editar" class="btn btn-primary btn-xs" href="' + data + '">'
                             + '<i class="fa fa-edit"></i></a>';
                    }
                }
            ],
            "oLanguage": {
                "sEmptyTable":     "Nenhum registro encontrado",
                "sInfo":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered":   "(Filtrados de _MAX_ registros)",
                "sInfoThousands":  ".",
                "sLengthMenu":     "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing":     "Processando...",
                "sZeroRecords":    "Nenhum registro encontrado",
                "oPaginate": {
                    "sNext":     "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst":    "Primeiro",
                    "sLast":     "Último"
                }
            },
            "order": [[0, "asc"]],
            "pageLength": 25,
            "initComplete": function (settings, json) {
                var total = json.data ? json.data.length : 0;
                $('#total-badge').text(total).show();
            }
        });
    });
</script>
@endsection
@endsection
