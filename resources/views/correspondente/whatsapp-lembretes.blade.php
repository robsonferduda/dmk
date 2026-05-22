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
        <div class="col-xs-12">
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
        <article class="col-xs-12">
            <div class="jarviswidget" data-widget-editbutton="false">
                <header>
                    <span class="widget-icon"><i class="fa fa-bell"></i></span>
                    <h2>
                        Processos a serem notificados em
                        <strong>{{ $amanha }}</strong>
                        <span class="badge" style="margin-left: 6px;">{{ $linhas->count() }}</span>
                    </h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if($linhas->isEmpty())
                            <p class="text-center text-muted" style="padding: 30px 0;">
                                Nenhum processo com prazo fatal para amanhã.
                            </p>
                        @else
                        <table class="table table-striped table-bordered table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Réu / Parte</th>
                                    <th>Status</th>
                                    <th class="center" style="width: 95px;">Data</th>
                                    <th class="center" style="width: 65px;">Hora</th>
                                    <th>Correspondente</th>
                                    <th class="center" style="width: 155px;">
                                        <i class="fa fa-whatsapp" style="color: #25D366;"></i> WhatsApp
                                    </th>
                                    <th class="center" style="width: 120px;">Situação</th>
                                    <th class="center" style="width: 110px;">Entrega</th>
                                    <th class="center" style="width: 90px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($linhas as $linha)
                                <tr>
                                    <td>{{ $linha->nu_processo_pro }}</td>
                                    <td>{{ $linha->nm_reu_pro }}</td>
                                    <td>{{ $linha->nm_status }}</td>
                                    <td class="center">{{ $linha->dt_prazo_fatal }}</td>
                                    <td class="center">{{ $linha->hr_audiencia }}</td>
                                    <td>{{ $linha->nm_correspondente }}</td>
                                    <td class="center">
                                        @if($linha->nu_whatsapp)
                                            <a href="https://wa.me/{{ $linha->nu_whatsapp }}" target="_blank" class="text-success">
                                                <i class="fa fa-whatsapp"></i> {{ $linha->nu_whatsapp }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->situacao === 'ENVIARA')
                                            <span class="label label-success"><i class="fa fa-check-circle"></i> Enviará</span>
                                        @elseif($linha->situacao === 'JA_ENVIADO')
                                            <span class="label label-info"><i class="fa fa-check-circle"></i> Já enviado</span>
                                        @elseif($linha->situacao === 'SEM_WHATSAPP')
                                            <span class="label label-warning"><i class="fa fa-times-circle"></i> Sem WhatsApp</span>
                                        @elseif($linha->situacao === 'SEM_CHATPRO')
                                            <span class="label label-danger"><i class="fa fa-times-circle"></i> Sem ChatPro</span>
                                        @else
                                            <span class="label label-default"><i class="fa fa-times-circle"></i> Sem correspondente</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->ds_status_entrega === 'read')
                                            <span class="label label-success"><i class="fa fa-eye"></i> Lida</span>
                                        @elseif(in_array($linha->ds_status_entrega, ['delivered','played']))
                                            <span class="label label-info"><i class="fa fa-check-circle"></i> Entregue</span>
                                        @elseif($linha->ds_status_entrega === 'sent')
                                            <span class="label label-primary"><i class="fa fa-paper-plane"></i> Enviada</span>
                                        @elseif($linha->ds_status_entrega === 'failed')
                                            <span class="label label-danger"><i class="fa fa-times-circle"></i> Falha</span>
                                        @elseif($linha->ds_status_entrega === 'pending')
                                            <span class="label label-default"><i class="fa fa-clock-o"></i> Pendente</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->situacao === 'ENVIARA' || $linha->ds_status_entrega === 'failed')
                                            <form method="POST" action="{{ url('correspondente/whatsapp/lembretes/reenviar/' . $linha->cd_processo_pro) }}" class="form-reenviar" style="margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-xs btn-warning btn-reenviar">
                                                    <i class="fa fa-whatsapp"></i> Reenviar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if(session('success'))
    Swal.fire({
        title: 'Enviado!',
        text: '{{ session('success') }}',
        icon: 'success',
        timer: 3500,
        showConfirmButton: false
    });
    @elseif(session('error'))
    Swal.fire({
        title: 'Falha no envio',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
    @endif

    document.querySelectorAll('.btn-reenviar').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            Swal.fire({
                title: 'Reenviar lembrete?',
                text: 'O lembrete pré-diligência será enviado via WhatsApp agora.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                cancelButtonColor: '#aaa',
                confirmButtonText: '<i class="fa fa-whatsapp"></i> Sim, reenviar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});
</script>
@endsection
