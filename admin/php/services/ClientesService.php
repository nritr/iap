<?php
include_once $path.'/admin/php/dao/ClientesDao.php';
include_once $path.'/admin/php/filters/ClienteFilter.php';

class ClientesService {

	private $dao;
	private $log;
	
	public function __construct() {
	    $this->dao = new ClienteDao();
		//$this->log = new Logger('UsuarioService');
	}

	
	/**
	 * 
	 * @param int $id
	 * @return Cliente
	 */
	public function getById(int $id) {
	    $filter = new ClienteFilter();
	    $filter->id = $id;
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	

	/**
	 * 
	 * @param ClienteFilter $filter
	 * @return Cliente[]
	 */
	public function load(ClienteFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new ClienteFilter();
	    }
	    return $this->dao->load($filter);
	}
	
	public function generateCode() {
	    $last = $this->dao->getLastCode();
	    return ++$last;
	}
	public function saveUpdate(Cliente $obj,$is_crear_planta=null) {
	    if ($obj->idCliente!="" && $obj->idCliente!=0) {
	        return $this->dao->update($obj);
		} else {
		    $code = $this->dao->getLastCode();
		    $code++;
		    $obj->codigo = $code;
		    
		    $obj = $this->dao->save($obj);
		    if ($is_crear_planta==1) {
		        //$planService      = new PlantaService();
		        //$planta           = new Planta(null, "POR DEFECTO", $obj->idCliente);
		        $planta->celular  = $obj->celular;
		        $planta->contacto = $obj->nombreContacto;
		        $planta->direccion= $obj->direccion;
		        $planta->email    = $obj->email;
		        $planta->observacion = $obj->observacion;
		        $planta->telefono = $obj->telefono;
		        $planService->saveUpdate($planta);
		    }
		    return $obj;
		}
	}
	
	public function delete(int $id) {
	    $this->dao->delete($id);
	}
		
}
?>