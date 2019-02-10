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

$skin 			= new SkinManager("/admin/modulos/prospectos/html/prospect_main.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$clienteService = new ClientesService();
$request        = new RequestUtil();
$comboService   = new ComboService();
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

$skin->addVariable("TXT_CELULAR"                    , $celular);
$skin->addVariable("TXT_DOMICILIO_PART"             , $domicilio_part);
$skin->addVariable("TXT_DOMICILIO_LABORAL"          , $domicilio_laboral);
$skin->addVariable("TXT_DNI"                        , $dni);



//$filter = new ClienteFilter();
//$list = $clienteService->load($filter);
$loop = new SkinLoop('LOOP_PROSPECTOS');
//foreach ($list as $value) {
    //$ctaCte = $movService->loadByCliente($value->idCliente);

    $loop->addData('TXT_SHOW_OPORTUNIDAD'   , "display:in-line");
    $loop->addData('TXT_SHOW_REVISION'      , "display:none");
    $loop->addData('TXT_SHOW_ORIG_CONSUL'   , "display:none");
    $loop->addData('TXT_SHOW_PROP_SEND'     , "display:in-line");
    $loop->addData('TXT_CANT_PROP_SEND'     , "1");
    
    $loop->addData('TXT_ID_PROSPECT'        , 1);
    $loop->addData('TXT_ID_REGION'          , 1);
    $loop->addData('TXT_ID_CREDITO'         , 111);
    $loop->addData('TXT_SHOW_PROP_SEND'     , "display:in-line");
    $loop->addData('TXT_CANT_PROPUESTA_ENV' , " - 2");
    $loop->addData('TXT_DEBE'               , "<span style='color:green'>FIN</span>");
    $loop->addData('TXT_NOMBRE'             , "Alberto Perez");
    $loop->addData('TXT_CEL'                , 123456676);
    
    $loop->closeRow();
    
    $loop->addData('TXT_SHOW_OPORTUNIDAD'   , "display:none");
    $loop->addData('TXT_SHOW_REVISION'      , "display:in-line");
    $loop->addData('TXT_SHOW_ORIG_CONSUL'   , "display:in-line");
    $loop->addData('TXT_SHOW_PROP_SEND'     , "display:none");
    $loop->addData('TXT_CANT_PROP_SEND'     , "0");
    $loop->addData('TXT_ID_PROSPECT'        , 2);
    $loop->addData('TXT_ID_REGION'          , 1);
    $loop->addData('TXT_ID_CREDITO'         , 222);
    $loop->addData('TXT_SHOW_PROP_SEND'     , "display:none");
    $loop->addData('TXT_CANT_PROPUESTA_ENV' , "");
    $loop->addData('TXT_DEBE'               , "<span style='color:red'>+2c</span>");
    $loop->addData('TXT_NOMBRE'             , "Juan Rodriguez");
    $loop->addData('TXT_CEL'                , 1234556666);
    
    $loop->closeRow();
//}
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

function getHtmlPlantasModal() {
    $skin = new SkinManager("/admin/modulos/clientes/html/inc.plantas-modal.html");
    $skin->addVariable("TXT_ID_CLIENTE","");
    return $skin->getSkin();
}
?>