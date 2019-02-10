// @ts-nocheck
$(document).ready(function() {
			
	pageSetUp();

	attachElements();
});

function guardar() {
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
	$("[attach-click]").click(function () {
		var id 		= $(this).attr("attach-click");
		var type 	= $(this).attr("attach-type");
		console.log(type)
		if (type=='') {
			
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
}

var fnErrorFormResponse = function (result) {
	$.LoadingOverlay("hide");
};