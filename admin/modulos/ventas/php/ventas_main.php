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
include_once $path."/admin/php/services/ClientesService.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/ComboService.php";

$skin 			= new SkinManager("/admin/modulos/ventas/html/ventas_main.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$request        = new RequestUtil();
$userService->notLoguedRedirect();
$usr 			= $userService->getUserLogued();
$rol            = $usr->rol->idRol;


$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

GeneralUtils::cargarDatosParametricos($skin, "LOOP_ORIGENES", "TXT_ID_ORIGEN_LOOP", "TXT_DESC_ORIGEN_LOOP", "TXT_SELECTED_ORIGEN", CategoriaParametrica::$ID_CATEGORIA_ORIGEN_PROSP);

generateHoras($skin);

echo $skin->getSkin();

function generateHoras(SkinManager &$skin) {
    $loop = new SkinLoop('LOOP_HORA');
    for ($i=9;$i<18;$i++) {
        $loop->addData('TXT_HORA'           , $i);
        $loop->closeRow();
    }
    $skin->addLoop($loop);
    
    $loop = new SkinLoop('LOOP_MINUTO');
    for ($i=0;$i<60;$i++) {
        $loop->addData('TXT_MINUTO'         , ($i<10 ? "0".$i : $i));
        $loop->closeRow();
    }
    $skin->addLoop($loop);
}
?>