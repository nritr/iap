<?php

class CategoriaParametrica {


    public $idCategoriaParametrica;
	public $nombre;
	public $tipo;
	public $posicion;

	public static $ID_CATEGORIA_ORIGEN_PROSP   = 9;
	public static $ID_CATEGORIA_FREQ_COBRO     = 13;
	public static $ID_CATEGORIA_TIPO_VIVIENDA  = 14;
	public static $ID_CATEGORIA_CONTRATO       = 15;
	
	public function __construct($id=null,$nombre=null) {
	    $this->idCategoriaParametrica=$id;
		$this->nombre=$nombre;
	}
}
?>