@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Financeiro</li>
        <li>Entradas</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Financeiro <span> > Entradas</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('financeiro/entrada/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data prazo fatal inicial</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data prazo fatal final</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}" >                            
                        </section>

                        <section class="col col-md-2">
                            <label class="label label-black">Data da baixa inicial</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicioBaixa" value="{{ old('dtInicioBaixa') ? old('dtInicioBaixa') : \Session::get('dtInicioBaixa')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data da baixa final</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFimBaixa" value="{{ old('dtFimBaixa') ? old('dtFimBaixa') : \Session::get('dtFimBaixa')}}" >                            
                        </section>


                         <section class="col col-md-4">                           
                            <label class="label label-black">Cliente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_cliente_cli" value="{{(old('cd_cliente_cli') ? old('cd_cliente_cli') : (\Session::get('cliente') ? \Session::get('cliente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_cliente_cli" placeholder="Digite 3 caracteres para busca" type="text" id="cliente_auto_complete" value="{{(old('nm_cliente_cli') ? old('nm_cliente_cli') : (\Session::get('nmCliente') ? \Session::get('nmCliente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-cliente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                    </div> 
                    <div class="row">

                        <section style="width:18%" class="col col-md-3">
                            <br />                                        
                            <label class="label label-black">Pago</label>  
                            <input type="checkbox" name="pago" id="pago"  {{ (!empty(\Session::get('pago')) ? 'checked' : '') }} > 
                        </section> 

                        <section style="width:18%" class="col col-md-3">
                            <br />                                        
                            <label class="label label-black">Parcialmente Pago</label>  
                            <input type="checkbox" name="parcialmente" id="parcialmente"  {{ (!empty(\Session::get('parcialmente')) ? 'checked' : '') }} > 
                        </section> 
                        
                         <section style="width:18%" class="col col-md-3">
                            <br />                                        
                            <label class="label label-black">Nenhum Pagamento</label>  
                            <input type="checkbox" name="nenhum" id="nenhum"  {{ (!empty(\Session::get('nenhum')) ? 'checked' : '') }} > 
                        </section> 
                        <section class="col col-md-1">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa fa-search"></i> Buscar </button>
                        </section>    

                    </div>
                    <div style="display: block;margin-top: 10px">
                       <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #8ec9bb;float: left;margin-right: 2px"></div>Pago
                       </span>
                       <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #f2cf59; float: left; margin-right: 2px"></div>Parcialmente Pago 
                       </span>
                       <span style="display: inline-block;">
                            <div style="width: 20px;height: 20px;border: 1px solid #ccc;background-color: #fb8e7e; float: left; margin-right: 2px"></div>Nenhum pagamento
                       </span>                       
                    </div>  
                </form>
            </div>
            <div style="clear: both;"></div>
             <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">    
                <header>
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Entradas</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic_financeiro" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th>Nº Processo</th>                    
                                    <th>Prazo Fatal</th>
                                    <th>Tipo de Serviço</th>    
                                    <th>Cliente</th>
                                    <th style="min-width:8%">Honorário</th>
                                    <th style="min-width:8%">Despesa</th>
                                    <th style="min-width:8%">Nota F. %</th>
                                    <th style="min-width:8%">Total</th>  
                                    <th style="text-align: center;" class="no-sort">
                                            <a title="Pagamentos em Lote" class="btn btn-warning btn-xs check-pagamento-cliente-lote"  href="javascript:void(0)" ><i class="fa fa-arrow-down" ></i></a>
                                            <input type="checkbox" class="seleciona-todos" >
                                    </th> 

                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($entradas as $entrada)

                                @php
                                    $totalDespesas = $entrada->processo->tiposDespesa->sum('pivot.vl_processo_despesa_pde');
                                    $totalBaixaHonorario = $entrada->baixaHonorario->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::ENTRADA)->sum('vl_baixa_honorario_bho');

                                @endphp

                                <tr {{ ($totalBaixaHonorario <= 0 && $totalDespesas+$entrada->vl_taxa_honorario_cliente_pth > 0
                                                    ? 'style=background-color:#fb8e7e' : 
                                                            ($totalBaixaHonorario < ($totalDespesas+$entrada->vl_taxa_honorario_cliente_pth) && $totalBaixaHonorario > 0
                                                                ? 'style=background-color:#f2cf59' : 
                                                                    'style=background-color:#8ec9bb')) }} >
                                    <td>{{ $entrada->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($entrada->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($entrada->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $entrada->tipoServico->nm_tipo_servico_tse }}</td>
                                    <td>{{ $entrada->processo->cliente->nm_razao_social_cli }}</td>
                                    <td>{{ 'R$ '.number_format($entrada->vl_taxa_honorario_cliente_pth,2,',',' ') }}</td>

                        
                                    <td>{{ 'R$ '.number_format($totalDespesas,2,',',' ') }}</td>
                                    <td>{{ (!empty($entrada->vl_taxa_cliente_pth) ? $entrada->vl_taxa_cliente_pth.'%' : ' ') }}</td>
                                    <td>{{ 'R$ '.number_format(($entrada->vl_taxa_honorario_cliente_pth-
                                    ((($entrada->vl_taxa_honorario_cliente_pth)*$entrada->vl_taxa_cliente_pth)/100))+$totalDespesas,2,',',' ') }}</td>
                                    <td style="text-align: center;">
                                        <a title="Pagamentos"  data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}'  class="btn btn-warning btn-xs check-pagamento-cliente"  href="javascript:void(0)" ><i class="fa fa-money"></i></a>

                                        <input type="checkbox" class="checkbox-check-pagamento-cliente" style="width: 100%" data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}' {{ ($entrada->fl_pago_cliente_pth == 'N') ? '' : 'checked' }}  >                                

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

<div class="modal fade modal_top_alto" id="addBaixa" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close fechar" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-money"></i>
                </h4>
            </div>
            <div class="modal-body no-padding">
                {!! Form::open(['id' => 'frm-add-baixa', 'url' => '', 'class' => 'smart-form']) !!}
                    <input type="hidden" name="cdBaixaFinanceiro" id="cdBaixaFinanceiro" >
                     <fieldset>
                        <section>
                            <div class="col col-sm-12">
                                    <header>
                                        <i class="fa fa-arrow-circle-o-down"></i> Registro de Baixa
                                    </header>
                                    <fieldset style="padding: 10px 14px 5px;">
                                        <div class="row">    
                                            <section class="col col-2">
                                                <label class="label">Data</label>
                                                <label class="input">
                                                     <input type="text" id='dtBaixaCliente' class='form-control dt_solicitacao_pro' name="dtBaixa" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                                                </label>
                                            </section>                    
                                         
                                            <section class="col col-2">
                                                <label class="label">Valor<span class="text-danger">*</span></label>
                                                <label class="input">
                                                    <input type="text" class="form-control taxa-honorario" name="valor" id="valor" required>
                                                </label>
                                            </section>    

                                            <section class="col col-3">
                                                <label class="label" >Tipo<span class="text-danger">*</span></label>          
                                                <label class="select">
                                                <select  id="tipo" name="tipo" class='form-control' required>
                                                    <option selected value="">Selecione um tipo</option>
                                                    @foreach(\App\TipoBaixaHonorario::get() as $tipo)
                                                    <option value="{{$tipo->cd_tipo_baixa_honorario_bho}}">{{$tipo->nm_tipo_baixa_honorario_bho}}</option>                                                    
                                                    @endforeach
                                                </select> <i></i>
                                                </label>
                                            </section>    

                                            <section class="col col-3">
                                                <label class="label">Nota</label>
                                                <label class="input">
                                                   <input type="text" id='notaFiscal' class='form-control' name="notaFiscal" placeholder="" >                                                        
                                                </label>
                                            </section>    
                                             <section class="col col-1">
                                                <label class="label">&nbsp</label>
                                                <button type="submit" id="btnSalvarRegistroBaixa" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Registrar</button>
                                            </section>
                                           
                                        </div>

                                        <div class="row" style="margin: 0; padding: 5px 0px">
                                           
                                            <table id="tabelaRegistro" class="table table-bordered table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th class="center">Data</th>
                                                        <th class="center">Valor</th>
                                                        <th class="center">Tipo</th>
                                                        <th class="center">Nota</th>                       
                                                        <th class="center">Opções</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   
                                                </tbody>
                                            </table>                                      
                                           
                                        </div>
                                    </fieldset>
                                    <header>
                                        <i class="fa fa-file-image-o"></i> Anexos
                                    </header>

                                    <fieldset>
                                        <div class="alert fade in none">
                                            <button class="close" data-dismiss="alert">×</button>
                                            <i class="fa-fw fa"></i>
                                            <strong class="msg_titulo"></strong> <span class="msg_mensagem"></span>
                                        </div>

                                        <!-- Drop área -->    
                                        <div class="row">                            
                                            <section class="col col-sm-12">
                                                <div id="filepicker">
                                                    <!-- Button Bar -->
                                                    <div class="button-bar">

                                                        <span class="start-all"></span>
                                                       
                                                        <div class="btn btn-success btn-upload-plugin fileinput">
                                                            <i class="fa fa-files-o"></i> Buscar Arquivos
                                                            <input type="file" name="files[]" id="input-file" multiple>
                                                        </div>  
                                                       

                                                    </div>

                                                    <!-- Listar Arquivos -->
                                                    <div class="table-responsive div-table">
                                                        <table class="table table-upload">
                                                            <thead>
                                                                <tr>
                                                                    <th class="column-name">Nome</th>
                                                                    <th class="column-size center">Tamanho</th>
                                                                    <th class="column-date">Data Envio</th>
                                                                    <th class="center">Opções</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="files">
                                                               
                                                            </tbody>                        
                                                        </table>
                                                    </div>

                                                    <!-- Drop Zone -->
                                                    <div class="drop-window">
                                                        <div class="drop-window-content">
                                                            <h3><i class="fa fa-upload"></i> Drop files to upload</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </fieldset>

                                   
                                </div>
                        </section>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-primary fechar" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                       
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal_top_alto" id="addBaixaLote" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close fechar" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    <i class="icon-append fa fa-money"></i> Registro de Baixa em Lote
                </h4>
            </div>
            <div class="modal-body no-padding">

                
                {!! Form::open(['id' => 'frm-add-baixa-lote', 'url' => '', 'class' => 'smart-form']) !!}
                    
                     <fieldset style="padding-top: 0px">
                        <section>
                            <div class="col col-sm-12">
                                    <header>
                                        <i class="fa fa-arrow-circle-o-down"></i> Registro de Baixa<br />
                                        
                                        <h5 id="valor_total_operacao"></h5>
                                        <h5 id="valor_total_operacao_despesas"></h5>
                                    </header>
                                    <fieldset style="padding: 10px 14px 5px;">
                                        <div class="row">    
                                            <section class="col col-3">
                                                <label class="label">Data</label>
                                                <label class="input">
                                                     <input type="text" id='dtBaixaCliente' class='form-control dt_solicitacao_pro' name="dtBaixa" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                                                </label>
                                            </section>                     
                                                         
                                            <section class="col col-3">
                                                <label class="label" >Tipo<span class="text-danger">*</span></label>          
                                                <label class="select">
                                                <select  id="tipo" name="tipo" class='form-control' required>
                                                    <option selected value="">Selecione um tipo</option>
                                                    @foreach(\App\TipoBaixaHonorario::get() as $tipo)
                                                    <option value="{{$tipo->cd_tipo_baixa_honorario_bho}}">{{$tipo->nm_tipo_baixa_honorario_bho}}</option>                                                    
                                                    @endforeach
                                                </select> <i></i>
                                                </label>
                                            </section>   
                                            <section class="col col-1">
                                                <label class="label">&nbsp</label>
                                                <button type="submit" id="btnSalvarRegistroBaixa" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Registrar</button>
                                            </section>
                                        </div>
                                        <div class="row" style="margin: 0; padding: 5px 0px">
                                           
                                            <table id="tabelaRegistroLote" class="table table-bordered table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th class="center">Data</th>
                                                        <th class="center">Valor Total</th>
                                                        <th class="center">Tipo</th>                                                                                
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   
                                                </tbody>
                                            </table>                                      
                                           
                                        </div>


                                        <div class="row" style="margin: 0; padding: 5px 13px;">
                                            
                                            <h2 class="retornoLote"></h2>                               
                                        </div>
                                    </fieldset>
                                    <header>
                                        <i class="fa fa-file-image-o"></i> Anexos

                                    </header>
                                    <fieldset>

                                        <div class="alert fade in none">
                                            <button class="close" data-dismiss="alert">×</button>
                                            <i class="fa-fw fa"></i>
                                            <strong class="msg_titulo"></strong> <span class="msg_mensagem"></span>
                                        </div>

                                        <!-- Drop área -->    
                                        <div class="row">                            
                                            <section class="col col-sm-12">
                                                <div id="filepickerLote">
                                                    <!-- Button Bar -->
                                                    <div class="button-bar">

                                                        <span class="start-all"></span>
                                                       
                                                        <div class="btn btn-success btn-upload-plugin fileinput">
                                                            <i class="fa fa-files-o"></i> Buscar Arquivos
                                                            <input type="file" name="files[]" id="input-file" multiple>
                                                        </div>  
                                                       

                                                    </div>

                                                    <!-- Listar Arquivos -->
                                                    <div class="table-responsive div-table">
                                                        <table class="table table-upload">
                                                            <thead>
                                                                <tr>
                                                                    <th class="column-name">Nome</th>
                                                                    <th class="column-size center">Tamanho</th>
                                                                    <th class="column-date">Data Envio</th>
                                                                    <th class="center">Opções</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="files">
                                                               
                                                            </tbody>                        
                                                        </table>
                                                    </div>

                                                    <!-- Drop Zone -->
                                                    <div class="drop-window">
                                                        <div class="drop-window-content">
                                                            <h3><i class="fa fa-upload"></i> Drop files to upload</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>

                                    </fieldset>
                                </div>
                        </section>
                     
                        <div class="msg_retorno"></div>
                    </fieldset>
                    <footer>
                        <button type="button" class="btn btn-primary fechar" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                       
                    </footer>
                {!! Form::close() !!}                    
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {


        localStorage.setItem("idsBaixaFinanceiro",JSON.stringify([]));

        $('#filepicker').filePicker({
            url: '../entrada/anexo',                
            data: function(){
                var _token = "{{ csrf_token() }}";
                var id_processo_baixa = localStorage.getItem("idsBaixaFinanceiro");
                return {
                    _token: _token,
                    id_processo_baixa: id_processo_baixa
                }
            },
            plugins: ['ui']
        })
        .on('done.filepicker', function (e, data) {
            if(data.files[0].size){            
                $.ajax({
                    url: "../../anexo-processo-baixa-add",
                    type: 'POST',
                    data: {
                        "_token": $('meta[name="token"]').attr('content'),
                        "id_processo_baixa": localStorage.getItem("idsBaixaFinanceiro"),
                        "nome_arquivo": data.files[0].name
                    },
                    success: function(response){   
                        $(".fa").addClass("fa-check");
                        $(".msg_titulo").html("Sucesso");
                        $(".msg_mensagem").html("Arquivo anexado com sucesso");
                        $(".alert").addClass("alert-success");
                        $(".alert").removeClass("none");                            
                    },
                    error: function(response){
                        $(".fa").addClass("fa-times");
                        $(".msg_titulo").html("Erro");
                        $(".msg_mensagem").html("Erro ao enviar o arquivo");
                        $(".alert").addClass("alert-danger");
                        $(".alert").removeClass("none");
                    }
                });
            }
        })
        .on('delete.filepicker', function (e, data) {

            //Antes de excluir o arquivo, ele remove o registro do banco. Caso ocorra erro no banco, ele não exclui o arquivo e retorna false. Caso exclua do banco, mas não consiga remover o arquivo, ele recupera o arquivo no método deletedone

            $.ajax({
                url: '../../anexo-processo-baixa-delete',
                type: 'POST',
                dataType: "JSON",
                data: {
                    "_method": 'DELETE',
                    "id": localStorage.getItem("idsBaixaFinanceiro"),                    
                    "nome_arquivo": data.filename,
                    "_token": $('meta[name="token"]').attr('content'),
                },
                success: function(response)
                {
                    $(".fa").addClass("fa-check");
                    $(".msg_titulo").html("Sucesso");
                    $(".msg_mensagem").html("Arquivo excluído com sucesso");
                    $(".alert").addClass("alert-success");
                    $(".alert").removeClass("none");
                },
                error: function(response)
                {
                    $(".fa").addClass("fa-times");
                    $(".msg_titulo").html("Erro");
                    $(".msg_mensagem").html("Erro ao excluir o arquivo");
                    $(".alert").addClass("alert-danger");
                    $(".alert").removeClass("none");

                    return false;
                }
            });

        });

        $('#filepickerLote').filePicker({
            url: '../entrada/anexo',                
            data: function(){
                var _token = "{{ csrf_token() }}";
                var id_processo_baixa = localStorage.getItem("idsBaixaFinanceiro");
                return {
                    _token: _token,
                    id_processo_baixa: id_processo_baixa
                }
            },
            plugins: ['ui']
        })
        .on('done.filepicker', function (e, data) {
            if(data.files[0].size){            
                $.ajax({
                    url: "../../anexo-processo-baixa-add",
                    type: 'POST',
                    data: {
                        "_token": $('meta[name="token"]').attr('content'),
                        "id_processo_baixa": localStorage.getItem("idsBaixaFinanceiro"),
                        "nome_arquivo": data.files[0].name
                    },
                    success: function(response){   
                        $(".fa").addClass("fa-check");
                        $(".msg_titulo").html("Sucesso");
                        $(".msg_mensagem").html("Arquivo anexado com sucesso");
                        $(".alert").addClass("alert-success");
                        $(".alert").removeClass("none");                            
                    },
                    error: function(response){
                        $(".fa").addClass("fa-times");
                        $(".msg_titulo").html("Erro");
                        $(".msg_mensagem").html("Erro ao enviar o arquivo");
                        $(".alert").addClass("alert-danger");
                        $(".alert").removeClass("none");
                    }
                });
            }
        })
        .on('delete.filepicker', function (e, data) {

            //Antes de excluir o arquivo, ele remove o registro do banco. Caso ocorra erro no banco, ele não exclui o arquivo e retorna false. Caso exclua do banco, mas não consiga remover o arquivo, ele recupera o arquivo no método deletedone

            $.ajax({
                url: '../../anexo-processo-baixa-delete',
                type: 'POST',
                dataType: "JSON",
                data: {
                    "_method": 'DELETE',
                    "id": localStorage.getItem("idsBaixaFinanceiro"),                    
                    "nome_arquivo": data.filename,
                    "_token": $('meta[name="token"]').attr('content'),
                },
                success: function(response)
                {
                    $(".fa").addClass("fa-check");
                    $(".msg_titulo").html("Sucesso");
                    $(".msg_mensagem").html("Arquivo excluído com sucesso");
                    $(".alert").addClass("alert-success");
                    $(".alert").removeClass("none");
                    $('.table-upload > tbody').html('');
                },
                error: function(response)
                {
                    $(".fa").addClass("fa-times");
                    $(".msg_titulo").html("Erro");
                    $(".msg_mensagem").html("Erro ao excluir o arquivo");
                    $(".alert").addClass("alert-danger");
                    $(".alert").removeClass("none");

                    return false;
                }
            });

        });

        var addBaixado = function(id,valorTotalPago){

            $(".check-pagamento-cliente").each(function(index,element){
                if($(this).data('id') == id){

                    var total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   

                    //alert(total);

                    if(valorTotalPago >= total){
                        $(this).closest('tr').css('background-color','#8ec9bb');
                    }else{
                        if(valorTotalPago > 0 && total > 0)
                            $(this).closest('tr').css('background-color','#f2cf59');
                    }
                }
            }); 
        }

        var delBaixado = function(id,valorTotalPago,cdBaixaFinanceiro){

            $(".check-pagamento-cliente").each(function(index,element){

                if($(this).data('id') == cdBaixaFinanceiro){

                    var total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));    

                    //alert(total);

                    if(valorTotalPago <= 0){
                        $(this).closest('tr').css('background-color','#fb8e7e');
                    }else{
                        if(valorTotalPago > 0 && valorTotalPago < total){
                            $(this).closest('tr').css('background-color','#f2cf59');
                        }
                    }
                }
            }); 
        }

        $(".fechar").click(function(){
           // alert('teste');
        });

        var responsiveHelper_dt_basic_financeiro = undefined;
        var responsiveHelper_datatable_fixed_column = undefined;
        var responsiveHelper_datatable_col_reorder = undefined;
        var responsiveHelper_datatable_tabletools = undefined;
             
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

        $('#dt_basic_financeiro').dataTable({
                    "paging": false,
                    "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>"+
                        "t"+
                        "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
                    "autoWidth" : true,
                    "ordering": true,
                    "columnDefs": [
                                    { "orderable": false, "targets": [4,5,6,7,8] }
                                  ],
                    "aaSorting": [],
                    "oLanguage": {
                        "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>',
                        "sEmptyTable": "Nenhum registro encontrado",
                        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sInfoThousands": ".",
                        "sLengthMenu": "_MENU_ resultados por página",
                        "sLoadingRecords": "Carregando...",
                        "sProcessing": "Processando...",
                        "sZeroRecords": "Nenhum registro encontrado",
                        
                        "oPaginate": {
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                            "sFirst": "Primeiro",
                            "sLast": "Último"
                        },
                        "oAria": {
                            "sSortAscending": ": Ordenar colunas de forma ascendente",
                            "sSortDescending": ": Ordenar colunas de forma descendente"
                        },
                    },
                    "preDrawCallback" : function() {
                        // Initialize the responsive datatables helper once.
                        if (!responsiveHelper_dt_basic_financeiro) {
                            responsiveHelper_dt_basic_financeiro = new ResponsiveDatatablesHelper($('#dt_basic_financeiro'), breakpointDefinition);
                        }
                    },
                    "rowCallback" : function(nRow) {
                        responsiveHelper_dt_basic_financeiro.createExpandIcon(nRow);
                    },
                    "drawCallback" : function(oSettings) {
                        responsiveHelper_dt_basic_financeiro.respond();
                    }
        });

        $("#frm-add-baixa").on('submit',function(event){

            $('.modal-body').loader('show');

            event.preventDefault();
            $.ajax({
                url: "{{ url('/financeiro/cliente/baixa') }}",
                method: "POST",
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(registros){
                    $('#tabelaRegistro > tbody').html('');

                    var valorTotal = 0;
                    $.each(registros, function(index, value){   
                            
                        valorTotal += parseFloat(value.vl_baixa_honorario_bho);                        

                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            
                                                            '<td class="center">'+                                                                
                                                                '<a class="btnRegistroExcluir"  style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                            '</td>'+
                                                        '</tr>');
                     });
                    
                    addBaixado($("#cdBaixaFinanceiro").val(),valorTotal);

                    $('.modal-body').loader('hide');
                }
            });

        });

        $("#frm-add-baixa-lote").on('submit',function(event){

            //$('.modal-body').loader('show');
            
            event.preventDefault();
            var form = this;
            var contadorEntradas = 0;            
            var valorTotalLabel = 0;
            var tipo = '';
            var data = '';
            $('.modal-body').loader('show');

            $(".checkbox-check-pagamento-cliente").each(function(index,element){
                    
                if ($(this).is(':checked') ) {  

                    
                    $('.retornoLote').text('');                    
                   
                    var formData = new FormData(form);
                    var id =  $(this).data('id');

                    tipo = formData.get('tipo');
                    data = formData.get('dtBaixa');

            
                    if(formData.get('tipo') == 1){
                        var valor = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.'));
                    }else{
                        var valor = parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));
                    }

                    formData.append('valor',valor);   
                    formData.append('cdBaixaFinanceiro',id);   

                    $.ajax({
                        url: "{{ url('/financeiro/cliente/baixa') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false, 
                        async: false,                       
                        success: function(registros){
                            
                            var valorTotal = 0;
                            contadorEntradas++;
                            valorTotalLabel += valor;
                            
                            $('.retornoLote').text("Valor total da operação: R$"+valorTotalLabel+" / Total de entrada(s): "+contadorEntradas);

                            $.each(registros, function(index, value){   
                                        
                                valorTotal += parseFloat(value.vl_baixa_honorario_bho);                                 
                                
                            });
                            
                            addBaixado(id,valorTotal);     
                            
                        }
                    });

                    formData = null;
                }
            }); 


            $('.modal-body').loader('hide');  
            $('#tabelaRegistroLote > tbody').append('<tr>'+
                                                       '<td class="center">'+data+'</td>'+
                                                       '<td >'+valorTotalLabel+'</td>'+
                                                       '<td >'+(tipo == 1 ? "HONORÁRIO" : "DESPESA")+'</td>'+
                                                       
                                                    '</tr>'); 
        });

        $(".seleciona-todos").click(function(){

            if ($(".seleciona-todos").is(':checked') ) {                
                
                //var total = 0;            
                $(".checkbox-check-pagamento-cliente").each(function(index,element){
                    
                    $(this).prop('checked',true);
                    //total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   
                    //alert(total);

                }); 
                
               //$("#addBaixa").modal('show');   
                //$('#valor_total_operacao').text('Valor total dessa operação :'+' R$ '+total.toFixed(2).toString().replace('.',','));  
                
            }else{

                $(".checkbox-check-pagamento-cliente").each(function(index,element){
                    
                    $(this).prop('checked',false);
                   
                }); 
                
            }
            
        });
        
        $("#dt_basic_financeiro").on("click", ".check-pagamento-cliente-lote", function(){

            var total = 0;  
            var total_despesas = 0;      
            var controle = false;    

            $(".modal-title").html('<i class="icon-append fa fa-money"></i> Registro de Baixa em Lote');
            $(".retornoLote").text('');

            $('#dtBaixaCliente').val();
            $('#tipo').val();
            $('#tabelaRegistroLote > tbody').html('');
            $(".msg_titulo").html('');
            $(".msg_mensagem").html('');
            $(".alert").addClass("none");
            $(".alert").removeClass("alert-success");   
            $(".alert").removeClass("alert-danger");     

            var idsBaixaFinanceiro = [];
            
            
            $(".checkbox-check-pagamento-cliente").each(function(index,element){

                if ($(this).is(':checked') ) {         
                
                    total += parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.'));
                    total_despesas += parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   
                    
                    controle = true;

                    idsBaixaFinanceiro.push($(this).data('id'));

                }

            }); 

            localStorage.setItem("idsBaixaFinanceiro",JSON.stringify(idsBaixaFinanceiro));
            $('.table-upload > tbody').html('');

            if(controle == true){
                $("#addBaixaLote").modal('show');
                $('#valor_total_operacao').text('Valor total dessa operação para honorário(s):'+' R$ '+total.toFixed(2).toString().replace('.',','));
                $('#valor_total_operacao_despesas').text('Valor total dessa operação para despesa(s) :'+' R$ '+total_despesas.toFixed(2).toString().replace('.',','));
            }
        });

        $("#dt_basic_financeiro").on("click", ".check-pagamento-cliente", function(){

            $('.modal-body').loader('show');

            var id = $(this).data('id');
            $("#dtBaixaCliente").val('');
            $("#notaFiscal").val('');
            $("#tipo").val('');
            $('#tabelaRegistro > tbody').html('');
            $("#cdBaixaFinanceiro").val(id);       
            $("#valor").val( $(this).parent().parent().children().eq(4).text().replace('R$ ',''));
            $(".msg_titulo").html('');
            $(".msg_mensagem").html('');
            $(".alert").addClass("none");
            $(".alert").removeClass("alert-success");   
            $(".alert").removeClass("alert-danger");     

            var idsBaixaFinanceiro = [];
            idsBaixaFinanceiro.push(id);
            
            localStorage.setItem("idsBaixaFinanceiro",JSON.stringify(idsBaixaFinanceiro));

            $(".modal-title").html('<i class="icon-append fa fa-money"></i>');
            $(".modal-title").append(' '+$(this).parent().parent().children().eq(0).text()+' - '+$(this).parent().parent().children().eq(3).text()+'('+$(this).parent().parent().children().eq(2).text()+')');

             $.ajax({
                type:'GET',
                url: "{{ url('financeiro/cliente/baixa/entrada') }}/"+id,
                success:function(data){
                    var registros = JSON.parse(data);   
                    $('#tabelaRegistro > tbody').html('');
                    $.each(registros, function(index, value){         
                    
                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            
                                                            '<td class="center">'+                                                                
                                                                '<a class="btnRegistroExcluir"   style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                            '</td>'+
                                                        '</tr>');
                    });      
                    
                    $('.modal-body').loader('hide');
                    
                }
            });

           $('.table-upload > tbody').html('');
           var FP = $('#filepicker').filePicker();

           FP.fetch({limit: 10, offset: 0})
                .done((data) => {
                   $.each(data.files, function (_, file) {                      
                        FP.addProps(file);
                        file.context = FP.plugins.ui.renderTemplate(FP.options.ui.downloadTemplateId, { file: file });

                        file.context.find(FP.options.ui.selectors['delete']).data('filename', file.name);

                        if (file.original) {
                            file.original.context.removeClass('in');
                            file.original.context.replaceWith(file.context);
                            file.context.data('data', data);
                        } else {
                            FP.options.ui.filesList.append(file.context);
                        }

                        file.context.addClass('in');
                      console.log(file);
                  });
                });

            $("#addBaixa").modal('show');
        
        });

       $('body').on('click','.btnRegistroExcluir', function(){

                        $('.modal-body').loader('show');

                        var id = $(this).data("id");
                        var cdBaixaFinanceiro = $("#cdBaixaFinanceiro").val();
                    
                        $.ajax(
                            {
                                url: "{{ url('financeiro/cliente/baixa/entrada/excluir') }}/"+id,
                                type: 'DELETE',
                                dataType: "JSON",
                                success: function(data)
                                {                       
                                    var registros = data;   
                                    $('#tabelaRegistro > tbody').html('');                                                            

                                    var valorTotal = 0;
                                    $.each(registros, function(index, value){      

                                        valorTotal += parseFloat(value.vl_baixa_honorario_bho);
             
                                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                                            
                                                                            '<td class="center">'+                                                                
                                                                                '<a class="btnRegistroExcluir" style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                                            '</td>'+
                                                                        '</tr>');
                                    });      

                                    delBaixado(id,valorTotal,cdBaixaFinanceiro);

                                    $('.modal-body').loader('hide');
                                }
                        });

        });                        

        $( "#cliente_auto_complete" ).focusout(function(){
           if($("input[name='cd_cliente_cli']").val() == ''){
                $("#cliente_auto_complete").val('');
           }
        });

        var pathCliente = "{{ url('autocompleteCliente') }}";

        $( "#cliente_auto_complete" ).autocomplete({
          source: pathCliente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_cliente_cli']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-cliente').click(function(){
            $("input[name='cd_cliente_cli']").val('');
            $("input[name='nm_cliente_cli']").val('');

        });

        $.validator.addMethod("dateFormat",
                function(value, element) { 

                    if(value == '')
                        return true;

                    return value.match(/^(?:(?:31(\/)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)\d{2})$/);
                },
                "Data inválida.");

        var validobj = $("#baixa").validate({

            rules : {
                    dt_baixa_cliente_pth : {
                        //required: true,
                        dateFormat: true,
                    }, 
               },
                // Messages for form validation
            messages : {
                        dt_baixa_cliente_pth : {
                            required : 'Campo data é obrigatório'
                        },                      
                },
            errorPlacement: function(error, element) 
            {
                error.insertAfter( element );
            }

        });

        var validobj = $("#baixa_single").validate({

            rules : {
                    dt_baixa_cliente_pth : {
                        //required: true,
                        dateFormat: true,
                    }, 
               },
                // Messages for form validation
            messages : {
                        dt_baixa_cliente_pth : {
                            required : 'Campo data é obrigatório'
                        },                      
                },
            errorPlacement: function(error, element) 
            {
                error.insertAfter( element );
            }

        });

    });
