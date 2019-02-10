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

$skin 			= new SkinManager("/admin/modulos/generico/html/main.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$dpService      = new DatosParametricosService();
$request		= new RequestUtil();

$userService->notLoguedRedirect();
$usr 			= $userService->getUserLogued();
$rol            = $usr->rol->idRol;

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$filtro_estado 		= $request->getPost("filtro_estado",0);
$estado 			= $request->getPost("estado",1);
$id_categoria_padre	= $request->getPost("id_categoria_padre",0);

$filter				= new DatoParametricoFilter();
if ($filtro_estado==1) {
	$filter->estado = $estado;
	$filter->idCategoriaPadre= $id_categoria_padre;
	$skin->addVariable("TXT_SELECTED_ELIMINADO",($estado==0 ? "selected" : ""));
}

GeneralUtils::listCategoriasParametricas($skin, $id_categoria_padre, "LOOP_CATEGORIAS", "TXT_ID_LOOP_CATEGORIA", "TXT_DESC_CATEGORIA", "TXT_SELECTED");
listDatosParametricos($skin, $dpService, $id_categoria_padre,$estado,$filtro_estado);

echo $skin->getSkin();

/*---------------------------------------------------------------------------------------------**/



function listDatosParametricos(&$skin,DatosParametricosService $dpService,$id_categoria_padre,$estado,$filtro_estado) {
    $filter = new DatoParametricoFilter();
    $filter->idCategoria = $id_categoria_padre;
    $list = $dpService->load($filter);
    
    $loop = new SkinLoop('LOOP_DATOS_PARAM');
    foreach ($list as $value) {
        $loop->addData('TXT_CATEGORIA'          ,$value->categoria->nombre);
        $loop->addData('TXT_NOMBRE'             ,$value->nombre);
        $loop->addData('TXT_VALOR'              ,$value->valor);
        $loop->addData('TXT_ID_DATO'            ,$value->idDato);
        $loop->addIfZone("IFZONE_BTN_ELIMINAR"  , ($filtro_estado==1 && $estado==0 ? 0 : 1));
        $loop->addIfZone("IFZONE_BTN_ACTIVAR"   , !($filtro_estado==1 && $estado==0 ? 0 : 1));
        $loop->closeRow();
        
    }
    $skin->addLoop($loop);
}
?>