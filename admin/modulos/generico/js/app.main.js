$( document ).ready(function() {

	$("[eliminar]").click(function() {

		id_dato = $(this).attr('eliminar');
		$('#confirm-delete').modal('show');
		var name = $(this).attr('data-name');
		$("#nombre_reg_elim").html(name);
	});
	
	$("[activar]").click(function() {

		id_dato = $(this).attr('activar');
		$('#confirm-activar').modal('show');

	});

	
	//
	var table = $('#datatable-buttons').DataTable({
		lengthChange: false,
		buttons: ['copy', 'excel', 'pdf'],
		"ordering": false
	});

	$("[filtros]").change(function() {
		$.LoadingOverlay("show");
		$("#frm_filter_rubros").submit();
	});
	
	table.buttons().container()
			.appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
});
var id_dato="";
function eliminar() {

	$('#confirm-delete').modal('hide');
	var data = new Object();
	data.tipo = "delete";

	data.id_dato = id_dato;
	loadData('/admin/modulos/generico/json/json.main.php',fnSuccessFormResponse,data,fnErrorFormResponse);
	$.LoadingOverlay("show");
}
function activar() {

	$('#confirm-activar').modal('hide');
	var data = new Object();
	data.tipo = "activar";

	data.id_rubro = id_rubro;
	loadData('/admin/modulos/stock/json/json.rubros.php',fnSuccessFormResponseActive,data,fnErrorFormResponse);
	$.LoadingOverlay("show");
}
var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		document.location.href='main.php';
	} else {
		errManager.showError();
	}
	$.LoadingOverlay("hide");
};
var fnSuccessFormResponseActive = function (result) {
	if (result.result==1) {
		document.location.href='rubros.php'
		
	} else {
		alert ("Error Interno: " + result.error)
	}
	$.LoadingOverlay("hide");
};
var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};