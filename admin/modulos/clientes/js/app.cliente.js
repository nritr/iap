// @ts-nocheck
$(document).ready(function() {
			
	pageSetUp();
	
	$("#btn_submit").click(function() {
		guardar(0);
	});
	$("#btn_veritifar_cuit").click(function() {

		var cuit = $("#cuit").val();
		if (cuit.length>=7) {
			var data = new Object();
			data.cuit = cuit;
			loadData('/ajax/json.consulta-cuit.php',fnSuccessFormResponseCuit,data,fnErrorFormResponse) 
			$.LoadingOverlay("show");

		} else {
			$.notify("DNI/CUIT Incorrecto",{globalPosition: 'top center',className: 'error'});
		}
		


	});
	
	//
	var table = $('#datatable-buttons').DataTable({
		lengthChange: false,
		buttons: ['copy', 'excel', 'pdf'],
		"ordering": false
	});

	table.buttons().container()
			.appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
	
	setScreenPlantas($("#id_cliente_planta").val());
	attachElements();
});
var tipo_volver="";//si es 1, al guardar vuelve a la misma pagina
var id_planta="";
/*function iniAdminPlantas() {
	$("#modal-plantas").modal("show")
	$("#nombre-planta").val("");
	$("#direccion").val("");
	$("#observacion-planta").val("");
}
function addPlanta() {
	var result = $("#frm-add-planta").parsley().validate();

	if (result) {
		var data = $("#frm-add-planta").serializeArray();
		loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormAddPlantaResponse,getFormData(data),fnErrorFormResponse) 
		//$.LoadingOverlay("show");
	}
}*/
function guardar(tipo) {
	tipo_volver=tipo
	var result = $("#contact-form").parsley().validate();

	if (result) {
		var data = $("#contact-form").serializeArray();
		loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormResponse,getFormData(data),fnErrorFormResponse) 
		$.LoadingOverlay("show");
	}
}
/*function deletePlanta(id) {
	id_planta = id;
	var data 		= new Object();
	data.tipo		= 'detele-planta';
	data.id_planta	= id;
	loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormDeletePlantaResponse,data,fnErrorFormResponse) 
}*/
var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	$.LoadingOverlay("hide");
	if (errManager.isSuccess()) {
		if (tipo_volver==1) {
			document.location.href='/admin/modulos/clientes/php/cliente.php?id_cliente='+result.data.idCliente;
		} else {
			document.location.href='/admin/modulos/clientes/php/clientes.php';
		}
	} else {
		errManager.showError(EXISTS_RECORD,"el código de Cliente");
	}
};
/*
var fnSuccessFormAddPlantaResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#nombre-planta").val("");
		$("#direccion").val("");
		$("#observacion-planta").val("");
		setScreenPlantas()
	} else {
		$.LoadingOverlay("hide");
		errManager.showError();
	}
};
function setScreenPlantas() {
	if ($("#id_cliente").val()>0) {
		var data 		= new Object();
		data.tipo		= 'list-plamntas-by-client';
		data.id_cliente	= $("#id_cliente").val();
		loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormListPlantasResponse,data,fnErrorFormResponse) 
	}
}
var fnSuccessFormListPlantasResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#body-list-plantas").empty();
		for (i=0;i<result.data.length;i++) {
			var idPlanta 			= result.data[i].idPlanta;
			var nombre 				= result.data[i].nombre;
			var direccion			= result.data[i].direccion;
			var observacion			= (result.data[i].observacion ? result.data[i].observacion : "");
			var newRowContent = "<tr id='tr-"+idPlanta+"'>"+
												"<td>"+nombre+"</td>"+
												"<td>"+direccion+"</td>"+
												"<td>"+observacion+"</td>"+
												"<td>"+
													"<form action='cliente.php' method='post'>"+
														"<button type='button' attach-type='delete-planta' attach-click='"+idPlanta+"' class='btn btn-secondary waves-effect' >" +
																"<span class='fa fa-trash'></span> " +
																"Eliminar" +
														"</button>"+
													"</form>"+
												"</td>"+
		                                    "</tr>";
			$("#body-list-plantas").append(newRowContent);
			attachElements();
		}
	} else {
		errManager.showError();
	}
	
};
var fnSuccessFormDeletePlantaResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#tr-"+id_planta).empty();
	} else {
		errManager.showError();
	}
	
}*/
function attachElements() {
	$("[attach-click]").unbind();
	$("[attach-click]").click(function () {
		var id 		= $(this).attr("attach-click");
		var type 	= $(this).attr("attach-type");
		console.log(type)
		if (type=='plantas') {
			iniAdminPlantas($("#id_cliente_planta").val());
		} else if (type=='add-planta') {
			iniAddPlanta();
		} else if (type=='guardarycontinuar') {
			guardar(1);
		} else if (type=='delete-planta') {
			deletePlanta(id);
		} else if (type=='editar-planta') {			
			iniEditarPlanta(id);
		} else if (type=='volver-planta-list') {
			iniVolverPlantaList();
		} else if (type=='guardar-planta') {
			addPlanta();
		}
	})
}

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};