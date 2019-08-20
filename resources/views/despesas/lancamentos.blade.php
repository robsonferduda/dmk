@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="{{ url('clientes') }}">Despesas</a></li>
        <li>Lançamentos</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-dollar"></i> Despesas <span>> Lançamentos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 boxBtnTopo">
            <a data-toggle="modal" href="{{ url('despesas/novo') }}" class="btn btn-success pull-right header-btn btnMargin"><i class="fa fa-plus fa-lg"></i> Novo</a>
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
                                    <th style="width: 30%;">Despesa</th> 
                                    <th style="width: 10%;" class="center">Valor</th>         
                                    <th style="width: 10%;" class="center">Data de Vencimento</th>
                                    <th style="width: 10%;" class="center">Data de Pagamento</th>   
                                    <th style="width: 10%;" class="center">Situação</th>                                
                                    <th style="width: 10%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lancamentos as $despesa)

                                    @php $cor = ''; 

                                        if(!empty($despesa->dt_vencimento_des)){

                                            if(strtotime(date(\Carbon\Carbon::today()->toDateString()))  == strtotime($despesa->dt_vencimento_des))  
                                                $cor = "#f2cf59";   

                                            if(strtotime(\Carbon\Carbon::today())  < strtotime($despesa->dt_vencimento_des))  
                                                $cor = "#8ec9bb";

                                            if(strtotime(\Carbon\Carbon::today())  > strtotime($despesa->dt_vencimento_des))
                                                $cor = "#fb8e7e";                                         
                                            
                                        }else{
                                            $cor = "#ffffff"; 
                                        }
                                        
                                    @endphp
                                    <tr style="background-color: {{ $cor }};">
                                        <td data-id="{{ $despesa->cd_despesa_des }}">{{ ($despesa->tipo->categoriaDespesa) ? $despesa->tipo->categoriaDespesa->nm_categoria_despesa_cad : 'Não informado' }}</td>
                                        <td data-id="{{ $despesa->cd_despesa_des }}">{{ ($despesa->tipo) ? $despesa->tipo->nm_tipo_despesa_tds : 'Não informado' }}</td>
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

                                                <a title="Editar" class="btn btn-primary btn-xs" href="{{ url('despesa/editar/'.$despesa->cd_despesa_des) }}"><i class="fa fa-edit"></i> </a>

                                                <button title="Excluir" class="btn btn-danger btn-xs excluir_registro" data-url="../despesas/"><i class="fa fa-trash"></i> </button> 
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