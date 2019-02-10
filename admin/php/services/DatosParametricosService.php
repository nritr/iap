<?php
include_once $path.'/admin/php/dao/DatosParametricosDao.php';


class DatosParametricosService {

	private $objDao;
	public function __construct() {
		$this->objDao = new DatosParametricosDao();

	}

	

	public function getById(int $id) {
	    $filter = new DatoParametricoFilter($id);
	    $list = $this->objDao->load($filter);
	    if (count($list)>0) {
	        return $list[0];
	    }
	    return null;
	}
	/**
	 * 
	 * @param DatoParametricoFilter $filter
	 * @return DatoParametrico[]
	 */
	public function load(DatoParametricoFilter $filter=null) {
		return $this->objDao->load($filter);
	}
	
	/**
	 * 
	 * @param DatoParametrico $obj
	 * @return number
	 */
	public function saveUpdate(DatoParametrico $obj) {
		return $this->objDao->save($obj);
	}
	
	public function delete($id) {
	    $obj = $this->getById($id);
	    $obj->estado = 0;
		$this->saveUpdate($obj);
	}
	
	/**
	 * 
	 * @param DatoParametricoFilter $filter
	 * @return DatoParametrico[]
	 */
	public function loadCategorias(DatoParametricoFilter $filter) {
		return $this->objDao->loadCategorias($filter);
	}

}
?>