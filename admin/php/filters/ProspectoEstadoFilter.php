<?php
include_once $path.'/admin/php/filters/Filter.php';

class ProspectoEstadoFilter extends Filter {

    public $idEstadoPadre;
    public $onlyParents = false;
	
    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "pe.orden" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>