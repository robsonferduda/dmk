@extends('layouts.v2')

@section('title', 'Correspondentes')
@section('menu', 'correspondentes')
@section('classico', 'correspondentes')
@section('page-class', 'page-users')

@section('content')
<div class="page-users users-lab">
    <div class="users-lab-hero mb-3">
        <div>
            <h1 class="page-title mb-1">Correspondentes</h1>
            <p class="users-page-subtitle">Correspondentes vinculados ao escritório, com atuação, categoria e comarca de origem.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ url('v2/correspondentes') . '?' . http_build_query(array_merge($filtros, ['exportar' => 1])) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Exportar
            </a>
            <a href="{{ url('correspondente/novo') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Novo correspondente
            </a>
        </div>
    </div>

    <div class="card dmk-filters mb-3">
        <div class="card-body">
            <form method="GET" action="{{ url('v2/correspondentes') }}" class="row g-2 align-items-end">
                <div class="col-md-6 col-xl-2">
                    <label class="form-label" for="filtro-estado">Estado</label>
                    <select id="filtro-estado" name="cd_estado_est" class="form-select">
                        <option value="">Todos</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->cd_estado_est }}" {{ ($filtros['cd_estado_est'] ?? '') == $estado->cd_estado_est ? 'selected' : '' }}>{{ $estado->nm_estado_est }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label class="form-label" for="filtro-cidade" title="Considera todas as cidades de atuação, não só a comarca de origem">
                        Cidade de atuação <i class="bi bi-info-circle"></i>
                    </label>
                    <select id="filtro-cidade" name="cd_cidade_cde" class="form-select"></select>
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label" for="filtro-categoria">Categoria</label>
                    <select id="filtro-categoria" name="cd_categoria_correspondente_cac" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->cd_categoria_correspondente_cac }}" {{ ($filtros['cd_categoria_correspondente_cac'] ?? '') == $categoria->cd_categoria_correspondente_cac ? 'selected' : '' }}>{{ $categoria->dc_categoria_correspondente_cac }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-3">
                    <label class="form-label" for="filtro-identificacao">CPF/CNPJ</label>
                    <input type="text" id="filtro-identificacao" name="identificacao" class="form-control" placeholder="Exato, como cadastrado" value="{{ $filtros['identificacao'] ?? '' }}">
                </div>
                <div class="col-md-4 col-xl-2">
                    <label class="form-label" for="filtro-foto">Foto de perfil</label>
                    <select id="filtro-foto" name="foto" class="form-select">
                        <option value="">Todos</option>
                        <option value="com" {{ ($filtros['foto'] ?? '') === 'com' ? 'selected' : '' }}>Com foto ({{ $totalComFoto }})</option>
                        <option value="sem" {{ ($filtros['foto'] ?? '') === 'sem' ? 'selected' : '' }}>Sem foto</option>
                    </select>
                </div>
                @if(!empty($filtros['nome']))
                    <input type="hidden" name="nome" value="{{ $filtros['nome'] }}">
                @endif
                <div class="col-12 d-flex gap-2 justify-content-end">
                    @if(!empty($filtros))
                        <a href="{{ url('v2/correspondentes') }}" class="btn btn-light btn-sm"><i class="bi bi-x-lg me-1"></i> Limpar filtros</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card users-list-card">
        @php
            $abas = ['todos' => 'Todos', 'advogados' => 'Advogados', 'prepostos' => 'Prepostos', 'sem_atuacao' => 'Não informado'];
        @endphp
        <div class="users-toolbar">
            <div class="users-toolbar-left">
                <div class="users-filter-tabs">
                    @foreach($abas as $chave => $rotulo)
                        <a href="{{ url('v2/correspondentes') . '?' . http_build_query(array_merge($filtros, $chave !== 'todos' ? ['atuacao' => $chave] : [])) }}"
                           class="users-filter-tab text-decoration-none {{ $grupo === $chave ? 'active' : '' }}">
                            {{ $rotulo }} <span class="users-filter-count">{{ $totais[$chave] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="users-toolbar-right">
                <form method="GET" action="{{ url('v2/correspondentes') }}" class="users-search">
                    @foreach(array_diff_key($filtros, ['nome' => 1]) as $campo => $valor)
                        <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                    @endforeach
                    @if($grupo !== 'todos')
                        <input type="hidden" name="atuacao" value="{{ $grupo }}">
                    @endif
                    <i class="bi bi-search"></i>
                    <input type="search" name="nome" value="{{ $filtros['nome'] ?? '' }}" placeholder="Buscar pelo nome e tecle Enter" autocomplete="off">
                </form>
            </div>
        </div>

        <div class="table-responsive users-table-wrap">
            <table class="table table-hover align-middle mb-0" id="tabela-correspondentes">
                <thead>
                    <tr>
                        <th>Correspondente</th>
                        <th>Atuação</th>
                        <th>Categoria</th>
                        <th>Comarca de origem</th>
                        <th>Documentos</th>
                        <th class="users-th-actions text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($correspondentes as $correspondente)
                        @php
                            $nome = trim($correspondente->nm_conta_correspondente_ccr ?: 'Sem nome');
                            $urlDetalhes = url('v2/correspondentes/'.$correspondente->id_safe);
                        @endphp
                        <tr>
                            <td>
                                <div class="users-user">
                                    @if($correspondente->foto)
                                        <img src="{{ $correspondente->foto }}" alt="Foto de {{ $nome }}" class="dmk-avatar-foto sm" loading="lazy">
                                    @else
                                        <span class="dmk-avatar-letter sm">{{ mb_strtoupper(mb_substr($nome, 0, 1)) }}</span>
                                    @endif
                                    <div class="users-user-info">
                                        <a href="{{ $urlDetalhes }}" class="users-user-name">{{ $nome }}</a>
                                        <span class="users-user-email">{{ $correspondente->email ?: 'Sem email de acesso' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>@include('v2.correspondente.partes.atuacao', ['flAdvogado' => $correspondente->atuacao])</td>
                            <td>
                                @if($correspondente->dc_categoria_correspondente_cac)
                                    <span class="dmk-pill dmk-cat" style="--cat-color: {{ $correspondente->color_cac ?: '#6c757d' }}">{{ $correspondente->dc_categoria_correspondente_cac }}</span>
                                @else
                                    <span class="users-meta text-muted">—</span>
                                @endif
                            </td>
                            <td class="users-meta">{{ $correspondente->nm_cidade_cde ?: '—' }}</td>
                            <td class="dmk-docs">
                                <span class="dmk-mono d-block">{{ $correspondente->nu_identificacao_ide ?: 'CPF/CNPJ não informado' }}</span>
                                @if($correspondente->nu_oab_ide)
                                    <span class="users-user-email text-truncate" title="{{ $correspondente->nu_oab_ide }}">OAB {{ $correspondente->nu_oab_ide }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="users-actions">
                                    <a href="{{ $urlDetalhes }}" class="users-action-btn" title="Detalhes"><i class="bi bi-eye"></i></a>
                                    <a href="{{ url('v2/correspondentes/'.$correspondente->id_safe.'/editar') }}" class="users-action-btn" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <div class="dropdown">
                                        <button class="users-action-btn dropdown-toggle" data-bs-toggle="dropdown" type="button" title="Mais opções"><i class="bi bi-three-dots"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ url('correspondente/comarcas/'.$correspondente->id_crypt) }}"><i class="bi bi-geo-alt me-2"></i> Comarcas</a></li>
                                            <li><a class="dropdown-item" href="{{ url('correspondente/despesas/'.$correspondente->cd_correspondente_cor) }}"><i class="bi bi-cash-coin me-2"></i> Despesas</a></li>
                                            <li><a class="dropdown-item" href="{{ url('correspondente/honorarios/'.$correspondente->id_crypt) }}"><i class="bi bi-currency-dollar me-2"></i> Honorários</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ url('correspondente/detalhes/'.$correspondente->id_crypt) }}"><i class="bi bi-box-arrow-up-left me-2"></i> Abrir na versão clássica</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($correspondentes->isEmpty())
                <div class="dmk-empty">
                    <i class="bi bi-search"></i>
                    Nenhum correspondente encontrado com esses filtros.
                </div>
            @endif
        </div>

        <div class="card-footer bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="small text-muted">
                @if($correspondentes->total())
                    Exibindo {{ $correspondentes->firstItem() }}–{{ $correspondentes->lastItem() }} de {{ $correspondentes->total() }} correspondentes.
                @endif
                A exportação não inclui a categoria INATIVO.
            </div>
            <div class="dmk-pagination">
                {{ $correspondentes->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        DMK.cidadesPorEstado(
            document.getElementById('filtro-estado'),
            document.getElementById('filtro-cidade'),
            { selecionada: @json($filtros['cd_cidade_cde'] ?? '') }
        );
        DMK.select(document.getElementById('filtro-categoria'));

    });
</script>
@endsection
