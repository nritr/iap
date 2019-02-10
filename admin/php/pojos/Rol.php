<?php

class Rol {
    // Declaraci�n de la propiedad
    public $idRol;
	public $nombre;
	public $tipoAsignacion;

	public static $ID_ROL_ADMINISTRADOR = 1;
	
	public static $TIPO_ASIG_CENTRAL   = 0;
	public static $TIPO_ASIG_REGION    = 1;
	public static $TIPO_ASIG_GRUPO     = 2;
	
	public function __construct($id,$desc=null) {
		$this->idRol      = $id;
		$this->nombre     = $desc;
	}
	
	public function isAdministrador() : bool {
	    return ($this->idRol==Rol::$ID_ROL_ADMINISTRADOR ? true : false);
	}
	
	public function isCentral() : bool  {
	    return $this->tipoAsignacion==Rol::$TIPO_ASIG_CENTRAL;
	}
	
	public function isRegion() : bool {
	    return $this->tipoAsignacion==Rol::$TIPO_ASIG_REGION;
	}
	
	public function isGrupo() : bool  {
	    return $this->tipoAsignacion==Rol::$TIPO_ASIG_GRUPO;
	}
}
?>