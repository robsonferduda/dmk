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
                                        <input type="checkbox" class="check-pagamento-cliente" data-id='{{ $entrada->cd_processo_taxa_honorario_pth }}' {{ ($entrada->fl_pago_cliente_pth == 'N') ? '' : 'checked' }}  >

                                        @if(!empty($entrada->dt_baixa_cliente_pth))

                                         <a href="#" rel="popover-hover" data-placement="top" data-content="Nota Fiscal: {{ $entrada->nu_cliente_nota_fiscal_pth }}"
                                         data-original-title="Data de pagamento: {{ date('d/m/Y', strtotime($entrada->dt_baixa_cliente_pth)) }}"><i class="fa fa-question-circle text-primary"></i></a>
                                        
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
    <div id="dialog_simple" title="">
        <h5>
            Essa ação irá alterar todos os itens em tela.
        </h5>
        <h5 id="valor_total_operacao" ></h5>
        <form id="baixa">
            <div class="row">
                <section class="col col-md-3">
                       <label class="label label-black">Data de recebimento</label><br />
                       <input type="text" id='dtBaixaCliente' class='form-control dt_solicitacao_pro' name="dt_baixa_cliente_pth" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                       <span style="display: none" ></span>
                </section>
                <section class="col col-md-4">
                       <label class="label label-black">Nota Fiscal</label><br />
                       <input type="text" id='notaFiscal' class='form-control' name="nu_cliente_nota_fiscal_pth" placeholder="" >        
                </section>
            </div>
        </form>
    </div>
    <div id="dialog_simple_single" title="">
        <h5>
            Essa ação irá o item selecionado.
        </h5>
        <form id="baixa_single">
            <div class="row">
                <section class="col col-md-3">
                       <label class="label label-black">Data de recebimento</label><br />
                       <input type="text" id='dtBaixaCliente_single' class='form-control dt_solicitacao_pro' name="dt_baixa_cliente_pth" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                       <span style="display: none" ></span>
                </section>
                <section class="col col-md-4">
                       <label class="label label-black">Nota Fiscal</label><br />
                       <input type="text" id='notaFiscal_single' class='form-control' name="nu_cliente_nota_fiscal_pth" placeholder="" >        
                </section>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')

<script type="text/javascript">
    $(document).ready(function() {
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

     

         $('#dialog_simple').dialog({
                autoOpen : false,
                width : 600,
                resizable : false,
                closeOnEscape: false,
                modal : true,
                title : "Você deseja continuar essa operação?",
                beforeClose: function() {

                    if ($(".seleciona-todos").is(':checked') ) {                
                        $(".seleciona-todos").prop('checked',false);
                    }else {
                        $(".seleciona-todos").prop('checked',true);
                    }
                },
                buttons : [{
                    html : "<i class='fa fa-exchange'></i>&nbsp; Continuar",
                    "class" : "btn btn-success",
                    click : function() {

                        if($("#baixa").valid()){

                            var ids = Array();
                            if ($(".seleciona-todos").is(':checked') ) {                
                                var checked = 'S';                
                                $(".check-pagamento-cliente").each(function(index,element){
                                    ids[index] = $(this).data('id');            
                                });   
                                   
                            }else {
                                var checked = 'N';   
                                $(".check-pagamento-cliente").each(function(index,element){
                                     ids[index] = $(this).data('id');    
                                });
                            }

                            if(ids.length > 0 ){
                                verificaTodos(ids,checked); 
                            }
                            $(this).dialog("close");
                        }
                    }
                }, {
                    html : "<i class='fa fa-times'></i>&nbsp; Cancelar",
                    "class" : "btn btn-danger",
                    click : function() {
                        $(this).dialog("close");
                    }
                }]
        });

        $('#dialog_simple_single').dialog({
                autoOpen : false,
                width : 600,
                resizable : false,
                closeOnEscape: false,
                modal : true,
                title : "Você deseja continuar essa operação?",
                beforeClose: function() {

                    if ($(this).data('checkbox').is(':checked') ) {                
                        $(this).data('checkbox').prop('checked',false);
                    }else {
                        $(this).data('checkbox').prop('checked',true);
                    }
                },
                buttons : [{
                    html : "<i class='fa fa-exchange'></i>&nbsp; Continuar",
                    "class" : "btn btn-success",
                    click : function() {

                        if($("#baixa_single").valid()){
                            
                            verifica($(this).data('checkbox')); 
                            
                            $(this).dialog("close");
                        }
                    }
                }, {
                    html : "<i class='fa fa-times'></i>&nbsp; Cancelar",
                    "class" : "btn btn-danger",
                    click : function() {
                        $(this).dialog("close");
                    }
                }]
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
           
            $("#dtBaixaCliente").val('');
            $("#notaFiscal").val('');
            if ($(this).is(':checked') ) {             
                $('#dialog_simple_single').data('checkbox', $(this)).dialog('open');         
            }else {
                verifica($(this));
            }
           
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
                    return value.match(/^(?:(?:31(\/)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)\d{2})$/);
                },
                "Data inválida.");

        var validobj = $("#baixa").validate({

            rules : {
                    dt_baixa_cliente_pth : {
                        required: true,
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
                        required: true,
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