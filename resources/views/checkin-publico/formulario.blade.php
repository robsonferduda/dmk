<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check-in da Diligência</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 0; padding: 16px; background: #f3f5f8; color: #222; }
        .card { background: #fff; border-radius: 10px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,.06); max-width: 540px; margin: 0 auto; }
        h1 { font-size: 18px; margin: 0 0 12px; color: #1f4391; }
        .row { padding: 6px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .row:last-child { border-bottom: 0; }
        .label { color: #777; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        .val   { color: #222; font-weight: 600; word-break: break-word; }
        .btn   { display: block; width: 100%; padding: 14px; border: 0; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 16px; }
        .btn-primary  { background: #1f4391; color: #fff; }
        .btn-primary:disabled { background: #9aa6c4; cursor: wait; }
        .btn-success  { background: #2c8d3a; color: #fff; }
        .alert { padding: 12px; border-radius: 6px; margin-top: 12px; font-size: 14px; }
        .alert-info    { background: #e7eefc; color: #1f4391; }
        .alert-success { background: #e2f5e6; color: #1d6727; }
        .alert-danger  { background: #fcebe7; color: #8a2210; }
        .muted { color: #888; font-size: 12px; margin-top: 10px; text-align: center; }
        .badge { display: inline-block; padding: 3px 9px; border-radius: 999px; font-size: 11px; background:#1f4391; color:#fff; }
        #map { width: 100%; height: 220px; border-radius: 8px; margin-top: 12px; background:#dfe4ec; display:none; overflow:hidden; }
        #map iframe { width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body>
<div class="card">
    <h1>📍 Check-in da Diligência</h1>

    <div class="row">
        <div class="label">Processo</div>
        <div class="val">{{ $processo->nu_processo_pro ?: ('#'.$processo->cd_processo_pro) }}</div>
    </div>
    <div class="row">
        <div class="label">Cliente</div>
        <div class="val">{{ $processo->cliente ? $processo->cliente->nm_razao_social_cli : '—' }}</div>
    </div>
    <div class="row">
        <div class="label">Vara</div>
        <div class="val">{{ $processo->vara ? $processo->vara->nm_vara_var : '—' }}</div>
    </div>
    <div class="row">
        <div class="label">Cidade</div>
        <div class="val">
            @if($processo->cidade)
                {{ $processo->cidade->nm_cidade_cde }}@if($processo->cidade->estado)/{{ $processo->cidade->estado->sg_estado_est }}@endif
            @else — @endif
        </div>
    </div>
    <div class="row">
        <div class="label">Data / Hora</div>
        <div class="val">
            @if($processo->dt_prazo_fatal_pro)
                {{ \Carbon\Carbon::parse($processo->dt_prazo_fatal_pro)->format('d/m/Y') }}
            @endif
            {{ $processo->hr_audiencia_pro ? ' às ' . date('H:i', strtotime($processo->hr_audiencia_pro)) : '' }}
        </div>
    </div>

    @if($checkin)
        <div class="alert alert-success">
            ✅ Check-in já registrado em
            {{ \Carbon\Carbon::parse($checkin->dt_checkin_pck)->format('d/m/Y H:i') }}.
        </div>
        @if($checkin->nu_latitude_pck && $checkin->nu_longitude_pck)
            <div id="map" style="display:block;">
                <iframe
                    src="https://maps.google.com/maps?q={{ $checkin->nu_latitude_pck }},{{ $checkin->nu_longitude_pck }}&z=16&output=embed"
                    allowfullscreen></iframe>
            </div>
        @endif
    @else
        <button id="btnCheckin" class="btn btn-primary">📍 Registrar check-in agora</button>
        <div id="status"></div>
        <div id="map"></div>
        <div class="muted">Ao clicar, o navegador vai pedir permissão para acessar sua localização.</div>
    @endif
</div>

<script>
(function() {
    var btn    = document.getElementById('btnCheckin');
    if (!btn) return;
    var status = document.getElementById('status');
    var mapDiv = document.getElementById('map');
    var csrf   = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    var url    = "{{ url('/c/' . $token) }}";

    function show(html, cls) {
        status.innerHTML = '<div class="alert ' + cls + '">' + html + '</div>';
    }

    btn.addEventListener('click', function() {
        if (!navigator.geolocation) {
            show('Seu navegador não suporta geolocalização.', 'alert-danger');
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Obtendo localização...';
        show('Aguardando GPS do dispositivo...', 'alert-info');

        navigator.geolocation.getCurrentPosition(function(pos) {
            btn.textContent = 'Enviando check-in...';
            var body = {
                latitude:  pos.coords.latitude,
                longitude: pos.coords.longitude,
                precisao:  pos.coords.accuracy
            };
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                    'X-CSRF-TOKEN':  csrf
                },
                body: JSON.stringify(body)
            })
            .then(function(r){ return r.json().then(function(j){ return {ok: r.ok, json: j}; }); })
            .then(function(res){
                if (res.ok && res.json.success) {
                    show('✅ Check-in registrado com sucesso!', 'alert-success');
                    btn.style.display = 'none';
                    mapDiv.style.display = 'block';
                    mapDiv.innerHTML =
                        '<iframe allowfullscreen src="https://maps.google.com/maps?q=' +
                        body.latitude + ',' + body.longitude + '&z=16&output=embed"></iframe>';
                } else {
                    show('❌ ' + (res.json.message || 'Falha ao registrar.'), 'alert-danger');
                    btn.disabled = false;
                    btn.textContent = 'Tentar novamente';
                }
            })
            .catch(function(err){
                show('❌ Erro de conexão: ' + err.message, 'alert-danger');
                btn.disabled = false;
                btn.textContent = 'Tentar novamente';
            });
        }, function(err) {
            var msg = 'Não foi possível obter sua localização.';
            if (err.code === 1) msg = 'Permissão de localização negada. Habilite no navegador e tente novamente.';
            if (err.code === 2) msg = 'Localização indisponível. Verifique o GPS.';
            if (err.code === 3) msg = 'Tempo esgotado ao obter a localização.';
            show('❌ ' + msg, 'alert-danger');
            btn.disabled = false;
            btn.textContent = 'Tentar novamente';
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        });
    });
})();
</script>
</body>
</html>
