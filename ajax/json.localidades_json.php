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


$userService        = new UsuarioService();
$comboService       = new ComboService();
$request            = new RequestUtil($_POST['data']);
$errorManager       = new ErrorManager();
$userService->notLoguedRedirect();
$usr                = $userService->getUserLogued();


$tipo               = $request->getArrayValue("tipo");

if ($tipo=="load_by_id") {
    $filter             = new LocalidadFilter();
    $filter->id         = $request->getArrayValue("id_localidad");
    try {
        $obj                = $comboService->loadLocalidades($filter)[0];
        $errorManager->setData($obj);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
}

echo $errorManager->getJsonResponse();
?>