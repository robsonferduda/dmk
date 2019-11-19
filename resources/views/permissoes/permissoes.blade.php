@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Permissões</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-lock"></i>Permissões <span> > {{ ($flag) ? "Permissões de ".$role->name : 'Todas'}} </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            @if($flag)
                <a href="{{ url('permissoes') }}" data-toggle="modal" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Todas</a>
            @endif
            <a href="{{ url('roles') }}" style="margin-right: 5px;" data-toggle="modal" class="btn btn-info pull-right header-btn"><i class="fa fa-check"></i> Perfis</a>
            <button style="margin-right: 5px;" data-toggle="modal" data-target="#modalAddPermissao" data-toggle="modal" class="btn btn-success pull-right header-btn"><i class="fa fa-lock"></i> Nova Permissão</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-z">
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Permissões</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                 
                                    <th style="width: 20%;">Permissão</th>
                                    <th style="width: 20%;">Slug</th>
                                    <th style="width: 50%;">Descrição</th>
                                    <th style="width: 10%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissoes as $permissao)
                                    <tr>
                                        <td data-id="{{ $permissao->id }}" data-descricao="{{ $permissao->name }}">{{ $permissao->name }}</td>
                                        <td>
                                            {{ $permissao->slug }}
                                        </td>
                                        <td>{{ $permissao->description }}</td>
                                        <td class="center">
                                            @if($flag)
                                                <a title="Remover do Perfil" href="{{ url('permissoes/'.\Crypt::encrypt($permissao->id).'/role/'.\Crypt::encrypt($permissao->pivot->role_id)) }}" class="btn btn-warning btn-xs" href=""><i class="fa fa-ban"></i> </a>
                                            @else
                                                <a title="Alterar Senha" data-id="{{ $permissao->id }}" class="btn btn-warning btn-xs add_role_permission" href="#"}}"><i class="fa fa-group"></i></a>
                                            @endif
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
                    {!! Form::open(['id' => 'frmRolePermissao', 'method' => 'POST', 'url' => 'permissoes/role/adicionar']) !!}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 msg-selecao-role" style="margin-bottom: 5px;">
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" name="id_permissao" id="id_permissao">
                                    <div class="form-group">
                                        <select class="form-control" name="role" id="role" required="required">
                                            <option value="">Selecione um perfil</option>
                                            @foreach(\App\Role::all() as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 box-left">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Adicionar</button>
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="modal fade in modal_top_alto" id="modalAddPermissao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-lock"></i> Nova Permissão</h4>
                    </div>
                    {!! Form::open(['id' => 'frmAddPermissao', 'method' => 'POST', 'url' => 'permissoes/novo']) !!}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Permissão <span class="text-primary" style="margin-bottom: 5px;"> Valores em minúsculo</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Permissão" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label>Slug <span class="text-primary" style="margin-bottom: 5px;"> Valores em minúsculo</span></label>
                                        <input type="text" name="slug" class="form-control" placeholder="Slug" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label>Descrição <span class="text-primary" style="margin-bottom: 5px;"> Separar palavras compostas com ">"</span></label>
                                        <input type="text" name="description" class="form-control" placeholder="Descrição" required="required">
                                    </div>
                                        
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group center">
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Salvar</button>
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
@endsection