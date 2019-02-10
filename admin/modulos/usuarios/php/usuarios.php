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

$skin 			= new SkinManager("/admin/modulos/usuarios/html/usuarios.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$userService->notLoguedRedirect();
$usr 			= $userService->getUserLogued();
$rol            = $usr->rol->idRol;

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());
$skin->addVariable("TXT_DISPLAY_ALTA",($rol==Rol::$ID_ROL_ADMINISTRADOR ? "" : "none"));

$list = $userService->load();
$loop = new SkinLoop('LOOP_USUARIOS');
foreach ($list as $value) {
    
    if ($rol==Rol::$ID_ROL_ADMINISTRADOR || ($rol!=Rol::$ID_ROL_ADMINISTRADOR && $usr->idUsuario==$value->idUsuario)) {
        $loop->addData('TXT_ID_USUARIO'     , $value->idUsuario);
        $loop->addData('TXT_USUARIO'        , $value->nombre);
        $loop->addData('TXT_EMAIL'          , $value->email);
        $loop->addData('TXT_ROL'            , $value->rol->nombre);
        $loop->addData('TXT_SUCURSAL'       , $value->region->nombre);
        $loop->addData('TXT_DISPLAY_ELIM'   , ($usr->idUsuario==$value->idUsuario ? "none" : ""));
        $loop->addData('TXT_DISPLAY_MODIF'  , ($rol!=Rol::$ID_ROL_ADMINISTRADOR && $usr->idUsuario==$value->idUsuario ? "none" : ""));
        
        $loop->closeRow();
    }
}
$skin->addLoop($loop);
echo $skin->getSkin();
?>