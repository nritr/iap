<?php
include_once $path.'/admin/php/filters/Filter.php';

class DatoParametricoFilter extends Filter {

	public $tipo;
	public $idCategoria;
	public $idDatoParametricoPadre;

	public static $ID_CATEGORIA_PARAMETRICA_TIPO_DOC = 6;

	public function __construct($id=null) {
	    $this->id=$id;
	}
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "cp.nombre " ;
		} else {
			return $this->orderBy;
		}
	}

}
?>