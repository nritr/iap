<?php

class TitularidadServicio {

    //`id_titularidad_servicio`, `titularidad_servicio`, `puntaje`, `monto_opc`, `relacion`
    public $idTitularidadServicio;
    public $titularidadServicio;
    public $puntaje;
    public $montoOpc;
    public $relacion;

	
	public function __construct($id=null,$titularidad=null) {
		$this->idTitularidadServicio  = $id;
		$this->titularidadServicio    = $titularidad;
	}
	

}
?>