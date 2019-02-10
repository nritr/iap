// @ts-nocheck
$( document ).ready(function() {
	
	
	attachElements();
	
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

function iniEliminar(id) {
	$('#id_cliente').val(id)
	$('#modal-eliminar').modal('show');
}
function iniVerSaldo(id) {
	$("#id_cliente_send").val(id)
	$("#ver_saldo").val(1);//indico que hizo click en ver saldo
	$("#frm_user").submit();
}



function eliminar() {
	//alert ()
	var data = new Object();
	data.tipo = "delete";
	data.id_cliente = $('#id_cliente').val();
	loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormResponse,data,fnErrorFormResponse) 
	$("#panel_usuario").LoadingOverlay("show");
}

var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	$.LoadingOverlay("hide");
	if (errManager.isSuccess()) {
		document.location.href='/admin/modulos/clientes/php/clientes.php';
	} else {
		errManager.showError(EXISTS_RECORD,"el código de Cliente");
	}
};

var fnErrorFormResponse = function (result) {
	$("#panel_usuario").LoadingOverlay("hide");

};

function attachElements() {
	$("[attach-click]").unbind();
	$("[attach-click]").click(function () {
		var id 		= $(this).attr('attach-click');
		var type	= $(this).attr('attach-type');
		console.log(type)
		if (type=="eliminar") {
			iniEliminar(id);
		} else if (type=="saldo") {
			iniVerSaldo(id);
		} else if (type=='cleanFormSearch') {
			cleanFormSearch();
		}
	});
}

function cleanFormSearch() {
	$("#id_proyecto").val("").trigger('change');

	$("#nombre").val("");
	$("#celular").val("");
	$("#domicilio_part").val("");
	$("#domicilio_laboral").val("");

}
