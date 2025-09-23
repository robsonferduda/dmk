@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Processos</a></li>
        <li>Editar</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o la-lg"></i> Processos <span>> Editar</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 boxBtnTopo">
            <a href="{{ url('processos') }}" class="btn btn-default pull-right"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            <a title="Relatório" class="btn btn-default pull-right header-btn" href="{{ url('processos/relatorio/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-usd fa-lg"></i> Relatório Financeiro</a>
            <a href="{{ url('processos/acompanhamento/'.\Crypt::encrypt($processo->cd_processo_pro)) }}" class="btn btn-default pull-right header-btn"><i class="fa fa-calendar fa-lg"></i> Acompanhamento</a> 
            <a title="Despesas" class="btn btn-warning pull-right header-btn" href="{{ url('processos/despesas/'.\Crypt::encrypt($processo->cd_processo_pro)) }}"><i class="fa fa-money fa-lg"></i> Despesas</a>
            <a data-toggle="modal" href="{{ url('processos/novo') }}" class="btn btn-success pull-right header-btn"><i class="fa fa-plus fa-lg"></i> Novo</a>                 
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <header role="heading" class="ui-sortable-handle">
                    <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                    <h2>Cadastro de Processo </h2>             
                    
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>

                <!-- widget div-->
                <div role="content">
                    
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                        
                    </div>
                    <!-- end widget edit box -->
                    
                    <!-- widget content -->
                    <div class="widget-body no-padding">
                        
                        {!! Form::open(['id' => 'frm-add-processo', 'url' => ['processos',$processo->cd_processo_pro], 'class' => 'smart-form', 'method' => 'PUT']) !!}
                        
                        <div class="row">
                            <div  class="col col-md-12">
                                <header>
                                    <i class="fa fa-file-text-o"></i> Dados do Processo <span class="text-danger">(*) Campos Obrigatórios</span>
                                </header>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div  class="col col-6">
                              

                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-sm-12">
                                            <input type="hidden" name="cd_cliente_cli" id="cd_cliente_cli" value="{{old('cd_cliente_cli') ? old('cd_cliente_cli') : $processo->cd_cliente_cli}}" >
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input required name="nm_cliente_cli" value="{{old('nm_cliente_cli') ? old('nm_cliente_cli') : $nome }}" class="form-control ui-autocomplete-input" placeholder="Digite 3 caracteres para busca" type="text" id="client" autocomplete="off">
                                            </label>
                                        </section>
                                    </div> 
                                    <div class="row">

                                        <section class="col col-4">
                                            <label class="label">Código Cliente <a href="#" rel="popover-hover" data-placement="top" data-original-title="Número ou código de acompanhamento externo."><i class="fa fa-question-circle text-primary"></i></a></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_acompanhamento_pro') ? old('nu_acompanhamento_pro') : $processo->nu_acompanhamento_pro }}" type="text" name="nu_acompanhamento_pro" maxlength="50">
                                            </label>
                                        </section> 

                                        <section class="col col-8">                                       
                                            <label class="label" >Advogado Solicitante <a href="#" data-toggle="modal" data-target="#novoAdvogado" style="padding: 1px 8px;"><i class="fa fa-plus-circle"></i> Novo </a></label>  
                                            <label class="select">
                                                <input type="hidden" id="contatoAux"  value="{{ old('cd_contato_cot') ? old('cd_contato_cot') : $processo->cd_contato_cot }}">
                                                <select  id="cd_contato_cot" name="cd_contato_cot" >
                                                    <option selected value="">Selecione um Advogado Solicitante</option>            
                                                </select><i></i>  
                                            </label>         
                                        </section>

                                    </div>
                                    <div class="row">
                                         <section class="col col-6">
                                            <label class="label">Nº Processo<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_processo_pro') ? old('nu_processo_pro') : $processo->nu_processo_pro }}" type="text" name="nu_processo_pro" required>
                                            </label>
                                        </section> 
                                         <section class="col col-6" >                                       
                                            <label class="label" >Tipo de Processo<span class="text-danger">*</span></label>          
                                            <label class="select">
                                                <select  name="cd_tipo_processo_tpo" required>
                                                    <option selected value="">Selecione o Tipo de Processo</option>     
                                                     @foreach($tiposProcesso as $tipo) 
                                                        <option {!! (old('cd_tipo_processo_tpo',$processo->cd_tipo_processo_tpo) == $tipo->cd_tipo_processo_tpo ? 'selected' : '' ) !!} value="{{$tipo->cd_tipo_processo_tpo}}">{{ $tipo->nm_tipo_processo_tpo}}</option>
                                                     @endforeach       
                                                </select><i></i>   
                                            </label>
                                        </section>
                                    </div>   
                                    
                                    <div class="row">
                                        <section class="col col-sm-12">       
                                            <label class="label" >Vara</label>          
                                            <select  name="cd_vara_var" class="select2">
                                                <option selected value="">Selecione uma vara</option>
                                                @foreach($varas as $vara) 
                                                    <option {!! (old('cd_vara_var',$processo->cd_vara_var) == $vara->cd_vara_var ? 'selected' : '' ) !!} value="{{$vara->cd_vara_var}}">{{ $vara->nm_vara_var}}</option>
                                                @endforeach
    
                                            </select> 
                                        </section>
                                    </div> 

                                    <div class="row">
                    
                                        <section class="col col-6">
                                           
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione um estado</option>
                                                
                                                @foreach($estados as $estado) 
                                                    <option {!! (old('cd_estado_est',!empty($processo->cidade->cd_estado_est) ? $processo->cidade->cd_estado_est : '') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach
    
                                            </select> 
                                        </section>
                                        <section class="col col-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{ old('cd_cidade_cde') ? old('cd_cidade_cde') : $processo->cd_cidade_cde }}">
                                           <label class="label" >Cidade<span class="text-danger">*</span></label>          
                                            <select  id="cidade" disabled name="cd_cidade_cde" class="select2" required>
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div>   
                                    

                                </fieldset>
                        </div>
                        <div  class="col col-6">
                            <fieldset>
                                <div class="row">
                                    <section class="col col-4">
                                        <label class="label">Data da Solicitação</label>
                                        <label class="input">
                                           <input class="dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{ old('dt_solicitacao_pro') ? old('dt_solicitacao_pro') : $processo->dt_solicitacao_pro }}">
                                        </label>
                                    </section>                                     
                                    <section class="col col-4">
                                        <label class="label">Data Prazo Fatal<span class="text-danger">*</span></label>
                                        <label class="input">
                                           <input class="dt_prazo_fatal_pro" placeholder="___ /___ /___" type="text" name="dt_prazo_fatal_pro" value="{{ old('dt_prazo_fatal_pro') ? old('dt_prazo_fatal_pro') : $processo->dt_prazo_fatal_pro }}">
                                        </label>
                                    </section> 
                                    <section class="col col-4">
                                        <label class="label">Hora da Audiência</label>
                                        <label class="input">
                                           <input class="hr_audiencia_pro" placeholder="___ : ___" type="text" name="hr_audiencia_pro" value="{{ old('hr_audiencia_pro') ? old('hr_audiencia_pro') : $processo->hr_audiencia_pro}}" >
                                        </label>
                                    </section> 
                                </div> 
                                <div class="row">
                                    <section class="col col-sm-12">
                                        <label class="label">Autor</label>
                                        <label class="input">
                                            <input class="form-control" placeholder="" maxlength="500" type="text" name="nm_autor_pro" value="{{ old('nm_autor_pro') ? old('nm_autor_pro') : $processo->nm_autor_pro }}">
                                        </label>
                                    </section> 
                                </div>                                    
                                <div class="row"> 
                                     <section class="col col-sm-12">
                                        <label class="label">Réu</label>
                                        <label class="input">
                                           <input class="form-control" placeholder="" type="text"  maxlength="500" name="nm_reu_pro" value="{{ old('nm_reu_pro') ? old('nm_reu_pro') : $processo->nm_reu_pro}}" >
                                        </label>
                                    </section> 
                                </div>
                                <div class="row">
                                    <input type="hidden" name="cd_correspondente_cor_aux" id="cd_correspondente_cor_aux" value="{{old('cd_correspondente_cor') ? old('cd_correspondente_cor') : $processo->cd_correspondente_cor}}"> 
                                    <input type="hidden" name="fl_correspondente_escritorio_ccr" value="{{ (old('fl_correspondente_escritorio_ccr') ? old('fl_correspondente_escritorio_ccr') : !empty($processo->correspondente->contaCorrespondente)) ? $processo->correspondente->contaCorrespondente->fl_correspondente_escritorio_ccr : 'N' }}" >       
                                    <section class="col col-xs-12">
                                        <label class="label">Correspondente <span class="text-info">Filtrado de acordo com estado/cidade escolhida</span></label>
                                        <select  id="correspondente_auto_complete"  name="cd_correspondente_cor" class="select2" disabled data-flag=''>
                                           <option selected value="">Aguardando Cidade... </option>
                                        </select>                                                         
                                    </section>
                                </div> 
                                <div class="row">
                                    <input type="hidden" name="cd_responsavel_pro" value="{{old('cd_responsavel_pro') ? old('cd_responsavel_pro') : $processo->cd_responsavel_pro}}" >      
                                    
                                    <section class="col col-xs-6 col-lg-6">
                                        <label class="label">Responsável</label>
                                        <label class="input">
                                            <input class="form-control ui-autocomplete-input"  name="name" value="{{ ($processo->responsavel) ? $processo->responsavel->name : old('name') }}" placeholder="Digite 3 caracteres para busca" id="responsavel_auto_complete" type="text" autocomplete="off">
                                        </label>
                                    </section>

                                    <section class="col col-xs-6 col-lg-6">
                                           
                                            <label class="label" >Área do Direito<span class="text-danger">*</span>          
                                            <select  id="cd_area_direito_ado" name="cd_area_direito_ado" class="select2">
                                                <option selected value="">Selecione um área</option>
                                                @foreach($areas as $area) 
                                                    <option {!! ($processo->cd_area_direito_ado == $area->cd_area_direito_ado) ? 'selected' : '' !!} value="{{ $area->cd_area_direito_ado }}">{{ $area->dc_area_direito_ado}}</option>
                                                @endforeach

                                            </select> 
                                            </label>
                                    </section>
                                </div>
                                
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <header>
                                <i class="fa fa-legal"></i> Dados da Audiência
                            </header>
                            <fieldset>
                                <div class="row" style=""> 
                                    <section class="col col-md-12 col-lg-12">
                                        <label class="label">Link da Audiência</label>
                                        <label class="input">
                                            <input type="text" class="form-control" placeholder="Link da Audiência" name="ds_link_audiencia_pro" id="ds_link_audiencia_pro" {{ ($processo->ds_link_audiencia_pro) ? $processo->ds_link_audiencia_pro : old('ds_link_audiencia_pro') }}>
                                        </label>
                                    </section> 
                                </div>
                                <div class="row" style="margin: 1px 0px 1px -10px !important;"> 
                                    <section class="col col-sm-6">
                                        <label class="label">Advogado</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_advogado_pro">{{ old('nm_advogado_pro') ? old('nm_advogado_pro') : $processo->nm_advogado_pro }}</textarea>
                                        </label>
                                    </section> 

                                    <section class="col col-sm-6">
                                        <label class="label">Preposto</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_preposto_pro">{{ old('nm_preposto_pro') ? old('nm_preposto_pro') : $processo->nm_preposto_pro }}</textarea>
                                        </label>
                                    </section>                               
                                    
                                    <section class="col col-sm-12">
                                        <label class="label">Observações do Processo <span class="text-info">Aparecem na Pauta Diária</span></label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="dc_observacao_processo_pro">{{ old('observacao_processo') ? old('observacao_processo') : $processo->dc_observacao_processo_pro }}</textarea>
                                        </label>
                                    </section> 
                                </div>
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <fieldset style="padding-top: 0px">
                                <div class="row" style="margin: 1px -10px 1px -10px !important;"> 
                                    <section class="col col-sm-12">
                                    <label class="label">Observações para Correspondentes</label>
                                    <label class="input">
                                        <textarea class="form-control" id="observacao" rows="8" name="dc_observacao_pro" value="{{ old('dc_observacao_pro') ? old('dc_observacao_pro') : $processo->dc_observacao_pro }}">{!! old('dc_observacao_pro') ? old('dc_observacao_pro') : $processo->dc_observacao_pro !!}</textarea>
                                    </label>
                                    </section> 
                                </div>
                            </fieldset>
                        </div>
                        <div class="col col-sm-12">
                            <header>
                                <i class="fa fa-money"></i> Honorários
                            </header>
                            <br />
                            <fieldset style="padding-top: 0px">
                                <div class="row"> 
                                    <section class="col col-sm-12">
                                        
                                                <div class="alert alert-info" role="alert">
                                                    <i class="fa-fw fa fa-info"></i>
                                                    <strong>Informação!</strong> Ao selecionar o tipo de serviço, os campos de valor serão preenchidos com os valores padrões cadastrados no Cliente e/ou Correspondente na cidade selecionada, caso os valores existam. Sendo permitida sua mudança.
                                                </div>
                                                <div class="">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th style="width: 50%">Tipo de Serviço do Cliente<span class="text-danger">*</span></th>
                                                            <th style="">Valor Cliente</th>               
                                                            <th style="">Nota Fiscal Cliente</th>
                                                        </thead>
                                                        <tbody>  
                                                            <tr>  
                                                                <td>      
                                                                    <input type="hidden" id="cd_tipo_servico_tse_aux" name="cd_tipo_servico_tse_aux" value="{{ ($processoTaxaHonorario) ? $processoTaxaHonorario->cd_tipo_servico_tse : old('cd_tipo_servico_tse') }}">                                 
                                                                    <select id="tipoServico" name="cd_tipo_servico_tse" class="select2" >
                                                                        <option selected value="">Selecione um cliente e cidade
                                                                        </option>      
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input style="width: 100px; padding-left: 12px" name="taxa_honorario_cliente"  id="taxa-honorario-cliente" type="text" class="form-control taxa-honorario" value="{{ old('taxa_honorario_cliente',(!empty($processoTaxaHonorario->vl_taxa_honorario_cliente_pth)) ? $processoTaxaHonorario->vl_taxa_honorario_cliente_pth : '')}}" >
                                                                        </div>
                                                                        </div>
                                                                </td>                                                                
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">%</span>
                                                                            <input name="nota_fiscal_cliente" style="width: 100px;padding-left: 12px" id="nota_fiscal_cliente" type="text" class="form-control taxa-honorario"  value="{{ old('nota_fiscal_cliente',(!empty($processoTaxaHonorario->vl_taxa_cliente_pth)) ? $processoTaxaHonorario->vl_taxa_cliente_pth : '')}}" title="Aguardando seleção do Cliente" >
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                           
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th style="width: 50%" id="tipoServicoCorrespondenteLabel" >Tipo de Serviço do Correspondente</th>
                                                            <th style="">Valor Correspondente</th>            
                                                        </thead>
                                                        <tbody>  
                                                            <tr>  
                                                                <td>                     
                                                                     <input type="hidden" id="cd_tipo_servico_correspondente_tse_aux" name="cd_tipo_servico_correspondente_tse_aux" value="{{ ($processoTaxaHonorario) ? $processoTaxaHonorario->cd_tipo_servico_correspondente_tse : old('cd_tipo_servico_correspondente_tse') }}">                  
                                                                    <select id="tipoServicoCorrespondente" name="cd_tipo_servico_correspondente_tse" class="select2" disabled>
                                                                        <option selected value="">Selecione um correspondente e cidade
                                                                        </option>                                
                                                                    </select>
                                                                </td>                                      
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">$</span>
                                                                            <input name="taxa_honorario_correspondente" style="width: 100px;padding-left: 12px" id="taxa-honorario-correspondente" type="text" class="form-control taxa-honorario"  value="{{ old('taxa_honorario_correspondente',(!empty($processoTaxaHonorario->vl_taxa_honorario_correspondente_pth)) ? $processoTaxaHonorario->vl_taxa_honorario_correspondente_pth : '')}}" >
                                                                    </div>
                                                                    </div>
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                     </section> 
                                </div>
                            </fieldset>
                        </div>
                    </div>
                     
                     
                          
                        <footer>
                            <button type="submit" class="btn btn-success">
                                <i class="fa-fw fa fa-save"></i>Atualizar
                            </button>
                            <a href="{{ url('processos') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                        </footer>
                        {!! Form::close() !!}                      
                        
                    </div>
                    <!-- end widget content -->
                    
                </div>
                <!-- end widget div -->
                
            </div>
            </article>
        </div>
    </div>
</div>
<div class="modal fade modal_top_alto" id="novoAdvogado" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-legal"></i> Adicionar Advogado Solicitante
                </h4>
            </div>
            <div class="modal-body">
                <div class="row box-cadastro">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Nome Completo</strong></label>
                            <input type="text" class="form-control" placeholder="Nome Completo" required="required" name="nome_advogado_solicitante" id="nome_advogado_solicitante">
                            <div id="msg_error_advogado" class="text-danger"></div>
                        </div>    
                    </div>
                    <div style="padding: 5px 15px;">
                        <p>Complete o cadastro com demais dados pessoais e informações de contato acessando a opção Agenda > Editar Contato.</p>
                    </div>
                </div>
                <div class="row box-erro-cadastro" style="display: none">
                    <div class="col-md-12 center">
                        <p class="text-danger">Obrigatório selecionar o cliente para habilitar o cadastro de Advogado Solicitante.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                <button type="button" class="btn btn-success" id="btnSalvarAdvogadoSolicitante"><i class="fa-fw fa fa-save"></i> Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {

        var controlaChangeTS = 0;
        var controlaChangeTSC = 0;

        var path = "{{ url('autocompleteCliente') }}";
        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";
        var pathResponsavel = "{{ url('autocompleteResponsavel') }}";

        if($("input[name='fl_correspondente_escritorio_ccr']").val()== 'N'){
            $('#tipoServicoCorrespondenteLabel').html($('#tipoServicoCorrespondenteLabel').text()+"<span class='text-danger'>*</span>");
        }

        $('#novoAdvogado').on('shown.bs.modal', function () {

            if($("#cd_cliente_cli").val()){

                $(".box-cadastro").css('display','block');
                $(".box-erro-cadastro").css('display','none');
                
            }else{
                $(".box-erro-cadastro").css('display','block');
                $(".box-cadastro").css('display','none');
            }

            $("#msg_error_advogado").html("");
            $("#nome_advogado_solicitante").trigger('focus');
        });

        $("#btnSalvarAdvogadoSolicitante").click(function(){

            var nome = $("#nome_advogado_solicitante").val();
            var cliente = $("#cd_cliente_cli").val();

            if(nome == ""){
                $("#msg_error_advogado").html("<span>Campo nome completo é obrigatório.</span>");
                $("#nome_advogado_solicitante").trigger('focus');
            }else{
                $("#msg_error_advogado span").remove();
            
                $.ajax(
                {
                    type: "POST",
                    url: "../../cliente/advogado",
                    data: {
                        "_token": $('meta[name="token"]').attr('content'),
                        "cliente": cliente,
                        "nome_advogado_solicitante": nome
                    },
                    beforeSend: function()
                    {
                        //$("#processamento").modal('show');
                    },
                    success: function(response)
                    {
                        buscaAdvogado(); 
                        $("#cd_contato_cot").val(response.id);
                        $("#novoAdvogado").modal('hide');
                    },
                    error: function(response)
                    {
                        
                        //location.reload();
                    }
                });
            }

        });

        $( "#responsavel_auto_complete" ).autocomplete({
          source: pathResponsavel,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_responsavel_pro']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $( "#responsavel_auto_complete" ).focusout(function(){
           if($("input[name='cd_responsavel_pro']").val() == ''){
                $("#responsavel_auto_complete").val('');
           }
        });

        $( "#client" ).focusout(function(){
           if($("input[name='cd_cliente_cli']").val() == ''){
                $("#client").val('');
           }
        });

        $( "#client" ).autocomplete({
          source: path,
          minLength: 2,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);
            $("input[name='nota_fiscal_cliente']").val(ui.item.nota);
            $("input[name='nota_fiscal_cliente']").prop('disabled', false);
            $("#taxa-honorario-cliente").val('');

            buscaAdvogado();

            var cliente = $("input[name='cd_cliente_cli']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();
            
            if(cliente != '' && cidade != ''){
                buscaTiposServico(cliente,cidade);
            } 
            
          },
          open: function(event, ui){
            
          }
        });

        var buscaTiposServico = function(cliente,cidade){
           
            $.ajax(
            {
               url: "../../tipos-de-servico/cliente/"+cliente+"/cidade/"+cidade,
               type: 'GET',
               dataType: "JSON",
            success: function(response)
            {              
                
                $('#tipoServico').empty();
                $('#tipoServico').append('<option selected value="">Selecione um tipo de serviço</option>');
                $.each(response,function(index,element){

                    if($("#cd_tipo_servico_tse_aux").val() != element.tipo_servico.cd_tipo_servico_tse){
                        $('#tipoServico').append('<option value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');                            
                    }else{
                        $('#tipoServico').append('<option selected value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');      
                    }
                                
                });       
                $('#tipoServico').trigger('change');     
                $('#tipoServico').prop( "disabled", false );        

            },
            error: function(response)
            {
            }
            });
        }

        var buscaTiposServicoCorrespondente = function(correspondente,cidade){

            $.ajax(
            {
               url: "../../tipos-de-servico/correspondente/"+correspondente+"/cidade/"+cidade,
               type: 'GET',
               dataType: "JSON",
            success: function(response)
            {              
                
                $('#tipoServicoCorrespondente').empty();
                $('#tipoServicoCorrespondente').append('<option selected value="">Selecione um tipo de serviço</option>');
                $.each(response,function(index,element){
                                        
                    if($("#cd_tipo_servico_correspondente_tse_aux").val() != element.tipo_servico.cd_tipo_servico_tse){
                        $('#tipoServicoCorrespondente').append('<option value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');                            
                    }else{                        
                        $('#tipoServicoCorrespondente').append('<option selected value="'+element.tipo_servico.cd_tipo_servico_tse+'">'+element.tipo_servico.nm_tipo_servico_tse+'</option>');      
                    }
                                
                });       
                $('#tipoServicoCorrespondente').trigger('change');     
                $('#tipoServicoCorrespondente').prop( "disabled", false );        

            },
            error: function(response)
            {
            }
            });
        }
  
        $('#cidade').change(function(){
            var cliente = $("input[name='cd_cliente_cli']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();
            var estado = $("select[name='cd_cidade_cde']").val();

            $('#tipoServicoCorrespondente').empty();
            $('#tipoServicoCorrespondente').append('<option selected value="">Selecione um correspondente e cidade</option>');
            $('#tipoServicoCorrespondente').trigger('change');
            
            if(correspondente != '' && cidade != ''){
                buscaTiposServicoCorrespondente(correspondente,cidade);
            }

            if(cliente != '' && cidade != ''){
                buscaTiposServico(cliente,cidade);
            }

            buscaCorrespondente(estado,cidade);
        });
   
        $('#tipoServico').change(function(){

            controlaChangeTS++;

            if(controlaChangeTS > 1){
                $("#taxa-honorario-cliente").val('');                 
            }
            var cliente = $("input[name='cd_cliente_cli']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();
            var tipoServico = $(this).val();
            if(cliente != '' && cidade != '' && tipoServico != '' && controlaChangeTS > 1 ){
                $.ajax({
                        
                        url: '../../busca-valor-cliente/'+cliente+'/'+cidade+'/'+tipoServico,
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function(){
                            // $('#cidade').empty();
                            // $('#cidade').append('<option selected value="">Carregando...</option>');
                            // $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                 
                            if(response){
                                var response = JSON.parse(response);  
                                $("#taxa-honorario-cliente").val(response.nu_taxa_the);       
                            }
                        },
                        error: function(response)
                        {
                                //console.log(response);
                        }
                });
            }
        });

         $('#tipoServicoCorrespondente').change(function(){

            controlaChangeTSC++;

            if(controlaChangeTSC > 2){
                $("#taxa-honorario-correspondente").val('');                 
            }

            var correspondente = $("select[name='cd_correspondente_cor']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();
            var tipoServico = $(this).val();

            if(correspondente != '' && cidade != '' && tipoServico != '' && controlaChangeTSC > 2){
                $.ajax({
                        
                        url: '../../busca-valor-correspondente/'+correspondente+'/'+cidade+'/'+tipoServico,
                        type: 'GET',
                        dataType: 'JSON',
                        beforeSend: function(){
                            // $('#cidade').empty();
                            // $('#cidade').append('<option selected value="">Carregando...</option>');
                            // $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                 
                            if(response){
                                var response = JSON.parse(response);  
                                $("#taxa-honorario-correspondente").val(response.nu_taxa_the);       
                            }
                        },
                        error: function(response)
                        {
                                //console.log(response);
                        }
                });         
            }


        });

        var buscaAdvogado = function(){

            var cliente = $("input[name='cd_cliente_cli']").val();

            $.ajax({
                    url: '../../advogados-por-cliente/'+cliente,
                    type: 'GET',
                    dataType: "JSON",
                    beforeSend: function(){
                        // $('#cidade').empty();
                        // $('#cidade').append('<option selected value="">Carregando...</option>');
                        // $('#cidade').prop( "disabled", true );

                    },
                    success: function(response)
                    {                   
                        
                        $('#cd_contato_cot').empty();
                        $('#cd_contato_cot').append('<option selected value="">Selecione um Advogado Solicitante</option>');
                        $.each(response,function(index,element){

                            if($("#contatoAux").val() != element.cd_contato_cot){
                                $('#cd_contato_cot').append('<option value="'+element.cd_contato_cot+'">'+element.nm_contato_cot+'</option>');                            
                            }else{
                                $('#cd_contato_cot').append('<option selected value="'+element.cd_contato_cot+'">'+element.nm_contato_cot+'</option>');      
                            }
                                
                            });       
                           
                        },
                        error: function(response)
                        {
                            //console.log(response);
                    }
            });

        }

        var buscaCorrespondente = function(cidade,estado){

            if(estado != '' && cidade != ''){
                $.ajax(
                    {
                        url: pathCorrespondente+'?estado='+estado+'&cidade='+cidade,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('#correspondente_auto_complete').empty();
                            $('#correspondente_auto_complete').append('<option selected value="">Carregando...</option>');
                            $('#correspondente_auto_complete').prop( "disabled", true );

                        },
                        success: function(response)
                        {                                            
                            $('#correspondente_auto_complete').empty();
                            $('#correspondente_auto_complete').append('<option selected value="">Selecione</option>');
                            $.each(response,function(index,element){

                                
                                if($("#cd_correspondente_cor_aux").val() != element.id){
                                    $('#correspondente_auto_complete').append('<option value="'+element.id+'" data-flag="'+element.flag+'">'+element.value+'</option>');         
                                }else{
                                    $('#correspondente_auto_complete').append('<option selected value="'+element.id+'" data-flag="'+element.flag+'" >'+element.value+'</option>');      
                                }
                                
                            });       
                            $('#correspondente_auto_complete').trigger('change');     
                            $('#correspondente_auto_complete').prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                });
            } else {
                $('#correspondente_auto_complete').empty();
                $('#correspondente_auto_complete').append('<option selected value="">Aguardando Cidade...</option>');
                 $('#correspondente_auto_complete').trigger('change');
            }
        }

        $('#correspondente_auto_complete').change(function(){
 
            if($(this).find(':selected').data('flag') == 'N'){
               $("input[name='fl_correspondente_escritorio_ccr']").val('N');
               $('#tipoServicoCorrespondenteLabel').text('Tipo de Serviço do Correspondente');
               $('#tipoServicoCorrespondenteLabel').html($('#tipoServicoCorrespondenteLabel').text()+"<span class='text-danger'>*</span>");
            }else{
               $("input[name='fl_correspondente_escritorio_ccr']").val('S');
               $('#tipoServicoCorrespondenteLabel').html('Tipo de Serviço do Correspondente');

            }

            var correspondente = $("select[name='cd_correspondente_cor']").val();
            var cidade = $("select[name='cd_cidade_cde']").val();

            if(correspondente != '' && cidade != ''){
                buscaTiposServicoCorrespondente(correspondente,cidade);
            }    
        })


        var buscaCidade = function(){

            estado = $("#estado").val();

            if(estado != ''){

                $.ajax(
                    {
                        url: '../../cidades-por-estado/'+estado,
                        type: 'GET',
                        dataType: "JSON",
                        beforeSend: function(){
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Carregando...</option>');
                            $('#cidade').prop( "disabled", true );

                        },
                        success: function(response)
                        {                    
                            $('#cidade').empty();
                            $('#cidade').append('<option selected value="">Selecione</option>');
                            $.each(response,function(index,element){

                                if($("#cd_cidade_cde_aux").val() != element.cd_cidade_cde){
                                    $('#cidade').append('<option value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');                            
                                }else{
                                    $('#cidade').append('<option selected value="'+element.cd_cidade_cde+'">'+element.nm_cidade_cde+'</option>');      
                                }
                                
                            });       
                            $('#cidade').trigger('change');     
                            $('#cidade').prop( "disabled", false );        
                        },
                        error: function(response)
                        {
                            //console.log(response);
                        }
                });
            }
        }

        if($("input[name='cd_cliente_cli']").val() != '' && $("input[name='cd_cliente_cli']").val() != null){

            buscaAdvogado();
        }

        buscaCidade();

        $("#estado").change(function(){
           
            buscaCidade(); 

        });

        $(function() {
                // Validation
            var validobj = $("#frm-add-processo").validate({

                    ignore: 'input[type=hidden], .select2-input, .select2-focusser',
                    rules : {
                        nm_cliente_cli : {
                            required: true,
                        },
                        nu_processo_pro : {
                            required: true
                        },
                        cd_tipo_processo_tpo : {
                            required: true
                        },
                        cd_cidade_cde: {
                            required: true
                        },
                        cd_tipo_servico_tse: {
                            required: true
                        },
                        cd_area_direito_ado:{
                            required: true
                        },
                        dt_prazo_fatal_pro:{
                            required: true
                        },
                        nm_reu_pro:{
                            maxlength: 500,
                            required: true
                        },
                        nm_autor_pro:{
                            maxlength: 500,
                            required: true
                        },
                        cd_tipo_servico_correspondente_tse : {
                            required: function(element){    

                                if($("select[name='cd_correspondente_cor']").val() == '' || $("input[name='fl_correspondente_escritorio_ccr']").val() == 'S'){
                                    return false;
                                }else{
                                    return true;
                                }

                            }
                        }
                        
                    },

                    // Messages for form validation
                    messages : {
                        nm_cliente_cli : {
                            required : 'Campo Cliente é Obrigatório'
                        },
                        nu_processo_pro : {
                            required : 'Campo Nº Processo é Obrigatório'
                        },
                        cd_tipo_processo_tpo : {
                            required : 'Campo Tipo de Processo é Obrigatório'
                        },
                        cd_cidade_cde: {
                            required : 'Campo Cidade é Obrigatório'
                        },
                        cd_tipo_servico_tse: {
                            required : 'Campo Tipo de Serviço é Obrigatório'
                        },
                        cd_area_direito_ado: {
                            required : 'Campo obrigatório'
                        },
                        dt_prazo_fatal_pro: {
                            required : 'Campo Prazo Fatal é Obrigatório'
                        },
                        nm_reu_pro:{
                            maxlength: 'O Campo Réu excedeu o número máximo de 500 caracteres',
                            required : 'Campo Réu é obrigatório'
                        },
                        nm_autor_pro:{
                            maxlength: 'O Campo Autor excedeu o número máximo de 500 caracteres',
                            required : 'Campo Autor é obrigatório'
                        },
                        cd_tipo_servico_correspondente_tse: {
                           required : 'Campo Tipo de Serviço é Obrigatório'
                        }                       
                        
                    },

                    errorPlacement: function (error, element) {
                        var elem = $(element);
                        if(element.attr("name") == "cd_cidade_cde" || element.attr("name") == "cd_tipo_servico_tse" || element.attr("name") == "cd_tipo_servico_correspondente_tse") {
                            
                            error.appendTo( element.next("span") );
                            
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
                        } else {
                            elem.addClass(errorClass);
                        }
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        var elem = $(element);
                        if (elem.hasClass("select2-offscreen")) {
                            $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
                        } else {
                            elem.removeClass(errorClass);
                        }
                    }    
                });

            $(document).on("change", ".select2", function () {
                if (!$.isEmptyObject(validobj.submitted)) {
                    validobj.form();
                }
            });

        });

        $('#cidade').select2({}).focus(function () {
            $(this).select2('focus');
        });

    });
   

    
</script>

@endsection

