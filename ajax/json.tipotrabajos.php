<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
include_once $path."/admin/php/services/UsuarioService.php";
include_once $path."/admin/php/services/ComboService.php";



$userService        = new UsuarioService();
$comboService       = new ComboService();
$request            = new RequestUtil();
$userService->notLoguedRedirect();
$usr                = $userService->getUserLogued();


$row = array();
$return_arr = array();
$row_array = array();

if(($request->getGet("term") && strlen($request->getGet("term")) > 0) || ($request->getGet("id") && is_numeric($request->getGet("id")))) {
    
    if ($request->getGet("term")) {
        $filter             = new TipoTrabajoFilter();
        $filter->nombreLike = $request->getGet("term");
        $list               = $comboService->loadTipoTrabajo($filter);
        foreach ($list as $value) {
            
            $row_array['id']    = $value->idTipoTrabajo;
            $row_array['text']  = $value->nombre;
            array_push($return_arr,$row_array);
            
        }
    } elseif ($request->getGet("id")) {
        $filter         = new TipoTrabajoFilter($request->getGet("id"));
        $obj            = $comboService->loadTipoTrabajo($filter)[0];
        $row_array['id']    = $obj->idTipoTrabajo;
        $row_array['text']  = $obj->nombre;
        array_push($return_arr,$row_array);

    } else {
        $row_array['id'] = 0;
        $row_array['text'] = utf8_encode('Escribe....');
        array_push($return_arr,$row_array);
    }
} else if ($request->getGet("term")=="" && $request->getGet("id")=="") {
    $list = $comboService->loadTipoTrabajo();
    foreach ($list as $value) {
        
        $row_array['id']    = $value->idTipoTrabajo;
        $row_array['text']  = $value->nombre;
        array_push($return_arr,$row_array);
        
    }
}

$ret = array();
/* this is the return for a single result needed by select2 for initSelection */
if($request->getGet("id")) {
    $ret = $row_array;
}
/* this is the return for a multiple results needed by select2
 * Your results in select2 options needs to be data.result
 */
else {
    $ret['results'] = $return_arr;
}
echo json_encode($ret);

?>