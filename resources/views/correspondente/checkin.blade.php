@extends('layouts.admin')
@section('content')
{{--
    [CHECK-IN]
    Tela do correspondente: lista os processos ativos e oferece um botão único
    de "Fazer Check-In" por processo. O botão captura GPS via navegador e
    envia para POST /processos/{id}/checkins.
--}}
<style>
    .ck-table td, .ck-table th { vertical-align: middle; }
    .ck-status-ok   { color: #739e73; font-weight: 600; }
    .ck-status-pend { color: #a90329; font-weight: 600; }
    .ck-msg         { font-size: 11px; color: #777; margin-top: 4px; display: block; }
</style>

<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Check-In</li>
    </ol>
</div>

<div id="content">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-map-marker"></i> Check-In
                <span>&gt; Meus processos</span>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="jarviswidget jarviswidget-color-blueDark"
                 data-widget-editbutton="false"
                 data-widget-deletebutton="false"
                 data-widget-fullscreenbutton="false">
                <header>
                    <span class="widget-icon"><i class="fa fa-map-marker"></i></span>
                    <h2>Processos disponíveis para check-in</h2>
                </header>
                <div>
                    <div class="widget-body">

                        <p class="text-muted">
                            <i class="fa fa-info-circle"></i>
                            Ao chegar ao fórum, clique em <strong>Fazer Check-In</strong> para registrar
                            sua presença (data/hora + localização). Cada processo permite apenas um check-in.
                        </p>

                        @if($processos->isEmpty())
                            <p class="text-muted" style="margin:0;">
                                <i class="fa fa-info-circle"></i>
                                Nenhum processo ativo para check-in.
                            </p>
                        @else
                            <table class="table table-striped table-bordered ck-table">
                                <thead>
                                    <tr>
                                        <th>Processo</th>
                                        <th>Cliente / Partes</th>
                                        <th>Comarca / Vara</th>
                                        <th>Prazo</th>
                                        <th style="width: 230px;">Check-In</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($processos as $p)
                                        @php $ck = $checkins->get($p->cd_processo_pro); @endphp
                                        <tr id="ck-row-{{ $p->cd_processo_pro }}">
                                            <td>
                                                <strong>{{ $p->nu_processo_pro }}</strong>
                                                @if($p->status)
                                                    <br>
                                                    <span class="label label-default" style="background-color: {{ $p->status->ds_color_stp }};">
                                                        {{ $p->status->nm_status_processo_conta_stp ?? '' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($p->cliente)
                                                    <small><strong>Cliente:</strong> {{ $p->cliente->nm_razao_social_cli }}</small><br>
                                                @endif
                                                <small><strong>Autor:</strong> {{ $p->nm_autor_pro ?: '-' }}</small><br>
                                                <small><strong>Réu:</strong> {{ $p->nm_reu_pro ?: '-' }}</small>
                                            </td>
                                            <td>
                                                @if($p->cidade)
                                                    {{ $p->cidade->nm_cidade_cde }}@if($p->cidade->estado)/{{ $p->cidade->estado->sg_estado_est }}@endif
                                                @endif
                                                @if($p->vara)
                                                    <br><small>{{ $p->vara->nm_vara_var }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $p->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($p->dt_prazo_fatal_pro)) : '-' }}
                                                @if($p->hr_audiencia_pro)
                                                    <br><small>{{ date('H:i', strtotime($p->hr_audiencia_pro)) }}</small>
                                                @endif
                                            </td>
                                            <td id="ck-cell-{{ $p->cd_processo_pro }}">
                                                @if($ck)
                                                    <span class="ck-status-ok">
                                                        <i class="fa fa-check-circle"></i>
                                                        Realizado em {{ \Carbon\Carbon::parse($ck->dt_checkin_pck)->format('d/m/Y H:i') }}
                                                    </span>
                                                    @if($ck->nu_latitude_pck && $ck->nu_longitude_pck)
                                                        <span class="ck-msg">
                                                            Lat {{ number_format($ck->nu_latitude_pck, 6, '.', '') }}
                                                            / Lng {{ number_format($ck->nu_longitude_pck, 6, '.', '') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <button type="button"
                                                            class="btn btn-primary btn-sm btn-checkin"
                                                            data-processo="{{ $p->cd_processo_pro }}">
                                                        <i class="fa fa-map-marker"></i> Fazer Check-In
                                                    </button>
                                                    <span class="ck-msg ck-status" id="ck-status-{{ $p->cd_processo_pro }}"></span>
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
        </div>
    </div>
</div>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var baseUrl = '{{ url('processos') }}';

    function setStatus(processoId, msg, isError) {
        var el = document.getElementById('ck-status-' + processoId);
        if (!el) return;
        el.textContent = msg || '';
        el.style.color = isError ? '#a94442' : '#777';
    }

    function getPosicao() {
        return new Promise(function (resolve) {
            if (!navigator.geolocation) return resolve(null);
            navigator.geolocation.getCurrentPosition(
                function (p) { resolve(p); },
                function ()  { resolve(null); },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        });
    }

    function formatDate(d) {
        function pad(n) { return n < 10 ? '0' + n : '' + n; }
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear()
             + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    document.querySelectorAll('.btn-checkin').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var processoId = btn.getAttribute('data-processo');
            btn.disabled = true;
            setStatus(processoId, 'Obtendo localização...');

            getPosicao().then(function (pos) {
                setStatus(processoId, 'Registrando check-in...');

                var payload = { dt_checkin: new Date().toISOString() };
                if (pos) {
                    payload.latitude  = pos.coords.latitude;
                    payload.longitude = pos.coords.longitude;
                    payload.precisao  = pos.coords.accuracy;
                }

                fetch(baseUrl + '/' + processoId + '/checkins', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                })
                .then(function (r) {
                    return r.json().then(function (d) { return { status: r.status, body: d }; });
                })
                .then(function (res) {
                    var data = res.body || {};
                    if (!data.success) {
                        setStatus(processoId, data.message || 'Falha ao registrar check-in.', true);
                        btn.disabled = false;
                        return;
                    }

                    var ck   = data.checkin || {};
                    var when = ck.dt_checkin_pck ? new Date(ck.dt_checkin_pck.replace(' ', 'T')) : new Date();
                    var cell = document.getElementById('ck-cell-' + processoId);
                    var html = '<span class="ck-status-ok"><i class="fa fa-check-circle"></i> '
                             + 'Realizado em ' + formatDate(when) + '</span>';
                    if (ck.nu_latitude_pck && ck.nu_longitude_pck) {
                        html += '<span class="ck-msg">Lat ' + parseFloat(ck.nu_latitude_pck).toFixed(6)
                              + ' / Lng ' + parseFloat(ck.nu_longitude_pck).toFixed(6) + '</span>';
                    }
                    cell.innerHTML = html;
                })
                .catch(function () {
                    setStatus(processoId, 'Erro de comunicação com o servidor.', true);
                    btn.disabled = false;
                });
            });
        });
    });
})();
</script>
@endsection
