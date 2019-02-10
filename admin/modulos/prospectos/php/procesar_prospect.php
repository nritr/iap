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
include_once $path."/admin/php/services/ClientesService.php";
include_once $path."/admin/php/services/ComboService.php";
include_once $path."/admin/php/services/ProspectoService.php";
include_once $path."/admin/php/services/DatosParametricosService.php";

$skin 			= new SkinManager("/admin/modulos/prospectos/html/procesar_prospect.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();
$userService	= new UsuarioService();
$datoService    = new DatosParametricosService();
$prospService   = new ProspectoService();
$comboService   = new ComboService();
$config         = GeneralUtils::getConfigIni();
$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();


$id_prospecto 	= $request->getRequest("id_prospecto");

$api_key        = $config['GOOGLE_API_KEY'];

$skin->addVariable("TXT_INCLUDE_HEAD"               , $skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD"          , $skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL"   , $skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER"             , $skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF"                   , $skinCommon->getClassLF());

$skin->addVariable("TXT_GOOGLE_API_KEY"             , $api_key);

if ($id_prospecto>0) {
    $obj            = $prospService->getById($id_prospecto);
    
    //TODO: poner el id del cliente en caso que ya este normalizado
    populateActLaboral($skin,$comboService,$obj->prospectoNormalizado->actividadLaboral->idTipoTrabajo);
    populateTitularidadServ($skin, $comboService,$obj->prospectoNormalizado->titularidadServicio->idTitularidadServicio);
    //TODO: poner el id del cliente en caso que ya este normalizado
    GeneralUtils::cargarDatosParametricos($skin, "LOOP_FREQ_COBRO", "TXT_ID_FREQ_COBRO", "TXT_DESC_FREQ_COBRO", "TXT_CHECKED_FREQ_COBRO", CategoriaParametrica::$ID_CATEGORIA_FREQ_COBRO,$obj->prospectoNormalizado->frecuenciaCobro->idDato);
    GeneralUtils::cargarDatosParametricos($skin, "LOOP_VIVIENDA", "TXT_ID_TIPO_VIVIENDA", "TXT_DESC_TIPO_VIV", "TXT_CHECKED_TIPO_VIV", CategoriaParametrica::$ID_CATEGORIA_TIPO_VIVIENDA,$obj->prospectoNormalizado->vivienda->idDato,$obj->vivienda);
    GeneralUtils::cargarDatosParametricos($skin, "LOOP_VIVIENDA_CONTRATO", "TXT_ID_CONTRATO", "TXT_DESC_CONTRATO", "TXT_CHECKED_CONTRATO", CategoriaParametrica::$ID_CATEGORIA_CONTRATO,null,$obj->contrato);
    $skin->addVariable("TXT_ID_PROSPECTO"               , $id_prospecto);
    $skin->addVariable("TXT_LOCALIDAD_ORIG"             , $obj->localidad);
    $skin->addVariable("TXT_ID_LOCALIDAD"               , "");
    $skin->addVariable("TXT_ID_LOCALIDAD_LABORAL"       , "");
    $skin->addVariable("TXT_PISO_DPTO"                  , "");
    $skin->addVariable("TXT_DNI"                        , ($obj->dniNormalizado=="" ? $obj->dni : $obj->dniNormalizado));
    $skin->addVariable("TXT_DIRECCION"                  , "");
    $skin->addVariable("TXT_DIRECCION_GOOGLE"           , "");
    $skin->addVariable("TXT_NRO_PUERTA"                 , "");
    $skin->addVariable("TXT_LOCALIDAD_GOOGLE"           , "");
    $skin->addVariable("TXT_ESTADO_GOOGLE"              , "");
    $skin->addVariable("TXT_COD_POST_GOOGLE"            , "");
    $skin->addVariable("TXT_DIRECCION_LAT"              , "");
    $skin->addVariable("TXT_DIRECCION_LNG"              , "");
    $skin->addVariable("TXT_MARKER_LAT"                 , "");
    $skin->addVariable("TXT_MARKER_LNG"                 , "");
    $skin->addVariable("TXT_NOMBRE"                     , $obj->getNombre());
    $skin->addVariable("TXT_NOMBRE_FB"                  , $obj->nombreFb);
    $skin->addVariable("TXT_APELLIDO"                   , $obj->getApellido());
    $skin->addVariable("TXT_FREQ_COBRO"                 , "Por Mes");
    
    if ($obj->prospectoNormalizado->getId()=="") {
        $skin->addVariable("TXT_DNI_COINCIDE_SI"            , $obj->dniCoincide!="" && $obj->isDNICoincide() ? "checked" : "");
        $skin->addVariable("TXT_DNI_COINCIDE_NO"            , $obj->dniCoincide!="" && !$obj->isDNICoincide() ? "checked" : "");
    } else {
        $skin->addVariable("TXT_DNI_COINCIDE_SI"            , $obj->prospectoNormalizado->dniDom==1 ? "checked" : "");
        $skin->addVariable("TXT_DNI_COINCIDE_NO"            , $obj->prospectoNormalizado->dniDom==2 ? "checked" : "");
    }
    if ($obj->prospectoNormalizado->getId()=="") {
        $skin->addVariable("TXT_GENERO_MASCULINO"           , $obj->genero!="" && $obj->isGeneroMasculino() ? "checked" : "");
        $skin->addVariable("TXT_GENERO_FEMENINO"            , $obj->genero!="" && !$obj->isGeneroMasculino() ? "checked" : "");
    } else {
        $skin->addVariable("TXT_GENERO_MASCULINO"           , $obj->prospectoNormalizado->genero==1 ? "checked" : "");
        $skin->addVariable("TXT_GENERO_FEMENINO"            , $obj->prospectoNormalizado->genero==2 ? "checked" : "");
    }
    
    $skin->addVariable("TXT_EDAD"                       , $obj->edad);

    $skin->addVariable("TXT_ACT_LAB"                    , $obj->oficio);
    $skin->addVariable("TXT_RELA_LABO"                  , $obj->blancoNegro);
    $skin->addVariable("TXT_TITUL_SERVICIO"             , $obj->servicioAnombrede);
    $skin->addVariable("TXT_ID_ACTIVIDAD_LABORAL"       , $obj->prospectoNormalizado->actividadLaboral->idTipoTrabajo);
    $skin->addVariable("TXT_SALARIO"                    , $obj->salario);
    $skin->addVariable("TXT_CELULAR"                    , $obj->normalizarCel());
    $skin->addVariable("TXT_CELULAR_ORIGINAL"           , $obj->celular);
    
    $skin->addVariable("TXT_TEL_PARTICULAR"             , "");
    $skin->addVariable("TXT_TEL_LABORAL"                , "");
    $skin->addVariable("TXT_TEL_REFERENCIA"             , "");
    $skin->addVariable("TXT_ESTAB_LAB"                  , "");
    $skin->addVariable("TXT_DIRECCION_LAB"              , "");
    $skin->addVariable("TXT_FECHA_ENTREGA"              , "");
    $skin->addVariable("TXT_ID_RECORRIDO"               , "");
    $skin->addVariable("TXT_DOCUMENTACION"              , "");
    $skin->addVariable("TXT_NOTAS"                      , "");
    $skin->addVariable("TXT_PISO_DPTO_LAB"              , "");
    
    $skin->addVariable("TXT_VIVIENDA_ALQUILA"           , DatoParametrico::$VIVIENDA_ALQUILA);
    
}
echo $skin->getSkin();

/*----------------------------------------------------------------------*/
function populateActLaboral(&$skin,ComboService $comboService,$id_tipo_ing_tope=null) {

    $list = $comboService->loadTipoIngresoTope();
    
    $loop = new SkinLoop("LOOP_ACTIVIDAD_LAB");
    foreach ($list as $value) {
        $loop->addData("TXT_ID_ACT_LABORAL"         , $value->idTipoIngresoTope);
        $loop->addData("TXT_CHECKED_ACT_LABORAL"    , ($id_tipo_ing_tope!="" && $id_tipo_ing_tope==$value->idTipoIngresoTope ? "selected" : ""));
        $loop->addData("TXT_DESC_ACT_LABORAL"       , $value->nombre);

        $loop->closeRow();
    }
    $skin->addLoop($loop);
}
function populateTitularidadServ(&$skin,ComboService $comboService,$id_titularidad_serv=null) {
    
    $list = $comboService->loadTitularidadServicio();
    
    $loop = new SkinLoop("LOOP_TITULO_SERVICIO");
    foreach ($list as $value) {
        $loop->addData("TXT_ID_TITULARIDAD"         , $value->idTitularidadServicio);
        $loop->addData("TXT_CHECKED_TITULARIDAD"    , ($id_titularidad_serv!="" && $id_titularidad_serv==$value->idTitularidadServicio ? "selected" : ""));
        $loop->addData("TXT_DESC_TITULARIDAD"       , $value->titularidadServicio);
        
        $loop->closeRow();
    }
    $skin->addLoop($loop);
}

?>