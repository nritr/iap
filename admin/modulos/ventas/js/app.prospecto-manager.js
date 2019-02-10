var tmpMgr;
function ProspectoManager() {
	/**
	 * carga los prospectos en la tabla
	 */
	this.load = function () {
		tmpMgr = this;
		var data = $("#frm_filter_clientes").serializeArray();
		if ($("#datos_like").val().length<5 && $("#datos_like").val().length>0) {
			$("#datos_like").addClass("errorInput");
		} else {
			$("#datos_like").removeClass("errorInput");
		}
		loadData('/admin/modulos/ventas/ajax/json.prospectos.php',this.fnSuccessFormLoadResponse,getFormData(data),fnErrorFormResponse) 
	}
	/**
	 * obtiene los prospectos de php y los arma
	 */
	this.fnSuccessFormLoadResponse = function (result) {
		var errManager = new ErrorMaganer(result);
		if (errManager.isSuccess()) {
			tmpMgr.addProspects(result.data);
		} else {
			errManager.showError();
		}
	}
	
	this.fnSuccessFormSendMsgResponse = function (result) {
		var errManager = new ErrorMaganer(result);
		if (errManager.isSuccess()) {
			tmpMgr.load();
		} else {
			errManager.showError();
		}
	}
	this.fnSuccessFormVolLlamarResponse = function (result) {
		var errManager = new ErrorMaganer(result);
		if (errManager.isSuccess()) {
			tmpMgr.load();
		} else {
			errManager.showError();
		}
	}
	this.fnSuccessFormGuardarVolvLlamarResponse = function (result) {
		var errManager = new ErrorMaganer(result);
		if (errManager.isSuccess()) {
			$("#modal-volUrg").modal("hide")
		} else {
			errManager.showError();
		}
	}
	this.fnSuccessFormHistoryResponse = function (result) {
		var errManager = new ErrorMaganer(result);
		if (errManager.isSuccess()) {
			$("#body-history").empty();
			for (ind=0;ind<result.data.length;ind++) {
				var html = "<tr>"+
								"<td>" + result.data[ind].fechaScreen + "</td>"+
								"<td>" + result.data[ind].usuario.nombre + "</td>"+
								"<td>" + result.data[ind].tipo.nombre + "</td>"+
							"</tr>"; 
				$("#body-history").append(html);
			}

		} else {
			errManager.showError();
		}
	}
	/**
	 * recorre cada prospecto del json y los va agregando a la tabla
	 */
	this.addProspects = function (data) {
		$("#table-data").empty();
		for (i=0;i<data.length;i++) {
			$("#table-data").append(tmpMgr.addProspect(data[i]));
		}
		attachElements();
		
	}
	
	/**
	 * arma el html de cada prospecto
	 */
	this.addProspect = function (data) {
		var cantLlamados= "";
		var ultLlam		= "";
		if (data.cantLlamados>0) {
			cantLlamados = " (" + data.cantLlamados.toString() + ") ";
		}
		
		if (data.ultLlamadoScreen != null) {
			ultLlam = data.ultLlamadoScreen + ' Hrs.' + cantLlamados + '<br>';
		}
		if (data.proxLlamadoScreen != null) {
			ultLlam += 'Prox. Llam: ' + data.proxLlamadoScreen;
		}
		var html = 
			'<tr>'+
				'<td>' + data.idProspecto + '</td>'+
				'<td><b>' + data.nombre + '</b><br>' + (data.nombreFb!=null ? data.nombreFb : "" ) + '</td>'+
				'<td>' + data.edad + '</td>'+
				'<td>' + data.localidad + '</td>'+
				'<td>' + data.estado.estadoPadre.nombre + ' (' + data.estado.nombreAbreviado + ')</td>'+
				'<td>' + ultLlam + '</td>'+
				'<td>' + data.vivienda + '</td>'+
				'<td>' + data.dniCoincide + '</td>'+
				'<td>' + data.servicioAnombrede + '</td>'+
				'<td>' + data.celular + '</td>'+
				'<td>' + data.oficio + '</td>'+
				'<td>' +
					'<button type="button" title="Enviar Mensaje" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:green" attach-click="' + data.idProspecto + '" attach-type="sendMsg">' +
					'Msg</button> ' +
					'<button type="button" title="Volver a LLamar" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:green" attach-click="' + data.idProspecto + '" attach-type="volLlamar">' +
					'Vol</button> ' +
					'<button type="button" title="Volver a LLamar Urgente" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:orange" attach-click="' + data.idProspecto + '" attach-type="volUrg">' +
					'enHora</button> ' +
				'</td>'+
				'<td>' +
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px" attach-click="' + data.idProspecto + '" attach-type="procesar">'+
						'<span class="fa fa-check-square-o"></span> Procesar' +
					'</button> ' +
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:red" attach-click="' + data.idProspecto + '" attach-type="req">' +
					'Req</button> ' +
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:red" attach-click="' + data.idProspecto + '" attach-type="zon"> '+
					'Zon</button> '+
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:red" attach-click="' + data.idProspecto + '" attach-type="amb">'+
					'Amb</button> ' +
					'&nbsp;&nbsp;&nbsp; '+
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:orange" attach-click="' + data.idProspecto + '" attach-type="ni">' +
					'N/I</button> '+
					'<button type="button" class="btn btn-primary" style="margin-top: 5px;padding:2px;background-color:orange" attach-click="' + data.idProspecto + '" attach-type="err">' +
					'Err</button> ' +
					'<button type="button" class="btn btn-default" style="margin-top: 5px;padding:2px" attach-click="' + data.idProspecto + '" attach-type="history">' +
					'<span class="fa fa-history"></span>'+
					'Historial</button> '+
				'</td>' +
			'</tr>';
		return html;
	}
	
	this.generateTable = function () {
		$('#datatable-buttons').unbind();
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
	}
	this.sendMsg = function (id) {
		tmpMgr = this;
		var data 			= new Object();
		data.tipo			= "sendMsg";
		data.id_prospecto	= id;
		loadData('/admin/modulos/ventas/ajax/json.prospectos.php',this.fnSuccessFormSendMsgResponse,data,fnErrorFormResponse) 
	}
	
	this.volLlamar = function (id) {
		tmpMgr = this;
		var data 			= new Object();
		data.tipo			= "volLlamar";
		data.id_prospecto	= id;
		loadData('/admin/modulos/ventas/ajax/json.prospectos.php',this.fnSuccessFormVolLlamarResponse,data,fnErrorFormResponse) 
	}
	this.volUrg = function (id) {
		$("#modal-volUrg").modal("show")
		$('#modal-volUrg').on('shown.bs.modal', function(e) {
				
				
		});
		
		$("#fecha_llamar").val("")
		$("#horario_llamar").val("")
		$("#minuto_llamar").val("")
		$("#id_prospecto_llamar").val(id)
	}
	this.guardarVolvLlamar = function () {
		var result 			= $("#frm-volllamar").parsley().validate();
		var result2			= false;
		var selectedDate 	= $('#fecha_llamar').datepicker('getDate');
		if (selectedDate) {
			
			selectedDate.setHours($("#horario_llamar").val(), $("#minuto_llamar").val(), 00, 00);
		
			result2 			= !(isInPastdate(selectedDate));
			console.log("pasa"+result2)
			if (!result2) {
				$("[err-msg-volv-llamar]").show()
			} else {
				$("[err-msg-volv-llamar]").hide()
			}
		}
		if (result && result2) {
			var data = $("#frm-volllamar").serializeArray();
			loadData('/admin/modulos/ventas/ajax/json.prospectos.php',this.fnSuccessFormGuardarVolvLlamarResponse,getFormData(data),fnErrorFormResponse) 
			//$.LoadingOverlay("show");
		}
	}
	this.history = function (id) {
		$('#modal-history').modal('show');
		var data			= new Object();
		data.id_prospecto 	= id;
		data.tipo			= "load_by_prospect";
		loadData('/ajax/json.history_json.php',this.fnSuccessFormHistoryResponse,data,fnErrorFormResponse) 
	}
	this.generateTable();
}