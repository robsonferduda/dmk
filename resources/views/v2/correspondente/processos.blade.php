@extends('layouts.v2')

@php
    $nome = trim($vinculo->nm_conta_correspondente_ccr ?: 'Sem nome');
    $moeda = function ($valor) {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    };
    $numero = function ($valor, $casas = 0) {
        return number_format((float) $valor, $casas, ',', '.');
    };
    $data = function ($valor) {
        return $valor ? \Carbon\Carbon::parse($valor)->format('d/m/Y') : '—';
    };
    $trend = function ($variacao) use ($numero) {
        if ($variacao === null) {
            return '<span class="nd-kpi-trend flat">sem base de comparação</span>';
        }
        $classe = $variacao > 0 ? 'up' : ($variacao < 0 ? 'down' : 'flat');
        $icone = $variacao > 0 ? 'bi-arrow-up-right' : ($variacao < 0 ? 'bi-arrow-down-right' : 'bi-dash');
        return '<span class="nd-kpi-trend ' . $classe . '" title="Último trimestre completo contra o anterior"><i class="bi ' . $icone . '"></i> '
            . ($variacao > 0 ? '+' : '') . $numero($variacao, 1) . '% no trimestre</span>';
    };

    $totalPeriodo = array_sum(array_column($serie, 'total'));
    $honorariosPeriodo = array_sum(array_column($serie, 'honorarios'));
    $classificacoes = [
        'crescimento' => ['Em crescimento', 'bi-graph-up-arrow', 'text-success'],
        'queda'       => ['Em queda', 'bi-graph-down-arrow', 'text-danger'],
        'estavel'     => ['Estável', 'bi-arrow-left-right', 'text-primary'],
        'sem_dados'   => ['Sem dados suficientes', 'bi-hourglass', 'text-muted'],
    ];
    $classificacao = $classificacoes[$tendencia['classificacao']];
    $gruposStatus = [
        'finalizado' => ['Finalizado', 'dmk-pill-advogado'],
        'andamento'  => ['Em andamento', 'dmk-pill-andamento'],
        'cancelado'  => ['Cancelado', 'dmk-pill-cancelado'],
    ];
    $paleta = ['#4154f1', '#16a34a', '#f59e0b', '#0ea5e9', '#dc3545', '#8b5cf6', '#14b8a6', '#ec4899'];
    $totalServicos = array_sum(array_column($servicos, 'total'));
    $maiorComarca = count($comarcas) ? max(array_column($comarcas, 'total')) : 0;
    $queryPeriodo = function ($valor) use ($filtros) {
        return url()->current() . '?' . http_build_query(array_merge($filtros, ['meses' => $valor]));
    };
@endphp

@section('title', 'Processos · '.$nome)
@section('menu', 'correspondentes')
@section('classico', 'correspondente/atividades/'.$vinculo->cd_correspondente_cor)
@section('page-class', 'page-users-view')

