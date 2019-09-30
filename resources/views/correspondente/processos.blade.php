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
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Meus Processos</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('correspondente/processos') }}" class="btn btn-primary pull-right header-btn" ><i class="fa fa-list"></i> Meus Processos</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('correspondente/processo/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">Nº Processo</span>
                        <input size="20" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="Nº Processo" value="{{ !empty($numero) ? $numero : '' }}" >
                    </div>                    
                    
                    <div style="width: 30%" class="form-group">
                        <select style="width: 100%" name="cd_tipo_servico_tse" class="form-control">
                            <option value="">Tipos de Serviço</option>
                            @foreach($tiposServico as $tipo)
                                <option {{ (!empty($tipoServico) && $tipoServico == $tipo->cd_tipo_servico_tse) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</option>
                            @endforeach
                        </select>
                    </div>                
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    
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
                                <tr style="font-size: 12px">                   
                                    <th style="width:11%">Prazo Fatal</th>                    
                                    <th>Nº Processo</th>
                                    <th>Cidade</th>                                                  
                                    <th>Tipo de Serviço</th>
                                    <th>Cliente</th>
                                    <th>Parte Adversa</th>
                                    <th>Status</th>
                                    <th class="center" style="min-width: 85px"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($processos as $processo)

                                    <tr>        
                                        <td>
                                            @if(!empty($processo->dt_prazo_fatal_pro))
                                                {{ date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }} {{ date('H:i', strtotime($processo->hr_audiencia_pro)) }}
                                            @endif
                                        </td>                                       
                                        <td data-id="{{ $processo->cd_processo_pro }}" >
                                            <a href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                        </td>
                                        <td>
                                            {{ (!empty($processo->cidade)) ? $processo->cidade->nm_cidade_cde.' - '.$processo->cidade->estado->sg_estado_est : '' }}
                                        </td>
                                                                   
                                       
                                         <td>{{ (!empty($processo->honorario->tipoServico)) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : '' }}</td>
                                        <td>
                                            {{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}                                           
                                        </td>
                                        <td>{{ $processo->nm_autor_pro }}</td>
                                        <td>{{ $processo->status->nm_status_processo_conta_stp }}</td>
                                        <td class="center">
                                            <div>
                                                <div style="display: block;padding: 1px 1px 1px 1px">
                                                    <a title="Detalhes" class="btn btn-default btn-xs"  href="{{ url('processos/detalhes/'. \Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-text-o"></i></a>
                                          
                                                    <a title="Acompanhamento" class="btn btn-info btn-xs" href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-search"></i></a>
                                                    
                                                </div> 
                                            </div>                                        
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
@endsection