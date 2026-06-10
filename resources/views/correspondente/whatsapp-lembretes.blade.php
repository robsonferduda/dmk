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
    </div>

    {{-- Barra de ações --}}
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-xs-12 text-right">
            <button type="button" id="btn-filtro-falhas" class="btn btn-danger btn-sm" style="margin-right:6px;">
                <i class="fa fa-times-circle"></i> Apenas Falhas
            </button>
            <form method="POST" action="{{ url('correspondente/whatsapp/lembretes/disparar') }}" id="form-disparar" style="margin:0;display:inline-block;">
                @csrf
                <button type="button" id="btn-disparar" class="btn btn-success btn-sm">
                    <i class="fa fa-whatsapp"></i> Disparar agora
                </button>
            </form>
        </div>
    </div>

    {{-- Cards de resumo por status de entrega --}}
    @php
        $totalLida     = $linhas->where('ds_status_entrega', 'read')->count();
        $totalEntregue = $linhas->whereIn('ds_status_entrega', ['delivered', 'played'])->count();
        $totalEnviada  = $linhas->whereIn('ds_status_entrega', ['sent', 'queued'])->count();
        $totalFalha    = $linhas->where('ds_status_entrega', 'failed')->count();
        $totalPendente = $linhas->where('ds_status_entrega', 'pending')->count();
        $totalSemEnvio = $linhas->filter(fn($l) => empty($l->ds_status_entrega))->count();
    @endphp
    <div class="row" style="margin-bottom: 20px;">
        @foreach([
            ['label' => 'Lida',     'icon' => 'fa-eye',          'cor' => '#5cb85c', 'total' => $totalLida],
            ['label' => 'Entregue', 'icon' => 'fa-check-circle',  'cor' => '#5bc0de', 'total' => $totalEntregue],
            ['label' => 'Enviada',  'icon' => 'fa-paper-plane',   'cor' => '#337ab7', 'total' => $totalEnviada],
            ['label' => 'Falha',    'icon' => 'fa-times-circle',  'cor' => '#d9534f', 'total' => $totalFalha],
            ['label' => 'Pendente', 'icon' => 'fa-clock-o',       'cor' => '#aaa',    'total' => $totalPendente],
            ['label' => 'Sem envio','icon' => 'fa-minus-circle',  'cor' => '#e0e0e0', 'total' => $totalSemEnvio],
        ] as $card)
        <div class="col-xs-6 col-sm-4 col-md-2" style="margin-bottom: 10px;">
            <div style="background:#fff;border-left:4px solid {{ $card['cor'] }};padding:12px 15px;border-radius:3px;box-shadow:0 1px 3px rgba(0,0,0,.12);">
                <div style="font-size:26px;font-weight:700;color:{{ $card['cor'] }};">{{ $card['total'] }}</div>
                <div style="font-size:12px;color:#666;margin-top:2px;">
                    <i class="fa {{ $card['icon'] }}"></i> {{ $card['label'] }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <article class="col-xs-12" style="padding:0;padding-bottom:40px;">
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
                                <tr data-entrega="{{ $linha->ds_status_entrega ?? '' }}">
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
                                        @if($linha->cd_correspondente_cor)
                                        <button type="button"
                                            class="btn btn-xs btn-default btn-editar-whatsapp"
                                            style="margin-left:4px;"
                                            data-id="{{ $linha->cd_correspondente_cor }}"
                                            data-numero="{{ $linha->nu_whatsapp }}"
                                            data-nome="{{ $linha->nm_correspondente }}"
                                            title="Editar número WhatsApp">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->situacao === 'ENVIARA')
                                            <span class="label label-success"><i class="fa fa-check-circle"></i> Enviará</span>
                                        @elseif($linha->situacao === 'JA_ENVIADO')
                                            <span class="label label-info"><i class="fa fa-check-circle"></i> Já enviado</span>
                                        @elseif($linha->situacao === 'SEM_WHATSAPP')
                                            <span class="label label-warning"><i class="fa fa-times-circle"></i> Sem WhatsApp</span>
                                        @elseif($linha->situacao === 'SEM_WHATSAPP_API')
                                            <span class="label label-danger"><i class="fa fa-times-circle"></i> Sem integração WhatsApp</span>
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
                                        @elseif($linha->ds_status_entrega === 'queued')
                                            <span class="label label-primary"><i class="fa fa-paper-plane"></i> Enviada</span>
                                        @elseif($linha->ds_status_entrega === 'failed')
                                            <span class="label label-danger" title="{{ $linha->erro_entrega ?? 'Falha no envio' }}"><i class="fa fa-times-circle"></i> Falha</span>
                                            @if($linha->erro_entrega)
                                                <br><small class="text-danger">{{ $linha->erro_entrega }}</small>
                                            @endif
                                        @elseif($linha->ds_status_entrega === 'pending')
                                            <span class="label label-default"><i class="fa fa-clock-o"></i> Pendente</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->situacao === 'ENVIARA' || in_array($linha->ds_status_entrega, ['failed', 'sent', 'queued']))
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

