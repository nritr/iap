<?php
include_once $path.'/admin/php/dao/WhatsappContactoDao.php';


class WhatsappContactoService {

	private $dao;
	
	public function __construct() {
	    $this->dao = new WhatsappContactoDao();
	}

	
	public function getById(int $id) {
	    $filter = new WhatsappContactoFilter($id);
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	
	public function load(WhatsappContactoFilter $filter) {
	    return $this->dao->load($filter);
	}
	

	public function saveUpdate(WhatsappContacto $obj) {
	    if ($obj->idWhatsappContacto>0) {
	        return $this->dao->update($obj);
		} else {
		    return $this->dao->save($obj);
		}
	}

}
?>