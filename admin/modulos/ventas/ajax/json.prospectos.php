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
include_once $path."/admin/php/utils/NotifyUtil.php";
include_once $path."/admin/php/utils/ErrorManager.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/ProspectoService.php";

$userService	= new UsuarioService();
$errorManager   = new ErrorManager();
$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$prospService   = new ProspectoService();
$notifyUtil     = new NotifyUtil();
$request        = new RequestUtil($_POST['data']);


$tipo           = $request->getArrayValue("tipo");

if ($tipo=='updatecreate') {
	/*$nombre 		= $data['nombre'];

	try {
	    $obj = $clienteService->saveUpdate($obj,$is_crear_planta);
	    $errorManager->setData($obj);
	} catch (Exception $ex) {
	    $errorManager = new ErrorManager($ex);
	}*/
} else if ($tipo=='load') {
    $datosLike              = $request->getArrayValue("datos_like");
    $llamadopend            = $request->getArrayValue("llamadopend");
    $id_origen              = $request->getArrayValue("id_origen");
    $id_sub_estado          = $request->getArrayValue("id_sub_estado");
    $id_estado              = $request->getArrayValue("id_estado");
    $propenviada            = $request->getArrayValue("propenviada");
    $ver_todos              = $request->getArrayValue("ver_todos");
    
    $filter                 = new ProspectoFilter();
    $filter->idOrigen       = $id_origen;
    
    if ($llamadopend=="" && $ver_todos=="") {//no esta el filtro, volver a llamar futuro
        $filter->volverLlamarOrNull = 1;
    } else if ($ver_todos=="") {
        
        $filter->llamadoPend = 1;
    }
    $filter->propuestaEnviada = $propenviada;
    if (strlen($datosLike)>4) {
        if (is_numeric($datosLike)) {
            $filter->telefonoLike   = $datosLike;
        } else {
            $filter->nombreLike     = $datosLike;
        }
    }
    
    $filter->idSubEstado    = $id_sub_estado;
    $filter->idEstado       = ($id_estado=="" ? ProspectoEstado::$ID_ESTADO_PENDIENTE : $id_estado);
    try {
        $list               = $prospService->load($filter);
        $errorManager->setData($list);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
} else if ($tipo=='sendMsg') {
    $id_prospecto       = $request->getArrayValue("id_prospecto");
    $obj                = $prospService->getById($id_prospecto);
    $obj->ultLlamado    = date("Y-m-d H:i:s");
    $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_PROSPECTO_LLAMADO);
} else if ($tipo=='volLlamar') {
    $id_prospecto       = $request->getArrayValue("id_prospecto");
    $obj                = $prospService->getById($id_prospecto);
    $obj->ultLlamado    = date("Y-m-d H:i:s");
    $obj->cantLlamados  = ($obj->cantLlamados=="" || $obj->cantLlamados==0 ? 1 : $obj->cantLlamados+1);
    $obj->proxLlamado   = $obj->proxLlamado();
    $obj->estado->idProspectoEstado = ProspectoEstado::$ID_PEND_VOLV_LLAMAR;
    $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_PROSPECTO_VOLV_LLAMAR);
} else if ($tipo=='save-volv-llamar') {
    $fecha_llamar       = $request->getArrayValue("fecha_llamar");
    $id_prospecto       = $request->getArrayValue("id_prospecto_llamar");
    $horario_llamar     = $request->getArrayValue("horario_llamar");
    $minuto_llamar      = $request->getArrayValue("minuto_llamar");
    $obj                = $prospService->getById($id_prospecto);
    $obj->ultLlamado    = date("Y-m-d H:i:s");
    $obj->cantLlamados  = ($obj->cantLlamados=="" || $obj->cantLlamados==0 ? 1 : $obj->cantLlamados+1);
    $obj->proxLlamado   = GeneralUtils::getDateSqlWithHours($fecha_llamar,$horario_llamar,$minuto_llamar);
    $obj->estado->idProspectoEstado = ProspectoEstado::$ID_PEND_VOLV_LLAMAR;
    $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_PROSPECTO_VOLV_URG);
} else if ($tipo=='changeEstado') {
    $str_estado                     = $request->getArrayValue("str_estado");
    $id_prospecto                   = $request->getArrayValue("id_prospecto");
    $obj                            = $prospService->getById($id_prospecto);
    
    $obj->estado->idProspectoEstado = ($str_estado=='req' ? ProspectoEstado::$ID_DEN_NO_CUMPL_REQ : 
        ($str_estado=='zon' ? ProspectoEstado::$ID_DEN_FUERA_COBERTURA : 
            $str_estado=='amb' ? ProspectoEstado::$ID_DEN_AMBIENTAL : 
            ($str_estado=='ni' ? ProspectoEstado::$ID_IGN_NI : 
                $str_estado=='err' ? ProspectoEstado::$ID_IGN_ERROR : $obj->estado->idProspectoEstado)));
    $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_CAMBIO_ESTADO);
}

echo $errorManager->getJsonResponse();
?>