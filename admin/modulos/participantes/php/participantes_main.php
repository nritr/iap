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
include_once $path."/admin/php/services/DatosService.php";

$skin 			= new SkinManager("/admin/modulos/participantes/html/participantes_main.html");
$skinCommon 	= new SkinCommons();
$userService	= new UsuarioService();
$userService->notLoguedRedirect();
$usr 			= $userService->getUserLogued();
$datosServices  = new DatosService();
$request        = new RequestUtil();


$value                  = $request->getPost("value");
$filter                 = $request->getPost("filter");

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$skin->addVariable("TXT_FILTER"                     , $filter);
$skin->addVariable("TXT_VALUE"                      , $value);

$skin->addVariable("TXT_COUNTRIES"                  , getCountries());
$skin->addVariable("TXT_JOB_TITLE"                  , getJobTitles());

$filter2                 = new DatoFilter();
$filter2->setDynamicFilter($filter, $value);
$list = $datosServices->load($filter2);
$loop = new SkinLoop('LOOP_PARTICIPANTS');
foreach ($list as $value) {
    $loop->addData('TXT_ID_DATOS'           , $value->id);
    $loop->addData('TXT_CODE'               , $value->inscription->code);
    $loop->addData('TXT_FULL_NAME'          , $value->firstName." ".$value->lastName);
    $loop->addData('TXT_PASSPORT'           , $value->passportNumber);
    $loop->addData('TXT_COUNTRY'            , $value->country);
    $loop->addData('TXT_EMAIL'              , $value->email);
    $loop->addData('TXT_STATUS'             , $value->inscription->status);
    $loop->addData('TXT_ARRIVED'            , ($value->inscription->arrived==0 ? "NO" : "YES"));
    $loop->addData('TXT_REGISTER_DATE'      , $value->registerDate);
    
    $loop->closeRow();

}
$skin->addLoop($loop);
echo $skin->getSkin();


function getCountries() {
    $skin 			= new SkinManager("/admin/modulos/participantes/html/countries.html");
    return $skin->getSkin();
}
function getJobTitles() {
    $skin 			= new SkinManager("/admin/modulos/participantes/html/jobtitle.html");
    return $skin->getSkin();
}
?>