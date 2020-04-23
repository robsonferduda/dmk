@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Mural</a></li>
        <li>Processos</li>
        <li>Acompanhamento</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-archive"></i>Processos <span> > Acompanhamento</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('home') }}" class="btn btn-default pull-right header-btn" ><i class="fa fa-desktop"></i> Mural</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ url('correspondente/processo/buscar/acompanhamento') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <section class="col col-md-4" style="padding-left: 0px;">  
                        <div class="input-group" style="width: 100%">
                            <span class="input-group-addon">Nº Processo</span>
                            <input size="20" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="Nº Processo" value="{{ !empty($numero) ? $numero : '' }}" >
                        </div>            
                    </section>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>  
                    <div style="display: block;margin-top: 15px">
                        <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #8ec9bb;float: left;margin-right: 2px"></div>Dentro do Prazo
                        </span>
                        <span style="display: inline-block;">
                           <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #f2cf59;float: left; margin-right: 2px"></div>Data limite
                        </span>
                        <span style="display: inline-block;">
                           <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #fb8e7e; float: left; margin-right: 2px"></div>Atrasado
                        </span>
                    </div>                 
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
                                    <th>Autor</th>
                                    <th>Réu</th>
                                    <th>Status</th>
                                    <th style="width: 100px" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($processos as $processo)

                                    @php $cor = ''; 
                                        
                                        if($processo->cd_status_processo_stp != \StatusProcesso::FINALIZADO){
                                            if(!empty($processo->dt_prazo_fatal_pro)){

                                                if(strtotime(date(\Carbon\Carbon::today()->toDateString()))  == strtotime($processo->dt_prazo_fatal_pro))  
                                                    $cor = "#f2cf59";   

                                                if(strtotime(\Carbon\Carbon::today())  < strtotime($processo->dt_prazo_fatal_pro))  
                                                    $cor = "#8ec9bb";

                                                if(strtotime(\Carbon\Carbon::today())  > strtotime($processo->dt_prazo_fatal_pro))
                                                    $cor = "#fb8e7e";                                         
                                                
                                            }else{
                                                $cor = "#ffffff"; 
                                            }
                                        }
                                        
                                    @endphp

                                    <tr style="background-color: {{ $cor }};">        
                                        <td>
                                            @if(!empty($processo->dt_prazo_fatal_pro))
                                                {{ date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }} {{ date('H:i', strtotime($processo->hr_audiencia_pro)) }}
                                            @endif
                                        </td>                                       
                                        <td data-id="{{ $processo->cd_processo_pro }}" >
                                            <a href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                        </td>
                                        <td>
                                            {{ (!empty($processo->cidade)) ? $processo->cidade->nm_cidade_cde.' - '.$processo->cidade->estado->sg_estado_est : 'Não informada' }}
                                        </td>
                                        <td>{{ (!empty($processo->honorario->tipoServico)) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : 'Não informado' }}</td>
                                        <td>{{ ($processo->nm_autor_pro) ? $processo->nm_autor_pro : 'Não informado' }}</td>
                                        <td>{{ ($processo->nm_reu_pro) ? $processo->nm_reu_pro : 'Não informado' }}</td>
                                        <td>{{ ($processo->status) ? $processo->status->nm_status_processo_conta_stp : 'Sem status' }}</td>
                                        <td class="center">
                                            <div>
                                                <div style="display: block;padding: 1px 1px 1px 1px">
                                                    <a title="Detalhes" class="btn btn-default btn-xs" href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-text-o"></i></a>
                                          
                                                    <a title="Acompanhamento" class="btn btn-info btn-xs" href="{{ url('correspondente/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-calendar"></i></a>
                                                    
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