{{-- Modal: editar número WhatsApp --}}
<div class="modal fade" id="modal-editar-whatsapp" tabindex="-1" role="dialog" aria-labelledby="modal-editar-whatsapp-label">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-editar-whatsapp-label">
                    <i class="fa fa-whatsapp" style="color:#25D366"></i> Editar WhatsApp
                </h4>
            </div>
            <form id="form-editar-whatsapp">
                @csrf
                <div class="modal-body">
                    <p id="modal-whatsapp-nome" class="text-muted" style="margin-bottom:10px;"></p>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="input-nu-whatsapp">Número <small class="text-muted">(somente dígitos, com DDI)</small></label>
                        <input type="text" name="nu_whatsapp" id="input-nu-whatsapp" class="form-control"
                               placeholder="5548999999999" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-whatsapp">
                        <i class="fa fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
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

    var FILTRO_KEY = 'dmk_lembretes_filtro_falhas';
    var btnFiltroFalhas = document.getElementById('btn-filtro-falhas');

    function aplicarFiltro(ativo) {
        var linhas = document.querySelectorAll('tbody tr[data-entrega]');
        linhas.forEach(function (tr) {
            tr.style.display = (ativo && tr.getAttribute('data-entrega') !== 'failed') ? 'none' : '';
        });
        if (ativo) {
            btnFiltroFalhas.classList.remove('btn-danger');
            btnFiltroFalhas.classList.add('btn-default');
            btnFiltroFalhas.innerHTML = '<i class="fa fa-list"></i> Mostrar todos';
        } else {
            btnFiltroFalhas.classList.remove('btn-default');
            btnFiltroFalhas.classList.add('btn-danger');
            btnFiltroFalhas.innerHTML = '<i class="fa fa-times-circle"></i> Apenas Falhas';
        }
        sessionStorage.setItem(FILTRO_KEY, ativo ? '1' : '0');
    }

    if (btnFiltroFalhas) {
        // Restaura o estado ao recarregar a página.
        var estadoSalvo = sessionStorage.getItem(FILTRO_KEY) === '1';
        if (estadoSalvo) {
            aplicarFiltro(true);
        }

        btnFiltroFalhas.addEventListener('click', function () {
            var ativo = sessionStorage.getItem(FILTRO_KEY) !== '1';
            aplicarFiltro(ativo);
        });
    }

    // Editar WhatsApp
    document.querySelectorAll('.btn-editar-whatsapp').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('modal-whatsapp-nome').textContent = btn.getAttribute('data-nome');
            document.getElementById('input-nu-whatsapp').value = btn.getAttribute('data-numero') || '';
            document.getElementById('form-editar-whatsapp').setAttribute('data-id', btn.getAttribute('data-id'));
            $('#modal-editar-whatsapp').modal('show');
        });
    });

    document.getElementById('form-editar-whatsapp').addEventListener('submit', function (e) {
        e.preventDefault();
        var id  = this.getAttribute('data-id');
        var btn = document.getElementById('btn-salvar-whatsapp');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Salvando…';

        var formData = new FormData(this);
        fetch('{{ url("correspondente/whatsapp/lembretes/whatsapp") }}/' + id, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function () {
            $('#modal-editar-whatsapp').modal('hide');
            location.reload();
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save"></i> Salvar';
            Swal.fire('Erro', 'Não foi possível salvar o número.', 'error');
        });
    });

    var btnDisparar = document.getElementById('btn-disparar');
    if (btnDisparar) {
        btnDisparar.addEventListener('click', function () {
            Swal.fire({
                title: 'Disparar lembretes agora?',
                text: 'Serão enviados lembretes pré-diligência para todos os processos pendentes de {{ $amanha }}.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#aaa',
                confirmButtonText: '<i class="fa fa-whatsapp"></i> Sim, disparar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    btnDisparar.disabled = true;
                    btnDisparar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';
                    document.getElementById('form-disparar').submit();
                }
            });
        });
    }

});
</script>
@endsection
