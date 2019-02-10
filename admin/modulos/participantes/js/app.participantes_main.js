// @ts-nocheck
$( document ).ready(function() {
	
	
	//se agrega funcionalidad al boton eliminar de la tabla de movimientos
	$("[eliminar]").click(function() {
		var id = $(this).attr('eliminar');
		$('#id_usuario').val(id)
		$('#modal-eliminar').modal('show');
	});
	

	//
	var table = $('#datatable-buttons').DataTable({
		lengthChange: false,
		pageLength: 25,
		pagingType: "full_numbers",
		lengthChange: true,
		buttons: ['copy', 'excel', 'pdf']
	});

	table.buttons().container()
			.appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
		

	
	attachElements();
	
});

function eliminar() {
	//alert ()
	var data = new Object();
	data.tipo = "delete";
	data.id_usuario = $('#id_usuario').val();
	loadData('/admin/modulos/usuarios/json/json.usuario.php',fnSuccessFormResponse,data,fnErrorFormResponse) 
	$("#panel_usuario").LoadingOverlay("show");
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

var fnErrorFormResponse = function (result) {
	//$("#section_contacto_response").show();
	//$("#section_contacto_form").hide();
	$("#panel_usuario").LoadingOverlay("hide");

};

function attachElements() {
	$("[attach-change]").unbind();
	$("[attach-change]").change(function () {
		var id 		= $(this).attr("attach-change");
		var type 	= $(this).attr("attach-type");
		
		if (type=='filter') {
			filter(this);
		}
	})
	$("[attach-click]").click(function () {
		var id 		= $(this).attr("attach-click");
		var type 	= $(this).attr("attach-type");
		
		if (type=='buscar') {
			buscar();
		} else if (type=='ver') {
			ver(id);
		}
	});
	if ($("#filterload").val()!="") {
		$("#filter").val($("#filterload").val());
		filter($("#filter")[0]);
		$("[panel='" + $("#filterload").val() + "']").each (function () {
			console.log("pasa")
			$(this).find("[input-filter]").val($("#value").val());
		});
	}
}

function filter(obj) {
	$("[panel]").hide();
	$("[panel='" + $(obj).val() + "']").show();
	$("[input-filter]").each (function () {
		//console.log("")
		$(this).val("");
	})
}
function buscar() {
	$("[input-filter]").each (function () {
		if ($(this).val()!="") {
			console.log($(this).val())
			$("#value").val($(this).val());
		}
	})
	$("#frm_filter_clientes").submit();
}
function ver(id) {
	$("#id_datos").val(id)
	$("#frm_edit").submit();
}