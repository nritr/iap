<?php

class TipoTrabajo {

    //(`id_tipo_trabajo`, `nombre`, `categoria_trabajo`, `gcia_presunta`, `puntaje_tipo_trabajo`, `tope_tipo_trabajo`, `posible_independiente`, `posible_emprendimiento`, `posible_comercio`, `denegar_auto`)
    public $idTipoTrabajo;
	public $nombre;
	public $categoriaTrabajo;
	public $gciaPresunta;
	public $puntajeTipoTrabajo;
	public $topeTipoTrabajo;
	public $posibleIndependiente;
	public $posibleEmprendimiento;
	public $posibleComercio;
	public $denegarAuto;

	
	public function __construct($id=null,$desc=null) {
		$this->idTipoTrabajo  = $id;
		$this->nombre         = $desc;
	}
	

}
?>