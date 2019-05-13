$(document).ready(function() {

	var _location = document.location.toString();
    var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
    var applicationName = _location.substring(0, applicationNameIndex) + '/';
    var webFolderIndex = _location.indexOf('/', _location.indexOf(applicationName) + applicationName.length);
    var pathname = _location.substring(0, webFolderIndex);
    var pathnameX = _location.substring(0, webFolderIndex);


	/** ======================== Masks ========================   **/
	$('.hr_audiencia_pro').mask('00:00');
	$('.dt_prazo_fatal_pro').mask('00/00/0000');
	$('.dt_solicitacao_pro').mask('00/00/0000');
	$('.data_nascimento').mask('00/00/0000');
	$('.data_fundacao').mask('00/00/0000');
	$('.data_admissao').mask('00/00/0000');
	$('.cep').mask('00000-000');
	$('.cpf').mask('000.000.000-00');
	$('.cnpj').mask("00.000.000/000-00");
	$('.telefone').mask("(00) 0000-00009");
	$(".taxa-honorario").mask('#####000,00', {reverse: true});
	$(".taxa-despesa").mask('#####000,00', {reverse: true});
	$("#taxa_imposto_cli").mask('#####000,00', {reverse: true});
	

	/** =======================================================   **/

	$('.upload-result').on('click', function (ev) {
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (resp) {
                $.ajax({
                    url: pathname+"/image-crop",
                    type: "POST",
                    data: {"image":resp},
                    success: function (data) {
                        html = '<img src="' + resp + '" />';
                        $("#upload-demo-i").html(html);
                    }
                });
            });
            location.reload();
        });

	if($('input:radio[name=cd_tipo_pessoa_tpp]:checked').val() == 1){
	    $(".label-tipo-pessoa").html('Nome');
	    $("#cpf").focus();
	    $(".box-pessoa-fisica").css('display','block');
	    $(".box-pessoa-juridica").css('display','none');
	}else{
	    $(".label-tipo-pessoa").html('Nome Fantasia');
	    $(".box-pessoa-fisica").css('display','none');
	    $(".box-pessoa-juridica").css('display','block');
	    $("#cnpj").focus();
	}

	$(".tipo-pessoa").click(function(){
	    if($('input:radio[name=cd_tipo_pessoa_tpp]:checked').val() == 1){
	    	$(".label-tipo-pessoa").html('Nome');
	        $(".box-pessoa-fisica").css('display','block');
	        $(".box-pessoa-juridica").css('display','none');
	        $("#cpf").focus();
	    }else{
	    	$(".label-tipo-pessoa").html('Nome Fantasia');
	    	$(".box-pessoa-fisica").css('display','none');
	        $(".box-pessoa-juridica").css('display','block');
	        $("#cnpj").focus();
	    }
	});

	$(".btn-save-area").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-area").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".excluir_registro").click(function(){

		var id  = $(this).closest('tr').find('td[data-id]').data('id');
		var url = $(this).data('url');

		$("#modal_exclusao #url").val(url);
		$("#modal_exclusao #id_exclusao").val(id);
		$("#modal_exclusao").modal('show');
	});

	$(".adicionar_registro").click(function(){

		var id  = $(this).data('id');
		var url = $(this).data('url');

		$("#modal_confirma_correspondente #url").val(url);
		$("#modal_confirma_correspondente #id_correspondente").val(id);
		$("#modal_confirma_correspondente").modal('show');
	});

	$(".remover_registro").click(function(){

		var id  = $(this).data('id');
		var url = $(this).data('url');

		$("#modal_cancela_correspondente #url").val(url);
		$("#modal_cancela_correspondente #id_correspondente").val(id);
		$("#modal_cancela_correspondente").modal('show');
	});

	$(".editar_area").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var descricao = $(this).closest('tr').find('td[data-descricao]').data('descricao');
		var action    = "../areas/"+id;

		$('#frm-edit-area #id_area').val(id);
		$('#frm-edit-area #dc_area_direito_ado').val(descricao);
		$('#frm-edit-area').attr('action', action);						

	    $('#editArea').modal('show');
	});

	$("#btn_confirma_exclusao").click(function(){

		var id = $("#id_exclusao").val();
		var url= $("#url").val();
        var token = $(this).data("token");

        $.ajax(
        {
            url: url+id,
            type: 'DELETE',
            dataType: "JSON",
            data: {
                "id": id,
                "_method": 'DELETE',
                "_token": $('meta[name="token"]').attr('content'),
            },
            beforeSend: function()
            {
            	$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');
            },
            success: function(response)
            {
            	$(".msg_retorno").html('<h4 class="text-success marginTop10"><strong>Registro excluído com sucesso. Os dados serão atualizados!</strong></h4>')
                location.reload();
            },
		   	error: function(response)
		   	{
		   		$(".msg_retorno").html('<h4 class="text-danger marginTop10"><strong>Ocorreu um erro na sua requisição.</strong></h4>')
		   	}
        });

	});

	$(".editar_tipo_contato").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../tipos-de-contato/"+id;

		$('#frm-edit-tipo-contato #cd_tipo_contato_tct').val(id);
		$('#frm-edit-tipo-contato #nm_tipo_contato_tct').val(nome);

		$('#frm-edit-tipo-contato').attr('action', action);						

	    $('#editTipoContato').modal('show');
	});

	$(".btn-save-tipo-contato").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-tipo-contato").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_tipo_servico").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../tipos-de-servico/"+id;

		$('#frm-edit-tipo-servico #cd_tipo_servico_tse').val(id);
		$('#frm-edit-tipo-servico #nm_tipo_servico_tse').val(nome);

		$('#frm-edit-tipo-servico').attr('action', action);						

	    $('#editTipoServico').modal('show');
	});

	$(".btn-save-tipo-processo").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-tipo-processo").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_tipo_processo").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../tipos-de-processo/"+id;

		$('#frm-edit-tipo-processo #cd_tipo_processo_tpo').val(id);
		$('#frm-edit-tipo-processo #nm_tipo_processo_tpo').val(nome);
		$('#frm-edit-tipo-processo').attr('action', action);						

	    $('#editTipoProcesso').modal('show');
	});

	$(".btn-save-tipo-servico").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-tipo-servico").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_tipo_despesa").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var reembolso = $(this).closest('tr').find('td[data-reembolso]').data('reembolso');
		var categoria = $(this).closest('tr').find('td[data-categoria]').data('categoria');
		var action    = "../tipos-de-despesa/"+id;

		$('#frm-edit-tipo-despesa #cd_tipo_despesa_tds').val(id);
		$('#frm-edit-tipo-despesa #nm_tipo_despesa_tds').val(nome);

		if(reembolso == 'S'){
			$('#frm-edit-tipo-despesa #fl_reembolso_tds').prop( "checked", true );
		}else{
			$('#frm-edit-tipo-despesa #fl_reembolso_tds').prop( "checked", false );
		}

		
		//alert(categoria);
		$('#frm-edit-tipo-despesa #categoriaDespesa').val(categoria).trigger('change');;

		$('#frm-edit-tipo-despesa').attr('action', action);						

	    $('#editTipoDespesa').modal('show');
	});

	$(".btn-save-tipo-despesa").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-tipo-despesa").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_vara").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../varas/"+id;

		$('#frm-edit-vara #cd_vara_var').val(id);
		$('#frm-edit-vara #nm_vara_var').val(nome);

		$('#frm-edit-vara').attr('action', action);						

	    $('#editVara').modal('show');
	});

	$(".btn-save-vara").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-vara").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_cargo").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../cargos/"+id;

		$('#frm-edit-cargo #cd_cargo_car').val(id);
		$('#frm-edit-cargo #nm_cargo_car').val(nome);

		$('#frm-edit-cargo').attr('action', action);						

	    $('#editCargo').modal('show');

	});

	$(".btn-save-cargo").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-cargo").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".editar_departamento").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../departamentos/"+id;

		$('#frm-edit-departamento #cd_departamento_dep').val(id);
		$('#frm-edit-departamento #nm_departamento_dep').val(nome);

		$('#frm-edit-departamento').attr('action', action);						

	    $('#editDepartamento').modal('show');

	});

	$(".btn-save-departamento").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-departamento").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});


	$(".editar_categoria_despesa").click(function(){

		var id        = $(this).closest('tr').find('td[data-id]').data('id');
		var nome      = $(this).closest('tr').find('td[data-nome]').data('nome');
		var action    = "../categorias-de-despesas/"+id;

		$('#frm-edit-categoria-despesa #cd_categoria_despesa_cad').val(id);
		$('#frm-edit-categoria-despesa #nm_categoria_despesa_cad').val(nome);

		$('#frm-edit-categoria-despesa').attr('action', action);						

	    $('#editCategoriaDespesa').modal('show');
	});

	$(".btn-save-categoria-despesa").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	$(".btn-edit-categoria-despesa").click(function(){
		$(".msg_retorno").html('<h3><i class="fa fa-spinner fa-spin"></i> Processando operação...</h3>');		
	});

	var telefones = new Array();
	var emails = new Array();

	$("#btnSalvarTelefone").click(function(){

		var flag = true;
		var tipo = $("#cd_tipo_fone_tfo option:selected").val();
		var ds_tipo = $("#cd_tipo_fone_tfo option:selected").text();
		var numero = $("#nu_fone_fon").val();
		var entidade = $("#entidade").val();

		if(tipo == 0){ flag = false; $("#erroFone").html("Campo tipo obrigatório"); }
		if(numero == ''){ flag = false; $("#erroFone").html("Número de telefone obrigatório"); }

		if(flag){

			var fone = {tipo: tipo, numero: numero, descricao: ds_tipo};

			telefones.push(fone);

			$("#tabelaFone > tbody > tr").remove();	
			loadTelefones(entidade);

			$.each(telefones, function(index, value){
				$('#tabelaFone > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.numero+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
			});			

			$('.excluirFone').on('click', function(){

				var id = $(this).data("id");
				var entidade = $("#entidade").val()

				telefones.splice(id,1); //Remove o registro do vetor que está na memória

				$("#tabelaFone > tbody > tr").remove();	
				loadTelefones(entidade);

				$.each(telefones, function(index, value){
					$('#tabelaFone > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.numero+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});

				$("#telefones").val(JSON.stringify(telefones));

			});

			$("#nu_fone_fon").val("");
			$("#cd_tipo_fone_tfo").prop('selectedIndex',0);
			$("#nu_fone_fon").focus();			

			$('#modalFone').modal('hide');
			$("#telefones").val(JSON.stringify(telefones));
		}

	});

	$("#btnSalvarEmail").click(function(){

		var flag = true;
		var tipo = $("#cd_tipo_endereco_eletronico_tee option:selected").val();
		var ds_tipo = $("#cd_tipo_endereco_eletronico_tee option:selected").text();
		var email = $("#dc_endereco_eletronico_ede").val();
		var entidade = $("#entidade").val();

		if(tipo == 0){ flag = false; $("#erroEmail").html("Campo tipo obrigatório"); }
		if(email == ''){ flag = false; $("#erroEmail").html("Email obrigatório"); }

		if(flag){

			var email = {tipo: tipo, email: email, descricao: ds_tipo};

			emails.push(email);

			$("#tabelaEmail > tbody > tr").remove();	
			loadEmails(entidade);

			$.each(emails, function(index, value){
				$('#tabelaEmail > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.email+'</td><td class="center"><a class="excluirEmail" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
			});			

			$('.excluirEmail').on('click', function(){

				var id = $(this).data("id");
				var entidade = $("#entidade").val()

				emails.splice(id,1); //Remove o registro do vetor que está na memória

				$("#tabelaEmail > tbody > tr").remove();	
				loadEmails(entidade);

				$.each(emails, function(index, value){
					$('#tabelaEmail > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.email+'</td><td class="center"><a class="excluirEmail" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});

				$("#emails").val(JSON.stringify(emails));

			});

			$("#dc_endereco_eletronico_ede").val("");
			$("#cd_tipo_endereco_eletronico_tee").prop('selectedIndex',0);
			$("#dc_endereco_eletronico_ede").focus();			

			$("#emails").val(JSON.stringify(emails));
		}

	});

	function loadEmails(entidade){

		$.ajax(
            {
                url: pathnameX+"email/entidade/"+entidade,
                type: 'GET',
                dataType: "JSON",
            success: function(response)
            {                    	
				$.each(response, function(index, value){
					$('#tabelaEmail > tbody').append('<tr><td class="center">'+value.tipo.dc_tipo_endereco_eletronico_tee+'</td><td>'+value.dc_endereco_eletronico_ede+'</td><td class="center"><a class="excluirEmailBase" data-codigo="'+value.cd_endereco_eletronico_ele+'"> <i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});   

				$('.excluirEmailBase').on('click', function(){

					var id = $(this).data("codigo");
					var entidade = $("#entidade").val();
					
					$.ajax(
			            {
			                url: pathnameX+"email/excluir/"+id,
			                type: 'GET',
			                dataType: "JSON",
			            success: function(response)
			            {                    	
			            	$("#tabelaEmail > tbody > tr").remove();	
							loadEmails(entidade);
							$.each(emails, function(index, value){
								$('#tabelaEmail > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.email+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
							});
			            },
			            error: function(response)
			            {
			            }
			        });

				});   
            },
            error: function(response)
            {
            }
        });

	}

	function loadTelefones(entidade){

		$.ajax(
            {
                url: pathnameX+"fones/entidade/"+entidade,
                type: 'GET',
                dataType: "JSON",
            success: function(response)
            {                    	
				$.each(response, function(index, value){
					$('#tabelaFone > tbody').append('<tr><td class="center">'+value.tipo.dc_tipo_fone_tfo+'</td><td>'+value.nu_fone_fon+'</td><td class="center"><a class="excluirFoneBase" data-codigo="'+value.cd_fone_fon+'"> <i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});   

				$('.excluirFoneBase').on('click', function(){

					var id = $(this).data("codigo");
					var entidade = $("#entidade").val();
					
					$.ajax(
			            {
			                url: pathnameX+"fones/excluir/"+id,
			                type: 'GET',
			                dataType: "JSON",
			            success: function(response)
			            {                    	
			            	$("#tabelaFone > tbody > tr").remove();	
							loadTelefones(entidade);
							$.each(telefones, function(index, value){
								$('#tabelaFone > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.numero+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
							});
			            },
			            error: function(response)
			            {
			            }
			        });

				});   
            },
            error: function(response)
            {
            }
        });

	}


	$('.excluirEmailBase').on('click', function(){

		var id = $(this).data("codigo");
		var entidade = $("#entidade").val();
		
		$.ajax(
            {
                url: pathnameX+"email/excluir/"+id,
                type: 'GET',
                dataType: "JSON",
            success: function(response)
            {                    	
            	$("#tabelaEmail > tbody > tr").remove();	
				loadEmails(entidade);
				$.each(emails, function(index, value){
					$('#tabelaEmail > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.email+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});
            },
            error: function(response)
            {
            }
        });

	}); 

	$('.excluirFoneBase').on('click', function(){

		var id = $(this).data("codigo");
		var entidade = $("#entidade").val();
		
		$.ajax(
            {
                url: pathnameX+"fones/excluir/"+id,
                type: 'GET',
                dataType: "JSON",
            success: function(response)
            {                    	
            	$("#tabelaFone > tbody > tr").remove();	
				loadTelefones(entidade);
				$.each(telefones, function(index, value){
					$('#tabelaFone > tbody').append('<tr><td class="center">'+value.descricao+'</td><td>'+value.numero+'</td><td class="center"><a class="excluirFone" data-id="'+index+'"><i class="fa fa-trash"></i> Excluir</a></td></tr>');
				});
            },
            error: function(response)
            {
            }
        });

	});   

	$('#modalFone').on('show.bs.modal', function (e) {
		$("#nu_fone_fon").val("");
		$("#erroFone").html("");
		$("#nu_fone_fon").focus();
	});

	// $('#tipoServico').change(function(){
	// 	$('#taxa-honorario-cliente').val($(this).children("option:selected").data('cliente').toString().replace('.',','));
	// 	$('#taxa-honorario-correspondente').val($(this).children("option:selected").data('correspondente').toString().replace('.',','));
	// });

	$('.btn_sigla').click(function(){ $("#processamento").modal('show'); });

	$("#btnSalvarHonorariosProcesso").click(function (){

		var processo = $("#cd_processo_pro").val();		
		var servico  = $("#tipoServico").val();
		var valor_cliente  = $("#taxa-honorario-cliente").val();
		var valor_correspondente  = $("#taxa-honorario-correspondente").val();

		var dados = {servico: servico, valor_cliente: valor_cliente, valor_correspondente: valor_correspondente};

		$.ajax(
        {
        	type: "POST",
            url: pathname+"/processo/honorarios/salvar",
            data: {
                "_token": $('meta[name="token"]').attr('content'),
                "dados": JSON.stringify(dados),
                "processo": processo
            },
            beforeSend: function()
            {
            	$("#processamento").modal('show');
            },
            success: function(response)
            {
            	console.log("Sucesso");
            	window.location.href = pathname+"/processos/despesas/"+processo
            },
		   	error: function(response)
		   	{
		   		console.log("Erro");
		   		location.reload();
		   	}
        });

	});

	/*$("#btnSalvarHonorariosProcesso").click(function (){

		var valores = new Array();
		var processo = $("#cd_processo_pro").val();		

		$('.taxa-honorario').each(function(i,obj){

			var valor = $(this).val();
    		var servico = $(this).data("servico");
			var entidade = $(this).data("entidade");
			var oldvalue = $(this).data("oldvalue");

			if(oldvalue != '' || valor.trim() != ''){

				var dados = {servico: servico, entidade: entidade, valor: valor};
				valores.push(dados);

    		}
		});

		$.ajax(
        {
        	type: "POST",
            url: pathname+"/processo/honorarios/salvar",
            data: {
                "_token": $('meta[name="token"]').attr('content'),
                "valores": JSON.stringify(valores),
                "processo": processo
            },
            beforeSend: function()
            {
            	$("#processamento").modal('show');
            },
            success: function(response)
            {
            	console.log("Sucesso");
            	window.location.href = pathname+"/processos/despesas/"+processo
            },
		   	error: function(response)
		   	{
		   		console.log("Erro");
		   		location.reload();
		   	}
        });


	});
*/
	$("#limparValoresDespesa").click(function(){

		$('.taxa-despesa').each(function(i, obj) {

			 $(this).val('');
		});

	})

	$("#btnSalvarDespesasProcesso").click(function (){

		var valores = new Array();
		var processo = $("#cd_processo_pro").val();
		
		$('.taxa-despesa').each(function(i, obj) {
    		
    		var valor = $(this).val();
    		var despesa = $(this).data("despesa");
			var entidade = $(this).data("entidade");
			var oldvalue = $(this).data("oldvalue");
			var reembolso = 'N';

			if($('#'+$(this).data("identificador")).prop("checked") == true)
				reembolso = 'S';

    		if(oldvalue != '' || valor.trim() != ''){

				var dados = {despesa: despesa, entidade: entidade, valor: valor, reembolso: reembolso};
				valores.push(dados);

    		}
    		
		});

		console.log(valores);

		$.ajax(
        {
        	type: "POST",
            url: pathname+"/processo/despesas/salvar",
            data: {
                "_token": $('meta[name="token"]').attr('content'),
                "valores": JSON.stringify(valores),
                "processo": processo
            },
            beforeSend: function()
            {
            	$("#processamento").modal('show');
            },
            success: function(response)
            {
            	console.log("Sucesso");
            	window.location.href = pathname+"/processos/despesas/"+processo
            },
		   	error: function(response)
		   	{
		   		console.log("Erro");
		   		location.reload();
		   	}
        });
        
	});	

	$("#btnSalvarHonorarios").click(function (){

		var valores = new Array();
		var entidade = $("#cd_entidade").val();
		var cliente = $("#cd_cliente").val();
		
		$('.taxa-honorario').each(function(i, obj) {
    		
    		var valor = $(this).val();
    		var servico = $(this).data("servico");
			var cidade = $(this).data("cidade");;

    		if(valor){
    			
				var dados = {servico: servico, cidade: cidade, valor: valor};
				valores.push(dados);

    		}
    		
		});
		
		$.ajax(
        {
        	type: "POST",
            url: pathname+"/cliente/honorarios/salvar",
            data: {
                "_token": $('meta[name="token"]').attr('content'),
                "valores": JSON.stringify(valores),
                "entidade": entidade
            },
            beforeSend: function()
            {
            	$("#processamento").modal('show');
            },
            success: function(response)
            {
            	console.log("Sucesso");
            	window.location.href = pathname+"/cliente/honorarios/"+cliente;
            },
		   	error: function(response)
		   	{
		   		console.log("Erro");
		   		location.reload();
		   	}
        });
        
	});	

	$("#btnSalvarHonorariosCorrespondente").click(function (){

		var valores = new Array();
		var entidade = $("#cd_entidade").val();
		var correspondente = $("#cd_correspondente").val();
		
		$('.taxa-honorario').each(function(i, obj) {
    		
    		var valor = $(this).val();
    		var servico = $(this).data("servico");
			var cidade = $(this).data("cidade");;

    		if(valor){
    			
				var dados = {servico: servico, cidade: cidade, valor: valor};
				valores.push(dados);

    		}
    		
		});
		
		$.ajax(
        {
        	type: "POST",
            url: "http://localhost/dmk/public/correspondente/honorarios/salvar",
            data: {
                "_token": $('meta[name="token"]').attr('content'),
                "valores": JSON.stringify(valores),
                "entidade": entidade
            },
            beforeSend: function()
            {
            	$("#processamento").modal('show');
            },
            success: function(response)
            {
            	console.log("Sucesso");
            	window.location.href = "http://localhost/dmk/public/correspondente/honorarios/"+correspondente;
            },
		   	error: function(response)
		   	{
		   		console.log("Erro");
		   		location.reload();
		   	}
        });
        
	});	

	$("#grupo_cidade").change(function(){

		var grupo = $("#grupo_cidade option:selected").val();

		$.ajax(
            {
                url: pathname+"/grupo/cidade/"+grupo,
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
                $('#cidade').append('<option selected value="0">Todas</option>');
                
                $.each(response,function(index,value){
                    $('#cidade').append('<option value="'+value.cd_cidade_cde+'">'+value.cidade.nm_cidade_cde+'</option>');                   
                });  

                $('#cidade').trigger('change');     
                $('#cidade').prop( "disabled", false );        
            },
            error: function(response)
            {
            }
        });
	});

	$(".toda-cidade").click(function(){

		var obj_cidade = $(this).data("cidade");
		var obj_servico = $(this).data("servico");
		var obj_valor = $(this).closest("td")    
                          .find(".taxa-honorario")
                          .val()
                          .replace(".", ",");

		$(".taxa-honorario").each(function(){

			var servico = $(this).data("servico");

			if(obj_servico === servico){
				$(this).val(obj_valor);
			}
			
		});

	});

	$(".todo-servico").click(function(){

		var obj_cidade = $(this).data("cidade");
		var obj_servico = $(this).data("servico");
		var obj_valor = $(this).closest("td")    
                          .find(".taxa-honorario")
                          .val()
                          .replace(".", ",");

		$(".taxa-honorario").each(function(){

			var cidade = $(this).data("cidade");
			
			if(obj_cidade === cidade){
				$(this).val(obj_valor);
			}
			
		});

	});

	$(".toda-tabela").click(function(){

		var obj_valor = $(this).closest("td")    
                          .find(".taxa-honorario")
                          .val()
                          .replace(".", ",");

		$(".taxa-honorario").each(function(){
			$(this).val(obj_valor);			
		});

	});

	$('.dialog_clone').click(function() {
		$('#dialog_clone_text').data('url',$(this).attr('href')).dialog('open');
		return false;
	});
	
	$('#dialog_clone_text').dialog({
		autoOpen : false,
		width : 600,
		resizable : false,
		modal : true,
		title : 'Deseja clonar esse processo?',
		buttons : [{
			html : "<i class='fa fa-clone fa-la'></i>&nbsp; Continuar",
			"class" : "btn sa-btn-danger",
			click : function() {
				window.location= $(this).data('url');
			}
		}, {
			html : "<i class='fa fa-times'></i>&nbsp; Cancelar",
			"class" : "btn btn-default",
			click : function() {
				$(this).dialog("close");
			}
		}]
	});

	

});