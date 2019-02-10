// @ts-nocheck
$(document).ready(function() {
			
	pageSetUp();
	
	$("#btn_submit").click(function() {
		guardar(0);
	});


	attachElements();
});
var topeMonto;
var localidadSelected;
var localidadLaboralSelected;
var listCuotas;
function guardar(tipo) {
	tipo_volver=tipo
	var result = $("#contact-form").parsley().validate();

	if (result) {
		var data = $("#contact-form").serializeArray();
		loadData('/admin/modulos/clientes/json/json.cliente.php',fnSuccessFormResponse,getFormData(data),fnErrorFormResponse) 
		$.LoadingOverlay("show");
	}
}

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

function attachElements() {
	$("[attach-click]").unbind();
	$("[attach-change]").unbind();
	$("[attach-click]").click(function () {
		var id 		= $(this).attr("attach-click");
		var type 	= $(this).attr("attach-type");
		console.log(type)
		if (type=='plantas') {
			iniAdminPlantas($("#id_cliente_planta").val());
		} else if (type=='direccionAlert') {
			direccionAlert(this);
		} else if (type=="asignarPlan") {
			asignarPlan();
		}
	})
	$("[attach-change]").change(function () {
		var id 		= $(this).attr("attach-change");
		var type 	= $(this).attr("attach-type");
		console.log(type)
		if (type=='processVivienda') {
			processVivienda();
		} else if (type=='chgLugarEntrega') {
			chgLugarEntrega(this);
		} else if (type=='chgMonto') {
			chgMonto(this);
		} else if (type=='chgFrecuencia') {
			chgFrecuencia();
		} else if (type=='chgCuota') {
			chgCuota();
		}
	})
	$('#id_localidad').select2({
        placeholder: 'Buscar Localidad',
        ajax: {
            url: "/ajax/json.localidades.php",
            dataType: 'json',
            quietMillis: 100,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }

        },
        initSelection: function(element, callback) {
            return $.getJSON("/ajax/json.localidades.php?id=" + (element.val()), null, function(data) {

                    return callback(data);

            });
        }

        
    });
	$('#id_localidad').change(function () {
		if ($(this).val()>0) {
			$("#autocomplete")[0].readOnly = false;
			loadPoligon($(this).val());
			setLocalidadSelected($(this).val());
		} else {
			$("#autocomplete")[0].readOnly = true;
		}
	})
	$('#id_localidad_laboral').select2({
        placeholder: 'Buscar Localidad',
        ajax: {
            url: "/ajax/json.localidades.php",
            dataType: 'json',
            quietMillis: 100,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }

        },
        initSelection: function(element, callback) {
            return $.getJSON("/ajax/json.localidades.php?id=" + (element.val()), null, function(data) {

                    return callback(data);

            });
        }

        
    });
	$('#id_localidad_laboral').change(function () {
		if ($(this).val()>0) {
			setLocalidadSelected($(this).val(),fnSuccessFormLoadLocalidadLabResponse);
		} else {
			//TODO:
		}
	})
	$('#id_actividad_laboral').select2({
        placeholder: 'Buscar Actividad Lab.',
        ajax: {
            url: "/ajax/json.tipotrabajos.php",
            dataType: 'json',
            quietMillis: 100,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }

        },
        initSelection: function(element, callback) {
            return $.getJSON("/ajax/json.tipotrabajos.php?id=" + (element.val()), null, function(data) {

                    return callback(data);

            });
        }

        
    });
	$('#recorrido').select2({
        placeholder: 'Buscar Recorrido',
        ajax: {
            url: "/ajax/json.recorridos.php",
            dataType: 'json',
            quietMillis: 100,
            data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
            results: function (data, page) {
                return { results: data.results };
            }

        },
        initSelection: function(element, callback) {
            return $.getJSON("/ajax/json.recorridos.php?id=" + (element.val()), null, function(data) {

                    return callback(data);

            });
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
			});
			
			//clear memory reference
			$this = null;
		});
	}
	processVivienda();
	
}

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};

