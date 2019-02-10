<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);


include_once $path."/admin/config.php";

include_once $path."/admin/php/utils/SkinManager.php";
include_once $path."/admin/php/utils/RequestUtil.php";
include_once $path."/admin/php/utils/GeneralUtils.php";


$skin 				= new SkinManager("/html/index.html");

$request			= new RequestUtil();








echo $skin->getSkin();


/*------------------------------------------------------------------------*/
?>