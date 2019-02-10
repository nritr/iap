<?php
include_once $path.'/admin/php/utils/NotifyUtil.php';
include_once $path.'/admin/php/utils/GeneralUtils.php';
class SkinCommons {

    /**
     * 
     * @var Usuario
     */
	private $usuario;
	private $idRol;
	
	//private $pixelFacebook;
	public function __construct() {
		$userService	= new UsuarioService();
		$this->usuario	= $userService->getUserLogued();
		if ($this->usuario!=null && $this->usuario!="") {
			$this->idRol	= $this->usuario->rol->idRol;
		}

	}

	public function getClassLF() {
	    return "smart-style-3";//menu-on-top
	}
	public function getNavigationPanel() {
		$skin = new SkinManager("/admin/html/include/navigation_panel.html");
		$skin->addVariable("TXT_NOMBRE_USUARIO", $this->usuario->nombre);
		$skin->addVariable("TXT_ID_ROL"           , $this->usuario->rol->idRol);

	    $skin->addVariable("TXT_GENERICO_ACTIVE"           , (strpos($_SERVER["REQUEST_URI"], "generico")!== false ? "active" : ""));
	    $skin->addVariable("TXT_USUARIOS_ACTIVE"           , (strpos($_SERVER["REQUEST_URI"], "usuarios/")!== false ? "active" : ""));


	    $skin->addVariable("TXT_PARTICIPANTS_MAIN_ACTIVE"  , (strpos($_SERVER["REQUEST_URI"], "participantes_main")!== false ? "active" : ""));
	    

		$notifyUtil = new NotifyUtil();
		$notify = $notifyUtil->getNotify();
		if ($notify) {
		    $skin->addVariable("TXT_NOTIF_TYPE",$notify->type);
		    $skin->addVariable("TXT_NOTIF_PLACE",$notify->place);
		    $skin->addVariable("TXT_NOTIF_TEXT",$notify->text);
		} else {
		    $skin->addVariable("TXT_NOTIF_TYPE","");
		    $skin->addVariable("TXT_NOTIF_PLACE","");
		    $skin->addVariable("TXT_NOTIF_TEXT","");
		}
		if ($this->idRol!=Rol::$ID_ROL_ADMINISTRADOR) {
		    $skin->addIfZone("IFZONE_USUARIOS", 0);
		    $skin->addIfZone("IFZONE_USUARIOS_MAIN", 0);
		} else {
		    $skin->addIfZone("IFZONE_USUARIOS", 1);
		    $skin->addIfZone("IFZONE_USUARIOS_MAIN", 1);
		}
		return $skin->getSkin();
	}
	
	public function getHead() {
	    $skin = new SkinManager("/admin/html/include/head.html");
	    return $skin->getSkin();
	}
	
	public function getFooter() {
	    $skin = new SkinManager("/admin/html/include/footer.html");
	    return $skin->getSkin();
	}
	
	public function getBodyHead() {
	    $skin = new SkinManager("/admin/html/include/body_head.html");
	    $skin->addVariable("TXT_NOMBRE_USUARIO", $this->usuario->nombre);
	    return $skin->getSkin();
	}


	
	public function getBienvenido($usr) {
		return "";
	}
	public function getUploadZoneAutomatico(array $images=null) {
	    $skin = new SkinManager("/admin/html/include/upload_zone_automatico.html");
	    $loop = new SkinLoop('LOOP_IMAGES');
	    if ($images) {
	        $skin->addVariable("TXT_DISPLAY_IMG","");
	        /* @var $value Imagen */
	        foreach ($images as $value) {
	            $loop->addData("TXT_PATH_IMAGEN",$value->getPath());
	            $loop->addData("TXT_ID_IMAGEN",$value->idImagen);
	            $loop->closeRow();
	        }
	    } else {
	        $skin->addVariable("TXT_DISPLAY_IMG","display:none");
	    }
	    $skin->addLoop($loop);
	    $skin->addVariable("TXT_UPLOAD_PHP", "/ajax/upload.php");
	    
	    return $skin->getSkin();
	}
	public function getUploadZone(array $images=null) {
	    $skin = new SkinManager("/admin/html/include/upload_zone.html");
	    $loop = new SkinLoop('LOOP_IMAGES');
	    if ($images) {
	        $skin->addVariable("TXT_DISPLAY_IMG","");
	        /* @var $value Imagen */
	        foreach ($images as $value) {
	            $loop->addData("TXT_PATH_IMAGEN",$value->getPath());
	            $loop->addData("TXT_ID_IMAGEN",$value->idImagen);
	            $loop->closeRow();
	        }
	    } else {
	        $skin->addVariable("TXT_DISPLAY_IMG","display:none");
	    }
	    $skin->addLoop($loop);
	    $skin->addVariable("TXT_UPLOAD_PHP", "/ajax/upload.php");
	    
	    return $skin->getSkin();
	}
}

?>