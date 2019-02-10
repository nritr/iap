<?php
include_once $path.'/admin/php/dao/UsuarioDao.php';
include_once $path.'/admin/php/services/ComboService.php';


class UsuarioService {

	private $usuarioDao;
	private $log;
	public function __construct() {
		$this->usuarioDao = new UsuarioDao();
		//$this->log = new Logger('UsuarioService');
	}
	public function isLogued() {
		$this->requestUtil = new RequestUtil();
		if (!isset($_SESSION['usuario'])) {
			$code = $this->requestUtil->getCookie("user2",null);
			if ($code) {
				$usuario = $this->getUserById($code);
				$_SESSION['usuario'] = serialize($usuario);
				setcookie("user2", $code, time()+60*60*24*30,"/");
			}
		}
		return isset($_SESSION['usuario']);
	}
	
	public function notLoguedRedirect() {
		if (!$this->isLogued()) {
			header( 'Location: /admin/login.php') ;
			die();
		}
	}

	
	public function login ($usuario,$recordar) {
		$usuarioDao = new UsuarioDao();
		//login del usuario
		$result = $usuarioDao->login($usuario);
		
		if (isset($result)) {
			$_SESSION['usuario'] = serialize($result);
			if ($recordar==1) {
				setcookie("user2", $result->idUsuario, time()+60*60*24*30,"/");
			}
			return true;
		} else {
			return false;
		}
	}

	
	/**
	 * 
	 * @return Usuario|NULL
	 */
	public function getUserLogued() {
		if (isset($_SESSION['usuario'])) {
			$usr = unserialize($_SESSION['usuario']);
			/*if ($db==null || !is_object($db)) {
				return $usr;
			}*/
			//$usr = $this->usuarioDao->getUserByCode($usr->uniqueCode);
			return $usr;
		}
		return null;
	}
	
	public function getUserById($idUser) {
	    if ($idUser=="") {
	        throw new Exception("Id de usuario vacío");
	    }
	    $filter    = new UsuarioFilter($idUser);
	    $list      = $this->usuarioDao->load($filter);
	    if (count($list)>0) {
	        return $list[0];
	    }
		return null;
	}
	public function getUserByCode($code) {
	    if ($idUser=="") {
	        throw new Exception("Code de usuario vacío");
	    }
	    $filter                = new UsuarioFilter();
	    $filter->codigoInterno = $code;
	    $list                  = $this->usuarioDao->load($filter);
	    if (count($list)>0) {
	        return $list[0];
	    }
	    return null;
	}
	public function logout() {
		setcookie("user2", "", 0,"/");
		session_destroy();
	}
	
	public function load(UsuarioFilter $filter=null) {
	    if ($filter==null) {
	        $filter    = new UsuarioFilter();
	    }
		return $this->usuarioDao->load($filter);
	}
	
	public function saveUpdate(Usuario $usr) {
	    $comboService = new ComboService();
	    $rol          = $comboService->loadRoles($usr->rol->idRol)[0];
	    if ($rol->isCentral()) {
	        $usr->region = new Region(Region::$ID_REGION_CENTRAL);
	    } else if (!$rol->isAdministrador() && !$rol->isCentral() && !$rol->isRegion()) {
	        $usr->region = new Region(null);
	    }
	    
		if ($usr->idUsuario!="" && $usr->idUsuario!=0) {
			return $this->usuarioDao->update($usr);
		} else {
			return $this->usuarioDao->save($usr);
		}
	}
	
	public function deleteUsuario($id_usuario) {
		$this->usuarioDao->deleteUsuario($id_usuario);
	}
	
	public function changePassword($id_usuario,$password,$codigo=null) {
	    if ($codigo) {
	        $filter                = new UsuarioFilter();
	        $filter->codigoInterno = $codigo;
	        $list                  = $this->load($filter);
	        if (count($list)==0) {
	            throw new Exception("Código Incorrecto");
	        }
	        $id_usuario = $list[0]->idUsuario;
	    }
		$this->usuarioDao->changePassword($id_usuario,$password);
	}
}
?>