<?php
include_once $path.'/admin/php/dao/WhatsappDao.php';
include_once $path.'/admin/php/services/UsuarioService.php';

class WhatsappService {

	private $dao;
	
	public function __construct() {
	    $this->dao = new WhatsappDao();
	}

	
	public function getById(int $id) {
	    $filter = new WhatsappFilter($id);
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	
	public function load(WhatsappFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new WhatsappFilter();
	    }
	    $usrService    = new UsuarioService();
	    $usr           = $usrService->getUserLogued();
	    $filter->idRol = $usr->rol->idRol;
	    return $this->dao->load($filter);
	}
	

	public function saveUpdate(Whatsapp $obj) {
	    if ($obj->idRtaWhatsapp>0) {
	        return $this->dao->update($obj);
		} else {
		    return $this->dao->save($obj);
		}
	}
	
	public function loadContacts(WhatsappFilter $filter=null) {
	    if (!$filter) {
	        $filter    = new WhatsappFilter();
	    }
	    $usrService    = new UsuarioService();
	    $usr           = $usrService->getUserLogued();
	    $filter->idRol = $usr->rol->idRol;
	    return $this->dao->loadContacts($filter);
	}
	
	public function findMessageEquals(WhatsappFilter $filter) {
	    if ($filter->telefono=="" || $filter->texto=="") {
	        throw new Exception("Debe estar el texto y teléfono en el filtro");
	    }
	    $obj = $this->dao->findMessageEquals($filter);
	    if (count($obj)>0) {
	        return $obj[0];
	    }
	    return null;
	}
	
	public function marcarLeido(int $nro) {
	    $this->dao->marcarLeido($nro);
	}
	
	public function saveContact(WhatsappContacto $obj) {
	    return $this->dao->saveContact($obj);
	}
	public function loadCuenta($id=null,$instance=null) {
	    if ($instance==null) {
	       $usrService    = new UsuarioService();
	       $usr           = $usrService->getUserLogued();
	       return $this->dao->loadCuenta($id,$instance,$usr->rol->idRol);
	    } else {
	        return $this->dao->loadCuenta($id,$instance);
	    }
	}
}
?>