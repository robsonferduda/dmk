@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Permissões</li>
        <li>Usuários</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-lock"></i>Permissões <span> > Usuários</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="#addArea" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                                    <th style="width: 25%;">Usuário</th>
                                    <th style="width: 25%;">Email</th>
                                    <th style="width: 38%;">Perfil</th>
                                    <th style="width: 12%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td data-id="{{ $user->id }}" data-descricao="{{ $user->name }}">{{ $user->name }}</td>
                                        <td data-slug="{{ $user->email }}">{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                {{ $role->name }}
                                            @endforeach
                                        </td>
                                        <td class="center">
                                            <button title="Perfil" data-id="{{ $user->id }}" class="btn btn-default btn-xs roleOption"><i class="fa fa-group"></i> </button>
                                            <a title="Permissões" href="{{ url('permissoes/usuario/'.\Crypt::encrypt($user->id)) }}" class="btn btn-warning btn-xs"><i class="fa fa-lock"></i> </a>
                                            <button title="Editar" class="btn btn-primary btn-xs editar_area" href=""><i class="fa fa-edit"></i> </button>
                                            <button title="Excluir" data-url="../roles/" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i> </button>
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

        <div class="modal fade in modal_top_alto" id="modal_roles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-group"></i> Gerenciar Perfis</h4>
                     </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 msg-selecao-role" style="margin-bottom: 5px;">
                            </div>
                            <div class="col-md-10">
                                <input type="hidden" name="user" id="user">
                                <div class="form-group">
                                    <select class="form-control" name="role" id="role">
                                        <option value="0">Selecione um perfil</option>
                                        @foreach(\Kodeine\Acl\Models\Eloquent\Role::all() as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 box-left">
                                <div class="form-group">
                                    <button type="button" id="btnAddRole" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Adicionar</button>
                                </div>
                            </div>
                        </div>
                        <table id="table-user-role" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:85%">Perfil</th>
                                    <th style="width:15%">Opções</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                        <div id="role_msg"></div>
                        <div id="role_msg_sistema"></div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</a>
                    </div>
                </div>
            </div>
        </div>
@endsection