@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Tipos de Contato</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Configurações <span> > Tipos de Contato</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="#addTipoContato" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                    <h2>Tipos de Contato</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 85%;">Tipo de Contato</th>
                                   
                                    <th style="width: 15%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipos as $tipo)
                                    <tr>                                    
                                        <td data-id="{{ $tipo->cd_tipo_contato_tct }}" data-nome="{{ $tipo->nm_tipo_contato_tct }}">{{ $tipo->nm_tipo_contato_tct }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs editar_tipo_contato" style="width: 48%;" href=""><i class="fa fa-edit"></i> Editar</button>
                                            <button data-url="../tipos-de-contato/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
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

<div class="modal fade modal_top_alto" id="addTipoContato" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Novo Tipo de Contato
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-tipo-contato', 'url' => 'tipos-de-contato', 'class' => 'smart-form']) !!}
                     <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_contato_tct" id="nm_tipo_contato_tct" required>
                                </label>
                            </div>
                        </section>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-tipo-contato"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="editTipoContato" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Editar Tipo de Contato
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-edit-tipo-contato', 'method' => 'PUT', 'url' => 'tipos-de-contato', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="cd_tipo_contato_tct" id="cd_tipo_contato_tct">
                    <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_contato_tct" id="nm_tipo_contato_tct" required>
                                </label>
                            </div>
                        </section>
                         
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa 
                        fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-edit-tipo-contato"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection