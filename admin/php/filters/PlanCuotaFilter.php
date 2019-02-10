<?php
include_once $path.'/admin/php/filters/Filter.php';

class PlanCuotaFilter extends Filter {

	public $montoPlanMax;//igual o menor al monto filtrado
	public $montoPlan;//igual al monto filtrado
	public $frecuenciaPlan;
	public $cuotas;


	public function __construct($id=null) {
		$this->id=$id;
	}
	
	public function getOrderBy() {
		if ($this->orderBy=="") {
			return "p.monto_plan" ;
		} else {
			return $this->orderBy;
		}
	}
	

}
?>