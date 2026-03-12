@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Clientes</a></li>
        <li>Acessos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-group"></i> Clientes <span>> Acessos </span> <span>> {{ $cliente->nm_razao_social_cli }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 boxBtnTopo">
            <a href="{{ url('clientes') }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-group fa-lg"></i> Listar Clientes</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                @include('layouts/messages')
            </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="well">
                    @if($usuario)
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-12">
                                <p><strong>Usuário:</strong> {{ $usuario->name }} &nbsp;|&nbsp; <strong>Email:</strong> {{ $usuario->email }}</p>
                            </div>
                        </div>
                        @if($acessos->count() > 0)
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Data / Hora</th>
                                    <th>IP</th>
                                    <th>Navegador / Dispositivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($acessos as $i => $acesso)
                                <tr>
                                    <td>{{ $acessos->firstItem() + $i }}</td>
                                    <td>{{ \Carbon\Carbon::parse($acesso->created_at)->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $acesso->ip_address }}</td>
                                    <td style="max-width: 400px; word-break: break-word; font-size: 12px;">{{ $acesso->user_agent }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $acessos->links() }}
                        </div>
                        @else
                        <p class="text-muted">Nenhum acesso registrado para este cliente.</p>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            Este cliente não possui usuário de acesso cadastrado.
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection