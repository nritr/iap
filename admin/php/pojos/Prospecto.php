<?php

class Prospecto {

    //`id_prospecto`, `id_estado`, `id_origen`, `nombre`, `nombre_fb`, `messenger_id`, `genero`, `celular`, `celular_normalizado`, `edad`, `dni`, `dni_normalizado`, `localidad`, `oficio`, 
    //`alquila`, `servicio_anombrede`, `blanco_negro`, `salario`, `normalizado`, `horario_preferencia`, `freq_cobro`, `fecha`
    
    public $idProspecto;
    
    /**
     * 
     * @var ProspectoNormalizado
     */
    public $prospectoNormalizado;
    /**
     * 
     * @var ProspectoEstado
     */
	public $estado;
	/**
	 * 
	 * @var PlanCuota
	 */
	public $plan;
	public $nombre;
	public $nombreFb;
	public $genero;
	public $messengerId;
	public $celular;
	public $celularNormalizado;
	public $edad;
	public $dni;
	public $dniNormalizado;
	public $dniCoincide;
	public $localidad;
	public $oficio;
	public $vivienda;
	public $contrato;
	public $servicioAnombrede;
	public $blancoNegro;
	public $salario;
	public $normalizado;//si el prospecto está o no normalizado
	public $horarioPreferencia;
	public $fecha;
	public $ultLlamado;
	public $ultLlamadoScreen;
	public $proxLlamado;
	public $proxLlamadoScreen;
	public $cantLlamados;
	public $apellido;
	
	public function __construct($id=null,$nombre=null,$nombreFb=null,$id_estado=null,$id_prospecto_normalizado=null) {
		$this->idProspecto        = $id;
		$this->nombre             = $nombre;
		$this->nombreFb           = $nombreFb;
		$this->estado             = new ProspectoEstado($id_estado);
		$this->prospectoNormalizado = new ProspectoNormalizado($id_prospecto_normalizado);
		$this->plan               = new PlanCuota();
	}
	
	public function getNombre() {
	    if ($this->nombre!="") {
	        $arr = explode(" ", $this->nombre);
	        $ret = "";
	        for ($i=0;$i<count($arr);$i++) {
	            if (($i+1)==count($arr)) {
	                break;
	            }
	            $ret .= $arr[$i]." ";
	        }
	        return trim($ret);
	    }
	    
	}

	public function getApellido() {
	    if ($this->nombre!="" && $this->apellido=="") {
	        $arr = explode(" ", $this->nombre);
	        
	        return $arr[count($arr)-1];
	    } else if ($this->apellido!="") {
	        return $this->apellido;
	    }
	    
	}
	public function getId() {
	    return ($this->idProspecto=="" ? null : $this->idProspecto);
	}
	public function isDNICoincide() {
	    return (strtoupper($this->dniCoincide)=="SI" ? true : false);
	}
	
	public function isGeneroMasculino() {
	    return (strtoupper($this->genero)=="MASCULINO" ? true : false);
	}
	
	/**
	 * devuelve la fecha en formato 
	 * Si la fecha es de hoy, solo visualizar HORA Y MINUTO en el formato 14:02
Si la fecha es de ayer, visualizar: “(ayer)” HORA Y MINUTO. ejemplo: “(ayer) 14:50”
Si la fecha es de mas dias hacia atras “(n dias)” HORA Y MINUTO. Ejemplo: “(3 dias) 15:15”
si la fecha es para mañana “(mañana)” HORA Y MINUTO
Si la fecha es de mas dias hacia adelante “(en n dias)” HORA Y MINUTO. Ejemplo: “(en 3 dias) 15:15”

	 * @param unknown $days cantidad de dias de diferencia con la fecha actual 
	 * @param unknown $hour HH:MM a mostrar
	 */
	public static function diffDateScreen($days,$hour) {
	    $ret = "";
	    
	    if ($days==1) {
	        $ret .= "(ayer)";
	    } else if ($days>1) {
	        $ret .= "(".$days.")";
	    } else if ($days==-1) {
	        $ret .= "(mañana)";
	    } else if ($days<-1) {
	        $ret .= "(en ".($days*-1)." días)";
	    }
	    return trim($ret ." ".$hour);
	}
	
	public function proxLlamado() {
	    $conf      = GeneralUtils::getConfigIni();
	    $nTiempo   = "";
	    /*
	     *   “If rs!llamadoVeces <= 2 Then
nTiempo = 30
ElseIf rs!llamadoVeces >= 3 And rs!llamadoVeces <= 6 Then
nTiempo = 60
ElseIf rs!llamadoVeces >= 7 And rs!llamadoVeces <= 8 Then
nTiempo = 120
Else
nTiempo = 240
End If
vbUrgOno = "Auto: " & CStr(DateAdd("n", nTiempo, CDate(Now)))
[TempVars]![horaFechallamar] = CStr(DateAdd("n", nTiempo, CDate(Now))) “

	     */
	    if ($this->cantLlamados<=$conf['llamado_veces_1']) {
	        $nTiempo = $conf['nTiempo_1'];
	    } else if ($this->cantLlamados >= $conf['llamado_veces_2_mayor_a'] && $this->cantLlamados <= $conf['llamado_veces_2_menor_a']) {
	        $nTiempo = $conf['nTiempo_2'];
	    } else if ($this->cantLlamados >= $conf['llamado_veces_3_mayor_a'] && $this->cantLlamados <= $conf['llamado_veces_3_menor_a']) {
	        $nTiempo = $conf['nTiempo_3'];
	    } else {
	        $nTiempo = $conf['nTiempo_else'];
	    }
	    
	    $time = new DateTime();
	    $time->add(new DateInterval('PT' . $nTiempo . 'M'));
	    
	    return $time->format('Y-m-d H:i:s');
	}
	
	public function normalizarCel() {
	    if ($this->celularNormalizado=="") {
	        $ret = (substr($this->celular, 0, 2)=="15" ? "11".substr($this->celular, 2, strlen($this->celular)) : $this->celular);
	        $ret = preg_replace("/[^0-9]/", "", $ret);
	    } else {
	        $ret =  $this->celularNormalizado;
	    }
	    return $ret;
	    
	}
	
	public function isNormalizado() {
	    return ($this->normalizado==1);
	}
}
?>