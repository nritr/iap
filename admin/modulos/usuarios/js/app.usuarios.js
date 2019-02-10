// @ts-nocheck
$( document ).ready(function() {
	
	
	//se agrega funcionalidad al boton eliminar de la tabla de movimientos
	$("[eliminar]").click(function() {
		var id = $(this).attr('eliminar');
		$('#id_usuario').val(id)
		$('#modal-eliminar').modal('show');
	});
	
	$("[cambiarpass]").click(function() {
		var id = $(this).attr('cambiarpass');
		$("#modal_name_pass").html($("#nombre_usuario_"+id).html());
		$('#id_usuario_pass').val(id)
		$('#change-password').modal('show');
	});
	
	//
	var table = $('#datatable-buttons').DataTable({
		lengthChange: false,
		pageLength: 25,
		pagingType: "full_numbers",
		lengthChange: true,
		buttons: ['copy', 'excel', 'pdf'],
		"ordering": true,
		"order": [[ 1, "asc" ]]
	});

	table.buttons().container()
			.appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
		

	
	
	
});

function eliminar() {
	//alert ()
	var data = new Object();
	data.tipo = "delete";
	data.id_usuario = $('#id_usuario').val();
	loadData('/admin/modulos/usuarios/json/json.usuario.php',fnSuccessFormResponse,data,fnErrorFormResponse) 
	$("#panel_usuario").LoadingOverlay("show");
}

function cambiarClave() {
	var result = $("#frm_change_pass").parsley().validate();
	if (result) {
		var data = $("#frm_change_pass").serializeArray();
		loadData('/admin/modulos/usuarios/json/json.usuario.php',fnSuccessFormResponsePass,getFormData(data),fnErrorFormResponse) 
		$("#modal-content").LoadingOverlay("show");
	}
}
var fnSuccessFormResponse = function (result) {
	$.LoadingOverlay("hide");
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$('#confirm-delete').modal('hide');
		document.location.href="usuarios.php"
	} else {
		
		errManager.showError();
	}
};
var fnSuccessFormResponsePass = function (result) {
	if (result.result==1) {
		$('#change-password').modal('hide');
		$.smallBox({
			title : "Clave Modificada",
			//content : "<i class='fa fa-clock-o'></i> <i>2 seconds ago...</i>",
			color : "#296191",
			//iconSmall : "fa fa-thumbs-up bounce animated",
			timeout : 3000
		});
	} else {
		alert ("Error Interno: " + result.error)
	}
};
var fnErrorFormResponse = function (result) {
	//$("#section_contacto_response").show();
	//$("#section_contacto_form").hide();
	$("#panel_usuario").LoadingOverlay("hide");

};