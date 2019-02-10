<?php
include_once $path.'/admin/php/dao/PlanCuotaDao.php';


class PlanCuotaService {

	private $dao;
	
	public function __construct() {
	    $this->dao = new PlanCuotaDao();
	}


	public function getById(int $id) {
	    $filter = new PlanCuotaFilter($id);
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}

	public function load(PlanCuotaFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new PlanCuotaFilter();
	    }
	    return $this->dao->load($filter);
	}
	

	/*public function save(LogHistorico $log) {
	    return $this->dao->save($log);
	}*/

		
}
?>