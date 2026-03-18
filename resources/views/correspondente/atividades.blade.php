@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title">
                            <i class="fa fa-user"></i> Correspondente
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                            Atividades &mdash; {{ $correspondente->nm_conta_correspondente_ccr }}
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('correspondente/detalhes/'.safe_encrypt($correspondente->cd_correspondente_cor)) }}" class="btn btn-info pull-right" style="margin-right:8px;">
                            <i class="fa fa-file-text-o"></i> Ficha Completa
                        </a>
                        <a href="{{ url('correspondentes') }}" class="btn btn-warning pull-right" style="margin-right:8px;">
                            <i class="fa fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                @include('layouts/messages')

                {{-- Cabeçalho do correspondente --}}
                <div class="row" style="margin-bottom:16px;">
                    <div class="col-md-1 text-center">
                        @if(file_exists(public_path('img/users/ent'.$correspondente->cd_entidade_ete.'.png')))
                            <img src="{{ asset('img/users/ent'.$correspondente->cd_entidade_ete.'.png') }}"
                                 class="img-circle img-responsive"
                                 style="width:64px;height:64px;object-fit:cover;margin:auto;">
                        @else
                            <img src="{{ asset('img/users/user.png') }}"
                                 class="img-circle img-responsive"
                                 style="width:64px;height:64px;object-fit:cover;margin:auto;">
                        @endif
                    </div>
                    <div class="col-md-11" style="padding-top:8px;">
                        <h5 style="margin:0 0 4px;"><strong>{{ $correspondente->nm_conta_correspondente_ccr }}</strong></h5>
                        @if($correspondente->categoria)
                            <span class="badge" style="background-color:{{ $correspondente->categoria->color_cac ?? '#aaa' }};color:#fff;">
                                {{ $correspondente->categoria->dc_categoria_correspondente_cac }}
                            </span>
                        @endif
                        @if($correspondente->obs_ccr)
                            <p class="text-muted" style="font-size:13px;margin:4px 0 0;">
                                <i class="fa fa-comment-o"></i> {{ $correspondente->obs_ccr }}
                            </p>
                        @endif
                    </div>
                </div>

                <hr style="margin:0 0 16px;">

                {{-- Cards de resumo --}}
                <div class="row" style="margin-bottom:16px;">
                    <div class="col-md-4">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="nc-icon nc-briefcase-24 text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p class="card-category">Processos Ativos</p>
                                            <p class="card-title">{{ $totalAtivos }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big text-center icon-success">
                                            <i class="nc-icon nc-check-2 text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p class="card-category">Finalizados</p>
                                            <p class="card-title">{{ $totalFinalizados }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="icon-big text-center icon-danger">
                                            <i class="nc-icon nc-simple-remove text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <div class="numbers">
                                            <p class="card-category">Cancelados</p>
                                            <p class="card-title">{{ $totalCancelados }}</p>
                                        </div>
                                    </div>
                                </div>
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

            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>{{-- /col-md-12 --}}
</div>{{-- /row --}}

@endsection
