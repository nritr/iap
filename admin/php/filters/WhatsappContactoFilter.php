<?php
include_once $path.'/admin/php/filters/Filter.php';

class WhatsappContactoFilter extends Filter {

    public $nombreLike;
    
    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "wc.nombre" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>