<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/ComboService.php";
include_once $path."/admin/php/utils/ErrorManager.php";

$userService	   = new UsuarioService();
$comboService      = new ComboService();
$request           = new RequestUtil();
$userService->notLoguedRedirect();
$usr 			   = $userService->getUserLogued();
$errorManager       = new ErrorManager();

$data 	= $_POST['data'];

$tipo	= $data['tipo'];

if ($tipo=="load") {
    $filter						= new ProspectoEstadoFilter();
    $filter->onlyParents        = (!isset($data['id_estado_padre']) ? true : false);
    $filter->idEstadoPadre      = (isset($data['id_estado_padre']) ? $data['id_estado_padre'] : null);
    
    try {
        $list                       = $comboService->loadProspectosEstados($filter);
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
}

echo $errorManager->getJsonResponse();
?>