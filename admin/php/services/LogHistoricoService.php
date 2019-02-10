<?php
include_once $path.'/admin/php/dao/LogHistoricoDao.php';
include_once $path.'/admin/php/services/DatosParametricosService.php';

class LogHistoricoService {

	private $dao;
	
	public function __construct() {
	    $this->dao = new LogHistoricoDao();
	}


	public function getById(int $id) {
	    $filter = new LogHistoricoFilter($id);
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}

	public function load(LogHistoricoFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new LogHistoricoFilter();
	    }
	    return $this->dao->load($filter);
	}
	

	public function save(LogHistorico $log) {
	    return $this->dao->save($log);
	}

		
}
?>