@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li><a href="#">Usuários</a></li>
        <li>Novo</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="hidden-xs col-sm-7 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-text-o la-lg"></i> Processos <span>> Novo</span>
            </h1>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-6 col-lg-6 box-button-xs">
            <div class="boxBtnTopo sub-box-button-xs">
                <a data-toggle="modal" href="{{ url('processos') }}" class="btn btn-default pull-right header-btn btnMargin"><i class="fa fa-list fa-lg"></i> Listar Processos</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
              <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-sortable" id="wid-id-4" data-widget-editbutton="false" data-widget-custombutton="false" role="widget">
                <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
                    
                    data-widget-colorbutton="false" 
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true" 
                    data-widget-sortable="false"
                    
                -->
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
                        
                        {!! Form::open(['id' => 'frm-add-processo', 'url' => 'processos', 'class' => 'smart-form']) !!}
                         <header>
                            <i class="fa fa-file-text-o"></i> Dados do Processo <span class="text-danger">(*) Campos Obrigatórios</span>
                        </header>
                        <div class="row">
                            <div  class="col col-xs-12 col-sm-6">
                                <fieldset>
                                   
                                    <div class="row">
                        
                                        <section class="col col-xs-12 col-sm-12">
                                            <input type="hidden" name="cd_cliente_cli" id="cd_cliente_cli" value="{{old('cd_cliente_cli')}}" >
                                            <label class="label">Cliente<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input required name="nm_cliente_cli" value="{{old('nm_cliente_cli')}}" class="form-control ui-autocomplete-input" placeholder="Digite 3 caracteres para busca" type="text" id="client" autocomplete="off">
                                            </label>
                                        </section>
                                    </div> 
                                    <div class="row">
                                        <section class="col col-xs-12 col-lg-4">
                                            <label class="label">Código Cliente <a href="#" rel="popover-hover" data-placement="top" data-original-title="Número ou código de acompanhamento externo."><i class="fa fa-question-circle text-primary"></i></a></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_acompanhamento_pro')}}" type="text" name="nu_acompanhamento_pro" maxlength="50">
                                            </label>
                                        </section> 
                                        <section class="col col-xs-12 col-lg-8">                                       
                                            <label class="label" >Advogado Solicitante <a href="#" data-toggle="modal" data-target="#novoAdvogado" style="padding: 1px 8px;"><i class="fa fa-plus-circle"></i> Novo </a></label> 
                                            <label class="select">
                                                <input type="hidden" id="contatoAux"  value="{{old('cd_contato_cot')}}">
                                                <select  id="cd_contato_cot" name="cd_contato_cot" >
                                                    <option value="">Selecione um Advogado Solicitante</option>            
                                                </select><i></i>  
                                            </label>         
                                        </section>
                                    </div>
                                    <div class="row">
                                         <section class="col col-xs-12 col-lg-6">
                                            <label class="label">Nº Processo<span class="text-danger">*</span></label>
                                            <label class="input">
                                                <input class="form-control" value="{{old('nu_processo_pro')}}" type="text" name="nu_processo_pro" required>
                                            </label>
                                        </section> 
                                         <section class="col col-xs-12 col-lg-6" >                                       
                                            <label class="label" >Tipo de Processo<span class="text-danger">*</span></label>          
                                            <label class="select">
                                                <select  name="cd_tipo_processo_tpo" required>
                                                    <option selected value="">Selecione o Tipo de Processo</option>     
                                                     @foreach($tiposProcesso as $tipo) 
                                                        <option {!! (old('cd_tipo_processo_tpo') == $tipo->cd_tipo_processo_tpo ? 'selected' : '' ) !!} value="{{$tipo->cd_tipo_processo_tpo}}">{{ $tipo->nm_tipo_processo_tpo}}</option>
                                                     @endforeach       
                                                </select><i></i>   
                                            </label>
                                        </section>
                                    </div>  

                                    <div class="row">
                                        <section class="col col-xs-12 col-sm-12">       
                                            <label class="label" >Vara</label>          
                                            <select  name="cd_vara_var" class="select2">
                                                <option selected value="">Selecione uma vara</option>
                                                @foreach($varas as $vara) 
                                                    <option {!! (old('cd_vara_var') == $vara->cd_vara_var ? 'selected' : '' ) !!} value="{{$vara->cd_vara_var}}">{{ $vara->nm_vara_var}}</option>
                                                @endforeach
    
                                            </select> 
                                        </section>
                                    </div> 
                                    
                                    <div class="row">
                    
                                        <section class="col col-xs-12 col-lg-6">
                                           
                                            <label class="label" >Estado</label>          
                                            <select  id="estado" name="cd_estado_est" class="select2">
                                                <option selected value="">Selecione um estado</option>
                                                @foreach($estados as $estado) 
                                                    <option {!! (old('cd_estado_est') == $estado->cd_estado_est ? 'selected' : '' ) !!} value="{{$estado->cd_estado_est}}">{{ $estado->nm_estado_est}}</option>
                                                @endforeach

                                            </select> 
                                        </section>
                                        <section class="col col-xs-12 col-lg-6">
                                           <input type="hidden" id="cd_cidade_cde_aux" name="cd_cidade_cde_aux" value="{{old('cd_cidade_cde')}}">
                                           <label class="label" >Cidade<span class="text-danger">*</span></label>          
                                            <select  id="cidade"  name="cd_cidade_cde" class="select2" required>
                                               <option selected value="">Selecione uma Cidade</option>
                                            </select> 
                                        </section>  
                                    </div> 

                                   

                                </fieldset>
                            </div>
                        <div  class="col col-xs-12 col-sm-6">
                           
                            <fieldset>
                                <div class="row">
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Data da Solicitação</label>
                                        <label class="input">
                                           <input class="dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dt_solicitacao_pro" value="{{old('dt_solicitacao_pro')}}">
                                        </label>
                                    </section> 
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Data Prazo Fatal<span class="text-danger">*</span></label>
                                        <label class="input">
                                           <input class="dt_prazo_fatal_pro" placeholder="___ /___ /___" type="text" name="dt_prazo_fatal_pro" value="{{old('dt_prazo_fatal_pro')}}">
                                        </label>
                                    </section> 
                                    <section class="col col-xs-12 col-lg-4">
                                        <label class="label">Hora da Audiência</label>
                                        <label class="input">
                                           <input class="hr_audiencia_pro" placeholder="___ : ___" type="text" name="hr_audiencia_pro" value="{{old('hr_audiencia_pro')}}" >
                                        </label>
                                    </section> 
                                </div>    

                                <div class="row">
                                    <section class="col col-xs-12">
                                        <label class="label">Autor<span class="text-danger">*</span></label>
                                        <label class="input">
                                            <input class="form-control" maxlength="500" placeholder="" type="text" name="nm_autor_pro" required value="{{old('nm_autor_pro')}}">
                                        </label>
                                    </section> 
                                </div>    

                                <div class="row"> 
                                    <section class="col col-xs-12 col-sm-12">
                                       <label class="label">Réu<span class="text-danger">*</span></label>
                                       <label class="input">
                                          <input class="form-control" placeholder="" maxlength="500" type="text" name="nm_reu_pro" required value="{{old('nm_reu_pro')}}" >
                                       </label>
                                   </section> 
                               </div>
                                                                       
                                <div class="row">    
                                    <input type="hidden" name="cd_correspondente_cor_aux" id="cd_correspondente_cor_aux" value="{{ old('cd_correspondente_cor') }}"> 
                                    <input type="hidden" name="fl_correspondente_escritorio_ccr" value="{{ old('fl_correspondente_escritorio_ccr') }}">           
                                    <section class="col col-xs-12">
                                        <label class="label">Correspondente <span class="text-info">Filtrado de acordo com estado/cidade escolhida</span></label>
                                        <select  id="correspondente_auto_complete"  name="cd_correspondente_cor" class="select2" disabled data-flag=''>
                                           <option selected value="">Aguardando Cidade... </option>
                                        </select>                                                         
                                    </section>
                                </div> 
                                <div class="row">    
                                    <input type="hidden" name="cd_responsavel_pro" value="{{ old('cd_responsavel_pro') }}">           
                                    <section class="col col-xs-12">
                                        <label class="label">Responsável</label>
                                        <label class="input">
                                            <input class="form-control" name="name" placeholder="Digite 3 caracteres para busca" type="text" id="responsavel_auto_complete" value="{{ old('nm_correspondente_cor') }}">
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
                                            <input type="text" class="form-control" placeholder="Link da Audiência" name="ds_link_audiencia_pro" id="ds_link_audiencia_pro">
                                        </label>
                                    </section> 
                                </div>
                                <div class="row" style="margin: 0px 0px 0px -10px !important;"> 
                                    <section class="col col-sm-6">
                                        <label class="label">Advogado</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_advogado_pro">{{ old('nm_advogado_pro') }}</textarea>
                                        </label>
                                    </section> 

                                    <section class="col col-sm-6">
                                        <label class="label">Preposto</label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="nm_preposto_pro">{{ old('nm_preposto_pro') }}</textarea>
                                        </label>
                                    </section>                               
                                    
                                    <section class="col col-sm-12">
                                        <label class="label">Observações do Processo <span class="text-info">Aparecem na Pauta Diária</span></label>
                                        <label class="input">
                                           <textarea class="form-control texto-processo" rows="8" name="dc_observacao_processo_pro">{{ old('dc_observacao_processo_pro') }}</textarea>
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
                                        <textarea class="form-control" id="observacao" rows="8" name="dc_observacao_pro" value="{{ old('dc_observacao_pro') }}">{{ old('dc_observacao_pro') }}</textarea>
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
                                                    <header class="hidden-sm" style="border-bottom: none;">
                                                        Tipo de Serviço do Cliente
                                                    </header>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th style="width: 50%">Tipo de Serviço do Cliente<span class="text-danger">*</span></th>
                                                            <th style="">Valor Cliente</th>                                        
                                                            <th style="">Nota Fiscal Cliente</th>
                                                        </thead>
                                                        <tbody>  
                                                            <tr>  
                                                                <td>                     
                                                                     <input type="hidden" id="cd_tipo_servico_tse_aux" name="cd_tipo_servico_tse_aux" value="{{old('cd_tipo_servico_tse')}}">                  
                                                                    <select id="tipoServico" name="cd_tipo_servico_tse" class="select2" disabled>
                                                                        <option selected value="">Selecione um cliente e cidade
                                                                        </option>      
                                                                                   
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                        <div class="input-group">
                                                                            <span class="input-group-addon">$</span>
                                                                            <input style="width: 100px; padding-left: 12px" name="taxa_honorario_cliente"  id="taxa-honorario-cliente" type="text" class="form-control taxa-honorario" value="{{old('taxa_honorario_cliente')}}" >
                                                                        </div>
                                                                        </div>
                                                                </td>                                                            
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">%</span>
                                                                            <input disabled name="nota_fiscal_cliente" style="width: 100px;padding-left: 12px" id="nota_fiscal_cliente" type="text" class="form-control taxa-honorario"  value="{{old('nota_fiscal_cliente')}}" title="Aguardando seleção do Cliente" >
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                           
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <header class="hidden-sm" style="border-bottom: none;">
                                                        Tipo de Serviço do Correspondente
                                                    </header>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th style="width: 50%" id="tipoServicoCorrespondenteLabel" >Tipo de Serviço do Correspondente</th>
                                                            <th style="">Valor Correspondente</th>            
                                                        </thead>
                                                        <tbody>  
                                                            <tr>  
                                                                <td>                     
                                                                     <input type="hidden" id="cd_tipo_servico_correspondente_tse_aux" name="cd_tipo_servico_correspondente_tse_aux" value="{{old('cd_tipo_servico_correspondente_tse')}}">                  
                                                                    <select id="tipoServicoCorrespondente" name="cd_tipo_servico_correspondente_tse" class="select2" disabled>
                                                                        <option selected value="">Selecione um correspondente e cidade
                                                                        </option>                                
                                                                    </select>
                                                                </td>                                      
                                                                <td>
                                                                    <div class="col-md-4 col-md-offset-2">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">$</span>
                                                                            <input name="taxa_honorario_correspondente" style="width: 100px;padding-left: 12px" id="taxa-honorario-correspondente" type="text" class="form-control taxa-honorario"  value="{{old('taxa_honorario_correspondente')}}" >
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
                                <i class="fa-fw fa fa-save"></i>Cadastrar
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
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function(){

        });
    </script>
@endsection