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
include_once $path."/admin/php/services/DocumentoService.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/DatosParametricosService.php";

$request        = new RequestUtil();
$userService	= new UsuarioService();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$id_documento   = $request->getRequest("id_documento");
$tipo           = $request->getRequest("tipo");

$doc            = $docService->getById($id_documento);

if ($tipo=="ver") {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="'.$doc->nombre.'"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    header('Content-Length: '.filesize($doc->getPath()));
} else {
    header('Cache-Control: public');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$doc->nombre.'"');
    header('Content-Length: '.filesize($doc->getPath()));
}




readfile($doc->getPath());
?>