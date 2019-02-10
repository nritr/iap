<?php

class Pojo {
    // Declaraci�n de la propiedad
    public $id;
	
	
    public function getId() {
        return ($this->id=="" ? null : $this->id);
    }
}
?>