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
                {{--<label class="text-primary"><i class="fa fa-info-circle"></i> Informação! Por padrão o sistema exibe os últimos 10 clientes cadastrados. Utilize as opções de busca para personalizar o resultado.</label> --}}
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

                         <section class="col col-md-4">                           
                            <label class="label label-black">Correspondente</label><br />
                            <div class="input-group" style="width: 100%">
                            <input type="hidden" name="cd_correspondente_cor" value="{{(old('cd_correspondente_cor') ? old('cd_correspondente_cor') : (\Session::get('correspondente') ? \Session::get('correspondente') : '')) }}">
                            <input style="width: 100%" class="form-control" name="nm_correspondente_cor" placeholder="Digite 3 caracteres para busca" type="text" id="correspondente_auto_complete" value="{{(old('nm_correspondente_cor') ? old('nm_correspondente_cor') : (\Session::get('nmCorrespondente') ? \Session::get('nmCorrespondente') : '')) }}"> 
                             <div style="clear: all;"></div>
                            <span id="limpar-correspondente" title="Limpar campo" class="input-group-addon btn btn-warning"><i class="fa fa-eraser"></i></span>
                            </div>                        
                        </section>
                         <section class="col col-md-3" style="width:20%">
                            <br />                                        
                            <label class="label label-black">Incluir saídas verificadas?</label>  
                            <input type="checkbox" name="todas" id="todas"  {{ (!empty(\Session::get('todas')) ? 'checked' : '') }} > 
                        </section> 
                        <section class="col col-md-1">
                            <br />
                            <button class="btn btn-primary" type="submit"><i class="fa fa-file-pdf-o"></i> Buscar </button>
                        </section>    

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
                                    <th><input type="checkbox" class="seleciona-todos" ></th> 
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px">
                            @foreach($saidas as $saida)
                                <tr {{ ($saida->fl_pago_correspondente_pth == 'N') ? 'style=background-color:#fb8e7e' : 'style=background-color:#8ec9bb' }} >
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

                                    @php

                                        $totalDespesas = 0;
                                        foreach($saida->processo->processoDespesa as $despesa){

                                            if($despesa->cd_tipo_entidade_tpe == \TipoEntidade::CORRESPONDENTE && $despesa->fl_despesa_reembolsavel_pde == 'S'){
                                                $totalDespesas += $despesa->vl_processo_despesa_pde;
                                            
                                            }
                                        }

                                    @endphp
                                    <td>{{ 'R$ '.number_format($totalDespesas,2,',',' ') }}</td>
                                    <td>{{ 'R$ '.number_format($saida->vl_taxa_honorario_correspondente_pth+$totalDespesas,2,',',' ')}}</td>
                                    <td style="text-align: center;"><input type="checkbox" class="check-pagamento-correspondente" data-id='{{ $saida->cd_processo_taxa_honorario_pth }}' {{ ($saida->fl_pago_correspondente_pth == 'N') ? '' : 'checked' }}  > 
                                    
                                     @if(!empty($saida->dt_baixa_correspondente_pth))
                                         <a href="#" rel="popover-hover" data-placement="top" data-original-title="Data de pagamento: {{ date('d/m/Y', strtotime($saida->dt_baixa_correspondente_pth)) }}"><i class="fa fa-question-circle text-primary"></i></a>
                                        
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
                       <label class="label label-black">Data de pagamento</label><br />
                       <input type="text" id='dtBaixaCorrespondente' class='form-control dt_solicitacao_pro' name="dt_baixa_correspondente_pth" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                       <span style="display: none" ></span>
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
                       <label class="label label-black">Data de pagamento</label><br />
                       <input type="text" id='dtBaixaCorrespondente_single' class='form-control dt_solicitacao_pro' name="dt_baixa_correspondente_pth" placeholder="___ /___ /___" pattern="\d{1,2}/\d{1,2}/\d{4}" >
                       <span style="display: none" ></span>
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
                                $(".check-pagamento-correspondente").each(function(index,element){
                                    ids[index] = $(this).data('id');            
                                });   
                                   
                            }else {
                                var checked = 'N';   
                                $(".check-pagamento-correspondente").each(function(index,element){
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
                $(".check-pagamento-correspondente").each(function(index,element){
                    total += parseFloat($(this).parent().parent().children().eq(6).text().replace('R$ ','').replace(',','.'));    

                }); 
                $('#valor_total_operacao').text('Valor total dessa operação :'+' R$ '+total.toFixed(2).toString().replace('.',','));  
                
            }

            $("#dtBaixaCorrespondente").val('');
            $('#dialog_simple').dialog('open');
            
        });

        $("#dt_basic_financeiro").on("click", ".check-pagamento-correspondente", function(){
            
            $("#dtBaixaCorrespondente").val('');        
            if ($(this).is(':checked') ) {             
                $('#dialog_simple_single').data('checkbox', $(this)).dialog('open');         
            }else {
                verifica($(this));
            }
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

        var verificaTodos = function(ids,checked){

            var data = $("#dtBaixaCorrespondente").val();
            
            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/correspondente/baixa') }}",
                data:{ids:ids,checked:checked,data:data},
                success:function(data){
                    ret = JSON.parse(data);        
                    
                    if(ret === true){
                        if(checked == 'S'){
                            $(".check-pagamento-correspondente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#8ec9bb');   
                                $(this).prop('checked',true);       
                            }); 
                            
                        }else{
                            $(".check-pagamento-correspondente").each(function(index,element){
                                $(this).closest('tr').css('background-color','#fb8e7e');         
                                $(this).prop('checked',false);      
                            }); 
                            
                        }
                    }                                               
                }
            });
        }
    
        var verifica = function(checkbox){

            var data = $("#dtBaixaCorrespondente_single").val();
            
            var input = checkbox;
            var id = checkbox.data('id');
            if (checkbox.is(':checked') ) {
                var checked = 'S';            
            }else {
                var checked = 'N';
            }

            $.ajax({
                type:'POST',
                url: "{{ url('financeiro/correspondente/baixa') }}",
                data:{ids:[id],checked:checked,data:data},
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

            var data = $("#dtBaixaCorrespondente_single").val('');
        }

        $.validator.addMethod("dateFormat",
                function(value, element) {
                    return value.match(/^(?:(?:31(\/)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)\d{2})$/);
                },
            "Data inválida.");

        var validobj = $("#baixa").validate({

            rules : {
                    dt_baixa_correspondente_pth : {
                        required: true,
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
                        required: true,
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