function direccionAlert(obj) {
	if (obj.readOnly) {
		alert ("Antes de ingresar la dirección debe completar la Localidad")
	}
}
function loadPoligon(id_region) {
	var data 		= new Object();
	data.tipo 		= "load_by_id";
	data.id_region 	= id_region; 
	loadData('/ajax/json.region_json.php',fnSuccessFormLoadPoligonResponse,data,fnErrorFormResponse);
	$.LoadingOverlay("show");
}

var fnSuccessFormLoadPoligonResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	$.LoadingOverlay("hide");
	if (errManager.isSuccess()) {
		poligon = result.data[0].poligon;
		bermudaTriangle = addPoligon(poligon);
        
        bermudaTriangle.addListener('click', function(event) {
        	$("#lat_new").val(event.latLng.lat());
        	$("#lng_new").val(event.latLng.lng());
            addMarker(event.latLng.lat(), event.latLng.lng(),false);
          });
	} else {
		errManager.showError(EXISTS_RECORD,"el código de Cliente");
	}
}
var fnSuccessFormLoadLocalidadResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		localidadSelected = result.data;
		setEntregaEn();
	} else {
		errManager.showError();
	}
}
var fnSuccessFormLoadLocalidadLabResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		localidadLaboralSelected = result.data;
		setEntregaEn();
	} else {
		errManager.showError();
	}
}
var fnSuccessFormLoadSetPlanResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		
	} else {
		errManager.showError();
	}
}
var fnSuccessFormLoadProspectResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		topeMonto = result.data;
		populateCuotas(result.data)
	} else {
		errManager.showError();
	}
}
var fnSuccessFormLoadMontosResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#id_monto_plan").empty();
		$("#id_monto_plan").append($("<option></option>")
				 .attr("value","")
				 .text("Seleccionar..."));
		for (ind=0;ind<result.data.length;ind++) {		
			$("#id_monto_plan").append($("<option></option>")
					 .attr("value",result.data[ind].montoPlan)
					 .text(result.data[ind].montoPlan));
		}
	} else {
		errManager.showError();
	}
}
var fnSuccessFormLoadCuotasResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#cuotas").empty();
		$("#cuotas").append($("<option></option>")
				 .attr("value","")
				 .text("Seleccionar..."));
		listCuotas = result.data;
		if (result.data!="null") {
			for (ind=0;ind<result.data.length;ind++) {		
				$("#cuotas").append($("<option></option>")
						 .attr("value",result.data[ind].id)
						 .text(result.data[ind].cuotas));
			}
		}
	} else {
		errManager.showError();
	}
}
function processVivienda() {
	if ($("#id_vivienda").val()==$("#VIVIENDA_ALQUILA").val()) {
		$("#id_vivienda_contrato")[0].disabled=false;
		$("#id_vivienda_contrato").removeClass("txtReadonly");
	} else {
		$("#id_vivienda_contrato")[0].disabled=true;
		$("#id_vivienda_contrato").addClass("txtReadonly");
		$("#id_vivienda_contrato").val("");
	}
}

function setLocalidadSelected(id_localidad,callback) {
	var data 			= new Object();
	data.tipo 			= "load_by_id";
	data.id_localidad 	= id_localidad;
	if (callback==undefined) {
		loadData('/ajax/json.localidades_json.php',fnSuccessFormLoadLocalidadResponse,data,fnErrorFormResponse);
	} else {
		loadData('/ajax/json.localidades_json.php',callback,data,fnErrorFormResponse);
	}
}
function setEntregaEn() {
	console.log(localidadLaboralSelected+" "+localidadSelected);
	if (localidadLaboralSelected!=undefined && localidadSelected!=undefined) {
		if (localidadLaboralSelected.region.idRegion==localidadSelected.region.idRegion) {
			$("#se_entrega_en").empty();
			$("#se_entrega_en").append($("<option></option>")
					 .attr("value","")
					 .text("Seleccionar..."));
			$("#se_entrega_en").append($("<option></option>")
					 .attr("value","1")
					 .text("Casa"));
			$("#se_entrega_en").append($("<option></option>")
					 .attr("value","2")
					 .text("Trabajo"));
		} else {
			$("#se_entrega_en").empty();
			$("#se_entrega_en").append($("<option></option>")
					 .attr("value","")
					 .text("Seleccionar..."));
			$("#se_entrega_en").append($("<option></option>")
					 .attr("value","1")
					 .text("Casa"));
		}
	}
}

