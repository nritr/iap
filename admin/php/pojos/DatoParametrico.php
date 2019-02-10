<?php

class DatoParametrico {


    public $idDato;
    /**
     * 
     * @var CategoriaParametrica
     */
	public $categoria;
	public $idDatoPadre;
	public $nombre;
	public $tipo;
	public $valor;
	public $estado;
	public $listChildren; //list datoparametrico para valores de atributos de productos

	public static $VIVIENDA_ALQUILA = 14;
	public function __construct($id=null,$nombre=null) {
		$this->idDato=$id;
		$this->nombre=$nombre;
	}
	
	/**
	 * 
	 * @return DatoParametrico
	 */
	public function getDatoParametricoPadre() {
	    if ($this->idDatoPadre!=null) {
	        $dpService = new DatosParametricosService();
	        $filter = new DatoParametricoFilter();
	        $filter->id = $this->idDatoPadre;
	        $obj = $dpService->load($filter);
	        return $obj[0];
	    }
	}
}
?>