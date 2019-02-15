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

    $filter = new DatoFilter();
    
    $code = $_GET["code"];
    $filter->code = $code ;
    
    $datoService = new DatosService();
    $list = $datoService->load($filter);
    
    $participant = $list[0];
    
    $participant->status = "approved";
    
    $participant->transaction = $_GET["transact"];
    $participant->transactionCode = $_GET["actioncode"];
    
    $datoService->updateInscription($participant);
    $sendMail = new SendmailService();
    $sendMail->sendMail($participant->email,"(VOUCHER) Confirmation of Registration for the 24rd Annual Conference and General Meeting of the IAP", $datoService->getMailMessage($participant));
    
   $msj="Dear ".$participant->firstName." ".$participant->lastName." You have been registered inside the system for the next IAP Conference. You will receive an email with of the information. Remember that your order is waiting bank confirmation.";
   header('Location: ../landingPage.php?msj='.base64_encode($msj));
    
    
?>
 