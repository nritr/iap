<?php
include_once $path.'/admin/php/filters/Filter.php';

class DatoFilter extends Filter {

    public $idDato;
    public $idCompanion;//filtro para el dado de companion, trae el companion por id, igualmente para traer por id, usar getById() del service de companion
    public $passportNumber='';
    public $code='';
    public $existsTicket;
    private $filterSql = "";
    private $valueSql = "";
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
	
	public function setDynamicFilter($sql,$value2) {
	    $this->filterSql   = $sql;
	    $this->valueSql    = $value2;
	}

	public function getDynamicFilter() {
	    return str_replace ( ":value:", $this->valueSql, $this->filterSql );
	}
}
?>