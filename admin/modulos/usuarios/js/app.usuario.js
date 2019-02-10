// @ts-nocheck
$(document).ready(function() {
			
	pageSetUp();
	
	$("#btn_submit").click(function() {

		var result = $("#contact-form").parsley().validate();

		if (result) {
			var data = $("#contact-form").serializeArray();
			loadData('/admin/modulos/usuarios/json/json.usuario.php',fnSuccessFormResponse,getFormData(data),fnErrorFormResponse) 
			$.LoadingOverlay("show");
		}
	});
	
	$("#id_rol").change(function () {
		validateCombos(this);
	})
	
	attachElements();
});

var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$('#confirm-delete').modal('hide');
		document.location.href='usuarios.php';
	} else {
		$.LoadingOverlay("hide");
		errManager.showError(EXISTS_RECORD,"El Email");
	}
	
};

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};
function attachElements() {
	if ($("#id_usuario").val()>0) {
		validateCombos($("#id_rol"))
	}
	
}
function validateCombos(obj) {
	var tipoAsig = $('option:selected', obj).attr("tipo-asignacion");

	if (tipoAsig==TIPO_ASIGNACION_CENTRAL) {
		$("#id_region").val(REGION_CENTRAL)
		$("#id_region")[0].disabled = true;
		$("[panel-region]").show();
		$("#id_region").attr("required","required");
	} else if (tipoAsig==TIPO_ASIGNACION_REGION) {
		$("#id_region")[0].disabled = false;
		$("[panel-region]").show();
		$("#id_region").attr("required","required");
	} else {
		$("[panel-region]").hide();
		$("#id_region").removeAttr("required");
	}
}