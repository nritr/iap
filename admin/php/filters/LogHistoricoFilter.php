<?php
include_once $path.'/admin/php/filters/Filter.php';

class LogHistoricoFilter extends Filter {

    // Declaraci�n de la propiedad
	public $idTipo;
	public $idProspecto;


	public function __construct($id=null) {
		$this->id=$id;
	}
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "lh.fecha DESC" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>