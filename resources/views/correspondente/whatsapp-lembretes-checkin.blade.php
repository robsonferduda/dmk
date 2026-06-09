@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondente/whatsapp') }}">WhatsApp</a></li>
        <li>Lembretes de Check-in</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa fa-whatsapp" style="color: #25D366;"></i> WhatsApp
                <span> > Lembretes de Check-in</span>
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
        <div class="col-xs-12 col-sm-6">
            <form method="GET" action="{{ url('correspondente/whatsapp/checkin') }}" class="form-inline" style="margin:0;">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="Buscar nº processo…" value="{{ $busca }}" style="width:220px;">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                        @if($busca)
                            <a href="{{ url('correspondente/whatsapp/checkin') }}" class="btn btn-default" title="Limpar busca"><i class="fa fa-times"></i></a>
                        @endif
                    </span>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-6 text-right" style="margin-top:4px;">
            <button type="button" id="btn-filtro-falhas" class="btn btn-danger btn-sm">
                <i class="fa fa-times-circle"></i> Apenas Falhas
            </button>
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
                    <span class="widget-icon"><i class="fa fa-map-marker"></i></span>
                    <h2>
                        Diligências de hoje
                        <strong>{{ $hoje }}</strong>
                        <span class="badge" style="margin-left: 6px;">{{ $linhas->count() }}</span>
                    </h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        @if($linhas->isEmpty())
                            <p class="text-center text-muted" style="padding: 30px 0;">
                                Nenhum processo com prazo fatal para hoje.
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
                                    <th class="center" style="width: 100px;">Check-in</th>
                                    <th class="center" style="width: 90px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($linhas as $linha)
                                <tr data-entrega="{{ $linha->ds_status_entrega ?? '' }}">
                                    <td><a href="{{ $linha->processo_url }}">{{ $linha->nu_processo_pro }}</a></td>
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
                                        @if($linha->situacao === 'JA_ENVIADO')
                                            <span class="label label-info"><i class="fa fa-check-circle"></i> Enviado {{ $linha->enviado_em }}</span>
                                        @elseif($linha->situacao === 'PENDENTE')
                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Pendente</span>
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
                                            <span class="label label-danger"><i class="fa fa-times-circle"></i> Falha</span>
                                        @elseif($linha->ds_status_entrega === 'pending')
                                            <span class="label label-default"><i class="fa fa-clock-o"></i> Pendente</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->checkin_status === 'correspondente')
                                            <a href="{{ $linha->checkin_url }}" target="_blank" class="label label-success" style="text-decoration:none;">
                                                <i class="fa fa-map-marker"></i> Feito
                                            </a>
                                        @elseif($linha->checkin_status === 'escritorio')
                                            <span class="label label-info"><i class="fa fa-building"></i> Feito pelo Escritório</span>
                                        @else
                                            <span class="label label-default"><i class="fa fa-clock-o"></i> Pendente</span>
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if($linha->situacao === 'PENDENTE' || $linha->ds_status_entrega === 'failed')
                                            <form method="POST" action="{{ url('correspondente/whatsapp/checkin/reenviar/' . $linha->cd_processo_pro) }}" class="form-reenviar" style="margin:0;">
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

    var FILTRO_KEY = 'dmk_checkin_filtro_falhas';
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
        var estadoSalvo = sessionStorage.getItem(FILTRO_KEY) === '1';
        if (estadoSalvo) {
            aplicarFiltro(true);
        }
        btnFiltroFalhas.addEventListener('click', function () {
            var ativo = sessionStorage.getItem(FILTRO_KEY) !== '1';
            aplicarFiltro(ativo);
        });
    }

    document.querySelectorAll('.btn-reenviar').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            Swal.fire({
                title: 'Reenviar lembrete?',
                text: 'O lembrete de check-in será enviado via WhatsApp agora.',
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
