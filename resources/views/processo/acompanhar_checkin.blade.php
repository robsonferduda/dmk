@extends('layouts.admin')
@section('content')
{{--
    [CHECK-IN]
    Tela dedicada aos check-ins do correspondente (data/hora + GPS, sem foto)
    enviados para um processo. Espelho de acompanhar_timemark.blade.php.
--}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .ck-map        { width: 100%; height: 180px; border-radius: 4px; }
    .ck-map-resumo { width: 100%; height: 320px; border-radius: 4px; }
    .ck-card .panel-body { padding: 12px; }
</style>

<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('processos') }}">Processos</a></li>
        <li><a href="{{ url('processos/acompanhamento/'.safe_encrypt($processo->cd_processo_pro)) }}">Acompanhamento</a></li>
        <li>Check-In</li>
    </ol>
</div>

<div id="content">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-map-marker"></i> Check-In
                <span>&gt; Processo {{ $processo->nu_processo_pro }}</span>
                <a href="{{ url('processos/acompanhamento/'.safe_encrypt($processo->cd_processo_pro)) }}"
                   class="btn btn-default btn-sm pull-right" style="margin-top:6px;">
                    <i class="fa fa-arrow-left"></i> Voltar ao acompanhamento
                </a>
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
                    <h2>Check-Ins do correspondente</h2>
                </header>
                <div>
                    <div class="widget-body">
                        <div id="ck-galeria">
                        @if(empty($checkins) || $checkins->isEmpty())
                            <p class="text-muted" id="ck-vazio" style="margin:0;">
                                <i class="fa fa-info-circle"></i>
                                Nenhum check-in registrado para este processo até o momento.
                            </p>
                        @else
                            <div class="row" style="margin-bottom:15px;">
                                <div class="col-sm-12">
                                    <div id="ck-map-resumo" class="ck-map-resumo"></div>
                                </div>
                            </div>

                            <div class="row" id="ck-cards-row">
                                @foreach($checkins as $ck)
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 ck-card" style="margin-bottom:15px;">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <p>
                                                    <i class="fa fa-clock-o"></i>
                                                    <strong>{{ \Carbon\Carbon::parse($ck->dt_checkin_pck)->format('d/m/Y H:i') }}</strong>
                                                </p>
                                                @if($ck->ds_endereco_pck)
                                                    <p><i class="fa fa-map-marker"></i> {{ $ck->ds_endereco_pck }}</p>
                                                @endif
                                                @if($ck->ds_observacao_pck)
                                                    <p><i class="fa fa-comment-o"></i> {{ $ck->ds_observacao_pck }}</p>
                                                @endif
                                                @if($ck->nu_latitude_pck && $ck->nu_longitude_pck)
                                                    <div class="ck-map"
                                                         data-lat="{{ $ck->nu_latitude_pck }}"
                                                         data-lng="{{ $ck->nu_longitude_pck }}"></div>
                                                    <p class="text-muted" style="margin-top:6px; font-size:11px;">
                                                        Lat {{ number_format($ck->nu_latitude_pck, 6, '.', '') }}
                                                        / Lng {{ number_format($ck->nu_longitude_pck, 6, '.', '') }}
                                                        @if($ck->nu_precisao_metros_pck)
                                                            <span>(±{{ (int) $ck->nu_precisao_metros_pck }}m)</span>
                                                        @endif
                                                    </p>
                                                    <a class="btn btn-xs btn-default" style="margin-top:6px;"
                                                       href="https://maps.google.com/?q={{ $ck->nu_latitude_pck }},{{ $ck->nu_longitude_pck }}"
                                                       target="_blank">
                                                        <i class="fa fa-external-link"></i> Abrir no Google Maps
                                                    </a>
                                                @else
                                                    <p class="text-muted"><i class="fa fa-map-marker"></i> Sem coordenadas.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var TILE_ATR = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

    function mountMiniMap(el) {
        var lat = parseFloat(el.getAttribute('data-lat'));
        var lng = parseFloat(el.getAttribute('data-lng'));
        if (isNaN(lat) || isNaN(lng)) return;
        var map = L.map(el, {
            zoomControl: false, attributionControl: false, dragging: false,
            scrollWheelZoom: false, doubleClickZoom: false, touchZoom: false,
            keyboard: false
        }).setView([lat, lng], 16);
        L.tileLayer(TILE_URL).addTo(map);
        L.marker([lat, lng]).addTo(map);
    }

    function mountAllMiniMaps() {
        document.querySelectorAll('.ck-map').forEach(function (el) {
            if (el.dataset.mounted) return;
            el.dataset.mounted = '1';
            mountMiniMap(el);
        });
    }

    var resumoMap = null, resumoLayer = null;
    function mountResumo(pontos) {
        var el = document.getElementById('ck-map-resumo');
        if (!el || !pontos || !pontos.length) return;

        if (!resumoMap) {
            resumoMap = L.map(el).setView([pontos[0].lat, pontos[0].lng], 14);
            L.tileLayer(TILE_URL, { attribution: TILE_ATR }).addTo(resumoMap);
        }
        if (resumoLayer) resumoMap.removeLayer(resumoLayer);

        var markers = pontos.map(function (p) {
            return L.marker([p.lat, p.lng]).bindPopup(p.popup || '');
        });
        resumoLayer = L.featureGroup(markers).addTo(resumoMap);
        resumoMap.fitBounds(resumoLayer.getBounds().pad(0.2));
    }

    function coletarPontosDoDOM() {
        var pts = [];
        document.querySelectorAll('.ck-card').forEach(function (card) {
            var miniMap = card.querySelector('.ck-map');
            if (!miniMap) return;
            var lat = parseFloat(miniMap.getAttribute('data-lat'));
            var lng = parseFloat(miniMap.getAttribute('data-lng'));
            if (isNaN(lat) || isNaN(lng)) return;
            var when = card.querySelector('.panel-body p');
            pts.push({
                lat: lat, lng: lng,
                popup: '<div style="text-align:center">'
                     + (when ? when.innerText : '')
                     + '</div>'
            });
        });
        return pts;
    }

    function renderMapas() {
        mountAllMiniMaps();
        mountResumo(coletarPontosDoDOM());
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderMapas);
    } else {
        renderMapas();
    }
})();
</script>
@endsection
