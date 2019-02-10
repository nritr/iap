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
include_once $path."/admin/php/services/ClientesService.php";


$userService	= new UsuarioService();
$errorManager   = new ErrorManager();
$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$clienteService = new ClientesService();
$notifyUtil             = new NotifyUtil();

$data 	= $_POST['data'];

$id_cliente		= (isset($data['id_cliente']) ? $data['id_cliente'] : null );

if ($data['tipo']=='updatecreate') {
	$nombre 		= $data['nombre'];
	$email 			= $data['email'];
	$nombre_contacto= $data['nombre_contacto'];
	$telefono 		= $data['telefono'];
	$celular 		= $data['celular'];
	$cuit 		    = $data['cuit'];
	$direccion 		= $data['direccion'];
	//$localidad 		= $data['localidad'];
	$observacion	= $data['observacion'];
	$cp 		    = $data['cp'];
	$codigo         = $data['codigo'];
	$is_crear_planta= isset($data['is_crear_planta']) ? 1 : 0;
	//$id_lista_precio= $data['id_lista_precio'];
	
	$obj = new Cliente();
	$obj->idCliente 	= $id_cliente;
	$obj->nombre		= $nombre;
	$obj->email			= $email;
	$obj->nombreContacto= $nombre_contacto;
	$obj->celular		= $celular;
	$obj->cp            = $cp;
	$obj->cuit          = $cuit;
	$obj->direccion     = $direccion;
	$obj->telefono      = $telefono;
	//$obj->localidad     = $localidad;
	$obj->observacion   = $observacion;
	$obj->estado        = 1;
	$obj->codigo        = $codigo;

	try {
	    $obj = $clienteService->saveUpdate($obj,$is_crear_planta);
	    $errorManager->setData($obj);
	} catch (Exception $ex) {
	    $errorManager = new ErrorManager($ex);
	}
} else if ($data['tipo']=='delete') {
    $clienteService->delete($id_cliente);
	$notifyUtil->setNotify(new Notify("El Cliente Fue Eliminado"));
}  else if ($data['tipo']=='cliente_by_id') {

    try {
        $cli = $clienteService->getById($id_cliente);
        $errorManager->setData(json_encode($cli,true));
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
    die ($errorManager->getJsonResponse(FALSE));
} else if ($data['tipo']=='detele-planta') {
    $id_planta = $data['id_planta'];
    $plantaService->delete($id_planta);
} else if ($data['tipo']=='get_planta_by_id') {
    $id_planta  = $data['id_planta'];
    $planta     = $plantaService->getById($id_planta);
    $errorManager->setData($planta);
}

echo $errorManager->getJsonResponse();
?>