function chgLugarEntrega(obj) {
	if ($("#se_entrega_en").val()==1) {
		$("#horario_entrega").val(localidadSelected.horarioEntrega)
	} else {
		$("#horario_entrega").val(localidadLaboralSelected.horarioEntrega)
	}
}

function asignarPlan() {
	var salario 		= $("#salario").val();
	var sexo			= $("input[name='sexo']:checked").val();
	var dniDom			= $("input[name='dni_dom']:checked").val();
	var edad			= $("#edad").val();
	var titServicio		= $("#id_titularidad_servicio").val();
	var bNemp			= $("#id_rela_laboral").val();
	var idVivienda		= $("#id_vivienda").val();
	var idTrabajo		= $("#id_actividad_laboral").val();
	var nombre			= $("#nombre").val();
	var apellido		= $("#apellido").val();
	var dni				= $("#dni").val();
	var id_prospecto	= $("#id_prospecto").val();
	var id_frecuencia_cobro = $("#id_frecuencia_cobro").val();
	
	var data 			= new Object();
	data.tipo 			= "cargar_planes";
	data.salario 		= salario;
	data.sexo			= sexo;
	data.dni_dom		= dniDom;
	data.edad			= edad;
	data.tit_servicio	= titServicio;
	data.bnemp			= bNemp;
	data.id_vivienda	= idVivienda;
	data.id_trabajo		= idTrabajo;
	data.nombre			= nombre;
	data.apellido		= apellido;
	data.id_prospecto	= id_prospecto;
	data.dni			= dni;
	data.id_frecuencia_cobro = id_frecuencia_cobro;
	
	if (validateAsignarPlan()) {
		desHabilitarCamposPlan();
		loadData('/admin/modulos/prospectos/ajax/json.prospect.php',fnSuccessFormLoadProspectResponse,data,fnErrorFormResponse);
	}
}
function desHabilitarCamposPlan() {

	var data = $('#id_actividad_laboral').select2('data');
	$("#span-txt-id_actividad_laboral").html(data.text);
	$('#s2id_id_actividad_laboral').hide();
	
	data = $("#id_rela_laboral option:selected").text();
	$("#span-txt-id_rela_laboral").html(data);
	$("#id_rela_laboral").hide();
	
	data = $("#id_titularidad_servicio option:selected").text();
	$("#span-txt-id_titularidad_servicio").html(data);
	$("#id_titularidad_servicio").hide();
	
	data = $("#id_vivienda option:selected").text();
	$("#span-txt-id_vivienda").html(data);
	$("#id_vivienda").hide();
	
	data = $("[name='dni_dom']:checked").val();
	$("#span-txt-dni_dom").html(data==1 ? "SI" : "NO");
	$("[hidden-input-ver-planes]").hide();
	
	data = $("[name='span-txt-sexo']:checked").val();
	$("#span-txt-sexo").html(data==1 ? "GENERO: MASCULINO" : "GENERO: FEMENINO");
	$("[hidden-input-ver-planes]").hide();

	
	$("[input-planes]").each (function () {
		$(this)[0].readOnly = true;
	})
}
function validateAsignarPlan() {
	var ret = true;
	var salario		= $("#salario").val();
	var sexo		= $("input[name='sexo']:checked").val();
	var dniDom		= $("input[name='dni_dom']:checked").val();
	var edad		= $("#edad").val();
	var titServicio	= $("#id_titularidad_servicio").val();
	var bNemp		= $("#id_rela_laboral").val();
	var idVivienda	= $("#id_vivienda").val();
	var idTrabajo	= $("#id_actividad_laboral").val();
	if (salario == parseInt(salario)) {		
		$("#salario").removeClass("border-err")
	} else {
		$("#salario").addClass("border-err");
		ret			= false;
	}
	
	if (sexo != undefined) {		
		$("#div-genero").removeClass("border-err")
	} else {
		console.log(sexo)
		$("#div-genero").addClass("border-err");
		ret			= false;
	}
	
	if (dniDom != undefined) {		
		$("#div-dnidom").removeClass("border-err")
	} else {
		console.log(sexo)
		$("#div-dnidom").addClass("border-err");
		ret			= false;
	}
	if (edad == parseInt(edad)) {		
		$("#edad").removeClass("border-err")
	} else {
		$("#edad").addClass("border-err");
		ret			= false;
	}
	if (titServicio>0) {		
		$("#id_titularidad_servicio").removeClass("border-err")
	} else {
		$("#id_titularidad_servicio").addClass("border-err");
		ret			= false;
	}
	if (bNemp>0) {		
		$("#id_rela_laboral").removeClass("border-err")
	} else {
		$("#id_rela_laboral").addClass("border-err");
		ret			= false;
	}
	if (idVivienda>0) {		
		$("#id_vivienda").removeClass("border-err")
	} else {
		$("#id_vivienda").addClass("border-err");
		ret			= false;
	}
	if (idTrabajo>0) {		
		$("#id_actividad_laboral").removeClass("border-err")
	} else {
		$("#id_actividad_laboral").addClass("border-err");
		ret			= false;
	}
	return ret;
}
function populateCuotas(monto) {
	var data 	= new Object();
	data.tipo	= "load_montos";
	data.monto	= monto;
	loadData('/ajax/json.plancuotas_json.php',fnSuccessFormLoadMontosResponse,data,fnErrorFormResponse);
}
function chgMonto(obj) {
	$("#id_frecuencia").empty();
	$("#id_frecuencia").append($("<option></option>")
			 .attr("value","")
			 .text("Seleccionar..."));
	if ($(obj).val()!="") {
		$("#id_frecuencia").append($("<option></option>")
				 .attr("value","semana")
				 .text("Semanal"));
		if ($("#id_rela_laboral").val()==1) {
			$("#id_frecuencia").append($("<option></option>")
					 .attr("value","quincena")
					 .text("Quincenal"));
		}
	}
}
function chgFrecuencia() {
	if ($("#id_frecuencia").val()!="") {
		var data 		= new Object();
		data.tipo		= "load_cuotas";
		data.monto		= $("#id_monto_plan").val();
		data.frecuencia	= $("#id_frecuencia").val();
		loadData('/ajax/json.plancuotas_json.php',fnSuccessFormLoadCuotasResponse,data,fnErrorFormResponse);
	}
}
function chgCuota() {
	var cuota = $("#cuotas").val();
	if (parseInt(cuota)>0) {
		$("#span-txt-tope").html("TOPE: "+topeMonto);
		for (ind=0;ind<listCuotas.length;ind++) {
			if (listCuotas[ind].id==cuota) {
				$("#span-txt-plan").html("PLAN: "+listCuotas[ind].nombre);
				$("#span-txt-monto").html("MONTO: "+listCuotas[ind].montoPlan);
				$("#span-txt-frecuencia").html("FREQ: "+listCuotas[ind].frecuenciaPlan);
				console.log("pasa")
				var data 			= new Object();
				data.tipo			= "set_plan";
				data.id_plan		= cuota;
				data.id_prospecto	= $("#id_prospecto").val();
				loadData('/admin/modulos/prospectos/ajax/json.prospect.php',fnSuccessFormLoadSetPlanResponse,data,fnErrorFormResponse);
			}
		}
	} else {
		$("#span-txt-plan").html("");
		$("#span-txt-tope").html("");
		$("#span-txt-monto").html("");
		$("#span-txt-frecuencia").html("");
	}
}