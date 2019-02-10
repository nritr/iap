<?php

class Area {

    public $idArea;
	public $nombre;
	public $color;


	
	public function __construct($id=null,$desc=null) {
		$this->idArea         = $id;
		$this->nombre         = $desc;
	}
	

}
?>