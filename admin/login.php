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
//die();

$skin 				= new SkinManager("/admin/html/login.html");
$skinCommon 		= new SkinCommons();
$userService		= new UsuarioService();
$request			= new RequestUtil();

//$userService->notLoguedRedirect();
//$usr = $userService->getUserLogued();

$skin->addVariable("TXT_INCLUDE_HEAD",$skinCommon->getHead());
//$skin->addVariable("TXT_INCLUDE_BODY_HEAD",$skinCommon->getBodyHead());
//$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL",$skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER",$skinCommon->getFooter());

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $usuario 		= $request->getPost("email");
    $password 		= $request->getPost("password");
    $recordar 		= $request->getPost("remember",0);
    
    $usr = new Usuario();
    $usr->email 	= $usuario;
    $usr->password 	= $password;
    $ret = $userService->login ($usr,$recordar);
    if ($ret==true) {
        header( 'Location: /admin/index.php') ;
        die();
    } else {
        $skin->addIfZone('IFZONE_ERROR',1);
    }
    
    //$usr = $userService->getUserLogued();
} else {
    $skin->addIfZone('IFZONE_ERROR',0);
}


echo $skin->getSkin();


/*------------------------------------------------------------------------*/

?>