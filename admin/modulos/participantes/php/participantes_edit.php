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
include_once $path."/admin/php/services/DatosService.php";

$skin 			= new SkinManager("/admin/modulos/participantes/html/participantes_edit.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();
$userService	= new UsuarioService();
$comboService   = new ComboService();
$datosServices  = new DatosService();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$id_datos 		= $request->getPost("id_datos");

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());


$obj			= $datosServices->getById($id_datos);
if ($obj) {
	$skin->addVariable("TXT_CODE"                          , $obj->inscription->code);
	$skin->addVariable("TXT_ID_DATOS"                      , $obj->id);
	$skin->addVariable("TXT_TOTAL"                         , $obj->ticket->total);
	$skin->addVariable("TXT_SELECTED_TICKET_DAY"           , strpos(" ".$obj->ticket->description, "Day")>0 ? "selected" : "");
	$skin->addVariable("TXT_SELECTED_TICKET_FULL"          , strpos(" ".$obj->ticket->description, "Early")>0 ? "selected" : "");
	$skin->addVariable("TXT_FECHAS_TICKETS"                , getDaysParticipantString($obj->ticket));
	$skin->addVariable("TXT_PREVIX"                        , $obj->prefix);
	$skin->addVariable("TXT_PASSPORT_EXPIRATION_DATE"      , $obj->passportExpiryDate);
	$skin->addVariable("TXT_FIRST_NAME"                    , $obj->firstName);
	$skin->addVariable("TXT_ADDRESS1"                      , $obj->address1);
	$skin->addVariable("TXT_MIDDLE_NAME"                   , $obj->middleName);
	$skin->addVariable("TXT_ADDRESS2"                      , $obj->address2);
	$skin->addVariable("TXT_LAST_NAME"                     , $obj->lastName);
	$skin->addVariable("TXT_POSTAL_CODE"                   , $obj->postalCode);
	$skin->addVariable("TXT_NAME_BADGE"                    , $obj->badgeName);
	$skin->addVariable("TXT_CITY"                          , $obj->city);
	$skin->addVariable("TXT_PASSPORT_NATIONALITY"          , $obj->passportNationality);
	$skin->addVariable("TXT_VAL_COUNTRY"                   , $obj->country);
	$skin->addVariable("TXT_COUNTRIES"                     , getCountries());
	$skin->addVariable("TXT_PASSPORT_NUMBER"               , $obj->passportNumber);
    $skin->addVariable("TXT_DIETARY"                       , $obj->specialDietaryPhysical);
    $skin->addVariable("TXT_JOBTITLES"                     , getJobTitles());
    $skin->addVariable("TXT_VAL_JOB_TITLE"                 , $obj->jobTitle);
    $skin->addVariable("TXT_DELEGATE_EMAIL"                , $obj->delegateEmail);
    $skin->addVariable("TXT_ORGANIZATION"                  , $obj->organisation);
    $skin->addVariable("TXT_WORKPHONE"                     , $obj->workPone);
    $skin->addVariable("TXT_EMAIL"                         , $obj->email);
    $skin->addVariable("TXT_MOBILEPHONE"                   , $obj->mobilePhone);
    $skin->addVariable("TXT_PICTURE"                       , $obj->getImageName());
    $skin->addVariable("DB_PICTURE"                        , $obj->inscription->image);
    $skin->addVariable("TXT_CONTRIBUTE_YES"                , $obj->isContribute() ? "checked" : "");
    $skin->addVariable("TXT_CONTRIBUTE_NO"                 , !$obj->isContribute() ? "checked" : "");
    $skin->addVariable("TXT_COMMITTEE_MEMBER_YES"          , $obj->isCommitteeMember() ? "checked" : "");
    $skin->addVariable("TXT_COMMITTEE_MEMBER_NO"           , !$obj->isCommitteeMember() ? "checked" : "");
    $skin->addVariable("TXT_CONTRIBUTE_TYPE"               , $obj->contributeType);
    $skin->addVariable("TXT_CONTRIBUTORS_TYPE"             , getContributorType());
    
    $skin->addVariable("TXT_GRANTING_PARTICIPANT_YES"      , $obj->isGrantingParticipant() ? "checked" : "");
    $skin->addVariable("TXT_GRANTING_PARTICIPANT_NO"       , !$obj->isGrantingParticipant() ? "checked" : "");
    
}
/*

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
*/
echo $skin->getSkin();


/***
 * 
 */
function getDaysParticipantString(Ticket $ticket) {
    $ret = "";
    if (isset($ticket->days) && count($ticket->days)>0) {
        foreach ($ticket->days as $value) {
            $ret .= $value->day.",";
        }
    }
    return rtrim($ret, ',');
}
function getCountries() {
    $skin 			= new SkinManager("/admin/modulos/participantes/html/countries.html");
    return $skin->getSkin();
}
function getJobTitles() {
    $skin 			= new SkinManager("/admin/modulos/participantes/html/jobtitle.html");
    return $skin->getSkin();
}
function getContributorType() {
    $skin 			= new SkinManager("/admin/modulos/participantes/html/contributorType.html");
    return $skin->getSkin();
}
?>