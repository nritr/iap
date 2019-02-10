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
include_once $path."/admin/php/services/ComboService.php";

$skin 			= new SkinManager("/admin/modulos/usuarios/html/usuario.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();
$userService	= new UsuarioService();
$comboService   = new ComboService();
$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$idUsuario 		= $request->getPost("id_usuario",0);

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$skin->addVariable("TXT_ACCION_USUARIO",($idUsuario==0 ? "Alta de Usuario" : "Modificar Usuario"));

$userService	= new UsuarioService();
$usr			= new Usuario();
if ($idUsuario!=0) {
	$usr = $userService->getUserById($idUsuario);
	$skin->addVariable("TXT_ID_USUARIO",$idUsuario);
	$skin->addVariable("TXT_NOMBRE",$usr->nombre);
	$skin->addVariable("TXT_EMAIL",$usr->email);
	$skin->addVariable("TXT_DISABLED","disabled");
	$skin->addVariable("TXT_REQUERIDO","");
	$skin->addVariable("TXT_ESTADO",$usr->estado);
	$skin->addIfZone('IFZONE_PASSWORD',0);
} else {
	$skin->addVariable("TXT_ID_USUARIO","");
	$skin->addVariable("TXT_NOMBRE","");
	$skin->addVariable("TXT_EMAIL","");
	$skin->addVariable("TXT_DISABLED","");
	$skin->addVariable("TXT_REQUERIDO","required");
	$skin->addVariable("TXT_ESTADO",1);
	$skin->addIfZone('IFZONE_PASSWORD',1);
}


$list = $comboService->loadRoles();
$loop = new SkinLoop('LOOP_ROLES');
foreach ($list as $value) {
	$loop->addData('TXT_ID_ROL',$value->idRol);
	$loop->addData('TXT_ROL_LOOP',$value->nombre);
	$loop->addData('TXT_ROL_TIPO_ASIG',$value->tipoAsignacion);
	if ($usr->rol!=null) {
		$loop->addData('TXT_ROL_SELECTED',($usr->rol->idRol==$value->idRol ? "selected" : ""));
	} else {
		$loop->addData('TXT_ROL_SELECTED',"");
	}
	$loop->closeRow();
}
$skin->addLoop($loop);


$list = $comboService->loadRegiones();
$loop = new SkinLoop('LOOP_REGIONES');
foreach ($list as $value) {
	$loop->addData('TXT_ID_REGION',$value->idRegion);
	$loop->addData('TXT_REGION_LOOP',$value->nombre);
	if ($usr->region!=null) {
		$loop->addData('TXT_REGION_SELECTED',($usr->region->idRegion==$value->idRegion ? "selected" : ""));
	} else {
		$loop->addData('TXT_REGION_SELECTED',"");
	}
	$loop->closeRow();
}
$skin->addLoop($loop);

echo $skin->getSkin();
?>