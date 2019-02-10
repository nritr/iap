<?php
include_once $path.'/admin/php/filters/Filter.php';

class LocalidadFilter extends Filter {

    public $likeAll;

    public function __construct($id=null) {
        $this->id=$id;
    }
    
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "l.localidad " ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>