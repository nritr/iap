<?php
//echo '{"code":"001052434906","total":"250"}';
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);

include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/dao/DatosDao.php";
include_once $path."/admin/php/services/DatosService.php";


$filter = new DatoFilter();
$filter->passportNumber = $_GET["passportNumber"];

$datoService = new DatosService();
$list = $datoService->load($filter);

if($list != null){
    echo json_encode($list[0]);
}
?>