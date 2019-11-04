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
                <i class="fa-fw fa fa-lock"></i>Permissões <span> > Listar</span>
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
                                    <th style="width: 20%;">Tag</th>
                                    <th style="width: 50%;">Descrição</th>
                                    <th style="width: 10%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissoes as $permissao)
                                    <tr>
                                        <td data-id="{{ $permissao->id }}" data-descricao="{{ $permissao->name }}">{{ $permissao->name }}</td>
                                        <td>
                                            @foreach($permissao->slug as $chave => $slug)
                                                {{ $chave }} {{ $slug }}<br/>
                                            @endforeach
                                        </td>
                                        <td>{{ $permissao->description }}</td>
                                        <td class="center">
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

<div class="modal fade modal_top_alto" id="addArea" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Nova Área de Direito
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-area', 'url' => 'areas', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="id_area" id="id_area">
                    <fieldset>
                        <section>
                            <div>
                                <label class="label">Descrição</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="dc_area_direito_ado" id="dc_area_direito_ado">
                                </label>
                            </div>
                        </section>
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="submit" class="btn btn-success btn-save-area"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="editArea" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Editar Área de Direito
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-edit-area', 'method' => 'PUT', 'url' => 'areas', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="id_area" id="id_area">
                    <fieldset>
                        <section>
                            <div>
                                <label class="label">Descrição</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="dc_area_direito_ado" id="dc_area_direito_ado">
                                </label>
                            </div>
                        </section>
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="submit" class="btn btn-success btn-edit-area"><i class="fa fa-save"></i> Salvar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection