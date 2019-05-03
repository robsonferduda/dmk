@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Listar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Lista</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('processos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Nº Processo</span>
                        <input size="25" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="Nº Processo" value="{{ !empty($numero) ? $numero : '' }}" >
                    </div>                    
                    <div class="form-group">
                        <select name="cd_tipo_processo_tpo" class="form-control">
                            <option value="">Tipos de Processo</option>
                            @foreach(\App\TipoProcesso::all() as $tipo)
                                <option {{ (!empty($tipoProcesso) && $tipoProcesso == $tipo->cd_tipo_processo_tpo) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                            @endforeach
                        </select>
                    </div>                
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a href="{{ url('processos') }}" class="btn btn-primary" ><i class="fa fa-list"></i> Listar</a>
                </form>
            </div>
            <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Processos</h2>
                </header>
                <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr>                                    
                                    <th style="width: 12%;">Nº Processo</th>
                                    <th style="width: 10%;">Data Prazo Fatal</th>
                                    <th style="width: 10%;">Hora da Audiência</th>
                                    <th style="width: 10%;">Data Solicitação</th>
                                    <th style="width: 10%;">Tipo de Processo</th>
                                    <th style="width: 21%;">Cliente</th>
                                    <th style="width: 21%;">Correspondente</th>
                                   
                                    <th style="width: 5%;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processos as $processo)
                                    @php $cor = ''; 
                                        if(date('d/m/Y', strtotime(\Carbon\Carbon::now()))  == date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)))  
                                            $cor = "#f0ad4e";   
                                        if(date('d/m/Y', strtotime(\Carbon\Carbon::now()))  < date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)))  
                                            $cor = "#5cb85c";  
                                        if(date('d/m/Y', strtotime(\Carbon\Carbon::now()))  > date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)))  
                                            $cor = "#d9534f";  

                                    @endphp

                                    <tr style="background-color: {{ $cor }}; font-weight: bold;">                                    
                                        <td data-id="{{ $processo->cd_processo_pro }}" ><a href="{{ url('processos/detalhes/'.$processo->cd_processo_pro) }}" >{{ $processo->nu_processo_pro }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }}</td>
                                        <td>{{ date('H:i', strtotime($processo->hr_audiencia_pro)) }}</td>
                                        <td>{{ date('d/m/Y', strtotime($processo->dt_solicitacao_pro)) }}</td>
                                        <td>{{ (!empty($processo->tipoProcesso->nm_tipo_processo_tpo)) ? $processo->tipoProcesso->nm_tipo_processo_tpo : '' }}</td>
                                        <td><a href="{{ url('cliente/detalhes/'.$processo->cliente->cd_cliente_cli) }}">{{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}</a></td>
                                        <td>
                                            @if($processo->correspondente)
                                                <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">{{ ($processo->correspondente->nm_fantasia_con) ? $processo->correspondente->nm_fantasia_con : $processo->correspondente->nm_razao_social_con }}</a>
                                            @endif
                                        </td>
                                      
                                        <td>
                                                                   
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
<div id="dialog_clone_text" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
     <p>
        Ao clicar em "Continuar" uma cópia do processo será realizada.
    </p>
</div>
@endsection