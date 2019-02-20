@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Tipos de Despesas</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Configurações <span> > Tipos de Despesas</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="#addTipoDespesa" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                    <h2>Tipos de Despesas</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 30%;">Tipo de Despesa</th>
                                    <th style="width: 20%;">Categoria</th>
                                    <th style="width: 25%;">É Reembolsável?</th>
                                    <th style="width: 25%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipos as $tipo)
                                    <tr>                                    
                                        <td data-id="{{ $tipo->cd_tipo_despesa_tds }}" data-nome="{{ $tipo->nm_tipo_despesa_tds }}">{{ $tipo->nm_tipo_despesa_tds }}</td>
                                        <td data-categoria="{{ $tipo->categoriaDespesa->cd_categoria_despesa_cad }}">{{ $tipo->categoriaDespesa->nm_categoria_despesa_cad }}</td>
                                        <td data-reembolso="{{ $tipo->fl_reembolso_tds }}">{{ ($tipo->fl_reembolso_tds) == 'S' ? 'Sim': 'Não' }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs editar_tipo_despesa" style="width: 48%;" href=""><i class="fa fa-edit"></i> Editar</button>
                                            <button data-url="../tipos-de-despesa/" class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
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

<div class="modal fade modal_top_alto" id="addTipoDespesa" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-plus"></i> Novo Tipo de despesa
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-tipo-despesa', 'url' => 'tipos-de-despesa', 'class' => 'smart-form']) !!}
                     <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_despesa_tds" id="nm_tipo_despesa_tds" required>
                                </label>
                            </div>
                        </section>
                        <section>
                            <div>
                                <label class="label">Categoria</label>                                
                                <select  required id="categoriaDespesa" name="cd_categoria_despesa_cad" class="select2">
                                    <option value="">Selecione</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{$cat->cd_categoria_despesa_cad}}">{{$cat->nm_categoria_despesa_cad}}</option>
                                    @endforeach                                    
                                </select>                               
                            </div>                        
                        </section>
                        <section>
                             <br />
                             <div class="onoffswitch-container">
                                <span class="onoffswitch-title">É Reembolsável?</span> 
                                <span class="onoffswitch">
                                    <input type="checkbox" name="fl_reembolso_tds" class="onoffswitch-checkbox" id="fl_reembolso_tds_add">
                                    <label class="onoffswitch-label" for="fl_reembolso_tds_add"> 
                                        <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                        <span class="onoffswitch-switch"></span>
                                    </label> 
                                </span> 
                            </div>
                        </section>
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-save-tipo-despesa"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal_top_alto" id="editTipoDespesa" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-edit"></i> Editar Tipo de Despesa
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-edit-tipo-despesa', 'method' => 'PUT', 'url' => 'tipos-de-despesa', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="cd_tipo_despesa_tds" id="cd_tipo_despesa_tds">
                    <fieldset>
                        <section>
                            <div>
                                <label class="label">Nome</label>
                                <label class="input"> <i class="icon-append fa fa-font"></i>
                                    <input type="text" name="nm_tipo_despesa_tds" id="nm_tipo_despesa_tds" required>
                                </label>
                            </div>
                        </section>
                        <section>
                            <div>
                                <label class="label">Categoria</label>                                
                                <select  required id="categoriaDespesa" name="cd_categoria_despesa_cad" class="select2">
                                    <option value="">Selecione</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{$cat->cd_categoria_despesa_cad}}">{{$cat->nm_categoria_despesa_cad}}</option>
                                    @endforeach                                    
                                </select>                               
                            </div>                        
                        </section>
                        <section>                          
                             <div class="onoffswitch-container">
                                <span class="onoffswitch-title">É Reembolsável?</span> 
                                <span class="onoffswitch">
                                    <input type="checkbox" name="fl_reembolso_tds" class="onoffswitch-checkbox" id="fl_reembolso_tds">
                                    <label class="onoffswitch-label" for="fl_reembolso_tds"> 
                                        <span class="onoffswitch-inner" data-swchon-text="SIM" data-swchoff-text="NÃO"></span> 
                                        <span class="onoffswitch-switch"></span>
                                    </label> 
                                </span> 
                            </div>
                        </section>
                         
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa 
                        fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-success btn-edit-tipo-despesa"><i class="fa fa-save"></i> Salvar</button>
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection