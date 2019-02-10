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
include_once $path."/admin/php/services/DatosParametricosService.php";
include_once $path."/admin/php/services/UsuarioService.php";


$skin 				= new SkinManager("/admin/modulos/generico/html/edit.html");
$skinCommon 		= new SkinCommons();
$userService		= new UsuarioService();
$dpService	        = new DatosParametricosService();
$request			= new RequestUtil();

$userService->notLoguedRedirect();
$usr 				= $userService->getUserLogued();

$id_dato    		= $request->getPost("id_dato",0);


$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$skin->addVariable("TXT_ACCION_RUBRO",($id_dato==0 ? "Alta de Dato Paramétrico" : "Modificar Dato Paramétrico" ));
$skin->addVariable("TXT_ID_DATO",($id_dato==0 ? "" : $id_dato ));


if ($id_dato==0) {
    $skin->addVariable("TXT_NOMBRE","");
	$skin->addVariable("TXT_VALOR","");
	GeneralUtils::listCategoriasParametricas($skin, null, "LOOP_CATEGORIAS", "TXT_ID_LOOP_CATEGORIA", "TXT_DESC_CATEGORIA", "TXT_SELECTED");

} else {
    $obj = $dpService->getById($id_dato);
    $skin->addVariable("TXT_NOMBRE",$obj->nombre);
    $skin->addVariable("TXT_VALOR",$obj->valor);
    $skin->addVariable("TXT_ID_DATO",$obj->idDato);

    GeneralUtils::listCategoriasParametricas($skin, $obj->categoria->idCategoriaParametrica, "LOOP_CATEGORIAS", "TXT_ID_LOOP_CATEGORIA", "TXT_DESC_CATEGORIA", "TXT_SELECTED");
}

echo $skin->getSkin();


/*---------------------------------------------------------------*/



?>