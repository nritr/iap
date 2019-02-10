<?php
include_once $path.'/admin/php/filters/Filter.php';

class RegionFilter extends Filter {

    // Declaraci�n de la propiedad
	public $nombreLike;
	public $idLocalidad;


	public function __construct($id=null) {
		$this->id=$id;
	}
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "r.nombre" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>