@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('correspondentes') }}">Correspondentes</a></li>
        <li>Despesas Reembolsáveis</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-legal"></i> Correspondentes <span> > Despesas Reembolsáveis</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('correspondentes') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-group fa-lg"></i> Listar Correspondentes</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-sortable">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Despesas Reembolsáveis</h2>             
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <div role="content">
                    <div class="widget-body no-padding">
                        {!! Form::open(['id' => 'frm-add-despesa', 'url' => 'correspondente/despesas', 'class' => 'smart-form']) !!}
                        <input type="hidden" name="entidade" id="entidade" value="{{ $correspondente->entidade->cd_entidade_ete }}">    
                        <input type="hidden" name="conta" id="conta" value="{{ $correspondente->cd_conta_con }}">                       
                            <header>
                                <i class="fa fa-dollar"></i> Despesas por Tipo de Serviço 
                                <a href="{{ url('configuracoes/tipos-de-despesa') }}" style="padding: 1px 8px;" ><i class="fa fa-plus-circle"></i> Novo </a>
                            </header>

                            <fieldset>
                                <section>
                                    <label class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> Selecione as despesas reembolsáveis relacionadas ao correspondente</label>
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <label style="margin-bottom: 5px;">Despesas disponíveis</label>
                                            @foreach($despesas_disponiveis as $despesa)
                                                <label class="checkbox">
                                                    <input type="checkbox" name="despesas[]" value="{{ $despesa->cd_tipo_despesa_tds }}">
                                                    <i></i>{{ $despesa->nm_tipo_despesa_tds }} 
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="col-md-6">
                                            <label style="margin-bottom: 5px;">Despesas selecionadas</label>
                                            @foreach($despesas as $despesa)
                                                <label class="checkbox">
                                                    <input type="checkbox" name="remover[]" checked="checked" value="{{ $despesa->cd_tipo_despesa_tds }}">
                                                    <i></i>{{ $despesa->tipoDespesa()->first()->nm_tipo_despesa_tds }} 
                                                </label>
                                            @endforeach
                                        </div> 
                                    </div>
                                </section>
                            </fieldset>

                            <footer>
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cadastrar</button>
                                <a href="{{ url('correspondentes') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </footer>
                        {!! Form::close() !!}                   
                    </div>
                </div>
            </div>
            </article>
        </div>
    </div>
</div>
@endsection