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
                <i class="fa-fw fa fa-lock"></i>Permissões <span> > Permissões do Usuário</span> <span> > {{ $user->name }}</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('users') }}" data-toggle="modal" href="#addPermissao" class="btn btn-default pull-right header-btn"><i class="fa fa-list fa-lg"></i> Listar Usuários</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Permissões Disponíveis</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                 
                                    <th style="width: 25%;">Permissão</th>
                                    <th style="width: 12%;" class="center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissoes_disponiveis as $p)
                                    <tr>
                                        <td data-id="{{ $user->id }}" data-descricao="{{ $user->name }}">{{ $p->description }}</td>
                                        <td class="center">
                                            <a title="Adicionar" href="{{ url('permissoes/adicionar/'.\Crypt::encrypt($p->id).'/usuario/'.\Crypt::encrypt($user->id)) }}" class="btn btn-primary btn-xs" href=""><i class="fa fa-plus"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </article>
        <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Permissões do Usuário</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                 
                                    <th style="width: 25%;">Permissão</th>
                                    <th style="width: 12%;" class="center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissoes as $p)
                                    <tr>
                                        <td data-id="{{ $user->id }}" data-descricao="{{ $user->name }}">{{ $p->description }}</td>
                                        <td class="center">
                                            <a title="Adicionar" href="{{ url('permissoes/remover/'.\Crypt::encrypt($p->id).'/usuario/'.\Crypt::encrypt($user->id)) }}" class="btn btn-danger btn-xs" href=""><i class="fa fa-trash"></i> </a>
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
@endsection