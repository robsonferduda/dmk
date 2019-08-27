@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Despesas</a></li>
        <li>Cadastrar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-dollar"></i> Despesas <span>> Cadastro de Despesas</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('despesas/lancamentos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Despesas</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Despesas </h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm_add_despesas', 'url' => 'despesas', 'class' => 'smart-form','files' => true]) !!}
                            <header>Dados da Despesa <small style="font-size: 12px;"><span class="text-danger">* Campos obrigatórios</span></small></header>
                            <fieldset>
                                <div class="row">
                                    <section class="col col-3">
                                        <label class="label">Categoria 
                                            <a href="#" rel="popover-hover" data-placement="top" data-html="true" data-original-title="Categoria da Despesa" data-content="Não é obrigatório preencher, utilize ele como filtro para o campo <strong>Tipo de Despesa</strong>. ">
                                            <i class="fa fa-question-circle text-primary"></i>
                                            </a> 
                                        </label>
                                        <label class="select">
                                            <select name="cd_categoria_despesa_cad" class="categoria_despesa">
                                                <option value="0">Selecione uma categoria</option>
                                                @foreach($categorias as $cat)
                                                    <option value="{{ $cat->cd_categoria_despesa_cad }}">{{ $cat->nm_categoria_despesa_cad }}</option>
                                                @endforeach
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-5">
                                        <label class="label">Tipo de Despesa <span class="text-danger"> *</span></label>
                                        <label class="select">
                                            <select name="cd_tipo_despesa_tds" class="tipo_despesa">
                                                <option value="">Selecione um tipo</option>
                                                @foreach($despesas as $despesa)
                                                    <option value="{{ $despesa->cd_tipo_despesa_tds }}" data-categoria="{{ $despesa->cd_categoria_despesa_cad }}">{{ $despesa->nm_tipo_despesa_tds }}</option>
                                                @endforeach
                                            </select> <i></i> </label>
                                    </section>
                                    <section class="col col-4">
                                        <label class="label">Descrição</label>
                                        <label class="input"> <i class="icon-append fa fa-font"></i>
                                            <input type="text" name="dc_descricao_des" id="dc_descricao_des" placeholder="Descrição">
                                        </label>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-2">
                                        <label class="label">Data de Vencimento <span class="text-danger"> *</span></label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_vencimento_des" id="dt_vencimento_des" class="date-mask hasDatepicker datepicker" placeholder="__/__/____">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Data de Pagamento</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_pagamento_des" id="dt_pagamento_des" class="date-mask hasDatepicker" placeholder="__/__/____">
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <label class="label">Valor da Despesa <span class="text-danger"> *</span></label>
                                        <label class="input"> <i class="icon-append fa fa-dollar"></i>
                                            <input type="text" name="vl_valor_des" id="vl_valor_des">
                                        </label>
                                    </section>
                                    <section class="col col-6">
                                        <label class="label">Arquivo </label>
                                        <div class="input input-file">
                                            <span class="button"><input id="file" type="file" name="file" onchange="this.parentNode.nextSibling.value = this.value">Buscar</span><input type="text" placeholder="Anexos do pagamento" readonly="">
                                        </div>
                                    </section>
                                </div>
                            </fieldset>
                            
                             <div class="row" style="padding: 5px 20px;">
                                    <header>
                                        <i class="fa  fa-file-text-o"></i> Observações 
                                    </header>
                                    <fieldset>
                                        <div class="row"> 
                                            <section class="col col-sm-12">
                                            <label class="input">
                                                <textarea class="form-control" rows="4" name="obs_des" id="observacao" value="{{old('obs_des')}}" >{{old('obs_des') ? old('obs_des') : ($despesa->obs_des) ? $despesa->obs_des : '' }}</textarea>
                                            </label>
                                            </section> 
                                        </div>
                                    </fieldset>
                                </div>
                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                <button type="button" class="btn btn-danger" onclick="window.history.back();"><i class="fa fa-times"></i> Cancelar</button>
                            </footer>
                        {!! Form::close() !!}                  
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">

     $(function() {

        $('#dt_vencimento_des').datepicker({
            dateFormat : 'dd/mm/yy',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>'
        });

        $("#frm_add_despesas").validate({
            rules : {
                cd_tipo_despesa_tds : {
                    required : true
                },
                dt_vencimento_des : {
                  required : true
                },
                vl_valor_des : {
                    required : true
                }
            },            

            messages : {
                cd_tipo_despesa_tds : {
                    required : 'Campo obrigatório'
                },
                dt_vencimento_des : {
                    required : 'Campo obrigatório'
                },
                vl_valor_des : {
                    required : 'Campo obrigatório'
                }
            },
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
    });

</script>
@endsection