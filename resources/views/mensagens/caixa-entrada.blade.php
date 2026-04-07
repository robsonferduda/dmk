@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Caixa de Entrada</li>
    </ol>
</div>
<div id="content">

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-inbox"></i> Caixa de Entrada
                @if($mensagens->total() > 0)
                    <span class="badge" style="background:#e74c3c; font-size:14px; vertical-align:middle;">
                        {{ $mensagens->where('fl_leitura_prm', null)->count() }}
                    </span>
                @endif
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.1);">
                <div class="panel-heading" style="padding:10px 15px;">
                    <i class="fa fa-envelope"></i>
                    <strong style="margin-left:6px;">Mensagens recebidas</strong>
                    <span class="text-muted" style="font-size:12px; margin-left:8px;">— {{ $mensagens->total() }} no total</span>
                </div>
                <div class="panel-body" style="padding:0;">

                    @if($mensagens->isEmpty())
                        <p class="text-center text-muted" style="padding:30px;">
                            <i class="fa fa-check-circle" style="font-size:32px; color:#27ae60;"></i><br>
                            Nenhuma mensagem pendente.
                        </p>
                    @else
                        <table class="table table-hover" style="margin-bottom:0;">
                            <thead>
                                <tr style="background:#f9f9f9;">
                                    <th style="width:36px;"></th>
                                    <th>Remetente</th>
                                    <th>Processo</th>
                                    <th>Mensagem</th>
                                    <th style="width:140px;">Data</th>
                                    <th style="width:80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mensagens as $msg)
                                    @php
                                        $naoLida = is_null($msg->fl_leitura_prm);

                                        if ($msg->cd_tipo_mensagem_tim == \App\Enums\TipoMensagem::EXTERNA) {
                                            $remetente = optional(optional($msg->entidadeRemetente))->nm_razao_social_con ?? 'Desconhecido';
                                            $entEte    = optional(optional($msg->entidadeRemetente)->entidade)->cd_entidade_ete;
                                            $avatar    = $entEte && file_exists(public_path('img/users/ent'.$entEte.'.png'))
                                                            ? asset('img/users/ent'.$entEte.'.png')
                                                            : asset('img/users/user.png');
                                        } else {
                                            $remetente = optional(optional($msg->entidadeInterna)->usuario)->name ?? 'Desconhecido';
                                            $entEte    = optional($msg->entidadeInterna)->cd_entidade_ete;
                                            $avatar    = $entEte && file_exists(public_path('img/users/ent'.$entEte.'.png'))
                                                            ? asset('img/users/ent'.$entEte.'.png')
                                                            : asset('img/users/user.png');
                                        }

                                        $nuProcesso = optional($msg->processo)->nu_processo_pro ?? 'Processo excluído';
                                        $linkProcesso = $msg->processo
                                            ? url('processos/acompanhamento/'.safe_encrypt($msg->cd_processo_pro))
                                            : '#';
                                    @endphp
                                    <tr style="{{ $naoLida ? 'background:#fffdf0; font-weight:600;' : '' }}">
                                        <td style="padding:10px 12px; text-align:center; vertical-align:middle;">
                                            <img src="{{ $avatar }}" alt="" width="32" height="32"
                                                 style="border-radius:50%; object-fit:cover;">
                                        </td>
                                        <td style="vertical-align:middle;">
                                            @if($naoLida)
                                                <span class="label label-danger" style="font-size:10px; margin-right:4px;">Nova</span>
                                            @endif
                                            {{ $remetente }}
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <a href="{{ $linkProcesso }}">{{ $nuProcesso }}</a>
                                        </td>
                                        <td style="vertical-align:middle; color:#555; font-weight:normal;">
                                            {{ \Illuminate\Support\Str::limit($msg->texto_mensagem_prm, 100) }}
                                        </td>
                                        <td style="vertical-align:middle; color:#888; font-size:12px;">
                                            {{ date('d/m/Y H:i', strtotime($msg->created_at)) }}
                                        </td>
                                        <td style="vertical-align:middle; text-align:right;">
                                            <a href="{{ $linkProcesso }}" class="btn btn-xs btn-default" title="Ver processo">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($mensagens->hasPages())
                            <div style="padding:12px 16px; border-top:1px solid #eee;">
                                {{ $mensagens->links() }}
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
