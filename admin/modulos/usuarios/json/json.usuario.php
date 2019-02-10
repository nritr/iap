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
include_once $path."/admin/php/services/SendmailService.php";

$usuarioService = new UsuarioService();
$notifyUtil             = new NotifyUtil();
$errorManager           = new ErrorManager();

$data 	= $_POST['data'];

$id_usuario		= (isset($data['id_usuario']) ? $data['id_usuario'] : null);

if ($data['tipo']=='updatecreate') {
	$nombre 		= $data['nombre'];
	$email 			= $data['email'];
	$password 		= (isset($data['password']) ? $data['password'] : null);
	$id_rol 		= $data['id_rol'];
	$id_region 		= (isset($data['id_region']) ? $data['id_region'] : null);
	$estado 		= $data['estado'];
	$usr = new Usuario();
	$usr->idUsuario 	= $id_usuario;
	$usr->nombre		= $nombre;
	$usr->rol			= new Rol($id_rol);
	$usr->region		= new Region($id_region);
	$usr->email			= $email;
	$usr->password		= $password;
	$usr->estado		= $estado;
	
	try {
	   $usuarioService->saveUpdate($usr);
	   $notifyUtil->setNotify(new Notify("El Usuario Fue Dado de Alta"));
	} catch (Exception $ex) {
	    $errorManager = new ErrorManager($ex);
	}
	die ($errorManager->getJsonResponse());
} else if ($data['tipo']=='change_pass') {
	$password	= $data['password'];
	$usuarioService->changePassword($id_usuario,$password);
} else if ($data['tipo']=='delete') {
	$usuarioService->deleteUsuario($id_usuario);
	$notifyUtil->setNotify(new Notify("El Usuario Fue Eliminado"));
	die ($errorManager->getJsonResponse());
} else if ($data['tipo']=='forgotpassword') {
    $email          = $data['email'];
    $filter         = new UsuarioFilter();
    $filter->email  = $email;
    $list           = $usuarioService->load($filter);
    if (count($list)>0) {
        $sendMailService        = new SendmailService();
        $sendMailService->sendMailRecuperoClave($list[0]->email, $list[0]->codigoInterno);
    }
    $errorManager->setData(count($list));
    die($errorManager->getJsonResponse());
} else if ($data['tipo']=='change_pass_forgot') {
    $password	= $data['password'];
    $codigo     = $data['codigo'];
    try {
        $usuarioService->changePassword(null,$password,$codigo);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
    die($errorManager->getJsonResponse());
} 

echo "{\"result\":1,\"error\":\"\"}";
?>