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
include_once $path."/admin/php/services/MovimientosService.php";
include_once $path."/admin/php/services/DatosParametricosService.php";

$skin 			= new SkinManager("/admin/modulos/clientes/html/cliente.html");
$skinCommon 	= new SkinCommons();
$request		= new RequestUtil();
$userService	= new UsuarioService();
$datoService    = new DatosParametricosService();

$userService->notLoguedRedirect();
$usr = $userService->getUserLogued();

$id_cliente 		= $request->getRequest("id_cliente");
$ver_saldo          = $request->getPost("ver_saldo");

$skin->addVariable("TXT_INCLUDE_HEAD",$skinCommon->getHead());
$skin->addVariable("TXT_INCLUDE_BODY_HEAD",$skinCommon->getBodyHead());
$skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL",$skinCommon->getNavigationPanel());
$skin->addVariable("TXT_INCLUDE_FOOTER",$skinCommon->getFooter());
$skin->addVariable("TXT_CLASS_LF",$skinCommon->getClassLF());
$skin->addVariable("TXT_INCLUDE_PLANTAS_MODAL",getHtmlPlantasModal($id_cliente));
$skin->addVariable("TXT_ACCION_USUARIO",($id_cliente=="" ? "Alta de Cliente" : "Modificar Cliente"));

if ($ver_saldo==1) {
    $skin->addIfZone("IFZONE_FORM", 0);
}
$clienteService	= new ClientesService();

if ($id_cliente>0) {
    $obj = $clienteService->getById($id_cliente);
    $skin->addVariable("TXT_ID_CLIENTE",$id_cliente);
    $skin->addVariable("TXT_CODIGO",$obj->codigo);
    $skin->addVariable("TXT_EMAIL",$obj->email);
    $skin->addVariable("TXT_NOMBRE",$obj->nombre);
    $skin->addVariable("TXT_NOMBRE_APE",$obj->nombreContacto);
    $skin->addVariable("TXT_TELEFONO",$obj->telefono);
    $skin->addVariable("TXT_CELULAR",$obj->celular);
    $skin->addVariable("TXT_CUIT",$obj->cuit);
    $skin->addVariable("TXT_DIRECCION",$obj->direccion);
    $skin->addVariable("TXT_CP",$obj->cp);
    $skin->addVariable("TXT_LOCALIDAD",$obj->localidad);
    $skin->addVariable("TXT_OBSERVACION",$obj->observacion);
    //GeneralUtils::cargarAreas($skin,$datoService,$obj->listaPrecios->idDato);
    listMovimientos($skin, $id_cliente);
    $skin->addIfZone("IFZONE_MOVIMIENTOS", 1);
    $skin->addIfZone("IFZONE_PLANTAS", 1);
    $skin->addIfZone("IFZONE_ADD_PLANTA", 0);
    
} else {
    $skin->addVariable("TXT_ID_CLIENTE","");
    $skin->addVariable("TXT_CODIGO","");
    $skin->addVariable("TXT_EMAIL","");
    $skin->addVariable("TXT_NOMBRE","");
    $skin->addVariable("TXT_NOMBRE_APE","");
    $skin->addVariable("TXT_TELEFONO","");
    $skin->addVariable("TXT_CELULAR","");
    $skin->addVariable("TXT_CUIT","");
    $skin->addVariable("TXT_DIRECCION","");
    $skin->addVariable("TXT_CP","");
    $skin->addVariable("TXT_LOCALIDAD","");
    $skin->addVariable("TXT_OBSERVACION","");
    GeneralUtils::cargarAreas($skin,$datoService);
    $skin->addIfZone("IFZONE_MOVIMIENTOS", 0);
    $skin->addIfZone("IFZONE_PLANTAS", 0);
    $skin->addIfZone("IFZONE_ADD_PLANTA", 1);
}



echo $skin->getSkin();

function listMovimientos(SkinManager &$skin,int $id_cliente) {
    //TODO: falta desarrollar
    //$movService     = new MovimientosService();
    $obj           = $movService->loadByCliente($id_cliente);
    $list = $obj->getList();
    $loop = new SkinLoop('LOOP_CLIENTES');
    
    foreach ($list as $value) {
        //$factura = new Factura();
        $factura->modoFactura = $value->modoFactura;
        $loop->addData('TXT_FECHA',$value->getFecha2Screen());
        $loop->addData('TXT_OPERACION',$value->getOperacion());
        $loop->addData('TXT_VENTA',$value->salida);
        $loop->addData('TXT_PAGO',$value->entrada);
        $loop->addData('TXT_SALDO',$value->getSaldo());
        $loop->addData('TXT_FACTURA',$value->factura);
        $loop->addData('TXT_TIPO',$factura->getModoFactura());
        $loop->addData('TXT_ID_FACTURA',$value->idFactura);
        $loop->addData('TXT_DISPLAY',($value->idFactura=="" ? "none" : ""));
        
        $loop->closeRow();
    }
    $skin->addLoop($loop);
    
    /**
     * <td>$TXT_TOTAL_COMPRA$</td>
											<td>$TXT_TOTAL_PAGO$</td>
											<td>$TXT_TOTAL_SALDO$</td>
     */
    $skin->addVariable("TXT_TOTAL_COMPRA",$obj->getSalidaTotal());
    $skin->addVariable("TXT_TOTAL_PAGO",$obj->getIngresoTotal());
    $skin->addVariable("TXT_TOTAL_SALDO",$obj->getSaldo());
}

?>