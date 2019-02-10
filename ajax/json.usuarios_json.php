<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/utils/ErrorManager.php";

$errorManager = new ErrorManager();

$data = $_POST['data'];

try {
    $usrService         = new UsuarioService();
    $filter             = new UsuarioFilter();
    $filter->estado     = $data["estado"];
    $list               = $usrService->load($filter);
    $errorManager->setData($list);
} catch (Exception $ex) {
    $errorManager = new ErrorManager($ex);
}


echo $errorManager->getJsonResponse();
?>