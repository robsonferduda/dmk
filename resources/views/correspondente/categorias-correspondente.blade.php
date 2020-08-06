@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Categorias</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i>Correspondentes <span> > Categorias</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="#addCategoriaCorrespondente" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                    <h2>Categorias</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 70%;">Categoria</th>
                                    <th style="width: 15%;">Cor</th>
                                    <th style="width: 15%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $categorias)
                                    <tr>                                    
                                        <td data-id="{{ $categorias->cd_categoria_correspondente_cac }}" data-nome="{{ $categorias->dc_categoria_correspondente_cac }}"><strong style="color: {{ $categorias->color_cac }}">{{ $categorias->dc_categoria_correspondente_cac }}</strong></td>
                                        <td data-cor="{{ $categorias->color_cac }}">{{ $categorias->color_cac }}</td>
                                        <td class="center">
                                            <button class="btn btn-primary btn-xs editar_categoria_correspondente" style="width: 48%;" href=""><i class="fa fa-edit"></i> Editar</button>
                                            <button data-url="../categorias-correspondentes/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
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

<div class="modal fade modal_top_alto" id="addCategoriaCorrespondente" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Nova Categoria
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-categoria-correspondente', 'url' => 'categorias-correspondentes', 'class' => 'smart-form']) !!}
                     <fieldset>
                        <div class="row">
                            <section class="col col-10">
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="dc_categoria_correspondente_cac" id="dc_categoria_correspondente_cac" required>
                                </label>
                            </section>
                            <section class="col col-2">
                                <label class="label">Cor</label>
                                <select name="color_cac" id="colorselector">
                                    <option value="#A0522D" data-color="#A0522D">sienna</option>
                                    <option value="#CD5C5C" data-color="#CD5C5C">indianred</option>
                                    <option value="#FF4500" data-color="#FF4500">orangered</option>
                                    <option value="#008B8B" data-color="#008B8B">darkcyan</option>
                                    <option value="#B8860B" data-color="#B8860B">darkgoldenrod</option>
                                    <option value="#32CD32" data-color="#32CD32">limegreen</option>
                                    <option value="#FFD700" data-color="#FFD700">gold</option>
                                    <option value="#48D1CC" data-color="#48D1CC">mediumturquoise</option>
                                    <option value="#87CEEB" data-color="#87CEEB">skyblue</option>
                                    <option value="#FF69B4" data-color="#FF69B4">hotpink</option>
                                    <option value="#87CEFA" data-color="#87CEFA">lightskyblue</option>
                                    <option value="#6495ED" data-color="#6495ED">cornflowerblue</option>
                                    <option value="#DC143C" data-color="#DC143C">crimson</option>
                                    <option value="#FF8C00" data-color="#FF8C00">darkorange</option>
                                    <option value="#C71585" data-color="#C71585">mediumvioletred</option>
                                    <option value="#000000" data-color="#000000">black</option>
                                </select>
                            </section>
                        </div>
                        
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-categoria-despesa"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="editCategoriaCorrespondente" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Editar Categoria
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-edit-categoria-correspondente', 'method' => 'PUT', 'url' => 'categorias-correspondentes', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="cd_categoria_correspondente_cac" id="cd_categoria_correspondente_cac">
                    <fieldset>
                            <section class="col col-10">
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="dc_categoria_correspondente_cac" id="dc_categoria_correspondente_cac" required>
                                </label>
                            </section>
                            <section class="col col-2">
                                <label class="label">Cor</label>
                                <select name="color_cac" id="colorselector_edit">
                                    <option value="#A0522D" data-color="#A0522D">sienna</option>
                                    <option value="#CD5C5C" data-color="#CD5C5C">indianred</option>
                                    <option value="#FF4500" data-color="#FF4500">orangered</option>
                                    <option value="#008B8B" data-color="#008B8B">darkcyan</option>
                                    <option value="#B8860B" data-color="#B8860B">darkgoldenrod</option>
                                    <option value="#32CD32" data-color="#32CD32">limegreen</option>
                                    <option value="#FFD700" data-color="#FFD700">gold</option>
                                    <option value="#48D1CC" data-color="#48D1CC">mediumturquoise</option>
                                    <option value="#87CEEB" data-color="#87CEEB">skyblue</option>
                                    <option value="#FF69B4" data-color="#FF69B4">hotpink</option>
                                    <option value="#87CEFA" data-color="#87CEFA">lightskyblue</option>
                                    <option value="#6495ED" data-color="#6495ED">cornflowerblue</option>
                                    <option value="#DC143C" data-color="#DC143C">crimson</option>
                                    <option value="#FF8C00" data-color="#FF8C00">darkorange</option>
                                    <option value="#C71585" data-color="#C71585">mediumvioletred</option>
                                    <option value="#000000" data-color="#000000">black</option>
                                </select>
                            </section>
                         
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa 
                        fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-edit-categoria-despesa"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection