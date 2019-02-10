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

$skin 			= new SkinManager("/admin/modulos/clientes/html/clientes_main.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$clienteService = new ClientesService();
$request        = new RequestUtil();

$userService->notLoguedRedirect();
$usr 			= $userService->getUserLogued();
$rol            = $usr->rol->idRol;

$nombre             = $request->getPost("nombre");
$celular            = $request->getPost("celular");
$id_recorrido       = $request->getPost("id_recorrido");
$domicilio_part     = $request->getPost("domicilio_part");
$domicilio_laboral  = $request->getPost("domicilio_laboral");
$dni                = $request->getPost("dni");


$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$skin->addVariable("TXT_NOMBRE"                     , $nombre);
$skin->addVariable("TXT_CELULAR"                    , $celular);
$skin->addVariable("TXT_DOMICILIO_PART"             , $domicilio_part);
$skin->addVariable("TXT_DOMICILIO_LABORAL"          , $domicilio_laboral);
$skin->addVariable("TXT_DNI"                        , $dni);

//$skin->addVariable("TXT_INCLUDE_PLANTAS_MODAL",getHtmlPlantasModal());
/*
$filter = new ClienteFilter();
$list = $clienteService->load($filter);
$loop = new SkinLoop('LOOP_CLIENTES');
foreach ($list as $value) {
    $ctaCte = $movService->loadByCliente($value->idCliente);
    $loop->addData('TXT_CODIGO',$value->codigo);
    $loop->addData('TXT_ID_CLIENTE',$value->idCliente);
    $loop->addData('TXT_NOMBRE',$value->nombre);
    $loop->addData('TXT_TELEFONO',$value->telefono);
    $loop->addData('TXT_DIRECCION',$value->direccion);
   // $loop->addData('TXT_AREA',$value->listaPrecios->nombre);
    $loop->addData('TXT_SALDO',$ctaCte->getSaldo());
    
    $loop->closeRow();

}
$skin->addLoop($loop);
*/
echo $skin->getSkin();

function getHtmlPlantasModal() {
    $skin = new SkinManager("/admin/modulos/clientes/html/inc.plantas-modal.html");
    $skin->addVariable("TXT_ID_CLIENTE","");
    return $skin->getSkin();
}
?>