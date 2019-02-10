<?php

class UsuarioFilter {
    // Declaraci�n de la propiedad
    public $id;
    public $idRol;
    public $idRolIN;
	public $idRegion;
	public $orderBy;
	public $idRegionOrNull;
	public $email;
	public $codigoInterno;
	public $estado;

	public function __construct($id=null) {
		$this->id=$id;
	}
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "u.fecha_creacion DESC" ;
		} else {
			return $this->orderBy;
		}
	}
	
	public function getEstado() {
	    return ($this->estado=="" ? 1 : $this->estado);
	}
}
?>