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
                    <div class="row">
                        <section class="col col-md-4">                            
                            <label class="label label-black">Nº Processo</label><br />
                            <input style="width: 100%" size="20" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="" value="{{ !empty($numero) ? $numero : '' }}" >                            
                        </section>               
                        <section class="col col-md-4">
                            <label class="label label-black">Autor</label><br />
                            <input style="width: 100%" size="20" type="text" name="nm_autor_pro" class="form-control" id="autor" placeholder="" value="{{ !empty($autor) ? $autor : '' }}" >                            
                        </section>                  
                        <section class="col col-md-4">
                            <label class="label label-black">Réu</label><br />
                            <input style="width: 100%" size="20" type="text" name="nm_reu_pro" class="form-control" id="reu" placeholder="" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section>                           
                    </div>  
                    <div class="row"> 
                        <section class="col col-md-3">
                            <label class="label label-black"></label><br />
                            <select style="width: 100%" name="cd_tipo_processo_tpo" class="form-control">
                                <option value="">Tipos de Processo</option>
                                @foreach($tiposProcesso as $tipo)
                                    <option {{ (!empty($tipoProcesso) && $tipoProcesso == $tipo->cd_tipo_processo_tpo) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                @endforeach
                            </select>
                        </section>
                        <section class="col col-md-4">
                            <label class="label label-black"></label><br />
                            <select style="width: 100%" name="cd_tipo_servico_tse" class="form-control">
                                <option value="">Tipos de Serviço</option>
                                @foreach($tiposServico as $tipo)
                                    <option {{ (!empty($tipoServico) && $tipoServico == $tipo->cd_tipo_servico_tse) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</option>
                                @endforeach
                            </select>
                        </section>   
                        <section class="col col-md-2">
                            <label class="label label-black">Nº Externo</label><br />
                            <input style="width: 100%" size="20" type="text" name="nu_acompanhamento_pro" class="form-control" id="acompanhamento" placeholder="" value="{{ !empty($acompanhamento) ? $acompanhamento : '' }}" >         
                        </section>    
                        <section class="col col-md-3">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                            <a href="{{ url('processos') }}" class="btn btn-primary" ><i class="fa fa-list"></i> Listar</a>
                        </section>
                    </div>
                    <div style="display: block;margin-top: 10px">
                       <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #8ec9bb;float: left;margin-right: 2px"></div>Finalizado
                       </span>
                       <span style="display: inline-block;">
                       <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #fb8e7e; float: left; margin-right: 2px"></div>Cancelado
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
                                    <th>Cliente</th>
                                    <th>Correspondente</th>
                                    <th>Parte Adversa</th>
                                    <th>Status</th>
                                    <th style="min-width: 85px" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($processos as $processo)
                                    @php $cor = ''; 

                                        $cor = "#ffffff"; 

                                        if($processo->status->cd_status_processo_stp == StatusProcesso::FINALIZADO){
                                            $cor = "#8ec9bb";
                                        }
                                            
                                        if($processo->status->cd_status_processo_stp == StatusProcesso::CANCELADO){
                                            $cor = "#fb8e7e";
                                        }
                                        
                                    @endphp

                                    <tr style="background-color: {{ $cor }};">        
                                        <td>
                                            @if(!empty($processo->dt_prazo_fatal_pro))
                                                {{ date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }} {{ date('H:i', strtotime($processo->hr_audiencia_pro)) }}
                                            @endif
                                        </td>                                       
                                        <td data-id="{{ $processo->cd_processo_pro }}" >
                                            <a href="{{ url('processos/detalhes/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                        </td>
                                        <td>
                                            {{ (!empty($processo->cidade)) ? $processo->cidade->nm_cidade_cde.' - '.$processo->cidade->estado->sg_estado_est : '' }}
                                        </td>
                                                                   
                                       
                                         <td>{{ (!empty($processo->honorario)) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : '' }}</td>
                                        <td>
                                            <a href="{{ url('cliente/detalhes/'.$processo->cliente->cd_cliente_cli) }}">{{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}</a>                                            
                                        </td>
                                        <td>
                                            @if(!empty($processo->correspondente->contaCorrespondente))
                                                <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">{{$processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr}}</a>
                                            @endif
                                        </td>
                                        <td>{{ $processo->nm_autor_pro }}</td>
                                        <td>{{ $processo->status->nm_status_processo_conta_stp }}</td>
                                        <td>
                                            <div>
                                                <div style="display: block;padding: 1px 1px 1px 1px">
                                                    <a title="Detalhes" class="btn btn-default btn-xs"  href="{{ url('processos/detalhes/'. \Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-text-o"></i></a>
                                                    <a title="Editar" class="btn btn-primary btn-xs editar_vara" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-edit"></i></a>
                                                    <a title="Despesas" class="btn btn-warning btn-xs" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money"></i></a>
                                                </div>
                                                <div style="display: block;padding: 1px 1px 1px 1px">
                                                    <a title="Relatório" class="btn btn-default btn-xs" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-usd"></i></a>
                                                    <a title="Acompanhamento" class="btn btn-info btn-xs" href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-search"></i></a>
                                                    <a title="Clonar" class="btn btn-primary btn-xs dialog_clone" href="{{ url('processos/clonar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-clone"></i></a>
                                                </div>
                                                <div style="display: block;padding: 1px 1px 1px 1px">
                                                    <button title="Excluir" data-url="processos/" class="btn btn-danger btn-xs excluir_registro" href=""><i class="fa fa-trash"></i></button>
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
<div id="dialog_clone_text" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; max-height: none; height: auto;">
     <p>
        Ao clicar em "Continuar" uma cópia do processo será realizada.
    </p>
</div>
@endsection