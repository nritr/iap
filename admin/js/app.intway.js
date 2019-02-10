// @ts-nocheck
/** Notify Function*/
$( document ).ready(function() {
	var type = $("#notificacion").attr('type');
	var place = $("#notificacion").attr('place');
	var text = $("#notificacion").attr('text');

	$.sound_on = false;
	
	if (type!="" && type!=undefined && text!="" && text!=undefined) {
		$.notify(text,{globalPosition: 'top center',className: 'success'});
	}
	
	try {
		rolMaganer = new RolMaganer(rolLogued);
		rolMaganer.executePermission();
	} catch (e) {
		console.log(e);
	}
	
	jQuery.fn.extend({
		insertAtCaret: function(myValue){
		  return this.each(function(i) {
		    if (document.selection) {
		      //For browsers like Internet Explorer
		      this.focus();
		      var sel = document.selection.createRange();
		      sel.text = myValue;
		      this.focus();
		    }
		    else if (this.selectionStart || this.selectionStart == '0') {
		      //For browsers like Firefox and Webkit based
		      var startPos = this.selectionStart;
		      var endPos = this.selectionEnd;
		      var scrollTop = this.scrollTop;
		      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
		      this.focus();
		      this.selectionStart = startPos + myValue.length;
		      this.selectionEnd = startPos + myValue.length;
		      this.scrollTop = scrollTop;
		    } else {
		      this.value += myValue;
		      this.focus();
		    }
		  });
		}
	});
});
var EXISTS_RECORD = 23000;
class ErrorMaganer {

	constructor(response) {
		console.log(response);
		this.response = response;
		this.errorsCode = [{"code":23000,"message":"Ya existe %Y"}];
	}

	isSuccess() {
		return (this.response.status=='success');
	}
	
	getCode() {
		return (this.response.error.code);
	}
	
	getMessage() {
		return (this.response.error.message);
	}
	
	getData() {
		return (this.response.data);
	}

	showError(code,str) {
		var i=0;
		for (i = 0;i<this.errorsCode.length;i++) {
			if (code==this.getCode()) {
				$.notify(this.errorsCode[i].message.replace("%Y", str),{globalPosition: 'top center',className: 'error',gap: 4});
				return null;
			}
		}
		if (this.getMessage()=="") {
			$.notify("Se Prodijo un error desconocido", "error",{globalPosition: 'top center',className: 'error'});
		} else {
			$.notify(this.getMessage(),{globalPosition: 'top center',className: 'error'});
		}
	}
}

class ParseAFIP {
	constructor(json) {
		this.json = json;
	}
	
	isSuccess() {
		var ret =  (this.json.tipoPersona!="" ? true : false);
		if (!ret) {
			$.notify("Error Procesando AFIP",{globalPosition: 'top center',className: 'error'});
		}
		return ret;
	}
	isPersona() {
		return (this.json.tipoPersona=="JURIDICA" ? false : true);
	}
	getNombre() {
		if (this.isSuccess() && this.isPersona()){
			return this.json.nombre + " "+ this.json.apellido
		} else if (this.isSuccess()) {
			return this.json.razonSocial+ " "+ this.json.formaJuridica;
		}
		
	}
	getDireccion() {
		/*
		 "codPostal":"1430",
         "descripcionProvincia":"CIUDAD AUTONOMA BUENOS AIRES",
         "direccion":"BALBIN RICARDO 3846",
         "idProvincia":0,
         "tipoDomicilio":"FISCAL"*/
	}
}