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
include_once $path."/admin/php/services/ComboService.php";
include_once $path."/admin/php/services/ProspectoService.php";

$userService	= new UsuarioService();
$errorManager   = new ErrorManager();
$propService    = new ProspectoService();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$notifyUtil             = new NotifyUtil();

$data 	= $_POST['data'];

$id_cliente		= (isset($data['id_cliente']) ? $data['id_cliente'] : null );

if ($data['tipo']=='updatecreate') {
	$nombre 		= $data['nombre'];
	$email 			= $data['email'];
	$nombre_contacto= $data['nombre_contacto'];
	$telefono 		= $data['telefono'];
	$celular 		= $data['celular'];
	$cuit 		    = $data['cuit'];
	$direccion 		= $data['direccion'];
	//$localidad 		= $data['localidad'];
	$observacion	= $data['observacion'];
	$cp 		    = $data['cp'];
	$codigo         = $data['codigo'];
	$is_crear_planta= isset($data['is_crear_planta']) ? 1 : 0;
	//$id_lista_precio= $data['id_lista_precio'];
	
	$obj = new Cliente();
	$obj->idCliente 	= $id_cliente;
	$obj->nombre		= $nombre;
	$obj->email			= $email;
	$obj->nombreContacto= $nombre_contacto;
	$obj->celular		= $celular;
	$obj->cp            = $cp;
	$obj->cuit          = $cuit;
	$obj->direccion     = $direccion;
	$obj->telefono      = $telefono;
	//$obj->localidad     = $localidad;
	$obj->observacion   = $observacion;
	$obj->estado        = 1;
	$obj->codigo        = $codigo;

	try {
	    //$obj = $clienteService->saveUpdate($obj,$is_crear_planta); OLD
	    $errorManager->setData($obj);
	} catch (Exception $ex) {
	    $errorManager = new ErrorManager($ex);
	}
} else if ($data['tipo']=='cargar_planes') {
    /*
     * data.salario 		= salario;
	data.sexo			= sexo;
	data.dni_dom			= dniDom;
	data.edad			= edad;
	data.tit_servicio	= titServicio;
	data.bnemp			= bNemp;
	data.id_vivienda	= idVivienda;
	data.id_trabajo		= idTrabajo;
     */
    $salario            = $data['salario'];
    $nombre             = $data['nombre'];
    $apellido           = $data['apellido'];
    $dni                = $data['dni'];
    $sexo               = $data['sexo'];
    $dni_dom            = $data['dni_dom'];
    $edad               = $data['edad'];
    $tit_servicio       = $data['tit_servicio'];
    $bnemp              = $data['bnemp'];
    $id_vivienda        = $data['id_vivienda'];
    $id_trabajo         = $data['id_trabajo'];
    $nombre             = $data['nombre'];
    $apellido           = $data['apellido'];
    $id_prospecto       = $data['id_prospecto'];
    $dni                = $data['dni'];
    $id_frecuencia_cobro= $data['id_frecuencia_cobro'];

     
    try {
        //TODO: PROSPECTO LOAD Y DESPUES UPDATE DE DATOS NORMALIZADOS
        $prospService       = new ProspectoService();
        $obj                = $prospService->getById($id_prospecto);
        if ($obj->prospectoNormalizado->getId()=="") {
            $obj->dniNormalizado                                        = $dni;
            $obj->nombre                                                = $nombre;
            $obj->apellido                                              = $apellido;
            $obj->prospectoNormalizado->actividadLaboral->idTipoTrabajo = $id_trabajo;
            $obj->prospectoNormalizado->dniDom                          = $dni_dom;
            $obj->prospectoNormalizado->frecuenciaCobro->idDato         = $id_frecuencia_cobro;
            $obj->prospectoNormalizado->genero                          = $sexo;
            $obj->prospectoNormalizado->relaLaboral->idTipoIngresoTope  = $bnemp;
            $obj->prospectoNormalizado->titularidadServicio->idTitularidadServicio = $tit_servicio;
            $obj->prospectoNormalizado->vivienda->idDato                = $id_vivienda;
            $obj->normalizado                                           = 1;
            $id                                                         = $prospService->normalizar($obj);
            $obj->prospectoNormalizado->id                              = $id;
            $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_PROSPECTO_NORMALIZACION);
            
        }
        $comboService       = new ComboService();
        $filter             = new TitularidadServicioFilter($tit_servicio);
        $list               = $comboService->loadTitularidadServicio($filter);
        $lazo               = "";
        if (count($list)>0) {
            $lazo           = $list[0];
        }
        
        $topemonto = $propService->topemonto($salario, $sexo, $dni_dom, $edad, $lazo->relacion, $bnemp, $id_vivienda, $id_trabajo);
        $errorManager->setData($topemonto==0 ? "0" : $topemonto);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
} else if ($data['tipo']=='set_plan') {
    $id_prospecto       = $data['id_prospecto'];
    $id_plan            = $data['id_plan'];
    try {
        $prospService       = new ProspectoService();
        $obj                = $prospService->getById($id_prospecto);
        $obj->plan->id      = $id_plan;
        $prospService->saveUpdate($obj,LogHistorico::$ID_TIPO_ELECCION_PLAN);
    } catch (Exception $ex) {
        $errorManager = new ErrorManager($ex);
    }
}

echo $errorManager->getJsonResponse();
?>