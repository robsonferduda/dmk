@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente/whatsapp') }}">WhatsApp</a></li>
        <li>Lembretes Pré-Diligência</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-whatsapp" style="color: #25D366;"></i> WhatsApp
                <span> > Lembretes Pré-Diligência</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget" id="wid-id-lembretes" data-widget-editbutton="false">
                <header>
                    <span class="widget-icon"><i class="fa fa-bell"></i></span>
                    <h2>
                        Processos a serem notificados amanhã
                        <small class="text-muted" style="font-size: 13px; margin-left: 8px;">
                            prazo fatal = {{ \Carbon\Carbon::tomorrow()->format('d/m/Y') }}
                        </small>
                    </h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_lembretes" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Réu / Parte</th>
                                    <th class="center" style="width: 100px;">Data</th>
                                    <th class="center" style="width: 70px;">Hora</th>
                                    <th>Correspondente</th>
                                    <th class="center" style="width: 160px;">
                                        <i class="fa fa-whatsapp" style="color: #25D366;"></i> WhatsApp
                                    </th>
                                    <th class="center" style="width: 150px;">Situação</th>
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

    var badges = {
        'ENVIARA':           '<span class="label label-success"><i class="fa fa-check-circle"></i> Enviará</span>',
        'JA_ENVIADO':        '<span class="label label-info"><i class="fa fa-check-circle"></i> Já enviado</span>',
        'SEM_WHATSAPP':      '<span class="label label-warning"><i class="fa fa-times-circle"></i> Sem WhatsApp</span>',
        'SEM_CHATPRO':       '<span class="label label-danger"><i class="fa fa-times-circle"></i> Sem ChatPro</span>',
        'SEM_CORRESPONDENTE':'<span class="label label-default"><i class="fa fa-times-circle"></i> Sem correspondente</span>',
    };

    $('#dt_lembretes').DataTable({
        "ajax": {
            "url": "{{ url('correspondente/whatsapp/lembretes/data') }}",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "nu_processo_pro" },
            { "data": "nm_reu_pro" },
            { "data": "dt_prazo_fatal", "className": "center" },
            { "data": "hr_audiencia",   "className": "center" },
            { "data": "nm_correspondente" },
            {
                "data": "nu_whatsapp",
                "className": "center",
                "render": function (data) {
                    if (data && data !== '-') {
                        return '<a href="https://wa.me/' + data + '" target="_blank" class="text-success">'
                             + '<i class="fa fa-whatsapp"></i> ' + data + '</a>';
                    }
                    return '<span class="text-muted">—</span>';
                }
            },
            {
                "data": "situacao",
                "className": "center",
                "render": function (data) {
                    return badges[data] || data;
                }
            }
        ],
        "order": [[2, 'asc'], [3, 'asc']],
        "oLanguage": {
            "sEmptyTable":     "Nenhum processo encontrado para amanhã",
            "sInfo":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered":   "(Filtrados de _MAX_ registros)",
            "sInfoThousands":  ".",
            "sLengthMenu":     "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing":     "Processando...",
            "sSearch":         "Buscar:",
            "sZeroRecords":    "Nenhum registro encontrado"
        },
        "drawCallback": function () {
            var info = this.api().page.info();
            $('#total-badge').text(info.recordsTotal).show();
        }
    });
});
</script>
@endsection
@endsection
