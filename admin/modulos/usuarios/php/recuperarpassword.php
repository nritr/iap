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


$skin 			= new SkinManager("/admin/modulos/usuarios/html/recuperarpassword.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();


$code           = $request->getGet("codigo");

$skin->addVariable("TXT_INCLUDE_HEAD"   , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_FOOTER" , $skinCommon->getFooter());

$usr            = getUsuario($code);

$skin->addIfZone("TXT_FORM_CLAVE"       , ($usr ? 1 : 0));
$skin->addIfZone("TXT_ERROR"            , !($usr ? 1 : 0));
$skin->addVariable("TXT_CODIGO"         , $code);
echo $skin->getSkin();

function getUsuario($codigo) {
    $usrService             = new UsuarioService();
    $filter                 = new UsuarioFilter();
    $filter->codigoInterno  = $codigo;
    $list                   = $usrService->load($filter);
    if (count($list)>0) {
        return $list[0];
    }
    return null;
}
?>