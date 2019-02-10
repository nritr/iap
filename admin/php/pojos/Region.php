<?php

class Region {

    public $idRegion;
	public $nombre;
	public $poligon;


	public static $ID_REGION_CENTRAL = 3;
	public function __construct($id=null,$desc=null) {
		$this->idRegion   = $id;
		$this->nombre     = $desc;
	}
	
	public function getId() {
	    return ($this->idRegion=="" ? null : $this->idRegion);
	}
}
?>