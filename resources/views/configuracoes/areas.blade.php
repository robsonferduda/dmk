@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Configurações</li>
        <li>Áreas de Direito</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Configurações <span> > Áreas de Direito</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <!-- Button trigger modal -->
            <a data-toggle="modal" href="#addArea" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                @include('layouts/messages')
            </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                
                            <!-- Widget ID (each widget will need unique ID)-->
                            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                                <!-- widget options:
                                usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                
                                data-widget-colorbutton="false"
                                data-widget-editbutton="false"
                                data-widget-togglebutton="false"
                                data-widget-deletebutton="false"
                                data-widget-fullscreenbutton="false"
                                data-widget-custombutton="false"
                                data-widget-collapsed="true"
                                data-widget-sortable="false"
                
                                -->
                                <header>
                                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                                    <h2>Áreas de Direito</h2>
                
                                </header>
                
                                <!-- widget div-->
                                <div>
                
                                    <!-- widget edit box -->
                                    <div class="jarviswidget-editbox">
                                        <!-- This area used as dropdown edit box -->
                
                                    </div>
                                    <!-- end widget edit box -->
                
                                    <!-- widget content -->
                                    <div class="widget-body no-padding">
                
                                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                                            <thead>                         
                                                <tr>
                                                    <th style="width: 10%;">Código</th>
                                                    <th style="width: 75%;">Área</th>
                                                    <th style="width: 15%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\AreaDireito::all() as $area)
                                                    <tr>
                                                        <td data-id="{{ $area->cd_area_direito_ado }}">{{ $area->cd_area_direito_ado }}</td>
                                                        <td data-descricao="{{ $area->dc_area_direito_ado }}">{{ $area->dc_area_direito_ado }}</td>
                                                        <td>
                                                            <button class="btn btn-primary btn-xs editar_area" style="width: 48%;" href=""><i class="fa fa-trash"></i> Editar</button>
                                                            <button class="btn btn-danger btn-xs excluir_registro" style="width: 48%;" href=""><i class="fa fa-trash"></i> Excluir</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                    <!-- end widget content -->
                
                                </div>
                                <!-- end widget div -->
                
                            </div>
                            </article>
                        <!-- WIDGET END -->
    </div>
</div>
<!-- Modal -->
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
                                <button type="submit" class="btn btn-success btn-save-area">
                                    <i class="fa fa-save"></i> Salvar
                                </button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>

                            </footer>
                        {!! Form::close() !!}                    
                        

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
                                <button type="submit" class="btn btn-success btn-edit-area">
                                    <i class="fa fa-save"></i> Salvar
                                </button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>

                            </footer>
                        {!! Form::close() !!}                    
                        

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection