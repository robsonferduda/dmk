@extends('layouts.admin')
@section('content')
{{--
    [TIMEMARK]
    Tela dedicada às comprovações Timemark (foto + data/hora + GPS + logo)
    enviadas pelo correspondente para um processo.
--}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .tm-map        { width: 100%; height: 180px; border-radius: 4px; }
    .tm-map-resumo { width: 100%; height: 320px; border-radius: 4px; }
    .tm-card .thumbnail img { max-height: 220px; object-fit: cover; width: 100%; }
</style>

<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('processos') }}">Processos</a></li>
        <li><a href="{{ url('processos/acompanhamento/'.safe_encrypt($processo->cd_processo_pro)) }}">Acompanhamento</a></li>
        <li>Timemark</li>
    </ol>
</div>

<div id="content">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-camera"></i> Timemark
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
                    <span class="widget-icon"><i class="fa fa-camera"></i></span>
                    <h2>Comprovações Timemark</h2>
                </header>
                <div>
                    <div class="widget-body">
                        <div class="row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <label class="btn btn-primary" for="tm-foto-input">
                                    <i class="fa fa-camera"></i> Capturar comprovação
                                </label>
                                <input type="file" id="tm-foto-input" accept="image/*" capture="environment" style="display:none;">
                                <input type="text" id="tm-observacao" class="form-control"
                                       placeholder="Observação (opcional)"
                                       style="display:inline-block;width:auto;max-width:400px;margin-left:10px;">
                                <span id="tm-status" class="text-muted" style="margin-left:10px;"></span>
                            </div>
                        </div>

                        <div id="tm-galeria">
                        @if(empty($comprovacoes) || $comprovacoes->isEmpty())
                            <p class="text-muted" id="tm-vazio" style="margin:0;">
                                <i class="fa fa-info-circle"></i>
                                Nenhuma comprovação Timemark enviada para este processo até o momento.
                            </p>
                        @else
                            <div class="row" style="margin-bottom:15px;">
                                <div class="col-sm-12">
                                    <div id="tm-map-resumo" class="tm-map-resumo"></div>
                                </div>
                            </div>

                            <div class="row" id="tm-cards-row">
                                @foreach($comprovacoes as $cmp)
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 tm-card" style="margin-bottom:15px;">
                                        <div class="thumbnail">
                                            <a href="{{ url('processos/comprovacao/'.$cmp->cd_processo_comprovacao_pcm.'/imagem') }}" target="_blank">
                                                <img src="{{ url('processos/comprovacao/'.$cmp->cd_processo_comprovacao_pcm.'/imagem') }}" alt="Comprovação">
                                            </a>
                                            <div class="caption">
                                                <p>
                                                    <i class="fa fa-clock-o"></i>
                                                    {{ \Carbon\Carbon::parse($cmp->dt_captura_pcm)->format('d/m/Y H:i') }}
                                                </p>
                                                @if($cmp->ds_endereco_pcm)
                                                    <p><i class="fa fa-map-marker"></i> {{ $cmp->ds_endereco_pcm }}</p>
                                                @endif
                                                @if($cmp->nu_latitude_pcm && $cmp->nu_longitude_pcm)
                                                    <div class="tm-map"
                                                         data-lat="{{ $cmp->nu_latitude_pcm }}"
                                                         data-lng="{{ $cmp->nu_longitude_pcm }}"></div>
                                                    <a class="btn btn-xs btn-default" style="margin-top:6px;"
                                                       href="https://maps.google.com/?q={{ $cmp->nu_latitude_pcm }},{{ $cmp->nu_longitude_pcm }}"
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
    var input    = document.getElementById('tm-foto-input');
    var status   = document.getElementById('tm-status');
    var galeria  = document.getElementById('tm-galeria');
    var obsField = document.getElementById('tm-observacao');

    var processoId = {{ $processo->cd_processo_pro }};
    var csrf       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
        document.querySelectorAll('.tm-map').forEach(function (el) {
            if (el.dataset.mounted) return;
            el.dataset.mounted = '1';
            mountMiniMap(el);
        });
    }

    var resumoMap = null, resumoLayer = null;
    function mountResumo(pontos) {
        var el = document.getElementById('tm-map-resumo');
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
        document.querySelectorAll('.tm-card').forEach(function (card) {
            var miniMap = card.querySelector('.tm-map');
            if (!miniMap) return;
            var lat = parseFloat(miniMap.getAttribute('data-lat'));
            var lng = parseFloat(miniMap.getAttribute('data-lng'));
            if (isNaN(lat) || isNaN(lng)) return;
            var img  = card.querySelector('img');
            var when = card.querySelector('.caption p');
            pts.push({
                lat: lat, lng: lng,
                popup: '<div style="text-align:center">'
                     + (img ? '<img src="' + img.src + '" style="max-width:160px;max-height:120px;border-radius:4px;display:block;margin:0 auto 4px"/>' : '')
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

    if (!input) return;

    function setStatus(msg, isError) {
        status.textContent = msg || '';
        status.style.color = isError ? '#a94442' : '';
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

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;

        setStatus('Obtendo localização...');
        getPosicao().then(function (pos) {
            setStatus('Enviando foto...');

            var fd = new FormData();
            fd.append('foto', file);
            fd.append('dt_captura', new Date().toISOString());
            if (obsField && obsField.value) fd.append('observacao', obsField.value);
            if (pos) {
                fd.append('latitude',  pos.coords.latitude);
                fd.append('longitude', pos.coords.longitude);
                fd.append('precisao',  pos.coords.accuracy);
            }

            fetch('{{ url('processos') }}/' + processoId + '/comprovacoes', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: fd,
                credentials: 'same-origin'
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) {
                    setStatus(data.message || 'Falha no envio', true);
                    return;
                }
                setStatus('Comprovação registrada com sucesso.');
                if (obsField) obsField.value = '';
                input.value = '';

                var vazio = document.getElementById('tm-vazio');
                if (vazio) vazio.parentNode.removeChild(vazio);

                if (!document.getElementById('tm-map-resumo')) {
                    var rRow = document.createElement('div');
                    rRow.className = 'row';
                    rRow.style.marginBottom = '15px';
                    rRow.innerHTML = '<div class="col-sm-12"><div id="tm-map-resumo" class="tm-map-resumo"></div></div>';
                    galeria.insertBefore(rRow, galeria.firstChild);
                }

                var rowEl = document.getElementById('tm-cards-row');
                if (!rowEl) {
                    rowEl = document.createElement('div');
                    rowEl.className = 'row';
                    rowEl.id = 'tm-cards-row';
                    galeria.appendChild(rowEl);
                }

                var c   = data.comprovacao;
                var col = document.createElement('div');
                col.className = 'col-xs-12 col-sm-6 col-md-6 col-lg-4 tm-card';
                col.style.marginBottom = '15px';

                var mapaHtml = '';
                if (c.nu_latitude_pcm && c.nu_longitude_pcm) {
                    mapaHtml = '<div class="tm-map" data-lat="' + c.nu_latitude_pcm + '" data-lng="' + c.nu_longitude_pcm + '"></div>'
                             + '<a class="btn btn-xs btn-default" style="margin-top:6px;" target="_blank" '
                             + 'href="https://maps.google.com/?q=' + c.nu_latitude_pcm + ',' + c.nu_longitude_pcm + '">'
                             + '<i class="fa fa-external-link"></i> Abrir no Google Maps</a>';
                } else {
                    mapaHtml = '<p class="text-muted"><i class="fa fa-map-marker"></i> Sem coordenadas.</p>';
                }

                col.innerHTML =
                    '<div class="thumbnail">' +
                        '<a href="' + data.url_marcada + '" target="_blank">' +
                            '<img src="' + data.url_marcada + '" alt="Comprovação">' +
                        '</a>' +
                        '<div class="caption">' +
                            '<p><i class="fa fa-clock-o"></i> ' +
                                new Date(c.dt_captura_pcm).toLocaleString('pt-BR') +
                            '</p>' +
                            (c.ds_endereco_pcm ? '<p><i class="fa fa-map-marker"></i> ' + c.ds_endereco_pcm + '</p>' : '') +
                            mapaHtml +
                        '</div>' +
                    '</div>';
                rowEl.insertBefore(col, rowEl.firstChild);

                renderMapas();
            })
            .catch(function (err) {
                setStatus('Erro: ' + err.message, true);
            });
        });
    });
})();
</script>
@endsection
