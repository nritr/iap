<?php
include_once $path.'/admin/php/filters/Filter.php';

class TitularidadServicioFilter extends Filter {


    public $titularidadLike;
    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "ts.titularidad_servicio " ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>