@extends('layouts.admin')
@section('content')
{{--
    [CHECK-IN]
    Tela do correspondente: lista os processos ativos como cards e oferece um
    botão único de "Fazer Check-In" por processo. Após o check-in (ou se já
    existir), o card exibe data/hora, coordenadas e mini-mapa Leaflet.
--}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .ck-card {
        border: 1px solid #d6dde5;
        border-radius: 8px;
        background: #fff;
        margin-bottom: 18px;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s;
    }
    .ck-card.is-done {
        border-color: #95ff9a;
        background: #f4fff5;
    }
    .ck-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eef1f5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
    }
    .ck-card.is-done .ck-card-header { background: #eaffea; border-bottom-color: #cdeccd; }
    .ck-card-header .ck-numero {
        font-size: 16px;
        font-weight: 700;
        color: #2d3a4a;
        margin: 0;
    }
    .ck-card-header .ck-status-label {
        font-size: 11px;
        color: #fff;
        padding: 3px 8px;
        border-radius: 3px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .ck-card-header .ck-prazo {
        margin-left: auto;
        font-size: 12px;
        color: #555;
        text-align: right;
    }
    .ck-card-header .ck-prazo strong { color: #2d3a4a; font-size: 13px; }

    .ck-card-body {
        padding: 14px 16px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 18px;
    }
    .ck-card-body .ck-field { font-size: 12px; color: #4a5667; }
    .ck-card-body .ck-field strong { color: #2d3a4a; display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
    .ck-card-body .ck-field-full { grid-column: 1 / -1; }

    .ck-card-footer {
        padding: 12px 16px;
        border-top: 1px solid #eef1f5;
        background: #fbfcfd;
    }
    .ck-card.is-done .ck-card-footer { background: #f4fff5; border-top-color: #cdeccd; }

    .ck-action {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }
    .ck-action .ck-status-msg {
        font-size: 12px;
        color: #777;
        flex: 1;
    }
    .ck-action .ck-status-msg.is-error { color: #a94442; }

    .ck-done-info {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: flex-start;
    }
    .ck-done-info .ck-done-text { flex: 1 1 220px; min-width: 0; }
    .ck-done-info .ck-done-text .ck-when {
        color: #2e7d32;
        font-weight: 600;
        font-size: 14px;
        display: block;
        margin-bottom: 4px;
    }
    .ck-done-info .ck-done-text .ck-coords {
        font-size: 12px;
        color: #555;
        font-family: monospace;
        word-break: break-all;
    }
    .ck-done-info .ck-map-wrap {
        flex: 1 1 320px;
        min-width: 260px;
    }
    .ck-done-info .ck-map {
        width: 100%;
        height: 200px;
        border-radius: 6px;
        border: 1px solid #d6dde5;
        background: #eef1f5;
    }
    .ck-done-info .ck-map-actions { margin-top: 6px; }

    @media (max-width: 600px) {
        .ck-card-body { grid-template-columns: 1fr; }
        .ck-card-header .ck-prazo { margin-left: 0; text-align: left; width: 100%; }
    }
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

                        <p class="text-muted" style="margin-bottom: 18px;">
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
                            @foreach($processos as $p)
                                @php $ck = $checkins->get($p->cd_processo_pro); @endphp
                                <div class="ck-card {{ $ck ? 'is-done' : '' }}"
                                     id="ck-card-{{ $p->cd_processo_pro }}"
                                     data-processo="{{ $p->cd_processo_pro }}">

                                    <div class="ck-card-header">
                                        <h4 class="ck-numero">
                                            <i class="fa fa-folder-open-o text-muted"></i>
                                            {{ $p->nu_processo_pro }}
                                        </h4>
                                        @if($p->status)
                                            <span class="ck-status-label"
                                                  style="background-color: {{ $p->status->ds_color_stp ?: '#777' }};">
                                                {{ $p->status->nm_status_processo_conta_stp ?? '' }}
                                            </span>
                                        @endif
                                        <div class="ck-prazo">
                                            <i class="fa fa-calendar"></i>
                                            <strong>{{ $p->dt_prazo_fatal_pro ? date('d/m/Y', strtotime($p->dt_prazo_fatal_pro)) : '-' }}</strong>
                                            @if($p->hr_audiencia_pro)
                                                <span>às {{ date('H:i', strtotime($p->hr_audiencia_pro)) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ck-card-body">
                                        @if($p->cliente)
                                            <div class="ck-field ck-field-full">
                                                <strong>Cliente</strong>
                                                {{ $p->cliente->nm_razao_social_cli }}
                                            </div>
                                        @endif
                                        <div class="ck-field">
                                            <strong>Autor</strong>
                                            {{ $p->nm_autor_pro ?: '—' }}
                                        </div>
                                        <div class="ck-field">
                                            <strong>Réu</strong>
                                            {{ $p->nm_reu_pro ?: '—' }}
                                        </div>
                                        <div class="ck-field">
                                            <strong>Comarca</strong>
                                            @if($p->cidade)
                                                {{ $p->cidade->nm_cidade_cde }}@if($p->cidade->estado)/{{ $p->cidade->estado->sg_estado_est }}@endif
                                            @else
                                                —
                                            @endif
                                        </div>
                                        <div class="ck-field">
                                            <strong>Vara</strong>
                                            {{ $p->vara->nm_vara_var ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="ck-card-footer" id="ck-footer-{{ $p->cd_processo_pro }}">
                                        @if($ck)
                                            <div class="ck-done-info">
                                                <div class="ck-done-text">
                                                    <span class="ck-when">
                                                        <i class="fa fa-check-circle"></i>
                                                        Check-in realizado em
                                                        {{ \Carbon\Carbon::parse($ck->dt_checkin_pck)->format('d/m/Y \à\s H:i') }}
                                                    </span>
                                                    @if($ck->nu_latitude_pck && $ck->nu_longitude_pck)
                                                        <span class="ck-coords">
                                                            Lat {{ number_format($ck->nu_latitude_pck, 6, '.', '') }}<br>
                                                            Lng {{ number_format($ck->nu_longitude_pck, 6, '.', '') }}
                                                            @if($ck->nu_precisao_metros_pck)
                                                                <br>Precisão ±{{ (int) $ck->nu_precisao_metros_pck }}m
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-muted" style="font-size:12px;">Sem coordenadas registradas.</span>
                                                    @endif
                                                </div>
                                                @if($ck->nu_latitude_pck && $ck->nu_longitude_pck)
                                                    <div class="ck-map-wrap">
                                                        <div class="ck-map"
                                                             data-lat="{{ $ck->nu_latitude_pck }}"
                                                             data-lng="{{ $ck->nu_longitude_pck }}"></div>
                                                        <div class="ck-map-actions">
                                                            <a class="btn btn-xs btn-default"
                                                               href="https://maps.google.com/?q={{ $ck->nu_latitude_pck }},{{ $ck->nu_longitude_pck }}"
                                                               target="_blank">
                                                                <i class="fa fa-external-link"></i> Abrir no Google Maps
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="ck-action">
                                                <button type="button"
                                                        class="btn btn-primary btn-checkin"
                                                        data-processo="{{ $p->cd_processo_pro }}">
                                                    <i class="fa fa-map-marker"></i> Fazer Check-In
                                                </button>
                                                <span class="ck-status-msg" id="ck-status-{{ $p->cd_processo_pro }}"></span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
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

    var TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var TILE_ATR = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

    function setStatus(processoId, msg, isError) {
        var el = document.getElementById('ck-status-' + processoId);
        if (!el) return;
        el.textContent = msg || '';
        el.classList.toggle('is-error', !!isError);
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
             + ' às ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function mountMap(el) {
        var lat = parseFloat(el.getAttribute('data-lat'));
        var lng = parseFloat(el.getAttribute('data-lng'));
        if (isNaN(lat) || isNaN(lng)) return;
        if (el.dataset.mounted) return;
        el.dataset.mounted = '1';
        var map = L.map(el, {
            zoomControl: true, attributionControl: true,
            scrollWheelZoom: false
        }).setView([lat, lng], 16);
        L.tileLayer(TILE_URL, { attribution: TILE_ATR }).addTo(map);
        L.marker([lat, lng]).addTo(map);
        // garante render correto após inserção dinâmica
        setTimeout(function () { map.invalidateSize(); }, 100);
    }

    function mountAllMaps(scope) {
        (scope || document).querySelectorAll('.ck-map').forEach(mountMap);
    }

    function renderDoneFooter(processoId, ck) {
        var footer = document.getElementById('ck-footer-' + processoId);
        var card   = document.getElementById('ck-card-' + processoId);
        if (!footer) return;

        var when = ck.dt_checkin_pck
            ? new Date(String(ck.dt_checkin_pck).replace(' ', 'T'))
            : new Date();

        var lat  = ck.nu_latitude_pck;
        var lng  = ck.nu_longitude_pck;
        var prec = ck.nu_precisao_metros_pck;

        var coordsHtml = '';
        var mapWrapHtml = '';
        if (lat != null && lng != null) {
            coordsHtml =
                '<span class="ck-coords">'
              +   'Lat ' + parseFloat(lat).toFixed(6) + '<br>'
              +   'Lng ' + parseFloat(lng).toFixed(6)
              +   (prec ? '<br>Precisão ±' + parseInt(prec, 10) + 'm' : '')
              + '</span>';

            mapWrapHtml =
                '<div class="ck-map-wrap">'
              +   '<div class="ck-map" data-lat="' + lat + '" data-lng="' + lng + '"></div>'
              +   '<div class="ck-map-actions">'
              +     '<a class="btn btn-xs btn-default" target="_blank"'
              +     '   href="https://maps.google.com/?q=' + lat + ',' + lng + '">'
              +       '<i class="fa fa-external-link"></i> Abrir no Google Maps'
              +     '</a>'
              +   '</div>'
              + '</div>';
        } else {
            coordsHtml = '<span class="text-muted" style="font-size:12px;">Sem coordenadas registradas.</span>';
        }

        footer.innerHTML =
            '<div class="ck-done-info">'
          +   '<div class="ck-done-text">'
          +     '<span class="ck-when">'
          +       '<i class="fa fa-check-circle"></i> '
          +       'Check-in realizado em ' + formatDate(when)
          +     '</span>'
          +     coordsHtml
          +   '</div>'
          +   mapWrapHtml
          + '</div>';

        if (card) card.classList.add('is-done');
        mountAllMaps(footer);
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
                    renderDoneFooter(processoId, data.checkin || {});
                })
                .catch(function () {
                    setStatus(processoId, 'Erro de comunicação com o servidor.', true);
                    btn.disabled = false;
                });
            });
        });
    });

    // Mapas dos check-ins já existentes (renderizados no PHP)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { mountAllMaps(); });
    } else {
        mountAllMaps();
    }
})();
</script>
@endsection
