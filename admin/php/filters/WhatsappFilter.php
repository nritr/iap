<?php
include_once $path.'/admin/php/filters/Filter.php';

class WhatsappFilter extends Filter {

    public $idChatApi;
    public $telefono;
    public $telefonoLike;
    public $texto;
    public $textoLike;
    public $noLeidos;
    public $idRol;

    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "w.fecha" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>