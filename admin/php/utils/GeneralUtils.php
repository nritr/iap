<?php
include_once $path."/admin/php/services/DatosParametricosService.php";


class GeneralUtils {

	
	public function generateRand() {
		return rand(0, 9999999999999).rand(0, 9999999999999);
	}

	public static function getConfigIni() {
	    $fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
	    $fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
	    $path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
	    return parse_ini_file($path."/admin/config.ini");
	}
	
	public static function in_array_field($needle, $needle_field, $haystack, $strict = false) {
		if (sizeof($haystack)>0 && is_array($haystack)) {
			if ($strict) { 
				foreach ($haystack as $item) 
					if (isset($item->$needle_field) && $item->$needle_field === $needle) 
						return true; 
			} 
			else { 
				foreach ($haystack as $item) 
					if (isset($item->$needle_field) && $item->$needle_field == $needle) 
						return true; 
			} 
		}
		return false;
	}
	
	public static function addCheckedToParameter($param=null) {
		$res = array();
		if ($param==null) {
			$param = $_GET;
		}
		foreach ($param as $param_name => $param_val) {
			if (strpos($param_name, 'chk')!==false) {
				$exp = explode("-",$param_name);
				$res[] = $exp[1];
			}
		}
		return $res;
	}
	
	
	public static function getClientIp() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	public static function getPropertie($propertie) {

		$array_ini = parse_ini_file($path."/admin/private/conf.ini");
		return $array_ini[$propertie];
	}
	

	
	public static function replaceSpecialChar($chr) {

		$healthy = array("<", ">", "'");
		$yummy   = array("&lt;", "&gt;", "&#39;");
		return str_replace($healthy, $yummy, $chr);
	}
	
	public static function getDateSql($date) {
	    if (trim($date)!="" && count(explode('/', trim($date)))>2) {
	        list($dia, $mes, $year) = explode('/', trim($date));
	        return $year.$mes.$dia;
	    } else if (trim($date)!="" && strpos($date, "/") === false) {
	        return $date;
	    }
	    return null;
	}
	/**
	 * 
	 * @param unknown $date DD/MM/YYYY
	 * @param unknown $hour 00 a 24
	 * @param unknown $minue 00 a 59
	 * @return YYYY-MM-DD HH:MM:SS
	 */
	public static function getDateSqlWithHours($date,$hour,$minue) {
	    if (trim($date)!="" && count(explode('/', trim($date)))>0) {
	        list($dia, $mes, $year) = explode('/', trim($date));
	        return $year."-".$mes."-".$dia." ".$hour.":".(strlen($minue)<2 ? "0".$minue : $minue).":00";
	    } else if (trim($date)!="" && strpos($date, "/") === false) {
	        return $date;
	    }
	    return null;
	}
	
	public static function addDayswithdate($date,$days){

		$date = strtotime($days." days", strtotime($date));
		return  date("Ymd", $date);

	}
	public static function getDateSql4View($date,$type=null) {
	    if ($date=="") {
	        return "";
	    }
	    //substr($date,6,2).'-'.substr($date,4,2).'-'.substr($date,0,4);
		if ($type==1)
		    return substr($date, 6, 2)."/".substr($date, 4, 2)."/".substr($date, 0, 4)." ".substr($date, 11,8);
		else
		    return substr($date, 8, 2)."/".substr($date, 5, 2)."/".substr($date, 0, 4)." ".substr($date, 11,8);
	}


	
	public static function getDateFromSqlInArray($date) {
		if ($date!="") {
			list($year, $mes, $dia) = explode('-', $date);
			return array($year,$mes,$dia);
		}
		return null;
	}
	


	
	public static function humanTiming ($time) {

		$time = time() - $time; // to get the time since that moment
		$time = ($time<1)? 1 : $time;
		$tokens = array (
			31536000 => 'año',
			2592000 => 'mes',
			604800 => 'semana',
			86400 => 'día',
			3600 => 'hora',
			60 => 'minuto',
			1 => 'segundo'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return (strlen($numberOfUnits)==2 ? $numberOfUnits : "0".$numberOfUnits).' '.$text.(($numberOfUnits>1)?'s':'');
		}
	}
	
	public static function loopMeses(&$skin,$name_loop,$name_id,$name_desc,$name_select,$mes_selected=0) {
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
	    
	    $loop = new SkinLoop($name_loop);
	    for ($i=1;$i<13;$i++) {
	        $loop->addData($name_id,str_pad($i, 2, "0", STR_PAD_LEFT));
	        $loop->addData($name_desc,$meses[$i-1]);
	        if ($mes_selected==$i) {
	            $loop->addData($name_select,"selected");
	        } else {
	            $loop->addData($name_select,"");
	        }
	        $loop->closeRow();
	    }

	    $skin->addLoop($loop);
	}

