<?php
include_once $path.'/admin/php/filters/Filter.php';

class ProspectoFilter extends Filter {

    public $idEstado;
    public $idSubEstado;
    public $idEstadoIN;
    public $nombreLike;
    public $telefonoLike;
    public $idOrigen;
    public $volverLlamarOrNull; //volver a llamar en pasado o volver a llamar null
    public $llamadoPend;//solo llamado pendiente futuro
    public $propuestaEnviada;
	
    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "p.fecha DESC" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>