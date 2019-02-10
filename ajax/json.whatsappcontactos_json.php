<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/WhatsappContactoService.php";
include_once $path."/admin/php/utils/ErrorManager.php";

$errorManager       = new ErrorManager();
$request            = new RequestUtil($_POST['data']);
$whatsappService    = new WhatsappContactoService();

$tipo           = $request->getArrayValue("tipo");

if ($tipo=="load") {
    try {
        
        $list                   = $comboService->loadWhtsContactoTipo();
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
} else if ($tipo=="load_by_id") {
    $id_contacto            = $request->getArrayValue("id_contacto");
    $obj                    = $whatsappService->getById($id_contacto);
    $errorManager->setData($obj);
}

echo $errorManager->getJsonResponse();

?>