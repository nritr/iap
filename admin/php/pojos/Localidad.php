<?php

class Localidad {
    //SELECT `id_localidad`, `id_localidad_access`, `id_region`, `localidad`, `zona_entrega`, `partido`, `horario_entrega` FROM `LOCALIDAD` WHERE 1
    public $idLocalidad;
    public $idLocalidadAccess;
	public $localidad;
	/**
	 * 
	 * @var Region
	 */
	public $region;
	public $zonaEntrega;
	public $partido;
	public $horarioEntrega;
	

	public function __construct($id=null,$desc=null,$partido=null) {
		$this->idLocalidad    = $id;
		$this->localidad      = $desc;
		$this->partido        = $partido;
	}
	

}
?>