@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Despesas</a></li>
        <li>Balanço</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-dollar"></i> Despesas <span>> Balanço </span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('despesas/lancamentos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Despesas</a>
            <a data-toggle="modal" href="{{ url('despesas/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-sm-12 col-md-12 col-lg-12">
                 <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Despesas</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>    
                                    <th style="width: 10%;">Categoria</th>
                                    <th style="width: 10%;">Tipo</th>
                                    <th style="width: 35%;">Despesa</th> 
                                    <th style="width: 10%;" class="center">Valor</th>         
                                    <th style="width: 10%;" class="center">Data de Vencimento</th>
                                    <th style="width: 10%;" class="center">Data de Pagamento</th>   
                                    <th style="width: 10%;" class="center">Situação</th>                                
                                    <th style="width: 5%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lancamentos as $despesa)
                                    <tr>
                                        <td data-id="{{ $despesa->cd_despesa_des }}">{{ $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad }}</td>
                                        <td data-id="{{ $despesa->cd_despesa_des }}">{{ $despesa->tipo->nm_tipo_despesa_tds }}</td>
                                        <td>{{ $despesa->dc_descricao_des }}</td>
                                        <td class="center">{{ $despesa->vl_valor_des }}</td>
                                        <td class="center">{{ ($despesa->dt_vencimento_des) ? date('d/m/Y', strtotime($despesa->dt_vencimento_des)) : '--' }}</td>
                                        <td class="center">{{ ($despesa->dt_pagamento_des) ? date('d/m/Y', strtotime($despesa->dt_pagamento_des)) : '--' }}</td>
                                        <td class="center">
                                            {{ ($despesa->dt_pagamento_des) ? 'Pago' : 'Pendente' }}
                                        </td>
                                        <td class="center">
                                            <div>
                                                <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('despesas/'.$despesa->cd_despesa_des) }}"><i class="fa fa-file-text-o"></i> </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="center">Nenhum registro encontrado</td>
                                    </tr>
                                @endforelse                                                         
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
             
        </article>
    </div>
</div>
@endsection