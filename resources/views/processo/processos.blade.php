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
        <div class="hidden-xs col-sm-6 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Lista</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 box-button-xs">
            <div class="boxBtnTopo sub-box-button-xs">
                @role('colaborador|administrator')
                    <a class="btn btn-default pull-right" title="Relatórios" href="{{ url('processos/relatorios') }}"><i class="fa fa-file-pdf-o"></i><span class="hidden-xs hidden-sm">Relatórios</span></a>
                    <a class="btn btn-success pull-right" title="Novo Processo" href="{{ url('processos/novo') }}"><i class="fa fa-plus fa-lg"></i><span class="hidden-xs hidden-sm">Novo</span></a>
                @endrole
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <form action="{{ (Auth::user()->cd_nivel_niv == 3) ? url('correspondente/processo/buscar/arquivo') : url('processos/buscar') }}" class="form-inline" method="GET" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-4">
                            <label class="label label-black">Data prazo fatal inicial</label><br />
                            <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ !empty($dtInicio) ? $dtInicio : ''}}" >
                            
                        </section>
                        <section class="col col-md-4">                           
                            <label class="label label-black">Data prazo fatal final</label><br />
                            <input style="width: 100%" class="form-control mascara_data" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ !empty($dtFim) ? $dtFim : '' }}"  >                            
                        </section>
                         <section class="col col-md-4">                            
                            <label class="label label-black">Nº Processo</label><br />
                            <input style="width: 100%" size="20" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="" value="{{ !empty($numero) ? $numero : '' }}" >                            
                        </section>               
                        <section class="col col-md-4">
                            <label class="label label-black">Autor</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="nm_autor_pro" class="form-control" id="autor" placeholder="" value="{{ !empty($autor) ? $autor : '' }}" >                            
                        </section>       
                         <section class="col col-md-4">
                            <label class="label label-black">Réu</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="nm_reu_pro" class="form-control" id="reu" placeholder="" value="{{ !empty($reu) ? $reu : '' }}" >         
                        </section>  
                         <section class="col col-md-4">
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
                                <option value="">Tipos de Serviço Cliente</option>
                                @foreach($tiposServico as $tipo)
                                    <option {{ (!empty($tipoServico) && $tipoServico == $tipo->cd_tipo_servico_tse) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</option>
                                @endforeach
                            </select>
                        </section>    
                        <section class="col col-md-2">
                            <label class="label label-black">Nº Externo</label><br />
                            <input style="width: 100%" minlength=3 type="text" name="nu_acompanhamento_pro" class="form-control" id="acompanhamento" placeholder="" value="{{ !empty($acompanhamento) ? $acompanhamento : '' }}" >         
                        </section>    
                        <section class="col col-md-3">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                        </section>                
                    </div>
                    <div style="display: block;margin-top: 10px">
                       <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #58ab583d;float: left;margin-right: 2px"></div>Finalizado
                       </span>
                       <span style="display: inline-block;">
                       <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #ffc3c3; float: left; margin-right: 2px"></div>Cancelado
                       </span>
                    </div>  
                </form>
            </div>
            @role('colaborador|administrator')
                <label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 50 processos cadastrados. Utilize as opções de busca para personalizar o resultado.</label>
            @endrole
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
                                    <th>Prazo Fatal</th>                    
                                    <th>Nº Processo</th>
                                    <th class="hidden-xs">Cidade</th>                                                  
                                    <th class="hidden-xs">Tipo de Serviço</th>
                                    <th class="hidden-xs">Cliente</th>
                                    <th class="hidden-xs">Correspondente</th>
                                    <th class="hidden-xs">Autor</th>
                                    <th class="hidden-xs">Status</th>
                                    <th style="width: 100px;" class="center"><i class="fa fa-fw fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                                @foreach($processos as $processo)
                                    @php $cor = ''; 

                                        $cor = "#ffffff"; 

                                        if($processo->status and $processo->status->cd_status_processo_stp == StatusProcesso::FINALIZADO){
                                            $cor = "#58ab583d";
                                        }
                                            
                                        if($processo->status and $processo->status->cd_status_processo_stp == StatusProcesso::CANCELADO){
                                            $cor = "#ffc3c3";
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
                                        <td class="hidden-xs">
                                            {{ (!empty($processo->cidade)) ? $processo->cidade->nm_cidade_cde.' - '.$processo->cidade->estado->sg_estado_est : '' }}
                                        </td>
                                                                   
                                       
                                         <td class="hidden-xs">{{ (!empty($processo->honorario->tipoServico)) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : '' }}</td>
                                        <td class="hidden-xs">
                                            @if(\Auth::user()->cd_nivel_niv != 3)
                                                <a href="{{ url('cliente/detalhes/'.$processo->cliente->cd_cliente_cli) }}">{{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}</a>
                                            @else
                                                {{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}
                                            @endif                                            
                                        </td>
                                        <td class="hidden-xs">
                                            @if(\Auth::user()->cd_nivel_niv != 3)
                                                @if(!empty($processo->correspondente->contaCorrespondente))
                                                    <a href="{{ url('correspondente/detalhes/'.\Crypt::encrypt($processo->correspondente->cd_conta_con)) }}">{{$processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr}}</a>
                                                @endif
                                            @else                                                   {{$processo->correspondente->nm_razao_social_con}}
                                            @endif
                                                                                                            
                                        </td>
                                        <td class="hidden-xs">{{ $processo->nm_autor_pro }}</td>
                                        <td class="hidden-xs">{{ ($processo->status) ? $processo->status->nm_status_processo_conta_stp : 'Não informado' }}</td>
                                        <td class="center">                                            
                                            @role('colaborador|administrator')
                                               
                                                <div class="dropdown" style="display: inline;">
                                                    <a href="javascript:void(0);" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a title="Detalhes" href="{{ url('processos/detalhes/'. \Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-text-o"></i> Detalhes</a>
                                                        </li>
                                                        <li>
                                                            <a title="Editar" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-edit"></i> Editar</a>
                                                        </li>
                                                        <li><a title="Despesas" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money"></i> Despesas</a></li>
                                                        <li><a title="Acompanhamento" href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-calendar"></i> Acompanhamento</a><li>
                                                        <li><a title="Clonar" class="dialog_clone" href="{{ url('processos/clonar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-clone"></i> Clonar</a></li>
                                                        <li><a title="Relatório" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-usd"></i> Relatório Financeiro</a></li>
                                                        <li><a title="Excluir" data-url="processos/" class="excluir_registro" href="#"><i class="fa fa-trash"></i> Excluir</a></li>
                                                    </ul>
                                                </div>   
                                            @endrole                                 
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