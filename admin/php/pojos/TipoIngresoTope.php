<?php

class TipoIngresoTope {

    //`id_tipo_ingreso_tope`, `nombre`, `tope`, `tope_especial`, `cond_topeEspecial`
    public $idTipoIngresoTope;
	public $nombre;
	public $tope;
	public $topeEspecial;
	public $condTopeEspecial;


	public static $ID_BLANCO           = 1;
	public static $ID_NEGRO            = 4;
	public static $ID_MONO_C1          = 6;
	public static $ID_EMPRENDEDOR_C1   = 3;
	public static $ID_MON_C2_PROF      = 6;
	public static $ID_COM_C1           = 2;
	public static $ID_EMPREND_C2       = 8;
	public static $ID_COM_C2           = 7;
	public function __construct($id=null,$desc=null) {
		$this->idTipoIngresoTope  = $id;
		$this->nombre             = $desc;
	}
	

}
?>