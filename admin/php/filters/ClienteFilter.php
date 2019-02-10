<?php
include_once $path.'/admin/php/filters/Filter.php';

class ClienteFilter extends Filter {

    // Declaraci�n de la propiedad
	public $idSucursal;
	public $codigo;
	public $nombre;
	public $orderBy;
	public $estado;

	/*public function __construct($idEmpleado) {
		$this->idEmpleado=$idEmpleado;
	}*/
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "c.nombre" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>