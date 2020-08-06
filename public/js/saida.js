$(document).ready(function() {


        localStorage.setItem("idsBaixaFinanceiro",JSON.stringify([]));

        $('#filepicker').filePicker({
            url: '../saida/anexo',                
            data: function(){
                var _token = $('meta[name="token"]').attr('content');
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
                    url: "../../anexo-processo-baixa-saida-add",
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
                url: '../../anexo-processo-baixa-saida-delete',
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
            url: '../saida/anexo',                
            data: function(){
                var _token = $('meta[name="token"]').attr('content');
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
                    url: "../../anexo-processo-baixa-saida-add",
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
                url: '../../anexo-processo-baixa-saida-delete',
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

            $(".check-pagamento-correspondente").each(function(index,element){
                if($(this).data('id') == id){

                    var total = parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.')) + parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));   

                    if(valorTotalPago >= total){
                        $(this).closest('tr').attr('style', function() {
                          this.style.removeProperty('background-color'); 
                          return this.style.cssText + 'background-color:#58ab583d;'; 
                        });   
                    }else{

                        if(valorTotalPago > 0 && total > 0)
                            $(this).closest('tr').css('background-color','#ffeba8');
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
                        $(this).closest('tr').css('background-color','#ffc3c3');
                    }else{
                        if(valorTotalPago > 0 && valorTotalPago < total){
                            $(this).closest('tr').css('background-color','#ffeba8');
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
                url: '../financeiro/correspondente/baixa',
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
            $('.btnSalvarRegistroBaixaLote').attr('disabled','disabled');

            setTimeout(function(){
                $(".checkbox-check-pagamento-correspondente").each(function(index,element){
                        
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
                            url: '../financeiro/correspondente/baixa',
                            method: "POST",
                            data: formData,
                            dataType: 'JSON',
                            contentType: false,
                            cache: false,
                            processData: false,
                            async:false,
                            success: function(registros){
                                $('#tabelaRegistro > tbody').html('');

                                var valorTotal = 0;
                                contadorEntradas++;
                                valorTotalLabel += valor;

                                $('.retornoLote').text("Valor total da operação: R$"+valorTotalLabel+" / Total de saída(s): "+contadorEntradas);

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
                $('.btnSalvarRegistroBaixaLote').prop('disabled',false);
                $('#tabelaRegistroLote > tbody').append('<tr>'+
                                                           '<td class="center">'+data+'</td>'+
                                                           //'<td >'+valorTotalLabel+'</td>'+
                                                           '<td >'+(tipo == 1 ? "HONORÁRIO" : "DESPESA")+'</td>'+
                                                           
                                                        '</tr>'); 
            },200);
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
            var total_despesas = 0;
            var controle = false;     

            $(".modal-title").html('<i class="icon-append fa fa-money"></i> Registro de Baixa em Lote');
            $(".retornoLote").text('');

            $('#dtBaixaCliente').val();
            $('#tipo').val();
            $('#tabelaRegistro > tbody').html('');
            $(".msg_titulo").html('');
            $(".msg_mensagem").html('');
            $(".alert").addClass("none");
            $(".alert").removeClass("alert-success");   
            $(".alert").removeClass("alert-danger");

            var idsBaixaFinanceiro = [];

            $(".checkbox-check-pagamento-correspondente").each(function(index,element){

                if ($(this).is(':checked') ) {         
                
                    total += parseFloat($(this).parent().parent().children().eq(4).text().replace('R$ ','').replace(',','.'));   
                    total_despesas +=  parseFloat($(this).parent().parent().children().eq(5).text().replace('R$ ','').replace(',','.'));
                    
                    controle = true;

                    idsBaixaFinanceiro.push($(this).data('id'));

                }

            }); 

            localStorage.setItem("idsBaixaFinanceiro",JSON.stringify(idsBaixaFinanceiro));
            $('.table-upload > tbody').html('');

            if(controle == true){
                $("#addBaixaLote").modal('show');
                $('#valor_total_operacao').text('Valor total dessa operação para honorário(s) :'+' R$ '+total.toFixed(2).toString().replace('.',','));
                $('#valor_total_operacao_despesas').text('Valor total dessa operação para despesa(s) :'+' R$ '+total_despesas.toFixed(2).toString().replace('.',','));
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
                url: '../financeiro/correspondente/baixa/saida/'+id,
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
                                url: '../financeiro/correspondente/baixa/saida/excluir/'+id,
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
        
        $( "#correspondente_auto_complete" ).focusout(function(){
           if($("input[name='cd_correspondente_cor']").val() == ''){
                $("#correspondente_auto_complete").val('');
           }
        });

        var pathCorrespondente = '../autocompleteCorrespondenteDeletedToo';

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