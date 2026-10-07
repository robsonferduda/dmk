@extends('layouts.v2')

@php
    $nome = trim($vinculo->nm_conta_correspondente_ccr ?: 'Sem nome');
    $cpf = optional($entidade)->cpf;
    $cnpj = optional($entidade)->cnpj;
    $oab = optional($entidade)->oab;
    $rg = optional($entidade)->rg;
    $emailAcesso = \App\User::where('cd_conta_con', $vinculo->cd_correspondente_cor)->where('cd_nivel_niv', \App\Enums\Nivel::CORRESPONDENTE)->orderBy('id')->value('email');
    $contratoGerado = $vinculo->contratoFoiGerado();
    $aguardandoAssinatura = $assinaturaPainel && $assinaturaPainel['emAndamento'];
@endphp

@section('title', $nome)
@section('menu', 'correspondentes')
@section('classico', 'correspondente/detalhes/'.$idCrypt)
@section('page-class', 'page-users-view')

@section('content')
<div class="page-users-view">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ url('v2/correspondentes') }}">Correspondentes</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $nome }}</li>
        </ol>
    </nav>

    <div class="uv-hero mb-3">
        <div class="uv-hero-user">
            @if($foto)
                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-foto" title="Ampliar foto">
                    <img src="{{ $foto }}" alt="Foto de {{ $nome }}" class="dmk-avatar-foto">
                </a>
            @else
                <span class="dmk-avatar-letter" title="Sem foto de perfil">{{ mb_strtoupper(mb_substr($nome, 0, 1)) }}</span>
            @endif
            <div>
                <h1 class="uv-hero-name">{{ $nome }}</h1>
                <p class="uv-hero-email mb-0">{{ $emailAcesso ?: 'Sem email de acesso' }}</p>
                <div class="uv-hero-tags">
                    @include('v2.correspondente.partes.atuacao', ['flAdvogado' => $flAdvogado])
                    @if($vinculo->categoria)
                        <span class="dmk-pill dmk-cat" style="--cat-color: {{ $vinculo->categoria->color_cac ?: '#6c757d' }}">{{ $vinculo->categoria->dc_categoria_correspondente_cac }}</span>
                    @endif
                    @if($oab)
                        <span class="uv-pill"><i class="bi bi-award"></i> OAB {{ $oab->nu_identificacao_ide }}</span>
                    @endif
                    <span class="uv-pill">{{ $vinculo->tipoPessoa ? $vinculo->tipoPessoa->nm_tipo_pessoa_tpp : 'Tipo não informado' }}</span>
                </div>
            </div>
        </div>
        <div class="uv-hero-actions">
            <a href="{{ url('v2/correspondentes/'.$idSafe.'/processos') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-graph-up me-1"></i> Processos e desempenho</a>
            <a href="{{ url('v2/correspondentes/'.$idSafe.'/editar') }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Editar dados</a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" type="button">
                    <i class="bi bi-grid me-1"></i> Mais
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ url('correspondente/comarcas/'.$idCrypt) }}"><i class="bi bi-geo-alt me-2"></i> Gerenciar comarcas</a></li>
                    <li><a class="dropdown-item" href="{{ url('correspondente/despesas/'.$vinculo->cd_correspondente_cor) }}"><i class="bi bi-cash-coin me-2"></i> Gerenciar despesas</a></li>
                    <li><a class="dropdown-item" href="{{ url('correspondente/honorarios/'.$idCrypt) }}"><i class="bi bi-currency-dollar me-2"></i> Honorários</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ url('correspondente/enviar-email-atualizacao/'.\Crypt::encrypt($vinculo->cd_conta_correspondente_ccr)) }}"
                           onclick="return confirm('Confirma o envio do email de atualização cadastral para {{ addslashes($nome) }}?')">
                            <i class="bi bi-envelope me-2"></i> Enviar email de atualização
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card uv-panel mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-1"></i> Dados básicos</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="uv-meta-item"><span>Razão social / Nome</span><strong>{{ $nome }}</strong></div>
                            <div class="uv-meta-item"><span>Tipo de pessoa</span><strong>{{ $vinculo->tipoPessoa ? $vinculo->tipoPessoa->nm_tipo_pessoa_tpp : 'Não informado' }}</strong></div>
                            <div class="uv-meta-item"><span>Atuação</span><strong>{{ $flAdvogado === true ? 'Advogado' : ($flAdvogado === false ? 'Preposto' : 'Não informado') }}</strong></div>
                            <div class="uv-meta-item">
                                <span>Comarca de origem</span>
                                <strong>{{ ($origem && $origem->cidade) ? $origem->cidade->nm_cidade_cde.' / '.optional($origem->cidade->estado)->sg_estado_est : 'Não informado' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($cnpj && $vinculo->cd_tipo_pessoa_tpp == 2)
                                <div class="uv-meta-item"><span>CNPJ</span><strong class="dmk-num">{{ $cnpj->nu_identificacao_ide ?: 'Não informado' }}</strong></div>
                            @else
                                <div class="uv-meta-item"><span>CPF</span><strong class="dmk-num">{{ ($cpf && $cpf->nu_identificacao_ide) ? $cpf->nu_identificacao_ide : (($cnpj && $cnpj->nu_identificacao_ide) ? $cnpj->nu_identificacao_ide : 'Não informado') }}</strong></div>
                            @endif
                            <div class="uv-meta-item"><span>OAB</span><strong class="dmk-num">{{ $oab ? $oab->nu_identificacao_ide : 'Não informado' }}</strong></div>
                            <div class="uv-meta-item"><span>RG</span><strong class="dmk-num">{{ $rg ? $rg->nu_identificacao_ide : 'Não informado' }}</strong></div>
                            <div class="uv-meta-item">
                                <span>WhatsApp</span>
                                <strong>
                                    @if(optional($vinculo->correspondente)->nu_telefone_whatsapp_con)
                                        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $vinculo->correspondente->nu_telefone_whatsapp_con) }}" target="_blank" rel="noopener">
                                            <i class="bi bi-whatsapp text-success"></i> {{ $vinculo->correspondente->nu_telefone_whatsapp_con }}
                                        </a>
                                    @else
                                        Não informado
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card uv-panel h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-telephone me-1"></i> Telefones</h5>
                        </div>
                        <div class="card-body">
                            @forelse($fones as $fone)
                                <div class="dmk-list-item">
                                    <span class="dmk-num">{{ $fone->nu_fone_fon }}</span>
                                    <small>{{ optional($fone->tipo)->dc_tipo_fone_tfo }}</small>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Nenhum telefone informado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card uv-panel h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-envelope me-1"></i> Emails</h5>
                        </div>
                        <div class="card-body">
                            @forelse($emails as $email)
                                <div class="dmk-list-item">
                                    <a href="mailto:{{ $email->dc_endereco_eletronico_ede }}" class="text-break">{{ $email->dc_endereco_eletronico_ede }}</a>
                                    <small>{{ optional($email->tipo)->dc_tipo_endereco_eletronico_tee }}</small>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Nenhum email informado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card uv-panel h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-geo me-1"></i> Endereço</h5>
                        </div>
                        <div class="card-body">
                            @if($endereco && $endereco->dc_logradouro_ede)
                                <div class="uv-meta-item"><span>Logradouro</span><strong>{{ $endereco->dc_logradouro_ede }}{{ $endereco->nu_numero_ede ? ', '.$endereco->nu_numero_ede : '' }}</strong></div>
                                <div class="uv-meta-item"><span>Complemento</span><strong>{{ $endereco->dc_complemento_ede ?: '—' }}</strong></div>
                                <div class="uv-meta-item"><span>Bairro</span><strong>{{ $endereco->nm_bairro_ede ?: '—' }}</strong></div>
                                <div class="uv-meta-item"><span>CEP</span><strong class="dmk-num">{{ $endereco->nu_cep_ede ?: '—' }}</strong></div>
                                <div class="uv-meta-item"><span>Cidade / UF</span><strong>{{ $endereco->cidade ? $endereco->cidade->nm_cidade_cde.' / '.optional($endereco->cidade->estado)->sg_estado_est : '—' }}</strong></div>
                            @else
                                <p class="text-muted small mb-0">Endereço não informado.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card uv-panel h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-bank me-1"></i> Dados bancários</h5>
                        </div>
                        <div class="card-body">
                            @forelse($bancos as $banco)
                                <div class="dmk-bank">
                                    <div class="d-flex justify-content-between">
                                        <span class="dmk-bank-title">{{ $banco->nm_titular_dba ?: 'Titular não informado' }}</span>
                                        <small class="text-muted">{{ optional($banco->tipoConta)->nm_tipo_conta_tcb }}</small>
                                    </div>
                                    <div class="text-muted small dmk-num">{{ $banco->nu_cpf_cnpj_dba }}</div>
                                    @if($banco->cd_tipo_conta_tcb == \App\Enums\TipoConta::PIX)
                                        <div class="mt-1"><i class="bi bi-qr-code me-1"></i> PIX: <span class="dmk-num">{{ $banco->dc_pix_dba }}</span></div>
                                    @else
                                        <div class="mt-1">
                                            {{ optional($banco->banco)->nm_banco_ban ?: 'Banco não informado' }}
                                            · Ag. <span class="dmk-num">{{ $banco->nu_agencia_dba ?: '—' }}</span>
                                            · Conta <span class="dmk-num">{{ $banco->nu_conta_dba ?: '—' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Nenhuma conta bancária informada.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card uv-panel">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-geo-alt me-1"></i> Comarcas de atuação <span class="badge bg-secondary-subtle text-secondary ms-1">{{ number_format($totalComarcas, 0, ',', '.') }}</span></h5>
                    <a href="{{ url('correspondente/comarcas/'.$idCrypt) }}" class="uv-inline-link">Gerenciar</a>
                </div>
                <div class="card-body">
                    @if($comarcasPorUf->count() > 1 || ($totalComarcas && $comarcas->isEmpty()))
                        <div class="d-flex flex-wrap gap-2 {{ $comarcas->count() ? 'mb-3' : '' }}">
                            @foreach($comarcasPorUf as $uf)
                                <span class="uv-pill"><strong>{{ $uf->sg_estado_est }}</strong> {{ number_format($uf->total, 0, ',', '.') }} {{ $uf->total == 1 ? 'cidade' : 'cidades' }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($totalComarcas && $comarcas->isEmpty())
                        <p class="text-muted small mb-0 mt-2">
                            @if($origem && $origem->cidade)
                                <i class="bi bi-star-fill text-danger"></i> Origem: {{ $origem->cidade->nm_cidade_cde }} / {{ optional($origem->cidade->estado)->sg_estado_est }}.
                            @endif
                            A lista completa está em <a href="{{ url('correspondente/comarcas/'.$idCrypt) }}">Gerenciar comarcas</a>.
                        </p>
                    @endif
                    @if($comarcas->count())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($comarcas as $comarca)
                                @if($comarca->cidade)
                                    <span class="uv-pill {{ $comarca->fl_origem_cat === 'S' ? 'uv-pill-admin' : '' }}" title="{{ $comarca->fl_origem_cat === 'S' ? 'Comarca de origem' : '' }}">
                                        @if($comarca->fl_origem_cat === 'S')<i class="bi bi-star-fill"></i>@endif
                                        {{ $comarca->cidade->nm_cidade_cde }} / {{ optional($comarca->cidade->estado)->sg_estado_est }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @elseif(!$totalComarcas)
                        <p class="text-muted small mb-0">Nenhuma comarca cadastrada.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card uv-panel mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-1"></i> Contrato de correspondência</h5>
                </div>
                <div class="card-body">
                    <div class="uv-meta-item">
                        <span>Status</span>
                        <strong class="{{ $contratoGerado ? 'text-success' : 'text-warning' }}">
                            <i class="bi {{ $contratoGerado ? 'bi-check-circle' : 'bi-clock' }}"></i> {{ $contratoGerado ? 'Gerado' : 'Não gerado' }}
                        </strong>
                    </div>
                    <div class="uv-meta-item">
                        <span>Data de geração</span>
                        <strong>{{ $vinculo->dt_contrato_gerado_ccr ? $vinculo->dt_contrato_gerado_ccr->format('d/m/Y H:i') : '—' }}</strong>
                    </div>

                    @if(!empty($contratoPendencias))
                        <div class="alert alert-warning small mt-3 mb-0">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i> Cadastro incompleto.</strong>
                            Estes dados ficarão em branco no contrato:
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($contratoPendencias as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="{{ url('correspondente/contrato/gerar/'.$idCrypt) }}" class="flex-fill"
                              onsubmit="return confirm('{{ !empty($contratoPendencias) ? 'Há dados incompletos no cadastro. Deseja gerar o contrato mesmo assim?' : ($contratoGerado ? 'Já existe um contrato gerado. Deseja gerar novamente?' : 'Confirma a geração do contrato deste correspondente?') }}')">
                            {{ csrf_field() }}
                            <input type="hidden" name="v2" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100" @if($aguardandoAssinatura) disabled title="Cancele o envio para assinatura antes de gerar um novo contrato" @endif>
                                <i class="bi bi-file-earmark-plus me-1"></i> {{ $contratoGerado ? 'Gerar novamente' : 'Gerar contrato' }}
                            </button>
                        </form>
                        @if($contratoGerado)
                            <a href="{{ url('correspondente/contrato/baixar/'.$idCrypt) }}?v2=1" class="btn btn-outline-success btn-sm flex-fill">
                                <i class="bi bi-download me-1"></i> Baixar PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($assinaturaPainel)
                @include('v2.correspondente.partes.assinatura-card')
            @endif

            <div class="card uv-panel mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-arrow-repeat me-1"></i> Atualização cadastral</h5>
                </div>
                <div class="card-body">
                    <div class="uv-meta-item">
                        <span>Email de atualização</span>
                        <strong class="{{ $vinculo->fl_atualizacao_cadastro_ccr ? 'text-success' : 'text-warning' }}">
                            {{ $vinculo->fl_atualizacao_cadastro_ccr ? 'Acessado' : 'Pendente' }}
                        </strong>
                    </div>
                    <a href="{{ url('correspondente/enviar-email-atualizacao/'.\Crypt::encrypt($vinculo->cd_conta_correspondente_ccr)) }}"
                       class="btn btn-outline-secondary btn-sm w-100 mt-3"
                       onclick="return confirm('Confirma o envio do email de atualização cadastral para {{ addslashes($nome) }}?')">
                        <i class="bi bi-envelope me-1"></i> Enviar email de atualização
                    </a>
                </div>
            </div>

            <div class="card uv-panel mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-cash-coin me-1"></i> Despesas reembolsáveis</h5>
                    <a href="{{ url('correspondente/despesas/'.$vinculo->cd_correspondente_cor) }}" class="uv-inline-link">Gerenciar</a>
                </div>
                <div class="card-body">
                    @forelse($despesas as $despesa)
                        <div class="uv-perm-item d-flex justify-content-between small py-1">
                            <span>{{ optional($despesa->tipoDespesa)->nm_tipo_despesa_tds }}</span>
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Nenhuma despesa informada.</p>
                    @endforelse
                </div>
            </div>

            <div class="card uv-panel">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-text me-1"></i> Observações</h5>
                </div>
                <div class="card-body">
                    @if($vinculo->obs_ccr)
                        <div class="dmk-obs">{{ trim(html_entity_decode(strip_tags(str_ireplace(['<br>', '<br/>', '<br />', '</p>'], "\n", $vinculo->obs_ccr)))) }}</div>
                    @else
                        <p class="text-muted small mb-0">Sem observações.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($foto)
    <div class="modal fade" id="modal-foto" tabindex="-1" aria-label="Foto de {{ $nome }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $nome }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ $foto }}" alt="Foto de {{ $nome }}" class="img-fluid rounded">
                </div>
                <div class="modal-footer small text-muted justify-content-start">
                    Foto enviada pelo próprio correspondente no perfil dele.
                </div>
            </div>
        </div>
    </div>
@endif

@if($assinaturaPainel && !$assinaturaPainel['emAndamento'] && empty($assinaturaPainel['impedimentos']))
    @include('v2.correspondente.partes.assinatura-modal')
@endif
@endsection
