<?php
session_start();

$pos = strpos($_SERVER['HTTP_HOST'], ".ar");

if((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") &&  $pos !== false ){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
/**
 * Database config variables
*/

//define("DB_HOST", "131.255.5.12");
define("DB_HOST", "localhost");
define("DB_USER", "followdevelopcom_iap_site");
define("DB_PASSWORD", "iap4376##YH");
define("DB_DATABASE", "followdevelopcom_iap_site");


/*
//define("DB_HOST", "131.255.5.12");
define("DB_HOST", "localhost");
define("DB_USER", "micreditochat_db_creditochat");
define("DB_PASSWORD", "miChatC\$ed321#");
define("DB_DATABASE", "micreditochat_db_creditochat");

/*
define("DB_HOST", "micreditodb.db.10977592.74e.hostedresource.net");
//define("DB_HOST", "localhost");
define("DB_USER", "micreditodb");
define("DB_PASSWORD", "M1cr3dito@");
define("DB_DATABASE", "micreditodb");
*/
define ("TITULO_WEB_PAGE","Administrador");
define ("ADMIN_USER",1);
define ("SUCURSAL_DEFAULT",1);
define ("PRIVATE_KEY","INTWAY.COM.AR");



date_default_timezone_set ( "America/Argentina/Buenos_Aires" );

define ("PATH_IMGS","/admin/img/participants/");

define ("SEXO_MASCULINO",1);
define ("SEXO_FEMENINO",2);

define("DNI_EN_DOM", 1);
?>
