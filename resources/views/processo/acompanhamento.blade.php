@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Processos</li>
        <li>Listar</li>
    </ol>
</div>



<style type="text/css">

    /* Row in default-state */
    .tbody td {
      position: relative;
      height: 50px;
    }
  

    .tbody td > span {
      
      transform: translateY(-50%);
      width: 100%;
      padding-left: 15px;
      padding-right: 15px;
      text-overflow: ellipsis;
      white-space: nowrap;
      overflow: hidden;
    }

    /** Menu **/
    div.menu {
      transition: opacity .2s;
      opacity: 0;
    }

    div.menu span {
      width: auto;
      
      left: auto;
      transform: none;
      
      
    }

    /* Row-Menu */
    .tbody tr:hover div.menu {
      opacity: 1;
      transition: opacity .2s .4s ease-out;
    }

</style>

<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-cog"></i>Processos <span> > Acompanhamento</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>
            <button title="Pauta Diária" data-toggle="modal" data-target="#modal_pauta" style="margin-right: 5px" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-file-pdf-o fa-lg"></i> Pauta Diária</button>
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
                    <input type="hidden" name="acompanhamento" value="S">
                    <div class="input-group">
                        <span class="input-group-addon">Nº Processo</span>
                        <input size="20" type="text" name="nu_processo_pro" class="form-control" id="Nome" placeholder="Nº Processo" value="{{ !empty($numero) ? $numero : '' }}" >
                    </div>                    
                    <div class="form-group">
                        <select name="cd_tipo_processo_tpo" class="form-control">
                            <option value="">Tipos de Processo</option>
                            @foreach($tiposProcesso as $tipo)
                                <option {{ (!empty($tipoProcesso) && $tipoProcesso == $tipo->cd_tipo_processo_tpo) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                            @endforeach
                        </select>
                    </div> 
                    <div style="width: 30%" class="form-group">
                        <select style="width: 70%" name="cd_tipo_servico_tse" class="form-control">
                            <option value="">Tipos de Serviço Cliente</option>
                            @foreach($tiposServico as $tipo)
                                <option {{ (!empty($tipoServico) && $tipoServico == $tipo->cd_tipo_servico_tse) ? 'selected' : '' }} value="{{ $tipo->cd_tipo_servico_tse }}">{{ $tipo->nm_tipo_servico_tse }}</option>
                            @endforeach
                        </select>
                    </div>    
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
                    <a href="{{ url('processos') }}" class="btn btn-primary" ><i class="fa fa-list"></i> Listar</a>
                    <div style="display: block;margin-top: 10px">
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
                                    <th style="width: 13%;">Nº Processo</th>
                                    <th style="width: 12%;">Cidade</th>                                                  
                                    <th style="width: 11%;">Tipo de Serviço Cliente</th>
                                    <th style="width: 15%;">Cliente</th>
                                    <th style="width: 15%;">Correspondente</th>
                                    <th style="width: 100px;">Parte Adversa</th>
                                    <th>Status</th>  
                                    <th style="width: 100px;" data-hide="phone,tablet"><i class="fa fa-fw fa-cog"></i> Ações</th>                                   
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px" class="tbody">
                                @foreach($processos as $processo)
                                    @php $cor = ''; 

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
                                        
                                    @endphp

                                    <tr style="background-color: {{ $cor }};">        
                                        <td>
                                            @if(!empty($processo->dt_prazo_fatal_pro))
                                                {{ date('d/m/Y', strtotime($processo->dt_prazo_fatal_pro)) }} {{ date('H:i', strtotime($processo->hr_audiencia_pro)) }}
                                            @endif
                                        </td>                                       
                                        <td data-id="{{ $processo->cd_processo_pro }}" >
                                            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" >{{ $processo->nu_processo_pro }}</a>
                                        </td>
                                        <td>
                                            {{ (!empty($processo->cidade)) ? $processo->cidade->nm_cidade_cde.' - '.$processo->cidade->estado->sg_estado_est : '' }}
                                        </td>
                                                                   
                                       
                                         <td>{{ (!empty($processo->honorario->tipoServico)) ? $processo->honorario->tipoServico->nm_tipo_servico_tse : '' }}

                                         </td>
                                        <td>
                                            @if($processo->cliente)
                                                <a href="{{ url('cliente/detalhes/'.$processo->cliente->cd_cliente_cli) }}">{{ ($processo->cliente->nm_fantasia_cli) ? $processo->cliente->nm_fantasia_cli : $processo->cliente->nm_razao_social_cli }}</a>
                                            @else
                                                <span>Nenhum recurso informado</span>
                                            @endif                                            
                                        </td>
                                        <td>
                                            @if($processo->correspondente)
                                                <a href="{{ url('correspondente/detalhes/'.$processo->correspondente->cd_conta_con) }}">{{ ($processo->correspondente->contaCorrespondente) ? $processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr : '' }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $processo->nm_autor_pro }}</td>
                                        <td>{{ $processo->status->nm_status_processo_conta_stp }}</td>
                                        <td>
                                            <a title="Detalhes" class="btn btn-default btn-xs"  href="{{ url('processos/detalhes/'. \Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-text-o"></i></a>
                                            <a title="Editar" class="btn btn-primary btn-xs editar_vara" href="{{ url('processos/editar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-edit"></i></a>
                                            <div class="dropdown" style="display: inline;">
                                                <a href="javascript:void(0);" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear"></i> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a title="Despesas" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money"></i> Despesas</a></li>
                                                    <li><a title="Acompanhamento" href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-search"></i> Acompanhamento</a></li>
                                                    <li><a title="Clonar" class="dialog_clone" href="{{ url('processos/clonar/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-clone"></i> Clonar</a></li>
                                                    <li><a title="Relatório" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-file-pdf-o"></i> Relatórios</a></li>
                                                    <li><a title="Excluir" data-url="../processos/" class="excluir_registro" href="#"><i class="fa fa-trash"></i> Excluir</a></li>
                                                </ul>
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
  <div class="modal fade in modal_top_alto" id="modal_pauta" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-file-pdf-o"></i> Pauta Diária</h4>
                     </div>
                    <div class="modal-body">
                        <form method="POST" class="smart-form" id="frm-pauta" action="{{ url('processo/pauta-diaria') }}">
                        @csrf
                            <fieldset>
                                <div class="row">
                                    <section class="col col-4">
                                        <label>Data prazo fatal início</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_inicio" id="dt_inicio" placeholder="___/___/____" class="mascara_data">
                                        </label>
                                    </section>
                                     <section class="col col-4">
                                        <label>Data prazo fatal fim</label>
                                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                                            <input type="text" name="dt_fim" id="dt_fim" placeholder="___/___/____" class="mascara_data" >
                                        </label>
                                    </section>
                                    <section class="col col-2">
                                        <br />              
                                        <input type="radio"  required name="tipo" id="tipo" value="excel" >  
                                        <label>Excel</label> 
                                    </section> 
                                     <section class="col col-2">
                                        <br />              
                                        <input type="radio" required name="tipo" id="tipo" value="pdf" >  
                                        <label>PDF</label> 
                                    </section> 
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Responsável</label>
                                        <select style="width: 100%"  class="select2" name="responsavel" >
                                            <option value="">Todos</option>
                                            @foreach($responsaveis as $user)
                                                 <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">
                                    <section class="col col-md-12">
                                        <label>Tipos de Processo</label>
                                        <select style="width: 100%"  class="select2" name="tipoProcesso" >
                                            <option value="">Todos</option>
                                            @foreach($tiposProcesso as $tipo)
                                                 <option value="{{ $tipo->cd_tipo_processo_tpo }}">{{ $tipo->nm_tipo_processo_tpo }}</option>
                                            @endforeach
                                        </select>
                                    </section>
                                </div>
                                <div class="row">    
                                    <input type="hidden" name="cdCorrespondente" value="">           
                                    <section class="col col-sm-12">
                                        <label class="label">Correspondente</label>
                                        <label class="input">
                                            <input class="form-control" name="nmCorrespondente" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete_pauta" value="">
                                        </label>
                                    </section>
                                </div> 
                            </fieldset>
                            <footer>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Gerar Pauta</button>
                                <a type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-user fa-remove"></i> Fechar</a>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
<script type="text/javascript">

    $(document).ready(function() {

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete_pauta" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          beforeSend: function(){
           
          },
          search: function(event, ui){
            
            $("input[name='cdCorrespondente']").val('');
          },
          select: function(event, ui) {

            $("input[name='cdCorrespondente']").val(ui.item.id);
            

          },
          open: function(event, ui){
            
          },
          appendTo: "#modal_pauta",
          
        });

        $( "#correspondente_auto_complete_pauta" ).focusout(function(){
           if($("input[name='cdCorrespondente']").val() == ''){
                $("#correspondente_auto_complete_pauta").val('');
                $('.ui-autocomplete').attr('style', 'z-index: 905 !important');

           }
        });

        $('select').on('select2:open', function(e){
             $('.custom-dropdown').parent().css('z-index', 99999);
        });

        $('#modal_pauta').on('shown.bs.modal', function () {
          $('#dt_pauta').trigger('focus');
        });

       
    });

</script>
@endsection