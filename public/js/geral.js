$(document).ready(function() {


	/** ======================== Masks ========================   **/

	$('.data_nascimento').mask('00/00/0000');
	$('.data_admissao').mask('00/00/0000');
	$('.cpf').mask('000.000.000-00');


	/** =======================================================   **/

	$(".tipo-pessoa").click(function(){
	    if($('input:radio[name=tipo-pessoa]:checked').val() == 1){
	        $(".box-pessoa-fisica").css('display','block');
	        $(".box-pessoa-juridica").css('display','none');
	    }else{
	    	$(".box-pessoa-fisica").css('display','none');
	        $(".box-pessoa-juridica").css('display','block');
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
		   		console.log(response);
		   	}
        });

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

	
});