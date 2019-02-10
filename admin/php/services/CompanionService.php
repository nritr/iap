<?php
include_once $path.'/admin/php/dao/CompanionDao.php';


class CompanionService {

	private $dao;

	
	public function __construct() {
	    $this->dao = new CompanionDao();
	}

	

	public function getById(int $id) {
	    $filter = new DatoFilter();
	    $filter->idCompanion = $id;
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	

	public function getByIdDatos(int $id) {
	    $filter = new DatoFilter();
	    $filter->idDato = $id;
	    $list = $this->dao->load($filter);
	    return $list;
	}
	public function load(DatoFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new DatoFilter();
	    }
	    return $this->dao->load($filter);
	}
	/**
	 * trae los dias, ya sea de un acompañante como de un participante
	 * @param int $id_companion acompañante
	 * @param int $id_ticket ticket del participante
	 * @return Day[]
	 */
	public function loadDays($id_companion=null,$id_ticket=null) {
	    return $this->dao->loadDays($id_companion,$id_ticket);
	}
}
?>