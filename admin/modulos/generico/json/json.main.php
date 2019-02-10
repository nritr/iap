<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/SkinManager.php";
include_once $path."/admin/php/utils/SkinCommons.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/utils/NotifyUtil.php";
include_once $path."/admin/php/utils/ErrorManager.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/DatosParametricosService.php";

$dpService 	        = new DatosParametricosService();
$userService		= new UsuarioService();
$notifyUtil         = new NotifyUtil();
$errorManager       = new ErrorManager();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$data 	= $_POST['data'];

$id_dato		= $data['id_dato'];

if ($data['tipo']=='updatecreate') {
	$nombre				= $data['nombre'];
	$valor				= $data['valor'];
	$id_categoria_padre	= $data['id_categoria_padre'];


	$obj 			= new DatoParametrico($id_dato,$nombre);
	$obj->valor		= $valor;
	$obj->categoria = new CategoriaParametrica($id_categoria_padre);
	$obj->estado    = 1;

	try {
	   $id = $dpService->saveUpdate($obj);
	   $notifyUtil->setNotify(new Notify("El Dato Paramétrico Fue Guardado"));
	   $errorManager->setData($id);
	} catch (Exception $ex) {
	    $errorManager = new ErrorManager($ex);
	}
} else if ($data['tipo']=='delete') {
    $dpService->delete($id_dato);
    $notifyUtil->setNotify(new Notify("El Dato Fue Eliminado"));
} else {
    $categoriaService->activar($id_rubro);
    $notifyUtil->setNotify(new Notify("El Rubro Fue Activado"));
}

echo $errorManager->getJsonResponse();
?>