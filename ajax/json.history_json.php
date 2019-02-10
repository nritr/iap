<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/LogHistoricoService.php";
include_once $path."/admin/php/utils/ErrorManager.php";

$errorManager   = new ErrorManager();
$logService     = new LogHistoricoService();
$request        = new RequestUtil($_POST['data']);

$tipo           = $request->getArrayValue("tipo");

if ($tipo=="load_by_prospect") {
    try {
        $id_prospecto           = $request->getArrayValue("id_prospecto");
        $filter                 = new LogHistoricoFilter();
        $filter->idProspecto    = $id_prospecto;
        $list                   = $logService->load($filter);
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
}

echo $errorManager->getJsonResponse();
?>