	public static function subLoopMeses(SkinLoop &$loop,$name_loop,$name_id,$name_desc,$name_select,$mes_selected=0) {
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	    
	    $subloop = new SkinLoop($name_loop);
	    for ($i=1;$i<13;$i++) {
	        $subloop->addData($name_id,str_pad($i, 2, "0", STR_PAD_LEFT));
	        $subloop->addData($name_desc,$meses[$i-1]);
	        if ($mes_selected==$i) {
	            $subloop->addData($name_select,"selected");
	        } else {
	            $subloop->addData($name_select,"");
	        }
	        $subloop->closeRow();
	    }
	    $loop->addSubLoop($subloop);
	}
	
	public static function subLoopAnios(SkinLoop &$loop,$name_loop,$name_id,$name_desc,$name_select,$anio_selected=0) {

	    $subloop = new SkinLoop($name_loop);
	    for ($i=2018;$i<2021;$i++) {
	        $subloop->addData($name_id,$i);
	        $subloop->addData($name_desc,$i);
	        if ($anio_selected==$i) {
	            $subloop->addData($name_select,"selected");
	        } else {
	            $subloop->addData($name_select,"");
	        }
	        $subloop->closeRow();
	    }
	    $loop->addSubLoop($subloop);
	}
	

	
	public static function levelLoop (&$loop,$list,$level,$id_categoria) {
	    foreach ($list as $value) {
	        $loop->addData('TXT_ID_LOOP_CATEGORIA',$value->idCategoria);
	        $name="";
	        if ($level>0) {
	            $name= GeneralUtils::getNbsp($level)."> ";
	        }
	        $loop->addData('TXT_DESC_CATEGORIA',$name.$value->nombre);
	        if (isset($id_categoria)) {
	            $loop->addData('TXT_SELECTED',($id_categoria==$value->idCategoria ? "selected" : ""));
	        } else {
	            $loop->addData('TXT_SELECTED',"");
	        }
	        $loop->closeRow();
	        if ($value->categoriasHijas!=null) {
	            GeneralUtils::levelLoop ($loop,$value->categoriasHijas,($level+1),$id_categoria);
	            
	        }
	    }
	}
	
	public static function getNbsp($level) {
	    $nbsp="";
	    for ($i=0;$i<$level;$i++) {
	        $nbsp.="&nbsp;&nbsp;&nbsp;&nbsp;";
	    }
	    return $nbsp;
	}
	

	
	public static function cargarDatosParametricos(SkinManager &$skin,$nameLoop,$nameId,$nameText,$nameChecked,$idCategoria,$id = null,$txtEqual = null) {
	    $datosParametricosService   = new DatosParametricosService();
	    $filter						= new DatoParametricoFilter();
	    $filter->idCategoria        = $idCategoria;
	    $list = $datosParametricosService->load($filter);
	    
	    $loop = new SkinLoop($nameLoop);
	    foreach ($list as $value) {
	        $loop->addData($nameId,$value->idDato);
	        if ($id>0) {
	            $loop->addData($nameChecked,($id==$value->idDato ? "selected" : ""));
	        } else if ($txtEqual!="") {
	            $loop->addData($nameChecked,(strtoupper($txtEqual)==strtoupper($value->nombre) ? "selected" : ""));
	        } else {
	            $loop->addData($nameChecked,"");
	        }
	        
	        $loop->addData($nameText,$value->nombre);
	        
	        
	        $loop->closeRow();
	    }
	    $skin->addLoop($loop);
	}
	
	public static function listCategoriasParametricas(&$skin,$id_categoria_padre,$nameLoop,$idLoop,$descLoop,$selectedloop) {
	    $dpService = new DatosParametricosService();
	    $list = $dpService->loadCategorias(new DatoParametricoFilter());
	    
	    $loop = new SkinLoop($nameLoop);
	    foreach ($list as $value) {
	        $loop->addData($idLoop,$value->idDato);
	        $loop->addData($descLoop,$value->nombre);
	        $loop->addData($selectedloop,($id_categoria_padre==$value->idDato ? "selected" : ""));
	        
	        $loop->closeRow();
	        
	    }
	    $skin->addLoop($loop);
	}
	
	public static function decrementMonths(int $months=1) {
	    $time = strtotime(date("Ymd"));
	    $final = date("d/m/Y", strtotime("-1 month", $time));
	    return $final;
	}
	
	public static function parse($text) {
	    // Damn pesky carriage returns...
	    $text = str_replace("\r\n", "\n", $text);
	    $text = str_replace("\r", "\n", $text);
	    
	    // JSON requires new line characters be escaped
	    $text = str_replace("\n", "\\n", $text);
	    return $text;
	}
}
?>