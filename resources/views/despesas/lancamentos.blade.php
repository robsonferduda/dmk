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
                <i class="fa-fw fa fa-dollar"></i>Despesas <span>> Lançamentos</span>
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
            <div class="well">
                <form action="{{ url('despesas/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row" style="margin: 0">

                        <section>
                            <label class="text-primary" style="margin-bottom: 5px;"><i class="fa fa-info-circle"></i> Determine um intervalo de tempo para busca. Caso o período seja igual a um dia, data inicial e final devem ser iguais</label>
                        </section>

                        <section class="col col-md-6 box_busca_data" style="width: 49%;">
                            <section class="col col-md-4">
                                <h2 class="pull-right">VENCIMENTO</h2>
                            </section>

                            <section class="col col-md-4">
                                <label class="label label-black">Data Inicial</label><br />
                                <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dt_vencimento_inicial" value="{{ \Session::get('dt_vencimento_inicial') ? date('d/m/Y', strtotime(\Session::get('dt_vencimento_inicial'))) : old('dt_vencimento_inicial') }}" >
                                
                            </section>

                            <section class="col col-md-4">
                                <label class="label label-black">Data Final</label><br />
                                <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dt_vencimento_final" value="{{ \Session::get('dt_vencimento_final') ? date('d/m/Y', strtotime(\Session::get('dt_vencimento_final'))) : old('dt_vencimento_final') }}" >
                                
                            </section>
                        </section>

                        <section class="col col-md-6 box_busca_data pull-right" style="width: 50%;">
                            <section class="col col-md-4">
                                <h2 class="pull-right">PAGAMENTO</h2>
                            </section>

                            <section class="col col-md-4">                           
                                <label class="label label-black">Data Inicial</label><br />
                                <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dt_pagamento_inicial" value="{{ \Session::get('dt_pagamento_inicial') ? date('d/m/Y', strtotime(\Session::get('dt_pagamento_inicial'))) : old('dt_pagamento_inicial') }}">                            
                            </section>

                            <section class="col col-md-4">                           
                                <label class="label label-black">Data Final</label><br />
                                <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dt_pagamento_final" value="{{ \Session::get('dt_pagamento_final') ? date('d/m/Y', strtotime(\Session::get('dt_pagamento_final'))) : old('dt_pagamento_final')  }}">                            
                            </section>
                        </section>

                    </div>
                    <hr/>
                    <div class="row">

                        <section class="col col-md-3">                                       
                            <label class="label label-black" >Categoria</label>          
                            <select  id="cd_categoria_despesa_cad" name="cd_categoria_despesa_cad" class="select2 categoria_despesa">
                                <option selected value="">Selecione</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->cd_categoria_despesa_cad }}" {{ (!empty(\Session::get('categoria') and \Session::get('categoria') == $cat->cd_categoria_despesa_cad) ? 'selected' : '') }} >{{ $cat->nm_categoria_despesa_cad }}</option>
                                @endforeach
                            </select>
                        </section>
                        <section class="col col-md-4">                                       
                            <label class="label label-black" >Tipo de Despesa</label>          
                            <select  id="cd_tipo_despesa_tds" name="cd_tipo_despesa_tds" class="select2 tipo_despesa">
                                <option selected value="">Selecione</option>
                                @foreach($despesas as $despesa)
                                    <option value="{{ $despesa->cd_tipo_despesa_tds }}" {{ (!empty(\Session::get('despesa') and \Session::get('despesa') == $despesa->cd_tipo_despesa_tds ) ? 'selected' : '') }} data-categoria="{{ $despesa->cd_categoria_despesa_cad }}">{{ $despesa->nm_tipo_despesa_tds }}</option>
                                @endforeach
                            </select>
                        </section>
                        <section class="col col-md-3">                                       
                            <label class="label label-black" >Situação</label>          
                            <select  id="situacao" name="situacao" class="select2">
                                <option selected value="">Selecione</option>
                                <option value="1" {{ (\Session::get('situacao') == 1) ? 'selected' : '' }}>Pago</option>
                                <option value="2" {{ (\Session::get('situacao') == 2) ? 'selected' : '' }}>Pendente</option>
                            </select>
                        </section>
                        <section class="col col-md-2">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar </button>
                        </section>    

                    </div>
                </form>
            </div>
            <div style="clear: both;"></div>
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
                                    <th style="width: 20%;">Descrição</th> 
                                    <th style="width: 10%;" class="center">Valor</th>         
                                    <th style="width: 10%;" class="center">Data de Vencimento</th>
                                    <th style="width: 10%;" class="center">Data de Pagamento</th>   
                                    <th style="width: 10%;" class="center">Situação</th>  
                                    <th style="width: 10%;" class="center">Anexos</th>                               
                                    <th style="width: 10%;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lancamentos as $despesa)

                                    @php $cor = ''; 

                                        if(empty($despesa->dt_pagamento_des)){

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
                                        <td>
                                            {{ ($despesa->dc_descricao_des) ? $despesa->dc_descricao_des : $despesa->tipo->nm_tipo_despesa_tds }}
                                        </td>
                                        <td class="center">{{ $despesa->vl_valor_des }}</td>
                                        <td class="center">{{ ($despesa->dt_vencimento_des) ? date('d/m/Y', strtotime($despesa->dt_vencimento_des)) : '--' }}</td>
                                        <td class="center">{{ ($despesa->dt_pagamento_des) ? date('d/m/Y', strtotime($despesa->dt_pagamento_des)) : '--' }}</td>
                                        <td class="center">
                                            {{ ($despesa->dt_pagamento_des) ? 'Pago' : 'Pendente' }}
                                        </td>
                                        <td class="center">
                                            @forelse($despesa->anexos as $anexo)
                                                <a href="{{ url('despesas/anexos/'.\Crypt::encrypt($anexo->cd_anexo_despesa_des)) }}">
                                                    <i class="fa fa-file"></i>
                                                </a>       
                                            @empty
                                                <span>Nenhum anexo disponível</span>
                                            @endforelse
                                        </td>
                                        <td class="center">
                                            <div>
                                                <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('despesas/'.\Crypt::encrypt($despesa->cd_despesa_des)) }}"><i class="fa fa-file-text-o"></i> </a>

                                                <a title="Editar" class="btn btn-primary btn-xs" href="{{ url('despesa/editar/'.\Crypt::encrypt($despesa->cd_despesa_des)) }}"><i class="fa fa-edit"></i> </a>

                                                <button title="Excluir" class="btn btn-danger btn-xs excluir_registro" data-url="../despesas/"><i class="fa fa-trash"></i> </button> 
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
</div>
@endsection