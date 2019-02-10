<?php
//http://localhost/ajax/testWan.php?transact=6304&orderid=558861673824&amount=100&currency=978&cardtype=VISA&transacknum=6304&actioncode=0&lang=en
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);

include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/dao/DatosDao.php";
include_once $path."/admin/php/services/DatosService.php";
include_once $path."/admin/php/services/SendmailService.php";


if (isset($_GET["errorcode"]) && 
    ($_GET["errorcode"] !="0" || $_GET["errorcode"] !="00")) {
    
    $msj = "The transaction was not approved, please check your credit card information.";
    header('Location: ../landingPage.php?msj='.base64_encode($msj));
    exit;
}

if (isset($_GET["transact"]) != true && isset($_GET["actioncode"]) != true) {
        
        $msj = "The transaction and action code are required.";
        header('Location: ../landingPage.php?msj='.base64_encode($msj));
        exit;
 }
    
 if (isset($_GET["orderid"]) != true ) {
     
     $msj = "The order id missing.";
     header('Location: ../landingPage.php?msj='.base64_encode($msj));
     exit;
 }
 


$filter = new DatoFilter();


$code = $_GET["orderid"];
$filter->code = $code ;



$datoService = new DatosService();
$list = $datoService->load($filter);


$participant = $list[0];

if($participant->status != 'approved'){
    $participant->status = "approved";
}

$participant->transaction = $_GET["transact"];
$participant->transactionCode = $_GET["actioncode"];

$msj = $datoService->getMailMessage($participant);
        $sendMail = new SendmailService();

        $sendMail->sendMail($participant->email,"(VOUCHER) Confirmation of Registration for the 23rd Annual Conference and General Meeting of the IAP", $msj);
//         $sendMail->sendMail("ignaciosc@gmail.com","(VOUCHER) Confirmation of Registration for the 23rd Annual Conference and General Meeting of the IAP", $msj);
//         $sendMail->sendMail("nicolas.racciatti@gmail.com","(VOUCHER) Confirmation of Registration for the 23rd Annual Conference and General Meeting of the IAP", $msj);
        
        $msj="Dear ".$participant->firstName." ".$participant->lastName."\\n\\rYou have been registered inside the system for the next IAP Conference.\\n\\rYou will receive an email with of the information. Remember that your order is waiting bank confirmation.";
        header('Location: ../landingPage.php?msj='.base64_encode($msj));
        
?>
 