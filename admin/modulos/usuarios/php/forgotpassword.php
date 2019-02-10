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


$skin 			= new SkinManager("/admin/modulos/usuarios/html/forgotpassword.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();



$skin->addVariable("TXT_INCLUDE_HEAD",$skinCommon->getHead());

$skin->addVariable("TXT_INCLUDE_FOOTER",$skinCommon->getFooter());

echo $skin->getSkin();
?>