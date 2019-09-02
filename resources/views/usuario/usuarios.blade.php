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
                <i class="fa-fw fa fa-cog"></i>Usuários <span> > Lista</span>
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
            <div class="well">
                <form action="{{ url('usuarios/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Nome</span>
                        <input size="30" type="text" name="nome" class="form-control" id="Nome" placeholder="Nome" >
                    </div>                    
                {{--    <div class="form-group">
                        <select name="perfil" class="form-control">
                            <option value="">Perfil</option>
                            @foreach(\App\Nivel::all() as $nivel)
                                <option value="{{ $nivel->cd_nivel_niv }}">{{ $nivel->dc_nivel_niv }}</option>
                            @endforeach
                        </select>
                    </div>  --}}          
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a href="{{ url('usuarios') }}" class="btn btn-primary" ><i class="fa fa-list"></i> Listar</a>
                </form>
            </div>
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
                                    <th style="">E-mail</th>
                                    {{--<th style="width: 15%;">Nível</th>--}}
                                   
                                    <th style="width: 100px;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                    <tr>                                    
                                        <td data-id="{{ \Crypt::encrypt($usuario->id) }}" data-nome="{{ $usuario->name }}">{{ $usuario->name }}</td>
                                        <td data-email="{{ $usuario->email }}">{{ $usuario->email }}</td>
                                      {{--  <td data-perfil="{{ $usuario->tipoPerfil->cd_nivel_niv }}">{{ $usuario->tipoPerfil->dc_nivel_niv }}</td> --}}
                                        <td>
                                            <a class="btn btn-default btn-xs" title="Detalhes" href="{{ url('usuarios/detalhes/'.\Crypt::encrypt($usuario->id)) }}"><i class="fa fa-file-text-o"></i></a>
                                            <a class="btn btn-primary btn-xs editar_vara" title="Editar" href="{{ url('usuarios/editar/'.\Crypt::encrypt($usuario->id)) }}"><i class="fa fa-edit"></i></a>
                                            <div class="dropdown" style="display: inline;">
                                                <a href="javascript:void(0);" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a data-toggle="modal" class="alterar_senha" title="Alterar Senha" href="#"}}"><i class="fa fa-key"></i> Alterar Senha</a></li>
                                                    <li><a data-url="usuarios/"  title="Excluir" class="excluir_registro"  href="#"><i class="fa fa-trash"></i> Excluir</a></li>
                                                </ul>
                                            </div>                                      
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
<div class="modal fade modal_top_alto" id="alterarSenha" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Alterar Senha
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-alterar-senha', 'method' => 'PUT', 'url' => 'usuarios/alterar-senha', 'class' => 'smart-form']) !!}
                     <fieldset>
                       <section class="col col-6">
                            <label class="label">Senha<span class="text-danger">*</span></label>
                            <label class="input">
                                 <input type="password" name="password" id="password" placeholder="Senha" required>
                            </label>
                            </section>  
                            <section class="col col-6">
                                <label class="label">Confirmar Senha<span class="text-danger">*</span></label>
                                <label class="input">
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Senha" required>
                                </label>
                            </section>     
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-alterar-senha"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection