<?php
include_once $path.'/admin/php/dao/ComboDao.php';



class ComboService {

	private $dao;
	public function __construct() {
	    $this->dao = new ComboDao();
	}
	
	
	public function loadRoles($id_rol=null) {
	    return $this->dao->loadRoles($id_rol);
	}
	
	public function loadRegiones(RegionFilter $filter=null) {
	    if ($filter=="") {
	        $filter = new RegionFilter();
	    }
	    if ($filter->id=="") {
	        return $this->dao->loadRegiones($filter);
	    } else {
	        return $this->dao->loadRegiones($filter)[0];
	    }
	}
	
	public function loadProspectosEstados(ProspectoEstadoFilter $filter) {
	    return $this->dao->loadProspectosEstados($filter);
	}
	public function loadLocalidades(LocalidadFilter $filter=null) {
	    if ($filter=="") {
	        $filter = new LocalidadFilter();
	    }
	    return $this->dao->loadLocalidades($filter);
	}
	
	public function loadTipoTrabajo(TipoTrabajoFilter $filter=null) {
	    if ($filter=="") {
	        $filter = new TipoTrabajoFilter();
	    }
	    return $this->dao->loadTipoTrabajo($filter);
	}
	
	public function loadTipoIngresoTope(TipoIngresoTopeFilter $filter=null) {
	    if ($filter=="") {
	        $filter = new TipoIngresoTopeFilter();
	    }
	    return $this->dao->loadTipoIngresoTope($filter);
	}
	
	public function loadTitularidadServicio(TitularidadServicioFilter $filter=null) {
	    if ($filter=="") {
	        $filter = new TitularidadServicioFilter();
	    }
	    return $this->dao->loadTitularidadServicio($filter);
	}
	
	public function loadWhtsContactoTipo($id=null) {
	    return $this->dao->loadWhtsContactoTipo($id);
	}
}
?>