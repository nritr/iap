<?php

class ProspectoEstado {

    public $idProspectoEstado;
    /**
     * 
     * @var ProspectoEstado
     */
	public $estadoPadre;
	public $nombre;
	public $nombreAbreviado;
	public $orden;


	public static $ID_ESTADO_PENDIENTE     = 3;
	public static $ID_ESTADO_SIN_PROCESAR  = 27;
	public static $ID_DEN_NO_CUMPL_REQ     = 10;
	public static $ID_DEN_FUERA_COBERTURA  = 9;
	public static $ID_DEN_AMBIENTAL        = 19;
	
	public static $ID_IGN_ERROR            = 21;
	public static $ID_IGN_NI               = 14;
	
	public static $ID_PEND_VOLV_LLAMAR     = 7;
	
	public function __construct($id=null,$nombre=null) {
		$this->idProspectoEstado  = $id;
		$this->nombre             = $nombre;
	}
	
	public function isParent() {
	    return ($this->estadoPadre->idProspectoEstado=="");
	}

}
?>