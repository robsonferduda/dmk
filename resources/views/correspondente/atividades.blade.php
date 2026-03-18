@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Correspondentes</li>
        <li>Atividades</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Dashboard</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a href="{{ url('correspondente/detalhes/'.safe_encrypt($correspondente->cd_correspondente_cor)) }}" class="btn btn-info pull-right" style="margin-right:8px;">
                                <i class="fa fa-file-text-o"></i> Ficha Completa
                            </a>
                            <a href="{{ url()->previous() }}" class="btn btn-warning pull-right" style="margin-right:8px;">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable">
                    <header role="heading" class="ui-sortable-handle">
                        <span class="widget-icon"> <i class="fa fa-tasks"></i> </span>
                        <h2>Dashboard do Correspondente</h2>
                    </header>

                    <div class="col-sm-12">

                    @include('layouts/messages')

                    {{-- Cabeçalho do correspondente --}}
                    @php $ent = $correspondente->correspondente->entidade ?? null; @endphp
                    <div class="row" style="margin-bottom:16px;">
                        {{-- Foto --}}
                        <div class="col-md-2 text-center">
                            @if($ent && file_exists(public_path('img/users/ent'.$ent->cd_entidade_ete.'.png')))
                                <img src="{{ asset('img/users/ent'.$ent->cd_entidade_ete.'.png') }}"
                                    class="img-circle img-responsive"
                                    style="width:90px;height:90px;object-fit:cover;margin:auto;">
                            @else
                                <img src="{{ asset('img/users/user.png') }}"
                                    class="img-circle img-responsive"
                                    style="width:90px;height:90px;object-fit:cover;margin:auto;">
                            @endif
                        </div>

                        {{-- Dados principais --}}
                        <div class="col-md-5">
                            <h4 style="margin:0 0 6px;">
                                <strong>{{ $correspondente->nm_conta_correspondente_ccr }}</strong>
                            </h4>

                            @if($correspondente->categoria)
                                <span class="badge" style="background-color:{{ $correspondente->categoria->color_cac ?? '#aaa' }};color:#fff;font-size:11px;">
                                    {{ $correspondente->categoria->dc_categoria_correspondente_cac }}
                                </span>
                            @endif

                            <ul class="list-unstyled" style="margin-top:10px;">
                                @if($ent)
                                    {{-- Email --}}
                                    @if($ent->usuario)
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-envelope"></i>&nbsp;
                                            <a href="mailto:{{ $ent->usuario->email }}">{{ $ent->usuario->email }}</a>
                                        </li>
                                    @endif

                                    {{-- CPF / CNPJ --}}
                                    @if($ent->cpf()->first())
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-tag"></i>&nbsp;
                                            <strong>CPF:</strong> {{ $ent->cpf()->first()->nu_identificacao_ide }}
                                        </li>
                                    @elseif($ent->cnpj()->first())
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-tag"></i>&nbsp;
                                            <strong>CNPJ:</strong> {{ $ent->cnpj()->first()->nu_identificacao_ide }}
                                        </li>
                                    @endif

                                    {{-- OAB --}}
                                    @if($ent->oab)
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-tag"></i>&nbsp;
                                            <strong>OAB:</strong> {{ $ent->oab->nu_identificacao_ide }}
                                        </li>
                                    @endif

                                    {{-- Comarca de origem --}}
                                    @if($ent->atuacao()->where('fl_origem_cat','S')->first())
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-map-marker"></i>&nbsp;
                                            <strong>Comarca de Origem:</strong>
                                            {{ $ent->atuacao()->where('fl_origem_cat','S')->first()->cidade()->first()->nm_cidade_cde }}
                                        </li>
                                    @endif

                                    {{-- Telefones --}}
                                    @foreach($ent->fone()->get() as $fone)
                                        <li class="text-muted" style="margin-bottom:4px;">
                                            <i class="fa fa-phone"></i>&nbsp;{{ $fone->nu_fone_fon }}
                                            <small class="text-muted"> — {{ $fone->tipo()->first()->dc_tipo_fone_tfo }}</small>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        {{-- Endereço + Dados bancários --}}
                        <div class="col-md-5">
                            @if($ent && $ent->endereco)
                                <h5><i class="fa fa-map-marker"></i> Endereço</h5>
                                <ul class="list-unstyled" style="font-size:13px;">
                                    <li><strong>CEP:</strong> {{ $ent->endereco->nu_cep_ede }}</li>
                                    <li><strong>Logradouro:</strong> {{ $ent->endereco->dc_logradouro_ede }}, {{ $ent->endereco->nu_numero_ede }}</li>
                                    @if($ent->endereco->dc_complemento_ede)
                                        <li><strong>Complemento:</strong> {{ $ent->endereco->dc_complemento_ede }}</li>
                                    @endif
                                    <li><strong>Bairro:</strong> {{ $ent->endereco->nm_bairro_ede }}</li>
                                    <li>
                                        <strong>Cidade/UF:</strong>
                                        {{ $ent->endereco->cidade ? $ent->endereco->cidade->nm_cidade_cde.'/'.$ent->endereco->cidade->estado->nm_estado_est : '—' }}
                                    </li>
                                </ul>
                            @endif

                            @if($ent && $ent->banco)
                                <h5 style="margin-top:14px;"><i class="fa fa-bank"></i> Dados Bancários</h5>
                                <ul class="list-unstyled" style="font-size:13px;">
                                    <li><strong>Tipo:</strong> {{ $ent->banco->tipoConta->nm_tipo_conta_tcb }}</li>
                                    @if($ent->banco->tipoConta->cd_tipo_conta_tcb != \App\Enums\TipoConta::PIX)
                                        <li><strong>Banco:</strong> {{ $ent->banco->banco->nm_banco_ban }}</li>
                                        <li><strong>Agência:</strong> {{ $ent->banco->nu_agencia_dba }}</li>
                                        <li><strong>Conta:</strong> {{ $ent->banco->nu_conta_dba }}</li>
                                    @else
                                        <li><strong>PIX:</strong> {{ $ent->banco->dc_pix_dba }}</li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>

                    <hr style="margin:0 0 16px;">

                    {{-- Cards de resumo --}}
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-4">
                            <div class="well" style="border-top:3px solid #f0ad4e; padding:18px 20px; overflow:hidden;">
                                <i class="fa fa-folder-open fa-4x text-warning" style="float:left; margin-right:15px; line-height:1;"></i>
                                <div style="text-align:right;">
                                    <h2 style="font-size:42px; font-weight:bold; margin:0; color:#555;">{{ $totalAtivos }}</h2>
                                    <p class="text-muted" style="margin:0; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Processos Ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well" style="border-top:3px solid #5cb85c; padding:18px 20px; overflow:hidden;">
                                <i class="fa fa-check-circle fa-4x text-success" style="float:left; margin-right:15px; line-height:1;"></i>
                                <div style="text-align:right;">
                                    <h2 style="font-size:42px; font-weight:bold; margin:0; color:#555;">{{ $totalFinalizados }}</h2>
                                    <p class="text-muted" style="margin:0; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Finalizados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="well" style="border-top:3px solid #d9534f; padding:18px 20px; overflow:hidden;">
                                <i class="fa fa-times-circle fa-4x text-danger" style="float:left; margin-right:15px; line-height:1;"></i>
                                <div style="text-align:right;">
                                    <h2 style="font-size:42px; font-weight:bold; margin:0; color:#555;">{{ $totalCancelados }}</h2>
                                    <p class="text-muted" style="margin:0; font-size:12px; text-transform:uppercase; letter-spacing:1px;">Cancelados</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Processos ativos + Últimos acessos --}}
                    <div class="row">

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title" style="margin:0;">
                                        <i class="fa fa-folder-open-o"></i> Processos Ativos
                                        <small class="text-muted">(últimos 10)</small>
                                        <a href="{{ url('processos') }}" class="btn btn-xs btn-info pull-right">Ver todos</a>
                                    </h6>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    @if($processosAtivos->isEmpty())
                                        <p class="text-muted text-center" style="padding:16px;">Nenhum processo ativo no momento.</p>
                                    @else
                                        <table class="table table-hover table-condensed" style="margin:0;">
                                            <thead>
                                                <tr>
                                                    <th>Nº Processo</th>
                                                    <th>Autor / Réu</th>
                                                    <th>Status</th>
                                                    <th>Prazo Fatal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($processosAtivos as $processo)
                                                @php
                                                    $prazo   = $processo->dt_prazo_fatal_pro ? \Carbon\Carbon::parse($processo->dt_prazo_fatal_pro) : null;
                                                    $urgente = $prazo && $prazo->isPast();
                                                @endphp
                                                <tr style="{{ $urgente ? 'background:#f8d7da;' : '' }}">
                                                    <td style="font-size:12px;">{{ $processo->nu_processo_pro ?: '—' }}</td>
                                                    <td style="font-size:12px;">
                                                        {{ $processo->nm_autor_pro ?: '—' }}<br>
                                                        <small class="text-muted">{{ $processo->nm_reu_pro ?: '' }}</small>
                                                    </td>
                                                    <td>
                                                        @if($processo->status)
                                                            <span class="label label-default" style="font-size:11px;">{{ $processo->status->nm_status_processo_conta_stp }}</span>
                                                        @endif
                                                    </td>
                                                    <td style="font-size:12px;{{ $urgente ? 'color:#721c24;font-weight:bold;' : '' }}">
                                                        {{ $prazo ? $prazo->format('d/m/Y') : '—' }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('processos/acompanhamento/'.safe_encrypt($processo->cd_processo_pro)) }}"
                                                        class="btn btn-xs btn-primary">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title" style="margin:0;">
                                        <i class="fa fa-clock-o"></i> Últimos Acessos
                                    </h6>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    @if($ultimosAcessos->isEmpty())
                                        <p class="text-muted text-center" style="padding:16px;">Nenhum acesso registrado.</p>
                                    @else
                                        <ul class="list-group" style="margin:0;border-radius:0;">
                                            @foreach($ultimosAcessos as $acesso)
                                                <li class="list-group-item" style="padding:8px 12px;border-left:none;border-right:none;">
                                                    <i class="fa fa-sign-in text-success"></i>
                                                    <strong style="font-size:12px;">
                                                        {{ \Carbon\Carbon::parse($acesso->created_at)->format('d/m/Y H:i') }}
                                                    </strong>
                                                    @if($acesso->ip_address)
                                                        <br><small class="text-muted"><i class="fa fa-globe"></i> {{ $acesso->ip_address }}</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>{{-- /row processos+acessos --}}

                    </div>{{-- /col-sm-12 --}}
                </div>{{-- /jarviswidget --}}
            </article>
        </div>{{-- /col-md-12 --}}
    </div>{{-- /row --}}
</div>
@endsection
