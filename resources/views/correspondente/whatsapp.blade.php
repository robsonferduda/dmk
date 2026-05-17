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
                    <h2>Status WhatsApp dos Correspondentes</h2>
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
                            <tbody>
                                @foreach($correspondentes as $c)
                                    @php
                                        $ativo = $c->fl_chatpro_ativo_con ?? false;
                                        if (is_string($ativo)) {
                                            $ativo = in_array(strtolower($ativo), ['t', 'true', '1', 's', 'y', 'yes'], true);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $c->nm_razao_social_con }}</td>
                                        <td class="center">
                                            @if($c->nu_telefone_whatsapp_con)
                                                <a href="https://wa.me/55{{ $c->nu_telefone_whatsapp_con }}" target="_blank" class="text-success">
                                                    <i class="fa fa-whatsapp"></i> {{ $c->nu_telefone_whatsapp_con }}
                                                </a>
                                            @else
                                                <span class="text-danger"><i class="fa fa-times-circle"></i> Não informado</span>
                                            @endif
                                        </td>
                                        <td class="center">
                                            @if($ativo)
                                                <span class="label label-success">
                                                    <i class="fa fa-check-circle"></i> Ativo
                                                </span>
                                            @else
                                                <span class="label label-default">
                                                    <i class="fa fa-times-circle"></i> Inativo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="center">
                                            <a title="Editar" class="btn btn-primary btn-xs"
                                               href="{{ url('correspondente/ficha/'.\Crypt::encrypt($c->cd_correspondente_cor)) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
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

@section('page-scripts')
<script>
    $(document).ready(function () {
        $('#dt_whatsapp').dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
            },
            "order": [[0, "asc"]],
            "pageLength": 25
        });
    });
</script>
@endsection
@endsection
