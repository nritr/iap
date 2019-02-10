Dropzone.autoDiscover = false;
var indUpload = 0;
var fnSuccessFormDeleteImgResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$("#modal-eliminar").modal('hide');
	} else {
		errManager.showError();
	}
};
//se ejecuta despues de modificar algun dato del listado de documentos
var fnSuccessFormUpdateResponse = function (result) {
	var errManager = new ErrorMaganer(result);
	if (errManager.isSuccess()) {
		$(objFocus).css('border','1px solid black');
	} else {
		errManager.showError();
		$(objFocus).css('border','1px solid red');
	}
};
var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};
$( function() {
    $( "#sortable" ).sortable({
      placeholder: "ui-state-highlight",
      stop: function(event, ui) {
    	  //$(el).id
    	  activePortada($("#sortable"));
    	  refreshIndexImages();
      }
    });
    $( "#sortable" ).disableSelection();
  } );
//muestra errores en el formateo de subida de los archivos/documentos
function addError(error,file) {
	var html = "<tr>"+
	"<td>"+file+
	"</td>"+
	"<td><a style='color:red'>"+error+"</a>"+
	"</td>"+
	"</tr>";
$( "#body-table-errors" ).append( html );
}
function addImg(path,name,id,empleado,fecha,idTipoDoc) {
	strOptions = getOptions(idTipoDoc);
	if (path) {
		var html = "<tr id='tr-doc-"+id+"'>"+
					"<td>"+empleado+
					"</td>"+
					"<td><label class='input'>"+
						"<i class='icon-append fa fa-file-pdf-o'></i>"+
						"<input type='text'  class='form-control' name='nombre' value='"+name+"' data-attach-nombre='"+id+"'/>"+
						"</label>"+
					"</td>"+
					"<td>"+
						"<select class='form-control'  data-attach-tipo-doc='"+id+"'>"+
						strOptions+
						"</select>"+
					"</td>"+
					"<td>"+
						"<div class='input-group date' datepick>"+
							"<input type='text' name='fecha_egreso' data-attach-date='"+id+"' value='"+fecha+"' class='form-control' style='padding-left:5px'>"+
							"<span class='input-group-addon'>"+
								"<span class='glyphicon glyphicon-calendar'></span>"+
							"</span>"+
						"</div>"+
					"</td>"+
					"<td>"+
						"<button type='button' class='btn btn-secondary waves-effect' style='padding:5px' eliminar='"+id+"'>"+
							"<span class='fa fa-trash'></span> Eliminar</button>"+
					"</td>"+
					"</tr>";
		$( "#body-table-docs" ).append( html );

		attachElements();
	}
}
function getOptions(id) {
	var json = JSON.parse($("#json_tipo_doc").val());
	var ret = "";
	ret += "<option value=''>SELECCIONAR...";
	for (i=0;i<json.length;i++) {
		ret += "<option value='"+json[i].idDato+"' "+(json[i].idDato==id ? 'selected' : '')+">"+json[i].nombre;
	}
	return ret;
}
$( document ).ready(function() {
	var acceptedFileTypes = ".pdf,.doc,.docx"; //dropzone requires this param be a comma separated list
	var fileList = new Array;
	indUpload = $("[data-delete-id]").length;
	console.log("ind init:"+indUpload);
	$("#dropzone").dropzone({
		
		addRemoveLinks: true,
		RemoveLinkTemplate: "<div class=\"remove\" data-dz-remove><i class=\"icon-cross\"></i></div>",
		maxFiles: 9999, //change limit as per your requirements
		dictMaxFilesExceeded: "Maximum upload limit reached",
		acceptedFiles: acceptedFileTypes,
		dictInvalidFileType: "upload only JPG/PNG",
		init: function () {

			// Hack: Add the dropzone class to the element
			$(this.element).addClass("dropzone");

			/*this.on("success", function (file, serverFileName) {
				fileList[i] = {
					"serverFileName": serverFileName,
					"fileName": file.name,
					"fileId": i
				};
				$('.dz-message').show();
				i += 1;
			});*/
			this.on("removedfile", function (file) {
				var rmvFile = "";
				//console.log("fileList.length"+fileList.length);
				/*for (var f = 0; f < fileList.length; f++) {
					console.log("fileList[f].fileName"+fileList[f].fileName);
					if (fileList[f].fileName == file.name) {
						rmvFile = fileList[f].serverFileName;
					}
				}*/

				/*if (rmvFile) {
					$.ajax({
						url: path, //your php file path to remove specified image
						type: "POST",
						data: {
							filenamenew: rmvFile,
							type: 'delete',
						},
					});
				}*/
				//alert (rmvFile/*file.name*/)
			});
			this.on("addedfile", function(file) {
				//console.log("Added file."+file.name); 
				//this.removeFile(file);
			});
			this.on("sending", function(file, xhr, formData) {
			  // Will send the filesize along with the file as POST data.
				console.log("ind i:"+indUpload);
				if (indUpload<9999) {
					//console.log("ind: "+i);
					formData.append("index", indUpload);
					indUpload++;
				} else {
					this.removeFile(file);
				}
			});
		},
		success : function( file, response ) {
			console.log(response);
			try {
				json = JSON.parse(response);
			} catch (err) {
				throw new Error("Error Parseando JSON");
			}
			if (json.data.result<1) {
				var error 			= json.data.error;
				var originalFile	= json.data.originalFile;
				$("#upload_errors").show();
				addError(error,originalFile);
			} else {
			    addImg(json.data.file,json.data.name,json.data.id,json.data.empleado,json.data.fecha,json.data.idTipoDoc);
			    $("#upload_sorted_group").show();
			}
			var that = this;
		    setTimeout(function(){ that.removeFile(file); }, 800);
		}
	});
	
	$("#sortable").bind("dragover", function(e) {
	    e.preventDefault();
	    return false;
	});

	$("#sortable").bind("drop", function(e){
	    e.preventDefault();
	    return false;
	});
	

});
var idDocumento = "";
var objFocus= "";
function attachElements() {
	$("[eliminar]").unbind( "click" );
	$("[data-attach-tipo-doc]").unbind( "change" );
	$("[data-attach-date]").unbind("change");
	$("[data-attach-nombre]").unbind("change");
	$('[datepick]').unbind();
	
	$("[eliminar]").click(function () {
		//console.log( $(this).attr('data-delete-id'))
		idDocumento = $(this).attr("eliminar");
		$("#modal-eliminar").modal('show');
	});
	$("[data-attach-tipo-doc]").change(function () {
		if ($(this).val()>0) {
			objFocus = this;
			var id = $(this).attr('data-attach-tipo-doc');
			var data = new Object();
			data.tipo 			= "change-tipo-doc";
			data.id_documento	= id;
			data.id_tipo_doc	= $(this).val();
			loadData('/admin/modulos/documentacion/json/json.documentos.php',fnSuccessFormUpdateResponse,data,fnErrorFormResponse);
		} else {
			$(this).css('border','1px solid red');
		}
	});
	$("[data-attach-nombre]").change(function () {
		if ($(this).val().length>4) {
			objFocus = this;
			var id = $(this).attr('data-attach-nombre');
			var data = new Object();
			data.tipo 			= "change-nombre";
			data.id_documento	= id;
			data.nombre			= $(this).val();
			loadData('/admin/modulos/documentacion/json/json.documentos.php',fnSuccessFormUpdateResponse,data,fnErrorFormResponse);
		} else {
			$(this).css('border','1px solid red');
		}
	});
	
	$('[datepick]').datetimepicker({format: 'DD/MM/YYYY',locale: 'es'}).on('changeDate', function(ev){
        $('#selectDate').datepicker('hide');
    });
	$("[datepick]").on('dp.change', function(e){
		objFocus = $( this ).find( "[data-attach-date]" );
		var id = objFocus.attr('data-attach-date');
		var date = getFecha(e.date._d);

		var data = new Object();
		data.tipo 			= "change-date";
		data.id_documento	= id;
		data.date			= date;
		loadData('/admin/modulos/documentacion/json/json.documentos.php',fnSuccessFormUpdateResponse,data,fnErrorFormResponse);

	})
}
function eliminar() {
	var data = new Object();
	data.tipo 		= "delete-doc";
	data.idDocumento= idDocumento;
	loadData('/ajax/upload.php',fnSuccessFormDeleteImgResponse,data,fnErrorFormResponse);
	$("#tr-doc-"+idDocumento).remove();
}
