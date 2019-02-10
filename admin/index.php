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


$skin 				= new SkinManager("/admin/html/index.html");
$skinCommon 		= new SkinCommons();
$userService		= new UsuarioService();
$request			= new RequestUtil();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$skin->addVariable("TXT_INCLUDE_HEAD",$skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD",$skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL",$skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER",$skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF",$skinCommon->getClassLF());



echo $skin->getSkin();


/*------------------------------------------------------------------------*/

?>