// @ts-nocheck
$( document ).ready(function() {
	
	
	attachElements();
	
	//
	
	
	loadEstado();
	prospectMgr = new ProspectoManager();
	prospectMgr.load();
});
var prospectMgr;




var fnSuccessFormResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#id_estado").empty();
		$("#id_estado")
				 .append($("<option></option>")
				 .attr("value","")
				 .text("Seleccionar..."));
		for (i=0; i<result.data.length;i++) {
			$("#id_estado")
				 .append($("<option></option>")
				 .attr("value",result.data[i].idProspectoEstado)
				 .text(result.data[i].nombre));
		}
	} else {
		errManager.showError(EXISTS_RECORD,"el código de Cliente");
	}
};

var fnSuccessFormSubEstadoResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#id_sub_estado").empty();
		$("#id_sub_estado")
				 .append($("<option></option>")
				 .attr("value","")
				 .text("Seleccionar..."));
		for (i=0; i<result.data.length;i++) {
			$("#id_sub_estado")
				 .append($("<option></option>")
				 .attr("value",result.data[i].idProspectoEstado)
				 .text(result.data[i].nombre));
		}
	} else {
		errManager.showError(EXISTS_RECORD,"el código de Cliente");
	}
};
var fnErrorFormResponse = function (result) {
	$("#panel_usuario").LoadingOverlay("hide");

};

function attachElements() {
	$("[attach-click]").unbind();
	$("[attach-change]").unbind();
	$("[attach-click]").click(function () {
		var id 		= $(this).attr('attach-click');
		var type	= $(this).attr('attach-type');
		console.log(type)
		if (type=="history") {
			prospectMgr.history(id);
		} else if (type=="saldo") {
			iniVerSaldo(id);
		} else if (type=='cleanFormSearch') {
			cleanFormSearch();
		} else if (type=='procesar') {
			procesar(id);
		} else if (type=='buscar') {
			prospectMgr.load();
		} else if (type=='filter') {
			filter(id);
		} else if (type=='sendMsg') {
			prospectMgr.sendMsg(id);
		} else if (type=='volLlamar') {
			prospectMgr.volLlamar(id);
		} else if (type=='volUrg') {
			prospectMgr.volUrg(id);
		} else if (type=='guardarVolvLlamar') {
			prospectMgr.guardarVolvLlamar();
		} else if (type=='filter-todos') {
			filterTodos(this);
		} else if (type=="req") {
			cambiarEstado(id,type);
		} else if (type=="zon") {
			cambiarEstado(id,type);
		} else if (type=="amb") {
			cambiarEstado(id,type);
		} else if (type=="ni") {
			cambiarEstado(id,type);
		} else if (type=="err") {
			cambiarEstado(id,type);
		}
	});
	$("[attach-change]").change(function () {
		var id 		= $(this).attr('attach-change');
		var type	= $(this).attr('attach-type');
		console.log(type)
		if (type=="estadoChange") {
			estadoChange(this);
		}
		
	});
	if ($.fn.datepicker) {
		$('.datepicker').each(function() {

			var $this = $(this),
				dataDateFormat = $this.attr('data-dateformat') || 'dd.mm.yy';

			$this.datepicker({
				dateFormat : dataDateFormat,
				prevText : '<i class="fa fa-chevron-left"></i>',
				nextText : '<i class="fa fa-chevron-right"></i>',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
		        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
		        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá']
			});
			
			//clear memory reference
			$this = null;
		});
	}
}
function procesar(id) {
	$("#frm-prospect").attr("action","/admin/modulos/prospectos/php/procesar_prospect.php");
	$("#id_prospecto_form").val(id);
	$("#frm-prospect").submit();
}
function cleanFormSearch() {
	//$("#id_proyecto").val("").trigger('change');

	$("#id_origen").val("");
	$("#id_estado").val("");
	$("#id_sub_estado").val("");
}

function loadEstado() {
	var data 				= new Object();
	data.tipo 				= "load";
	loadData('/ajax/json.prospectoestados.php',fnSuccessFormResponse,data,fnErrorFormResponse) 

}
function estadoChange(obj) {
	if ($(obj).val()>0) {
		$("#id_sub_estado").removeAttr("disabled");
		var data 				= new Object();
		data.tipo 				= "load";
		data.id_estado_padre	= $(obj).val();
		loadData('/ajax/json.prospectoestados.php',fnSuccessFormSubEstadoResponse,data,fnErrorFormResponse) 
	} else {
		$("#id_sub_estado").attr("disabled","disabled");
	}
}
function filter(id) {
	if ($("#"+id).val()==1) {
		$("#"+id).val("");
		$("#btn-"+id).removeClass("btn-primary");
		$("#btn-"+id).addClass("btn-default");
	} else {
		$("#"+id).val(1);
		$("#btn-"+id).removeClass("btn-default");
		$("#btn-"+id).addClass("btn-primary");
	}
	if ($("#filter-todos").val()==1) {
		filterTodos();
	}
}
function filterTodos(obj) {
	console.log($("#filter-todos").val());
	if ($("#filter-todos").val()==1) {
		$("#filter-todos").val("");
		
		$("#btn-filter-todos").removeClass("btn-primary");
		$("#btn-filter-todos").addClass("btn-default");
		
	} else {
		$("#filter-todos").val(1);
		$("#btn-filter-todos").addClass("btn-primary");
		$("#btn-filter-todos").removeClass("btn-default");
		
		$("#btn-llamadopend").removeClass("btn-primary");
		$("#btn-llamadopend").addClass("btn-default");
		$("#btn-propenviada").removeClass("btn-primary");
		$("#btn-propenviada").addClass("btn-default");
		$("#llamadopend").val("");
		$("#propenviada").val("");
		
	}
}
function cambiarEstado(id,type) {
	var data 				= new Object();
	data.tipo 				= "changeEstado";
	data.id_prospecto		= id;
	data.str_estado			= type;
	loadData('/admin/modulos/ventas/ajax/json.prospectos.php',fnSuccessFormSubEstadoResponse,data,fnErrorFormResponse);
}