</script>
    <script type="text/x-tmpl" id="uploadTemplate">
        <tr class="upload-template">
            <td class="column-name">
                <p class="name">{%= o.file.name %}</p>
                <span class="text-danger error">{%= o.file.error || '' %}</span>
            </td>
            <td colspan="2">
                <p>{%= o.file.sizeFormatted || '' %}</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
            </td>
            <td style="font-size: 150%; text-align: center;">
                {% if (!o.file.autoUpload && !o.file.error) { %}
                    <a href="#" class="action action-primary start" title="Upload">
                        <i class="fa fa-arrow-circle-o-up none"></i>
                    </a>
                {% } %}
                <a href="#" class="action action-warning cancel" title="Cancelar">
                    <i class="fa fa-ban"></i>
                </a>
            </td>
        </tr>
    </script>
    <!-- Download Template -->
    <script type="text/x-tmpl" id="downloadTemplate">
        {% o.timestamp = function (src) {
            return (src += (src.indexOf('?') > -1 ? '&' : '?') + new Date().getTime());
        }; %}
        <tr class="download-template">
            <td class="column-name">
                <p class="name">
                    {% if (o.file.url) { %}
                         <a href="{%= "../entrada/"+$("#cdBaixaFinanceiro").val()+"/anexo/"+o.file.url %}" data-id="{%= o.file.url %}" target="_blank">{%= o.file.name %}</a>
                    {% } else { %}
                        {%= o.file.name %}
                    {% } %}
                </p>
                {% if (o.file.error) { %}
                    <span class="text-danger">{%= o.file.error %}</span>
                {% } %}
            </td>
            <td class="column-size center"><p>{%= o.file.sizeFormatted %}</p></td>
            <td class="column-date">
                {% if (o.file.time) { %}
                    <time datetime="{%= o.file.timeISOString() %}">
                        {%= o.file.timeFormatted %}
                    </time>
                {% } %}
            </td>
            <td class="center">
                
                {% if (o.file.error) { %}
                    <a href="#" class="action action-warning cancel" title="Cancelar">
                        <i class="fa fa-ban"></i>
                    </a>
                {% } else { %}
                    <a href="#" class="action action-danger delete" title="Delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                {% } %}
            </td>
        </tr>
    </script>
    <!-- Pagination Template -->
    <script type="text/x-tmpl" id="paginationTemplate">
        {% if (o.lastPage > 1) { %}
            <ul class="pagination pagination-sm">
                <li {% if (o.currentPage === 1) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.prevPage %}" data-page="{%= o.prevPage %}" title="Previous">&laquo;</a>
                </li>

                {% if (o.firstAdjacentPage > 1) { %}
                    <li><a href="#!page=1" data-page="1">1</a></li>
                    {% if (o.firstAdjacentPage > 2) { %}
                       <li class="disabled"><a>...</a></li>
                    {% } %}
                {% } %}

                {% for (var i = o.firstAdjacentPage; i <= o.lastAdjacentPage; i++) { %}
                    <li {% if (o.currentPage === i) { %} class="active" {% } %}>
                        <a href="#!page={%= i %}" data-page="{%= i %}">{%= i %}</a>
                    </li>
                {% } %}

                {% if (o.lastAdjacentPage < o.lastPage) { %}
                    {% if (o.lastAdjacentPage < o.lastPage - 1) { %}
                        <li class="disabled"><a>...</a></li>
                    {% } %}
                    <li><a href="#!page={%= o.lastPage %}" data-page="{%= o.lastPage %}">{%= o.lastPage %}</a></li>
                {% } %}

                <li {% if (o.currentPage === o.lastPage) { %} class="disabled" {% } %}>
                    <a href="#!page={%= o.nextPage %}" data-page="{%= o.nextPage %}" title="Next">&raquo</a>
                </li>
            </ul>
        {% } %}
    </script><!-- end of #paginationTemplate -->
        
@endsection