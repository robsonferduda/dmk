@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Usuários</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Usuarios <span> > Lista</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="{{ url('usuarios/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Usuários</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 40%;">Usuário</th>
                                    <th style="width: 30%;">E-mail</th>
                                    <th style="width: 15%;">Perfil</th>
                                   
                                    <th style="width: 15%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                    <tr>                                    
                                        <td data-id="{{ $usuario->id }}" data-nome="{{ $usuario->name }}">{{ $usuario->name }}</td>
                                        <td data-email="{{ $usuario->email }}">{{ $usuario->email }}</td>
                                        <td data-perfil="{{ $usuario->tipoPerfil->cd_nivel_niv }}">{{ $usuario->tipoPerfil->dc_nivel_niv }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs editar_vara" style="width: 48%;" href="{{ url('usuarios/editar/'.$usuario->id) }}"><i class="fa fa-edit"></i> Editar</a>
                                            <button data-url="usuarios/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
s
@endsection