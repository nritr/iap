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
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/ClientesService.php";
include_once $path."/admin/php/services/ComboService.php";
include_once $path."/admin/php/services/DatosParametricosService.php";

$skin 			= new SkinManager("/admin/modulos/prospectos/html/prospect_new.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();
$userService	= new UsuarioService();
$datoService    = new DatosParametricosService();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$id_prospecto 		= $request->getRequest("id_prospecto");


$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

if ($id_prospecto==0) {
    $skin->addVariable("TXT_ACCION_USUARIO"                   , "Nuevo");
}
$skin->addVariable("TXT_ID_LOCALIDAD"               , "");
$skin->addVariable("TXT_FECHA_NAC"                  , "");
$skin->addVariable("TXT_NOMBRE"                     , "");
$skin->addVariable("TXT_OBSERVACION"                , "");


echo $skin->getSkin();

?>