@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Tipos de Serviços</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Configurações <span> > Tipos de Serviços</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="#addTipoServico" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                    <h2>Tipos de Serviços</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 85%;">Tipo de Serviço</th>
                                   
                                    <th style="width: 15%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipos as $tipo)
                                    <tr>                                    
                                        <td data-id="{{ $tipo->cd_tipo_servico_tse }}" data-nome="{{ $tipo->nm_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs editar_tipo_servico" style="width: 48%;" href=""><i class="fa fa-edit"></i> Editar</button>
                                            <button data-url="../tipos-de-servico/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
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

<div class="modal fade modal_top_alto" id="addTipoServico" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Novo Tipo de Servico
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-tipo-servico', 'url' => 'tipos-de-servico', 'class' => 'smart-form']) !!}
                     <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_servico_tse" id="nm_tipo_servico_tse" required>
                                </label>
                            </div>
                        </section>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-tipo-servico"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_top_alto" id="editTipoServico" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Editar Tipo de Serviço
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-edit-tipo-servico', 'method' => 'PUT', 'url' => 'tipos-de-servico', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="cd_tipo_servico_tse" id="cd_tipo_servico_tse">
                    <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_servico_tse" id="nm_tipo_servico_tse" required>
                                </label>
                            </div>
                        </section>
                         
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa 
                        fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-edit-tipo-servico"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection