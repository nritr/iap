$( document ).ready(function() {

	$("#btn_submit").click(function() {
		bContinue = false;
		send()
	});
	$("#btn_submit_continue").click(function() {
		bContinue = true;
		send()
	});
	
	$("#btn_submit").click(function() {

		
	});

});

var bContinue=null;

function send() {
	var result = $("#contact-form").parsley().validate();

	if (result) {
		this.disabled=true;
		var data = $("#contact-form").serializeArray();
		loadData('/admin/modulos/generico/json/json.main.php',fnSuccessFormResponse,getFormData(data),fnErrorFormResponse) 
		$.LoadingOverlay("show");
	}
}
var fnSuccessFormResponse = function (result) {
	$.LoadingOverlay("hide");
	//console.log(result);
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		if (!bContinue)
			document.location.href='main.php';
		else
			document.location.href='edit.php';
	} else {
		errManager.showError(23000,"El Dato Paramétrico");
	}

};

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
	//alert ("error")
};

