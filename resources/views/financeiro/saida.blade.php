@extends('layouts.admin')
@section('content')
<div id="ribbon">
    <ol class="breadcrumb">
        <li><a href="{{ url('home') }}">Início</a></li>
        <li>Financeiro</li>
        <li>Saídas</li>
    </ol>
</div>
<div id="content">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h1 class="page-title txt-color-blueDark">
                <i class="fa-fw fa fa-file-o"></i> Financeiro <span> > Saídas</span>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @include('layouts/messages')
        </div>
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 correspondentes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
                <form action="{{ url('financeiro/saida/buscar') }}" class="form-inline" method="POST" role="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <section class="col col-md-2">
                            <label class="label label-black">Data prazo fatal inicial</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtInicio" value="{{ old('dtInicio') ? old('dtInicio') : \Session::get('dtInicio')}}" >
                            
                        </section>
                        <section class="col col-md-2">                           
                            <label class="label label-black">Data prazo fatal final</label><br />
                            <input style="width: 100%" class="form-control dt_solicitacao_pro" placeholder="___ /___ /___" type="text" name="dtFim" value="{{ old('dtFim') ? old('dtFim') : \Session::get('dtFim')}}"  >                            
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
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
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
                    <h2>Saídas</h2>
                </header>
                 <div>
                    <div class="widget-body no-padding">
                        <table id="dt_basic_financeiro" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>                         
                                <tr style="font-size: 12px">                   
                                    <th>Nº Processo</th>                    
                                    <th>Prazo Fatal</th>
                                    <th>Tipo de Serviço</th>    
                                    <th>Correspondente</th>
                                    <th style="min-width:8%">Honorário</th>                                    
                                    <th style="min-width:8%">Despesa</th>
                                    <th style="min-width:8%">Total</th>                                      
                                    <th style="text-align: center;" class="no-sort">
                                        <a title="Pagamentos em Lote" class="btn btn-warning btn-xs check-pagamento-correspondente-lote"  href="javascript:void(0)" ><i class="fa fa-arrow-down" ></i></a>
                                        <input type="checkbox" class="seleciona-todos" >
                                    </th> 
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($saidas as $saida)

                                @php

                                   $totalDespesas = $saida->processo->processoDespesa->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE)->where('fl_despesa_reembolsavel_pde','S')->sum('vl_processo_despesa_pde');
                                  
                                   $totalBaixaHonorario = $saida->baixaHonorario->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::SAIDA)->sum('vl_baixa_honorario_bho');

                                   // dd($saida);
                                @endphp

                                <tr {{ ($totalBaixaHonorario <= 0 && $totalDespesas+$saida->vl_taxa_honorario_correspondente_pth > 0
                                                    ? 'style=background-color:#fb8e7e' : 
                                                            ($totalBaixaHonorario < ($totalDespesas+$saida->vl_taxa_honorario_correspondente_pth) && $totalBaixaHonorario > 0
                                                                ? 'style=background-color:#f2cf59' : 
                                                                    'style=background-color:#8ec9bb')) }} >
                                    <td>{{ $saida->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($saida->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($saida->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($saida->tipoServicoCorrespondente->nm_tipo_servico_tse))
                                            {{ $saida->tipoServicoCorrespondente->nm_tipo_servico_tse }} 
                                        @endif
                                    </td>
                                    <td>
                                         @if(!empty($saida->processo->correspondente->contaCorrespondente))
                                            {{ $saida->processo->correspondente->contaCorrespondente->nm_conta_correspondente_ccr }} 
                                        @endif               
                                    </td>
                                    <td>{{ 'R$ '.number_format($saida->vl_taxa_honorario_correspondente_pth,2,',',' ') }}</td>

                                    <td>{{ 'R$ '.number_format($totalDespesas,2,',',' ') }}</td>
                                    <td>{{ 'R$ '.number_format($saida->vl_taxa_honorario_correspondente_pth+$totalDespesas,2,',',' ')}}</td>
                                    <td style="text-align: center;">
                                        <a title="Pagamentos"  data-id='{{ $saida->cd_processo_taxa_honorario_pth }}'  class="btn btn-warning btn-xs check-pagamento-correspondente"  href="javascript:void(0)" ><i class="fa fa-money"></i></a>

                                        <input type="checkbox" class="checkbox-check-pagamento-correspondente" style="width: 100%" data-id='{{ $saida->cd_processo_taxa_honorario_pth }}' {{ ($saida->fl_pago_correspondente_pth == 'N') ? '' : 'checked' }}  >                                

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
                                                     <input type="text" id='dtBaixaCorrespondente' class='form-control dt_solicitacao_pro' name="dtBaixa" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                                                </label>
                                            </section>                     
                                         
                                             <section class="col col-3">
                                                <label class="label">Valor<span class="text-danger">*</label>
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

                                            <section class="col col-4">
                                                <label class="label">Nota</label>
                                                <label class="input">
                                                   <input type="text" id='notaFiscal' class='form-control' name="notaFiscal" placeholder="" >                                                         
                                                </label>
                                            </section>    
                                            
                                        </div>
                                        <div class="row"> 
                                             <section class="col col-10">
                                                <label class="label">Arquivo</label>
                                                <label class="input"> 
                                                   <input name="file" type="file" id="file" class="form-control">
                                                </label>
                                            </section>    
                                            <section class="col col-1">
                                                <label class="label">&nbsp</label>
                                                <button type="submit" id="btnSalvarRegistroBaixa" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Registrar</button>
                                            </section>
                                        </div> 
                                        <div class="row center" id="erroFone"></div>
                                    </fieldset>

                                    <div class="row" style="margin: 0; padding: 5px 13px;">
                                            
                                            <table id="tabelaRegistro" class="table table-bordered table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th class="center">Data</th>
                                                        <th class="center">Valor</th>
                                                        <th class="center">Tipo</th>
                                                        <th class="center">Nota</th>
                                                        <th class="center">Arquivo</th>
                                                        <th class="center">Opções</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>                                       
                                            
                                    </div>
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
                    
                     <fieldset>
                        <section>
                            <div class="col col-sm-12">
                                    <header>
                                        <i class="fa fa-arrow-circle-o-down"></i> Registro de Baixa<br /><h5 id="valor_total_operacao"></h5>
                                    </header>
                                    <fieldset style="padding: 10px 14px 5px;">
                                        <div class="row">    
                                            <section class="col col-3">
                                                <label class="label">Data</label>
                                                <label class="input">
                                                     <input type="text" id='dtBaixaCorrespondente' class='form-control dt_solicitacao_pro' name="dtBaixa" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                                                </label>
                                            </section>                     
                                         
                                             <section class="col col-3">
                                                <label class="label">Valor<span class="text-danger">*</label>
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
                                            <section class="col col-1">
                                                <label class="label">&nbsp</label>
                                                <button type="submit" id="btnSalvarRegistroBaixa" class="btn btn-success" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Registrar</button>
                                            </section>
                                        </div>
                                    
                                        <div class="row center" id="erroFone"></div>
                                    </fieldset>

                                    <div class="row" style="margin: 0; padding: 5px 13px;">
                                            
                                        <h2 class="retornoLote"></h2>                             
                                            
                                    </div>
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

        var addBaixado = function(id,valorTotalPago){

            $(".check-pagamento-correspondente").each(function(index,element){
                if($(this).data('id') == id){

                    var total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   

                    //alert(total);

                    if(valorTotalPago >= total){
                        $(this).closest('tr').css('background-color','#8ec9bb');
                    }else{
                        $(this).closest('tr').css('background-color','#f2cf59');
                    }
                }
            }); 
        }

        var delBaixado = function(id,valorTotalPago,cdBaixaFinanceiro){

            $(".check-pagamento-correspondente").each(function(index,element){

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
                                    { "orderable": false, "targets": -1 }
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
                url: "{{ url('/financeiro/correspondente/baixa') }}",
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

                        if(value.anexo_financeiro == null){
                            value.anexo_financeiro = {"cd_anexo_financeiro_afn":"","nm_anexo_financeiro_afn":""};
                           // value.anexo_financeiro.nm_anexo_financeiro_afn = '';
                        }                


                        valorTotal += parseFloat(value.vl_baixa_honorario_bho);

                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
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

            $(".checkbox-check-pagamento-correspondente").each(function(index,element){
                    
                if ($(this).is(':checked') ) {  

                    $('.modal-body').loader('show');
                    $('.retornoLote').text('');
                   
                    var formData = new FormData(form);
                    var id =  $(this).data('id');
                    formData.append('cdBaixaFinanceiro',id);    
                    for(var pair of formData.entries()) {
                       console.log(pair[0]+ ', '+ pair[1]); 
                    }
           
                    $.ajax({
                        url: "{{ url('/financeiro/correspondente/baixa') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(registros){
                            $('#tabelaRegistro > tbody').html('');

                            var valorTotal = 0;
                            contadorEntradas++;
                            $('.retornoLote').text("O valor R$"+$('#frm-add-baixa-lote .taxa-honorario').val()+" foi adicionando ao total de "+contadorEntradas+" entrada(s).");

                            $.each(registros, function(index, value){   
                                        
                                valorTotal += parseFloat(value.vl_baixa_honorario_bho);
                                
                            });
                            
                            addBaixado(id,valorTotal);

                            $('.modal-body').loader('hide');
                        }
                    });

                     formData = null;
                }
            }); 

        });

        $(".seleciona-todos").click(function(){

            if ($(".seleciona-todos").is(':checked') ) {                
                
                //var total = 0;            
                $(".checkbox-check-pagamento-correspondente").each(function(index,element){
                    
                    $(this).prop('checked',true);
                    //total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   
                    //alert(total);

                }); 
                
               //$("#addBaixa").modal('show');   
                //$('#valor_total_operacao').text('Valor total dessa operação :'+' R$ '+total.toFixed(2).toString().replace('.',','));  
                
            }else{

                $(".checkbox-check-pagamento-correspondente").each(function(index,element){
                    
                    $(this).prop('checked',false);
                   
                }); 
                
            }
            
        });

        $("#dt_basic_financeiro").on("click", ".check-pagamento-correspondente-lote", function(){

            var total = 0;        
            var controle = false;    

            $(".modal-title").html('<i class="icon-append fa fa-money"></i> Registro de Baixa em Lote');
            $(".retornoLote").text('');

            $('#dtBaixaCliente').val();
            $('#valor').val();
            $('#tabelaRegistro > tbody').html('');


            $(".checkbox-check-pagamento-correspondente").each(function(index,element){

                if ($(this).is(':checked') ) {         
                
                    total += parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   
                    
                    controle = true;

                }

            }); 

            if(controle == true){
                $("#addBaixaLote").modal('show');
                $('#valor_total_operacao').text('Valor total dessa operação :'+' R$ '+total.toFixed(2).toString().replace('.',','));
            }
        });

        $("#dt_basic_financeiro").on("click", ".check-pagamento-correspondente", function(){

            $('.modal-body').loader('show');

            var id = $(this).data('id');
            $("#dtBaixaCliente").val('');
            $("#notaFiscal").val('');
            $("#tipo").val('');
            $('#tabelaRegistro > tbody').html('');
            $("#cdBaixaFinanceiro").val(id);       
            $("#valor").val( String(parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'))).replace('.',','));

            $(".modal-title").html('<i class="icon-append fa fa-money"></i>');
            $(".modal-title").append(' '+$(this).parent().parent().children().eq(0).text()+' - '+$(this).parent().parent().children().eq(3).text()+'('+$(this).parent().parent().children().eq(2).text()+')');

             $.ajax({
                type:'GET',
                url: "{{ url('financeiro/correspondente/baixa/saida') }}/"+id,
                success:function(data){
                    var registros = JSON.parse(data);   
                    $('#tabelaRegistro > tbody').html('');
                    $.each(registros, function(index, value){         

                        if(value.anexo_financeiro == null){
                            value.anexo_financeiro = {"cd_anexo_financeiro_afn":"","nm_anexo_financeiro_afn":""};
                           // value.anexo_financeiro.nm_anexo_financeiro_afn = '';
                        }

                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
                                                            '<td class="center">'+                                                                
                                                                '<a class="btnRegistroExcluir"   style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                            '</td>'+
                                                        '</tr>');
                    });      
                    
                    $('.modal-body').loader('hide');
                    
                }
            });
 
            $("#addBaixa").modal('show');
        
        });

       $('body').on('click','.btnRegistroExcluir', function(){

                        $('.modal-body').loader('show');

                        var id = $(this).data("id");
                        var cdBaixaFinanceiro = $("#cdBaixaFinanceiro").val();
                    
                        $.ajax(
                            {
                                url: "{{ url('financeiro/correspondente/baixa/saida/excluir') }}/"+id,
                                type: 'DELETE',
                                dataType: "JSON",
                                success: function(data)
                                {                       
                                    var registros = data;   
                                    $('#tabelaRegistro > tbody').html('');                                                            

                                    var valorTotal = 0;
                                    $.each(registros, function(index, value){      

                                        valorTotal += parseFloat(value.vl_baixa_honorario_bho);

                                        if(value.anexo_financeiro == null){
                                            value.anexo_financeiro = {"cd_anexo_financeiro_afn":"","nm_anexo_financeiro_afn":""};
                                            // value.anexo_financeiro.nm_anexo_financeiro_afn = '';
                                        }                  
                                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.tipo_baixa_honorario.nm_tipo_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
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
        
        $( "#correspondente_auto_complete" ).focusout(function(){
           if($("input[name='cd_correspondente_cor']").val() == ''){
                $("#correspondente_auto_complete").val('');
           }
        });

        var pathCorrespondente = "{{ url('autocompleteCorrespondente') }}";

        $( "#correspondente_auto_complete" ).autocomplete({
          source: pathCorrespondente,
          minLength: 3,
          select: function(event, ui) {

            $("input[name='cd_correspondente_cor']").val(ui.item.id);

          },
          open: function(event, ui){
            
          }
        });

        $('#limpar-correspondente').click(function(){
            $("input[name='cd_correspondente_cor']").val('');
            $("input[name='nm_correspondente_cor']").val('');

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
                    dt_baixa_correspondente_pth : {
                        //required: true,
                        dateFormat: true,
                    }, 
               },
                // Messages for form validation
            messages : {
                        dt_baixa_correspondente_pth : {
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
                    dt_baixa_correspondente_pth : {
                        //required: true,
                        dateFormat: true,
                    }, 
               },
                // Messages for form validation
            messages : {
                        dt_baixa_correspondente_pth : {
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

@endsection