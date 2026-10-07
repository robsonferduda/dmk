@extends('layouts.v2')

@php
    $numero = function ($valor) { return number_format((float) $valor, 0, ',', '.'); };
    $pct = function ($parte, $todo) { return $todo ? round(100 * $parte / $todo) : 0; };
    $dataHora = function ($valor) { return $valor ? $valor->format('d/m H:i') : null; };
@endphp

@section('title', 'Atualização cadastral')
@section('menu', 'atualizacao-cadastral')
@section('classico', 'correspondentes')
@section('page-class', 'page-users-view')

@section('stylesheet')
    <link href="{{ asset('v2/vendor/apexcharts/apexcharts.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page-users-view">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes') }}">Correspondentes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Atualização cadastral</li>
        </ol>
    </nav>

    @if(!empty($tabelasAusentes))
        <div class="card uv-panel">
            <div class="card-body">
                <div class="dmk-empty"><i class="bi bi-database-exclamation"></i>
                    As tabelas da campanha ainda não existem. Rode <code>php artisan migrate</code> (ou o SQL em <code>database/sql/campanha_cadastro.sql</code>).
                </div>
            </div>
        </div>
    @elseif(!$campanha)
        <div class="uv-hero mb-3">
            <div>
                <h1 class="uv-hero-name">Atualização cadastral</h1>
                <p class="uv-hero-email mb-0">E-mail pedindo aos correspondentes que atualizem o cadastro, enviado em lotes de até {{ $limite }} por dia.</p>
            </div>
        </div>
        <div class="card uv-panel" style="max-width: 720px;">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-list-check me-1"></i> Montar a lista de envio</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('v2/atualizacao-cadastral') }}" class="mb-3">
                    <label class="form-label fw-semibold">Correspondentes ativos: processo nos últimos</label>
                    <div class="btn-group btn-group-sm d-flex" role="group">
                        @foreach($periodos as $periodo)
                            <button type="submit" name="meses" value="{{ $periodo }}" class="btn {{ $periodo === $meses ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $periodo }} meses</button>
                        @endforeach
                    </div>
                </form>
                <p class="mb-1"><strong>{{ $numero($candidatos) }}</strong> correspondentes entram na lista, do mais recente para o mais antigo.</p>
                <p class="text-muted small">
                    A {{ $limite }} por dia útil, o envio leva cerca de {{ max(1, (int) ceil($candidatos / max(1, $limite))) }} dia(s).
                    Nada é enviado nesta etapa: a lista fica pausada até alguém clicar em "Iniciar envio".
                </p>
                <form method="POST" action="{{ url('v2/atualizacao-cadastral/preparar') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="meses" value="{{ $meses }}">
                    <button type="submit" class="btn btn-primary" @if(!$candidatos) disabled @endif>
                        <i class="bi bi-list-check me-1"></i> Montar lista com {{ $numero($candidatos) }} correspondentes
                    </button>
                </form>
            </div>
        </div>
    @else
        @php
            $ativa = $campanha->dc_status_cmc === \App\CampanhaCadastro::ATIVA;
            $concluida = $campanha->dc_status_cmc === \App\CampanhaCadastro::CONCLUIDA;
            $statusCampanha = $ativa ? ['Enviando', 'text-success', 'bi-send'] : ($concluida ? ['Envios concluídos', 'text-primary', 'bi-check2-all'] : ['Pausada', 'text-warning', 'bi-pause-circle']);
            $funil = [
                ['Na lista', $resumo['total'], '#94a3b8'],
                ['E-mail enviado', $resumo['enviados'], '#4154f1'],
                ['Abriu o e-mail', $resumo['abertos'], '#0ea5e9'],
                ['Clicou no link', $resumo['cliques'], '#8b5cf6'],
                ['Salvou o cadastro', $resumo['confirmados'], '#f59e0b'],
                ['Pronto para contrato', $resumo['prontos'], '#16a34a'],
            ];
        @endphp

        <div class="uv-hero mb-3">
            <div>
                <h1 class="uv-hero-name">{{ $campanha->nm_campanha_cmc }}</h1>
                <p class="uv-hero-email mb-0">{{ $campanha->dc_criterio_cmc }} · prazo de {{ $campanha->nu_prazo_dias_cmc }} dias após cada envio</p>
                <div class="uv-hero-tags">
                    <span class="uv-pill {{ $statusCampanha[1] }}"><i class="bi {{ $statusCampanha[2] }}"></i> {{ $statusCampanha[0] }}</span>
                    @if($campanha->dt_inicio_cmc)
                        <span class="uv-pill"><i class="bi bi-calendar-event"></i> Início {{ $campanha->dt_inicio_cmc->format('d/m/Y H:i') }}</span>
                    @endif
                    <span class="uv-pill"><i class="bi bi-speedometer2"></i> Hoje {{ $resumo['enviados_hoje'] }} de {{ $resumo['limite_diario'] }}</span>
                    @if($resumo['previsao'] && !$concluida)
                        <span class="uv-pill"><i class="bi bi-flag"></i> Último envio previsto {{ $resumo['previsao']->format('d/m') }}</span>
                    @endif
                </div>
            </div>
            <div class="uv-hero-actions">
                @if($ativa)
                    <form method="POST" action="{{ url('v2/atualizacao-cadastral/pausar') }}">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-outline-warning btn-sm"><i class="bi bi-pause-fill me-1"></i> Pausar envio</button>
                    </form>
                @elseif(!$concluida)
                    <form method="POST" action="{{ url('v2/atualizacao-cadastral/iniciar') }}"
                          onsubmit="return confirm('Iniciar o envio para {{ $resumo['aguardando'] }} correspondentes, em lotes de até {{ $resumo['limite_diario'] }} por dia?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i> {{ $campanha->dt_inicio_cmc ? 'Retomar envio' : 'Iniciar envio' }}</button>
                    </form>
                @endif
                @if($resumo['vencidos'])
                    <form method="POST" action="{{ url('v2/atualizacao-cadastral/reenviar-vencidos') }}"
                          onsubmit="return confirm('Reenviar o e-mail para {{ $resumo['vencidos'] }} correspondente(s) que não atualizaram dentro do prazo?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-repeat me-1"></i> Reenviar para {{ $resumo['vencidos'] }} com prazo vencido</button>
                    </form>
                @endif
            </div>
        </div>

        @if($resumo['smtp_bloqueado'])
            <div class="alert alert-warning small"><i class="bi bi-exclamation-triangle me-1"></i> O servidor de e-mail recusou por limite diário. Os envios retomam automaticamente amanhã.</div>
        @elseif(!$campanha->dt_inicio_cmc)
            <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i> A lista está pronta e nenhum e-mail foi enviado. Clique em "Iniciar envio" para começar.</div>
        @endif

        <section class="row g-3 mb-3">
            <div class="col-xxl-3 col-md-6">
                <article class="nd-kpi-card nd-kpi-leads">
                    <span class="nd-kpi-icon"><i class="bi bi-envelope-paper"></i></span>
                    <span class="nd-kpi-label">E-mails enviados</span>
                    <strong class="nd-kpi-value">{{ $numero($resumo['enviados']) }} <small class="text-muted fs-6">de {{ $numero($resumo['total']) }}</small></strong>
                    <span class="nd-kpi-trend flat">{{ $numero($resumo['aguardando']) }} aguardando · {{ $resumo['falhas'] }} falha(s)</span>
                </article>
            </div>
            <div class="col-xxl-3 col-md-6">
                <article class="nd-kpi-card nd-kpi-cycle">
                    <span class="nd-kpi-icon"><i class="bi bi-cursor"></i></span>
                    <span class="nd-kpi-label">Clicaram no link</span>
                    <strong class="nd-kpi-value">{{ $numero($resumo['cliques']) }}</strong>
                    <span class="nd-kpi-trend flat">{{ $pct($resumo['cliques'], $resumo['enviados']) }}% dos enviados · {{ $numero($resumo['abertos']) }} abriram</span>
                </article>
            </div>
            <div class="col-xxl-3 col-md-6">
                <article class="nd-kpi-card nd-kpi-revenue">
                    <span class="nd-kpi-icon"><i class="bi bi-person-check"></i></span>
                    <span class="nd-kpi-label">Salvaram o cadastro</span>
                    <strong class="nd-kpi-value">{{ $numero($resumo['confirmados']) }}</strong>
                    <span class="nd-kpi-trend {{ $resumo['vencidos'] ? 'down' : 'flat' }}">{{ $pct($resumo['confirmados'], $resumo['total']) }}% da lista · {{ $numero($resumo['vencidos']) }} com prazo vencido</span>
                </article>
            </div>
            <div class="col-xxl-3 col-md-6">
                <article class="nd-kpi-card nd-kpi-retention">
                    <span class="nd-kpi-icon"><i class="bi bi-file-earmark-check"></i></span>
                    <span class="nd-kpi-label">Prontos para contrato</span>
                    <strong class="nd-kpi-value">{{ $numero($resumo['prontos']) }}</strong>
                    <span class="nd-kpi-trend flat">{{ $numero($resumo['com_pendencias']) }} salvaram mas ainda têm pendências</span>
                </article>
            </div>
        </section>

        <section class="row g-3 mb-3">
            <div class="col-xl-8">
                <div class="card uv-panel h-100">
                    <div class="card-header nd-chart-header">
                        <h5 class="card-title mb-0"><i class="bi bi-bar-chart me-1"></i> Andamento diário</h5>
                        <small class="text-muted">Envios, cliques e cadastros salvos por dia</small>
                    </div>
                    <div class="card-body">
                        @if($resumo['enviados'] || $resumo['confirmados'])
                            <div id="grafico-andamento" class="dmk-chart"></div>
                        @else
                            <div class="dmk-empty"><i class="bi bi-bar-chart"></i> O gráfico aparece quando os primeiros e-mails saírem.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card uv-panel h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-funnel me-1"></i> Funil</h5>
                    </div>
                    <div class="card-body">
                        @foreach($funil as [$rotulo, $valor, $cor])
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small">
                                    <span>{{ $rotulo }}</span>
                                    <strong>{{ $numero($valor) }} <span class="text-muted fw-normal">· {{ $pct($valor, $resumo['total']) }}%</span></strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pct($valor, $resumo['total']) }}%; background: {{ $cor }};"></div>
                                </div>
                            </div>
                        @endforeach
                        <p class="text-muted small mt-3 mb-0">
                            Aberturas são aproximadas: só contam quando o leitor de e-mail carrega as imagens.
                            "Salvou o cadastro" é quando o próprio correspondente grava a ficha no sistema.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="card uv-panel" id="envios">
            <div class="card-header">
                <form method="GET" action="{{ url('v2/atualizacao-cadastral') }}#envios" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="situacao" class="form-select form-select-sm w-100" onchange="this.form.submit()">
                            <option value="">Todas as situações ({{ $numero($resumo['total']) }})</option>
                            @foreach($situacoes as $chave => $rotulo)
                                <option value="{{ $chave }}" @if($situacao === $chave) selected @endif>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="search" name="busca" value="{{ $busca }}" class="form-control form-control-sm" placeholder="Buscar por nome ou e-mail">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm flex-fill"><i class="bi bi-search me-1"></i> Filtrar</button>
                        @if($situacao || $busca)
                            <a href="{{ url('v2/atualizacao-cadastral') }}#envios" class="btn btn-outline-secondary btn-sm">Limpar</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Correspondente</th>
                                <th>Último processo</th>
                                <th>Enviado</th>
                                <th>Prazo</th>
                                <th>Abriu</th>
                                <th>Clicou</th>
                                <th>Salvou</th>
                                <th>Contrato</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($envios as $envio)
                                @php
                                    $vencido = !$envio->dt_confirmacao_cme && $envio->dt_prazo_cme && $envio->dt_prazo_cme->lt(\Carbon\Carbon::today());
                                    $pendencias = $envio->pendencias();
                                    $podeReenviar = !$envio->dt_confirmacao_cme && $envio->dc_status_cme !== \App\CampanhaCadastroEnvio::PENDENTE;
                                @endphp
                                <tr>
                                    <td>
                                        @if($envio->vinculo)
                                            <a href="{{ url('v2/correspondentes/'.safe_encrypt($envio->vinculo->cd_correspondente_cor)) }}" class="fw-semibold">{{ $envio->nm_destinatario_cme ?: 'Sem nome' }}</a>
                                        @else
                                            <span class="fw-semibold">{{ $envio->nm_destinatario_cme ?: 'Sem nome' }}</span>
                                        @endif
                                        <div class="text-muted">{{ $envio->dc_email_cme }}</div>
                                    </td>
                                    <td>{{ $envio->dt_ultimo_processo_cme ? $envio->dt_ultimo_processo_cme->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if($envio->dc_status_cme === \App\CampanhaCadastroEnvio::FALHA)
                                            <span class="badge bg-danger" title="{{ $envio->dc_erro_cme }}">Falhou</span>
                                        @elseif($envio->dt_envio_cme)
                                            {{ $dataHora($envio->dt_envio_cme) }}
                                            @if($envio->nu_envios_cme > 1)<span class="text-muted">({{ $envio->nu_envios_cme }}×)</span>@endif
                                            @if($envio->dc_status_cme === \App\CampanhaCadastroEnvio::PENDENTE)<div class="text-muted">reenvio na fila</div>@endif
                                        @elseif($envio->dt_confirmacao_cme)
                                            <span class="text-muted">dispensado</span>
                                        @else
                                            <span class="text-muted">na fila</span>
                                            @if($envio->dc_erro_cme)<i class="bi bi-exclamation-circle text-warning" title="{{ $envio->dc_erro_cme }}"></i>@endif
                                        @endif
                                    </td>
                                    <td class="{{ $vencido ? 'text-danger fw-semibold' : '' }}">{{ $envio->dt_prazo_cme ? $envio->dt_prazo_cme->format('d/m') : '—' }}</td>
                                    <td>{!! $envio->dt_abertura_cme ? '<i class="bi bi-envelope-open text-info"></i> '.$dataHora($envio->dt_abertura_cme) : '<span class="text-muted">—</span>' !!}</td>
                                    <td>{!! $envio->dt_clique_cme ? '<i class="bi bi-cursor-fill text-primary"></i> '.$dataHora($envio->dt_clique_cme) : '<span class="text-muted">—</span>' !!}</td>
                                    <td>{!! $envio->dt_confirmacao_cme ? '<i class="bi bi-check-circle-fill text-success"></i> '.$dataHora($envio->dt_confirmacao_cme) : '<span class="text-muted">—</span>' !!}</td>
                                    <td>
                                        @if($pendencias === null)
                                            <span class="text-muted">—</span>
                                        @elseif(empty($pendencias))
                                            <span class="badge bg-success">Pronto</span>
                                        @else
                                            <span class="badge bg-warning text-dark" title="Falta: {{ implode('; ', $pendencias) }}">{{ count($pendencias) }} pendência(s)</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($podeReenviar)
                                            <form method="POST" action="{{ url('v2/atualizacao-cadastral/envios/'.$envio->cd_campanha_cadastro_envio_cme.'/reenviar') }}"
                                                  onsubmit="return confirm('Colocar {{ addslashes($envio->dc_email_cme) }} no início da fila para receber o e-mail de novo?')">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-outline-secondary btn-sm py-0" title="Reenviar"><i class="bi bi-arrow-repeat"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-4">Nenhum correspondente nesta situação.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($envios->hasPages())
                <div class="card-footer">
                    {{ $envios->fragment('envios')->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@section('script')
@if(!empty($campanha) && ($resumo['enviados'] || $resumo['confirmados']))
<script src="{{ asset('v2/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const serie = @json($serie);
    const escuro = document.documentElement.getAttribute('data-theme') === 'dark';
    new ApexCharts(document.getElementById('grafico-andamento'), {
        chart: { type: 'line', height: 300, toolbar: { show: false }, fontFamily: 'inherit', foreColor: escuro ? '#cbd5e1' : '#5d6877' },
        series: [
            { name: 'E-mails enviados', type: 'column', data: serie.map(function (d) { return d.enviados; }) },
            { name: 'Cliques', type: 'line', data: serie.map(function (d) { return d.cliques; }) },
            { name: 'Cadastros salvos', type: 'line', data: serie.map(function (d) { return d.confirmados; }) }
        ],
        xaxis: { categories: serie.map(function (d) { return d.rotulo; }) },
        yaxis: { min: 0, forceNiceScale: true, labels: { formatter: function (v) { return Math.round(v); } } },
        colors: ['#4154f1', '#8b5cf6', '#16a34a'],
        stroke: { width: [0, 3, 3], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '45%', borderRadius: 3 } },
        dataLabels: { enabled: false },
        legend: { position: 'top', horizontalAlign: 'left' },
        grid: { borderColor: escuro ? '#334155' : '#eef0f4' },
        theme: { mode: escuro ? 'dark' : 'light' }
    }).render();
});
</script>
@endif
@endsection
