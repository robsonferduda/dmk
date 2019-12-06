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
                        <section style="width:25%" class="col col-md-3">
                            <br />                                        
                            <label class="label label-black">Incluir entradas verificadas?</label>  
                            <input type="checkbox" name="todas" id="todas"  {{ (!empty(\Session::get('todas')) ? 'checked' : '') }} > 
                        </section> 
                         <section style="width:25%" class="col col-md-3">
                            <br />                                        
                            <label class="label label-black">Somente entradas verificadas?</label>  
                            <input type="checkbox" name="verificadas" id="verificadas"  {{ (!empty(\Session::get('verificadas')) ? 'checked' : '') }} > 
                        </section> 
                        <section class="col col-md-1">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa fa-search"></i> Buscar </button>
                        </section>    

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
                                    <th class="no-sort"><input type="checkbox" class="seleciona-todos" ></th> 
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($entradas as $entrada)
                                <tr {{ ($entrada->fl_pago_cliente_pth == 'N') ? 'style=background-color:#fb8e7e' : 'style=background-color:#8ec9bb' }} >
                                    <td>{{ $entrada->processo->nu_processo_pro }}</td>
                                    <td>
                                        @if(!empty($entrada->processo->dt_prazo_fatal_pro))
                                            {{ date('d/m/Y', strtotime($entrada->processo->dt_prazo_fatal_pro)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $entrada->tipoServico->nm_tipo_servico_tse }}</td>
                                    <td>{{ $entrada->processo->cliente->nm_razao_social_cli }}</td>
                                    <td>{{ 'R$ '.number_format($entrada->vl_taxa_honorario_cliente_pth,2,',',' ') }}</td>

                                    @php

                                        $totalDespesas = 0;
                                        foreach($entrada->processo->tiposDespesa as $despesa){
                                            $totalDespesas += $despesa->pivot->vl_processo_despesa_pde;
                                        }

                                    @endphp
                                    <td>{{ 'R$ '.number_format($totalDespesas,2,',',' ') }}</td>
                                    <td>{{ (!empty($entrada->vl_taxa_cliente_pth) ? $entrada->vl_taxa_cliente_pth.'%' : ' ') }}</td>
                                    <td>{{ 'R$ '.number_format(($entrada->vl_taxa_honorario_cliente_pth-
                                    ((($entrada->vl_taxa_honorario_cliente_pth)*$entrada->vl_taxa_cliente_pth)/100))+$totalDespesas,2,',',' ') }}</td>
                                    <td style="text-align: center;">
                                        <a title="Detalhes"  data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}'  class="btn btn-warning btn-xs check-pagamento-cliente"  href="javascript:void(0)"><i class="fa fa-money"></i></a>

                                        <input type="checkbox" class="check-pagamento-cliente" data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}' {{ ($entrada->fl_pago_cliente_pth == 'N') ? '' : 'checked' }}  >

                                        @if(!empty($entrada->dt_baixa_cliente_pth) || !empty($entrada->nu_cliente_nota_fiscal_pth))

                                         <a href="#" rel="popover-hover" data-placement="top" data-content="Nota Fiscal: {{ $entrada->nu_cliente_nota_fiscal_pth }}"
                                         data-original-title="Data de pagamento: {{ $entrada->dt_baixa_cliente_pth ? date('d/m/Y', strtotime($entrada->dt_baixa_cliente_pth)) : '' }}"><i class="fa fa-question-circle text-primary"></i></a>
                                        
                                        @endif


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
                                            <section class="col col-3">
                                                <label class="label">Data</label>
                                                <label class="input">
                                                     <input type="text" id='dtBaixaCliente' class='form-control dt_solicitacao_pro' name="dtBaixa" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                                                </label>
                                            </section>                     
                                         
                                             <section class="col col-3">
                                                <label class="label">Valor<span class="text-danger">*</label>
                                                <label class="input">
                                                    <input type="text" class="form-control taxa-honorario" name="valor" id="valor" required>
                                                </label>
                                            </section>    

                                            <section class="col col-6">
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
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {
        
        var addBaixado = function(id,valorTotalPago){

            $(".check-pagamento-cliente").each(function(index,element){
                if($(this).data('id') == id){

                    var total = parseFloat($(this).parent().parent().children().eq(7).text().replace('R$ ','').replace(',','.'));   

                    if(valorTotalPago >= total){
                        $(this).closest('tr').css('background-color','#8ec9bb');
                    }else{
                        $(this).closest('tr').css('background-color','#f2cf59');
                    }
                }
            }); 
        }

        var delBaixado = function(id,valorTotalPago){

            $(".check-pagamento-cliente").each(function(index,element){

                 alert($(this).data('id'));
                  alert(id);
                if($(this).data('id') == id){

                    var total = parseFloat($(this).parent().parent().children().eq(7).text().replace('R$ ','').replace(',','.'));   


                    if(valorTotalPago <= 0){
                        $(this).closest('tr').css('background-color','#fb8e7e');
                    }else{
                        if(valorTotalPago > 0 && valorTotalPago <= total){
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

                        if(value.anexo_financeiro == null){
                            value.anexo_financeiro = {"cd_anexo_financeiro_afn":"","nm_anexo_financeiro_afn":""};
                           // value.anexo_financeiro.nm_anexo_financeiro_afn = '';
                        }                


                        valorTotal += parseFloat(value.vl_baixa_honorario_bho);

                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
                                                            '<td class="center">'+                                                                
                                                                '<a class="btnRegistroExcluir"  style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                            '</td>'+
                                                        '</tr>');
                     });
                    
                    addBaixado($("#cdBaixaFinanceiro").val(),valorTotal);
                }
            });

        });

        $(".seleciona-todos").click(function(){

            if ($(".seleciona-todos").is(':checked') ) {                
                
                total = 0;            
                $(".check-pagamento-cliente").each(function(index,element){
                    total += parseFloat($(this).parent().parent().children().eq(7).text().replace('R$ ','').replace(',','.'));       

                }); 
                $('#valor_total_operacao').text('Valor total dessa operação :'+' R$ '+total.toFixed(2).toString().replace('.',','));  
                
            }

            $("#dtBaixaCliente").val('');
            $("#notaFiscal").val('');
            $('#dialog_simple').dialog('open');
            
        });
        
        $("#dt_basic_financeiro").on("click", ".check-pagamento-cliente", function(){

            var id = $(this).data('id');
            $("#dtBaixaCliente").val('');
            $("#notaFiscal").val('');
            $('#tabelaRegistro > tbody').html('');
            $("#cdBaixaFinanceiro").val(id);       
            $("#valor").val($(this).parent().parent().children().eq(7).text().replace('R$ ',''));

            $(".modal-title").html('<i class="icon-append fa fa-money"></i>');
            $(".modal-title").append(' '+$(this).parent().parent().children().eq(0).text()+' - '+$(this).parent().parent().children().eq(3).text()+'('+$(this).parent().parent().children().eq(2).text()+')');

             $.ajax({
                type:'GET',
                url: "{{ url('financeiro/cliente/baixa/entrada') }}/"+id,
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
                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
                                                            '<td class="center">'+                                                                
                                                                '<a class="btnRegistroExcluir"   style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                            '</td>'+
                                                        '</tr>');
                     });      
                    
                    
                }
            });
 
            $("#addBaixa").modal('show');
        
        });

       $('body').on('click','.btnRegistroExcluir', function(){

                        var id = $(this).data("id");
                    
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

                                        if(value.anexo_financeiro == null){
                                            value.anexo_financeiro = {"cd_anexo_financeiro_afn":"","nm_anexo_financeiro_afn":""};
                                            // value.anexo_financeiro.nm_anexo_financeiro_afn = '';
                                        }                  
                                        $('#tabelaRegistro > tbody').append('<tr>'+
                                                                            '<td class="center">'+value.dt_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.vl_baixa_honorario_bho+'</td>'+
                                                                            '<td >'+value.nu_nota_fiscal_bho+'</td>'+
                                                                            '<td >'+"<a href='{{ url('financeiro/entrada/file') }}/"+value.anexo_financeiro.cd_anexo_financeiro_afn+"' >"+value.anexo_financeiro.nm_anexo_financeiro_afn+"</a></td>"+
                                                                            '<td class="center">'+                                                                
                                                                                '<a class="btnRegistroExcluir" style="cursor:pointer" data-id="'+value.cd_baixa_honorario_bho+'"><i class="fa fa-trash"></i> </a>'+
                                                                            '</td>'+
                                                                        '</tr>');
                                    });      

                                    delBaixado(id,valorTotal);
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

        

        var verificaTodos = function(ids,checked){

            var data = $("#dtBaixaCliente").val();
            var nota = $("#notaFiscal").val();

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/cliente/baixa') }}",
                data:{ids:ids,checked:checked,data:data,nota:nota},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            $(".check-pagamento-cliente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#8ec9bb');   
                                $(this).prop('checked',true);       
                            }); 
                            
                        }else{
                            $(".check-pagamento-cliente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#fb8e7e');         
                                $(this).prop('checked',false);      
                            }); 
                            
                        }
                    }                                               
                }
            });
        }
    
        var verifica = function(checkbox){

            var data = $("#dtBaixaCliente_single").val();
            var nota = $("#notaFiscal_single").val();

            var input = checkbox;
            var id = checkbox.data('id');
            if (checkbox.is(':checked') ) {
                var checked = 'S';            
            }else {
                var checked = 'N';
            }

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/cliente/baixa') }}",
                data:{ids:[id],checked:checked,data:data,nota:nota},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            input.closest('tr').css('background-color','#8ec9bb');
                            input.prop('checked',true);
                        }else{
                            input.closest('tr').css('background-color','#fb8e7e');
                            input.next().remove();
                        }
                    }                                               
                }
            });

            var data = $("#dtBaixaCliente_single").val('');
            var nota = $("#notaFiscal_single").val('');
        }

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

@endsection