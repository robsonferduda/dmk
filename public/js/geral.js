$(document).ready(function() {
			
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

		var id = $(this).closest('tr').find('td[data-id]').data('id');

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
        var token = $(this).data("token");
        $.ajax(
        {
            url: "../areas/"+id,
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

});