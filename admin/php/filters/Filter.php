<?php

abstract class Filter {
    // Declaraci�n de la propiedad
    public $id;
	public $orderBy;
	public $estado;
	public $groupBy;

	/*public function __construct($idEmpleado) {
		$this->idEmpleado=$idEmpleado;
	}*/
	
	abstract public function getOrderBy(); /*{
		if ($this->orderBy=="") {
			return "e.fecha_creacion DESC" ;
		} else {
			return $this->orderBy;
		}
	}*/
	
	public function getEstado() {
		if ($this->estado==null) {
			return 1;
		}
		return $this->estado;	
	}
}
?>