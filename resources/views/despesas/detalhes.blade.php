@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Despesas</a></li>
        <li>Detalhes</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-dollar"></i> Despesas <span>> Detalhes</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('despesas/lancamentos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Lançamentos</a>

            <a data-toggle="modal" href="{{ url('despesas/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>

            <a data-toggle="modal" href="{{ url('despesa/editar/'.$despesa->cd_despesa_des) }}" class="btn btn-primary pull-right header-btn"><i class="fa fa-pencil fa-lg"></i> Editar</a>
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
                        <h2>Dados da Despesa </h2>             
                    </header>
                
                    <div class="col-sm-12">
                        <fieldset style="margin-bottom: 15px;">
                            <legend><i class="fa fa-dollar fa-fw"></i> <strong>Dados Básicos</strong></legend>
                            <div class="row" style="margin-left: 5px;">
                                <p>
                                    <ul class="list-unstyled">
                                        <li>
                                            <strong>Despesa: </strong> {{ $despesa->dc_descricao_des }}
                                        </li>
                                        <li>
                                            <strong>Categoria: </strong> {{ ($despesa->tipo->categoriaDespesa) ? $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad : 'Não informada'}}
                                        </li>
                                        <li>
                                            <strong>Tipo de Despesa: </strong> {{ $despesa->tipo->nm_tipo_despesa_tds }}
                                        </li>
                                        <li>
                                            <strong>Valor: </strong> R$ {{ ($despesa->vl_valor_des) ? $despesa->vl_valor_des : '--' }}
                                        </li>
                                        <li>
                                            <strong>Data de Vencimento: </strong> {{ date('d/m/Y', strtotime($despesa->dt_vencimento_des)) }}
                                        </li>
                                        <li>
                                            <strong>Data de Pagamento: </strong> {{ date('d/m/Y', strtotime($despesa->dt_pagamento_des)) }}
                                        </li>
                                    </ul>
                                </p>
                            </div>
                        </fieldset>

                        <fieldset style="margin-bottom: 15px;">
                            <legend><i class="fa fa-file-o"></i> <strong>Anexos</strong> {{ (count($despesa->anexos) > 0) ? (count($despesa->anexos) == 1) ? '1 arquivo anexado' : count($despesa->anexos).' arquivos anexados' : '' }}</legend>
                            <div class="row" style="margin-left: 5px;">
                                @forelse($despesa->anexos as $anexo)
                                    <div>
                                        <a href="{{ url('despesas/anexos/'.\Crypt::encrypt($anexo->cd_anexo_despesa_des)) }}">
                                            <i class="fa fa-file"></i> {{ $anexo->nm_anexo_despesa_des }}
                                        </a> 
                                    </div>       
                                @empty
                                    <span>Nenhum anexo disponível</span>
                                @endforelse
                            </div>
                        </fieldset>


                        <fieldset style="margin-bottom: 15px;">
                            <legend><i class="fa fa-pencil"></i> <strong>Observações</strong></legend>
                            <div class="row" style="margin-left: 5px;">
                                {!! ($despesa->obs_des) ? $despesa->obs_des : 'Não existem observações' !!}
                            </div>
                        </fieldset>


                    </div>
            </div>
        </article>
    </div>
</div>
</div>
@endsection