@section('stylesheet')
    <link href="{{ asset('v2/vendor/apexcharts/apexcharts.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="page-users-view">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes') }}">Correspondentes</a></li>
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes/'.$idSafe) }}">{{ $nome }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Processos e desempenho</li>
        </ol>
    </nav>

    <div class="uv-hero mb-3">
        <div class="uv-hero-user">
            @if($foto)
                <img src="{{ $foto }}" alt="Foto de {{ $nome }}" class="dmk-avatar-foto">
            @else
                <span class="dmk-avatar-letter">{{ mb_strtoupper(mb_substr($nome, 0, 1)) }}</span>
            @endif
            <div>
                <h1 class="uv-hero-name">{{ $nome }}</h1>
                <p class="uv-hero-email mb-0">Processos e desempenho no escritório</p>
                <div class="uv-hero-tags">
                    @if($resumo->primeiro)
                        <span class="uv-pill"><i class="bi bi-calendar-event"></i> Desde {{ $data($resumo->primeiro) }}</span>
                        <span class="uv-pill"><i class="bi bi-clock-history"></i> Último ato {{ $data($resumo->ultimo) }}</span>
                    @endif
                    @if($resumo->agendados)
                        <span class="uv-pill"><i class="bi bi-calendar-check"></i> {{ $numero($resumo->agendados) }} agendado(s)</span>
                    @endif
                    <span class="uv-pill {{ $classificacao[2] }}"><i class="bi {{ $classificacao[1] }}"></i> {{ $classificacao[0] }}</span>
                </div>
            </div>
        </div>
        <div class="uv-hero-actions">
            <div class="btn-group btn-group-sm" role="group" aria-label="Período da análise">
                @foreach($periodos as $periodo)
                    <a href="{{ $queryPeriodo($periodo) }}" class="btn {{ $periodo === $meses ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $periodo }}m</a>
                @endforeach
            </div>
            <a href="{{ url('v2/correspondentes/'.$idSafe) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-person me-1"></i> Perfil</a>
        </div>
    </div>

    <section class="row g-3 mb-3">
        <div class="col-xxl-3 col-md-6">
            <article class="nd-kpi-card nd-kpi-leads">
                <span class="nd-kpi-icon"><i class="bi bi-folder2-open"></i></span>
                <span class="nd-kpi-label">Processos em {{ $meses }} meses</span>
                <strong class="nd-kpi-value">{{ $numero($totalPeriodo) }}</strong>
                {!! $trend($tendencia['volume']['variacao']) !!}
            </article>
        </div>
        <div class="col-xxl-3 col-md-6">
            <article class="nd-kpi-card nd-kpi-cycle">
                <span class="nd-kpi-icon"><i class="bi {{ $classificacao[1] }}"></i></span>
                <span class="nd-kpi-label">Média mensal (12 meses)</span>
                <strong class="nd-kpi-value">{{ $numero($tendencia['media_mensal'], 1) }}</strong>
                @if($tendencia['variacao_mensal'] !== null)
                    <span class="nd-kpi-trend {{ $tendencia['variacao_mensal'] > 0 ? 'up' : ($tendencia['variacao_mensal'] < 0 ? 'down' : 'flat') }}" title="Inclinação da reta de tendência sobre a média">
                        {{ $tendencia['variacao_mensal'] > 0 ? '+' : '' }}{{ $numero($tendencia['variacao_mensal'], 1) }}% ao mês
                    </span>
                @else
                    <span class="nd-kpi-trend flat">tendência indisponível</span>
                @endif
            </article>
        </div>
        <div class="col-xxl-3 col-md-6">
            <article class="nd-kpi-card nd-kpi-revenue">
                <span class="nd-kpi-icon"><i class="bi bi-cash-stack"></i></span>
                <span class="nd-kpi-label">Honorários em {{ $meses }} meses</span>
                <strong class="nd-kpi-value">{{ $moeda($honorariosPeriodo) }}</strong>
                {!! $trend($tendencia['honorarios']['variacao']) !!}
            </article>
        </div>
        <div class="col-xxl-3 col-md-6">
            <article class="nd-kpi-card nd-kpi-retention">
                <span class="nd-kpi-icon"><i class="bi bi-receipt"></i></span>
                <span class="nd-kpi-label">Honorário médio (último trimestre)</span>
                <strong class="nd-kpi-value">{{ $tendencia['ticket']['atual'] !== null ? $moeda($tendencia['ticket']['atual']) : '—' }}</strong>
                {!! $trend($tendencia['ticket']['variacao']) !!}
            </article>
        </div>
    </section>

    <section class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card uv-panel h-100">
                <div class="card-header nd-chart-header">
                    <h5 class="card-title mb-0"><i class="bi bi-bar-chart me-1"></i> Volume mensal e tendência</h5>
                    <small class="text-muted">Data do ato · * mês corrente parcial</small>
                </div>
                <div class="card-body">
                    @if($totalPeriodo)
                        <div id="grafico-volume" class="dmk-chart"></div>
                    @else
                        <div class="dmk-empty"><i class="bi bi-bar-chart"></i> Nenhum processo nos últimos {{ $meses }} meses.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card uv-panel h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-lightbulb me-1"></i> Leitura da tendência</h5>
                </div>
                <div class="card-body">
                    @foreach($leitura as $frase)
                        <div class="dmk-insight dmk-insight-{{ $frase[0] }}">
                            <i class="bi {{ $frase[0] === 'up' ? 'bi-arrow-up-right-circle' : ($frase[0] === 'down' ? 'bi-arrow-down-right-circle' : 'bi-info-circle') }}"></i>
                            <span>{{ $frase[1] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if($totalPeriodo)
    <section class="row g-3 mb-3">
        <div class="col-xl-6">
            <div class="card uv-panel h-100">
                <div class="card-header nd-chart-header">
                    <h5 class="card-title mb-0"><i class="bi bi-cash-coin me-1"></i> Honorários e honorário médio</h5>
                    <small class="text-muted">Sem cancelados</small>
                </div>
                <div class="card-body">
                    <div id="grafico-honorarios" class="dmk-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card uv-panel h-100">
                <div class="card-header nd-chart-header">
                    <h5 class="card-title mb-0"><i class="bi bi-speedometer2 me-1"></i> Operação</h5>
                    <small class="text-muted">Check-in e prazo de finalização (dados a partir de 2025)</small>
                </div>
                <div class="card-body">
                    <div id="grafico-operacao" class="dmk-chart"></div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="row g-3 mb-3">
        <div class="col-xxl-6">
            <div class="card uv-panel h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-1"></i> Tipos de serviço</h5>
                </div>
                <div class="card-body">
                    @if(count($servicos))
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5"><div id="grafico-servicos"></div></div>
                        <div class="col-md-7">
                        <ul class="list-unstyled mb-0 dmk-legenda">
                            @foreach($servicos as $i => $servico)
                                <li>
                                    <span class="dmk-legenda-cor" style="background: {{ $paleta[$i % count($paleta)] }}"></span>
                                    <span class="dmk-legenda-rotulo">{{ $servico->rotulo }}</span>
                                    <strong class="dmk-num">{{ $numero($servico->total) }}</strong>
                                    <small class="text-muted dmk-num">{{ $numero($totalServicos ? $servico->total * 100 / $totalServicos : 0, 1) }}%</small>
                                </li>
                            @endforeach
                        </ul>
                        </div>
                    </div>
                    @else
                        <p class="text-muted small mb-0">Sem processos no período.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card uv-panel h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-geo-alt me-1"></i> Comarcas mais atendidas</h5>
                </div>
                <div class="card-body">
                    @forelse($comarcas as $comarca)
                        <div class="dmk-rank">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="text-truncate" title="{{ $comarca->rotulo }}">{{ $comarca->rotulo }}</span>
                                <strong class="dmk-num">{{ $numero($comarca->total) }}</strong>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $comarca->total }}" aria-valuemin="0" aria-valuemax="{{ $maiorComarca }}">
                                <div class="progress-bar bg-info" style="width: {{ $maiorComarca ? max(round($comarca->total * 100 / $maiorComarca), 1) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Sem processos no período.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card uv-panel h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-buildings me-1"></i> Principais clientes</h5>
                </div>
                <div class="card-body">
                    @php $maiorCliente = count($clientes) ? max(array_column($clientes, 'total')) : 0; @endphp
                    @forelse($clientes as $cliente)
                        <div class="dmk-rank">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="text-truncate" title="{{ $cliente->rotulo }}">{{ $cliente->rotulo }}</span>
                                <strong class="dmk-num">{{ $numero($cliente->total) }}</strong>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $cliente->total }}" aria-valuemin="0" aria-valuemax="{{ $maiorCliente }}">
                                <div class="progress-bar" style="width: {{ $maiorCliente ? max(round($cliente->total * 100 / $maiorCliente), 1) : 0 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $moeda($cliente->honorarios) }} em honorários</small>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Sem processos no período.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>


    <div class="card uv-panel" id="processos">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0"><i class="bi bi-list-task me-1"></i> Processos <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $numero($processos->total()) }}</span></h5>
            <form method="GET" action="{{ url()->current() }}#processos" class="d-flex flex-wrap gap-2 align-items-center dmk-proc-filtros">
                <input type="hidden" name="meses" value="{{ $meses }}">
                <select name="situacao" class="form-select form-select-sm" aria-label="Situação">
                    <option value="">Todas as situações</option>
                    <option value="andamento" {{ ($filtros['situacao'] ?? '') === 'andamento' ? 'selected' : '' }}>Em andamento</option>
                    <option value="finalizado" {{ ($filtros['situacao'] ?? '') === 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                    <option value="cancelado" {{ ($filtros['situacao'] ?? '') === 'cancelado' ? 'selected' : '' }}>Cancelados / recusados</option>
                </select>
                <input type="date" name="de" class="form-control form-control-sm" value="{{ $filtros['de'] ?? '' }}" aria-label="Ato a partir de" title="Data do ato a partir de">
                <input type="date" name="ate" class="form-control form-control-sm" value="{{ $filtros['ate'] ?? '' }}" aria-label="Ato até" title="Data do ato até">
                <input type="search" name="busca" class="form-control form-control-sm" placeholder="Nº do processo ou parte" value="{{ $filtros['busca'] ?? '' }}">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
                @if(!empty($filtros))
                    <a href="{{ url()->current() }}?meses={{ $meses }}#processos" class="btn btn-light btn-sm" title="Limpar filtros"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        <div class="dmk-stat-strip">
            <div><span>Histórico total</span><strong class="dmk-num">{{ $numero($resumo->total) }}</strong></div>
            <div><span>Finalizados</span><strong class="dmk-num text-success">{{ $numero($resumo->finalizados) }}</strong></div>
            <div><span>Em andamento</span><strong class="dmk-num">{{ $numero($resumo->total - $resumo->finalizados - $resumo->cancelados) }}</strong></div>
            <div><span>Cancelados / recusados</span><strong class="dmk-num text-danger">{{ $numero($resumo->cancelados) }}</strong></div>
            <div><span>Honorários</span><strong class="dmk-num">{{ $moeda($resumo->honorarios) }}</strong></div>
            <div><span>Clientes</span><strong class="dmk-num">{{ $numero($resumo->clientes) }}</strong></div>
            <div><span>Comarcas</span><strong class="dmk-num">{{ $numero($resumo->comarcas) }}</strong></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 dmk-table-proc">
                <thead>
                    <tr>
                        <th>Data do ato</th>
                        <th>Processo</th>
                        <th>Cliente</th>
                        <th>Comarca</th>
                        <th>Serviço</th>
                        <th class="text-end">Honorário</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $processo)
                        @php
                            $grupo = \App\Services\Correspondente\AnaliseProcessos::grupoStatus($processo->cd_status_processo_stp);
                            $cliente = $processo->cliente;
                        @endphp
                        <tr>
                            <td class="dmk-num text-nowrap">
                                {{ $data($processo->dt_prazo_fatal_pro) }}
                                @if($processo->hr_audiencia_pro)
                                    <small class="d-block text-muted">{{ substr($processo->hr_audiencia_pro, 0, 5) }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="fw-semibold dmk-num" title="Abrir na versão clássica">{{ $processo->nu_processo_pro ?: 'Sem número' }}</a>
                                @if($processo->nm_autor_pro || $processo->nm_reu_pro)
                                    <small class="d-block text-muted text-truncate dmk-partes" title="{{ $processo->nm_autor_pro }} x {{ $processo->nm_reu_pro }}">{{ $processo->nm_autor_pro ?: '—' }} x {{ $processo->nm_reu_pro ?: '—' }}</small>
                                @endif
                            </td>
                            <td class="small"><span class="dmk-clamp" title="{{ $cliente ? (trim($cliente->nm_fantasia_cli) ?: $cliente->nm_razao_social_cli) : '' }}">{{ $cliente ? (trim($cliente->nm_fantasia_cli) ?: $cliente->nm_razao_social_cli) : '—' }}</span></td>
                            <td class="small text-nowrap">{{ $processo->cidade ? $processo->cidade->nm_cidade_cde.' / '.optional($processo->cidade->estado)->sg_estado_est : '—' }}</td>
                            @php $servico = optional(optional($processo->honorario)->tipoServicoCorrespondente)->nm_tipo_servico_tse; @endphp
                            <td class="small"><span class="dmk-clamp" title="{{ $servico }}">{{ $servico ?: '—' }}</span></td>
                            <td class="text-end dmk-num text-nowrap">{{ ($processo->honorario && $processo->honorario->vl_taxa_honorario_correspondente_pth !== null) ? $moeda($processo->honorario->vl_taxa_honorario_correspondente_pth) : '—' }}</td>
                            <td>
                                <span class="dmk-pill {{ $gruposStatus[$grupo][1] }}" title="{{ optional($processo->status)->nm_status_processo_conta_stp }}">{{ $gruposStatus[$grupo][0] }}</span>
                                <small class="d-block text-muted text-truncate dmk-status" title="{{ optional($processo->status)->nm_status_processo_conta_stp }}">{{ optional($processo->status)->nm_status_processo_conta_stp }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="dmk-empty"><i class="bi bi-folder-x"></i> Nenhum processo encontrado.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($processos->hasPages())
            <div class="card-footer bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2">
                <small class="text-muted">Exibindo {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $numero($processos->total()) }}</small>
                {{ $processos->fragment('processos')->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('v2/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const serie = @json($serie);
    const linhaTendencia = @json($tendencia['linha_tendencia']);
    const servicos = @json($servicos);
    const paleta = @json($paleta);

    const css = getComputedStyle(document.documentElement);
    const cor = function (variavel, padrao) { return (css.getPropertyValue(variavel) || '').trim() || padrao; };
    const escuro = function () { return document.documentElement.getAttribute('data-theme') === 'dark'; };
    const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
    const moedaCentavos = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    const inteiro = new Intl.NumberFormat('pt-BR');
    const rotulos = serie.map(function (m) { return m.rotulo + (m.parcial ? '*' : ''); });

    const cores = {
        finalizados: cor('--success-color', '#16a34a'),
        andamento: cor('--accent-color', '#4154f1'),
        cancelados: cor('--danger-color', '#dc3545'),
        tendencia: cor('--warning-color', '#f59e0b'),
        participacao: cor('--info-color', '#0ea5e9')
    };

    function base(extra) {
        return Object.assign({
            chart: { fontFamily: 'Nunito Sans, sans-serif', toolbar: { show: false }, background: 'transparent', foreColor: cor('--muted-color', '#6c757d') },
            theme: { mode: escuro() ? 'dark' : 'light' },
            grid: { borderColor: cor('--border-color-light', '#eef0f4'), strokeDashArray: 4 },
            dataLabels: { enabled: false },
            legend: { position: 'top', horizontalAlign: 'left', fontSize: '12px', clusterGroupedSeriesOrientation: 'horizontal', itemMargin: { horizontal: 8, vertical: 2 } },
            tooltip: { theme: escuro() ? 'dark' : 'light' }
        }, extra);
    }

    const graficos = [];
    const criar = function (seletor, opcoes) {
        const el = document.querySelector(seletor);
        if (el) { graficos.push(new ApexCharts(el, opcoes)); }
    };

    criar('#grafico-volume', base({
        chart: { type: 'line', height: 360, stacked: true, fontFamily: 'Nunito Sans, sans-serif', toolbar: { show: false }, background: 'transparent', foreColor: cor('--muted-color', '#6c757d') },
        series: [
            { name: 'Finalizados', type: 'column', data: serie.map(function (m) { return m.finalizados; }) },
            { name: 'Em andamento', type: 'column', data: serie.map(function (m) { return m.andamento; }) },
            { name: 'Cancelados', type: 'column', data: serie.map(function (m) { return m.cancelados; }) },
            { name: 'Tendência (12m)', type: 'line', data: linhaTendencia },
            { name: '% do escritório', type: 'line', data: serie.map(function (m) { return m.participacao; }) }
        ],
        colors: [cores.finalizados, cores.andamento, cores.cancelados, cores.tendencia, cores.participacao],
        stroke: { width: [0, 0, 0, 3, 2], dashArray: [0, 0, 0, 6, 0], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '58%', borderRadius: 3 } },
        xaxis: { categories: rotulos, labels: { rotate: -45, hideOverlappingLabels: true } },
        yaxis: [
            { seriesName: 'Finalizados', title: { text: 'Processos' }, labels: { formatter: function (v) { return inteiro.format(Math.round(v)); } } },
            { seriesName: 'Finalizados', show: false },
            { seriesName: 'Finalizados', show: false },
            { seriesName: 'Finalizados', show: false },
            { seriesName: '% do escritório', opposite: true, min: 0, title: { text: '% do escritório' }, labels: { formatter: function (v) { return v === null ? '' : v.toFixed(1) + '%'; } } }
        ],
        tooltip: {
            shared: true, intersect: false, theme: escuro() ? 'dark' : 'light',
            y: { formatter: function (v, o) { if (v === null || v === undefined) { return '—'; } return o.seriesIndex === 4 ? v.toFixed(2) + '%' : inteiro.format(v); } }
        }
    }));

    criar('#grafico-honorarios', base({
        chart: { type: 'line', height: 300, fontFamily: 'Nunito Sans, sans-serif', toolbar: { show: false }, background: 'transparent', foreColor: cor('--muted-color', '#6c757d') },
        series: [
            { name: 'Honorários', type: 'area', data: serie.map(function (m) { return m.honorarios; }) },
            { name: 'Honorário médio', type: 'line', data: serie.map(function (m) { return m.ticket; }) }
        ],
        colors: [cores.finalizados, cores.tendencia],
        stroke: { width: [2, 3], curve: 'smooth' },
        fill: { type: 'solid', opacity: [0.18, 1] },
        xaxis: { categories: rotulos, labels: { rotate: -45, hideOverlappingLabels: true } },
        yaxis: [
            { title: { text: 'Honorários' }, labels: { formatter: function (v) { return moeda.format(v); } } },
            { opposite: true, title: { text: 'Médio por processo' }, labels: { formatter: function (v) { return v === null ? '' : moeda.format(v); } } }
        ],
        tooltip: { shared: true, intersect: false, theme: escuro() ? 'dark' : 'light', y: { formatter: function (v) { return v === null || v === undefined ? '—' : moedaCentavos.format(v); } } }
    }));

    criar('#grafico-operacao', base({
        chart: { type: 'line', height: 300, fontFamily: 'Nunito Sans, sans-serif', toolbar: { show: false }, background: 'transparent', foreColor: cor('--muted-color', '#6c757d') },
        series: [
            { name: 'Check-in nos finalizados', type: 'column', data: serie.map(function (m) { return m.taxa_checkin; }) },
            { name: 'Dias do ato à finalização (mediana)', type: 'line', data: serie.map(function (m) { return m.dias_finalizacao; }) }
        ],
        colors: [cores.participacao, cores.cancelados],
        stroke: { width: [0, 3], curve: 'smooth' },
        plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
        markers: { size: [0, 3] },
        xaxis: { categories: rotulos, labels: { rotate: -45, hideOverlappingLabels: true } },
        yaxis: [
            { min: 0, max: 100, title: { text: 'Check-in' }, labels: { formatter: function (v) { return v === null ? '' : Math.round(v) + '%'; } } },
            { opposite: true, min: 0, title: { text: 'Dias' }, labels: { formatter: function (v) { return v === null ? '' : v.toFixed(1); } } }
        ],
        noData: { text: 'Sem dados de check-in/finalização no período' },
        tooltip: {
            shared: true, intersect: false, theme: escuro() ? 'dark' : 'light',
            y: { formatter: function (v, o) { if (v === null || v === undefined) { return '—'; } return o.seriesIndex === 0 ? v.toFixed(1) + '%' : v.toFixed(1).replace('.', ',') + ' dia(s)'; } }
        }
    }));

    if (servicos.length) {
        criar('#grafico-servicos', base({
            chart: { type: 'donut', height: 240, fontFamily: 'Nunito Sans, sans-serif', background: 'transparent', foreColor: cor('--muted-color', '#6c757d') },
            series: servicos.map(function (s) { return parseInt(s.total, 10); }),
            labels: servicos.map(function (s) { return s.rotulo; }),
            colors: paleta,
            legend: { show: false },
            plotOptions: { pie: { donut: { size: '62%', labels: { show: true, total: { show: true, label: 'Processos', formatter: function (w) { return inteiro.format(w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0)); } } } } } },
            stroke: { width: 1, colors: [cor('--card-bg', '#fff')] }
        }));
    }

    graficos.forEach(function (grafico) { grafico.render(); });

    new MutationObserver(function () {
        const modo = escuro() ? 'dark' : 'light';
        graficos.forEach(function (grafico) {
            grafico.updateOptions({ theme: { mode: modo }, tooltip: { theme: modo }, chart: { background: 'transparent' } }, false, false);
        });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
});
</script>
@endsection
