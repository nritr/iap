// @ts-nocheck
function loadData(url2,fn,indata,fnError) {

	$.ajax({
		url: url2, // url where to submit the request
		type : "POST", // type of action POST || GET
		dataType : 'json', // data type
		data : {data: indata/*JSON.stringify(getFormData(data))*/}, // post data || get data
		success : function(result) {
			fn(result);
			
		},
		error: function(xhr, resp, text) {
			console.log(xhr, resp, text);
			fnError(resp);
		}
	});
}

//devuelve la fecha en formato string, desde un Date
function getFecha(fecha) {
	var today = null;
	if (fecha!=undefined) {
		today = fecha;
	} else {
		today = new Date();
	}
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd = '0'+dd
	} 

	if(mm<10) {
		mm = '0'+mm
	} 

	today = dd + '/' + mm + '/' + yyyy;
	return today;
}

function getDateFromString(date) {
	return new Date(date.split("-")[0],parseInt(date.split("-")[1])-1,date.split("-")[2]);
}

Date.prototype.addDays = function(days) {
  var dat = new Date(this.valueOf());
  dat.setDate(dat.getDate() + days);
  return getFecha(dat);
}

//utility function
function getFormData(data) {
   var unindexed_array = data;
   var indexed_array = {};

   $.map(unindexed_array, function(n, i) {
	indexed_array[n['name']] = n['value'];
   });

   return indexed_array;
}

function _calculateAge(birthday) { // birthday is a date
    var ageDifMs = Date.now() - birthday.getTime();
    var ageDate = new Date(ageDifMs); // miliseconds from epoch
    return Math.abs(ageDate.getUTCFullYear() - 1970);
}
function getMonthDayMM(month) {
	return (month.length>1 ? month : "0"+month)
}
function isValidDate(ano,mes,dia) {
    var isValid = false ;
	pText = ano +"/"+ getMonthDayMM(mes) +"/"+ getMonthDayMM(dia);
    var t = pText.match(/^(\d{4})\/(\d{2})\/(\d{2})$/);

    if (t !== null) {
        var y = +t[1], m = +t[2], d = +t[3];
        var date = new Date(y, m - 1, d);

        isValid = (date.getFullYear() === y && date.getMonth() === m - 1) ;
    }

    return isValid ;
}

function validateFecInputs(strAnio,strMes,strDia) {
	var ret=true;
	$("#"+strAnio).css("border","1px solid #cfcfcf");
	$("#"+strMes).css("border","1px solid #cfcfcf");
	$("#"+strDia).css("border","1px solid #cfcfcf");
	if ($("#"+strAnio).val()!="" || $("#"+strMes).val()!="" || $("#"+strDia).val()!="") {
		if (!isValidDate($("#"+strAnio).val(),$("#"+strMes).val(),$("#"+strDia).val())) {
			$("#"+strAnio).css("border","1px solid red");
			$("#"+strMes).css("border","1px solid red");
			$("#"+strDia).css("border","1px solid red");
			ret=false;
		}
	}
	return ret;
}

function buildMeses(strMes) {
	var meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
	var ret = "";
	for (i=0;i<12;i++){
		if (strMes==(i+1)) {
			ret += "<option value='"+addCero((i+1))+"' selected>"+meses[i];
		} else {
			ret += "<option value='"+addCero((i+1))+"'>"+meses[i];
		}
	}
	return ret;
}

function buildAnios(strAnio) {
	
	var ret = "";
	for (i=2018;i<2020;i++){
		if (strAnio==i) {
			ret += "<option value='"+i+"' selected>"+i;
		} else {
			ret += "<option value='"+i+"'>"+i;
		}
		
	}
	return ret;
}

function addCero(str) {
	str = str.toString();
	if (str.trim().length==1){
		return "0"+str.trim();
	}
	return str;
}

function isInPastdate(date) {
	var now = new Date();
	
	if (date < now) {
		return true;
	} else {
		return false;
	}
}
//CONSTANTS
var TIPO_ASIGNACION_CENTRAL = 0;
var TIPO_ASIGNACION_REGION 	= 1;
var TIPO_ASIGNACION_GRUPO 	= 2;
var REGION_CENTRAL = 3;