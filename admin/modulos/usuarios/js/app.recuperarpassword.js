// @ts-nocheck
$(document).ready(function() {

	attachElements();
	
	
});

var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	$.LoadingOverlay("hide");
	if (errManager.isSuccess()) {
		activeStep2()

	} else {
		errManager.showError();
	}
	
};

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};

function iniRecuperar() {

	var result = $("#login-form").parsley().validate();

	if (result) {
		var data = $("#login-form").serializeArray();
		loadData('/admin/modulos/usuarios/json/json.usuario.php',fnSuccessFormResponse,getFormData(data),fnErrorFormResponse) 
		$.LoadingOverlay("show");
	}

}
function attachElements() {
	$("[hidden-on-show]").hide();
	$("[attach-click]").unbind();
	$("[attach-click]").click(function () {
		var id 			= $(this).attr("attach-click");
		var type		= $(this).attr("attach-type");

		if (type=='recuperar') {
			iniRecuperar();
		}
	});
}
function activeStep2() {
	$("[step1]").hide(500);
	$("[step2]").show(500);
}
function activeError() {
	$("[error-message]").show(500);
}
function hideError() {
	$("[error-message]").hide(500);
}