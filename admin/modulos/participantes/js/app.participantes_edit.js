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
	$("[attach-click]").click(function () {
		var id 		= $(this).attr("attach-click");
		var type 	= $(this).attr("attach-type");
		if (type=='') {
			
		}
	})
	
	
	$("[ticket-days]").each (function () {
		if (exixtsDayTicket($(this).val())) {
			$(this)[0].checked = true;
		}
	})
	$("#DATOS-COUNTRY").val($("#country").val());
	$("#DATOS-JOBTITLE").val($("#job_title").val());
	$("#DATOS-CONTRIBUTE-TYPE").val($("#contribute-type").val());
	
	if ($.fn.datepicker) {
		$('.datepicker').each(function() {

			var $this = $(this),
				dataDateFormat = $this.attr('data-dateformat') || 'mm.dd.yy';

			$this.datepicker({
				dateFormat : dataDateFormat,
				prevText : '<i class="fa fa-chevron-left"></i>',
				nextText : '<i class="fa fa-chevron-right"></i>',
			});
			
			//clear memory reference
			$this = null;
		});
	}
}
function exixtsDayTicket(str) {
	var data = $("#fechas_ticket").val();
	var arr = data.split(",")
	for (ind=0;ind<arr.length;ind++) {
		if (arr[ind]==str) {
			return true;
		}
	}
	return false;
}