@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Grupo de Cidades</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Configurações <span> > Grupo de Cidades</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="{{ url('configuracoes/novo-grupo-de-cidades') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                    <h2>Grupo de Cidades</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 50%;">Grupo</th>
                                   
                                   
                                    <th style="width: 15%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gruposCidades as $grupo)
                                    <tr>                                    
                                        <td data-id="{{ $grupo->cd_grupo_cidade_grc }}" data-nome="{{ $grupo->nm_grupo_cidade_grc }}">{{ $grupo->nm_grupo_cidade_grc }}</td>
                                       
                                        <td>
                                            <a class="btn btn-primary btn-xs editar_grupo" style="width: 48%;" href="{{ url('configuracoes/editar-grupo-de-cidades/'.$grupo->cd_grupo_cidade_grc) }}" ><i class="fa fa-edit"></i> Editar</a>
                                            <button data-url="../grupos-de-cidades/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
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