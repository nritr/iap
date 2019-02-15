<?php
//echo '{"code":"001052434906","total":"250"}';
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);

include_once $path."/admin/config.php";
include_once $path."/admin/php/utils/DB_connect.php";
include_once $path."/admin/php/dao/DatosDao.php";
include_once $path."/admin/php/services/DatosService.php";
include_once $path."/admin/php/services/SendmailService.php";

//NICO: si descomentas esta linea, te guarda todo el json recibido como string en test.txt
//file_put_contents('test.txt', file_get_contents('php://input'));
$string = preg_replace("/[\r\n]+/", " ",file_get_contents('php://input'));
$string = utf8_encode($string);
$data = json_decode($string );

/*switch (json_last_error()) {
    case JSON_ERROR_NONE:
        echo ' - No errors';
        break;
    case JSON_ERROR_DEPTH:
        echo ' - Maximum stack depth exceeded';
        break;
    case JSON_ERROR_STATE_MISMATCH:
        echo ' - Underflow or the modes mismatch';
        break;
    case JSON_ERROR_CTRL_CHAR:
        echo ' - Unexpected control character found';
        break;
    case JSON_ERROR_SYNTAX:
        echo ' - Syntax error, malformed JSON';
        break;
    case JSON_ERROR_UTF8:
        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
    default:
        echo ' - Unknown error';
        break;
}*/


$datos = new Datos();
$datos->status = "pending";
$datos->code = randomNumber(12);

$content = base64_decode($data->image);

$imagenName = $data->passportNumber.'.'.explode(".", $data->imageName)[1];
$file = fopen($path.PATH_IMGS.$imagenName , "c");
fwrite($file, $content);
fclose($file);

$datos->image = $imagenName ;

$datos->grantingParticipant = ($data->{'grantingParticipant'} == true?1:0);
$datos->attend = ($data->{'attend'} == true?1:0);
$datos->attendType = $data->attendType;
$datos->contribute = ($data->contribute == true?1:0);
if(isset( $data->contributeType))
    $datos->contributeType = $data->contributeType;

$datos->president = ($data->social->president == true?1:0);
$datos->accompanyngPresident = ($data->social->accompanyingPresident == true?1:0);
$datos->conferenceDinner = ($data->social->conferenceDinner == true?1:0);
$datos->accompanyingConferenceDinner = ($data->social->accompanyingConferenceDinner == true?1:0);
$datos->farewellParty = ($data->social->farewellParty == true?1:0);
$datos->accompanyingFarewellParty = ($data->social->accompanyingFarewellParty == true?1:0);
$datos->networkingNight = ($data->social->networkingNight == true?1:0);
$datos->letterInvitation = ($data->requests->letterInvitation == true?1:0);
$datos->certificateAttendance = ($data->requests->certificateAttendance == true?1:0);
$datos->accompanying = ($data->accompanying == 'true'?1:0);

if($data->companion != null && $datos->accompanying == 1){
    $i = 0;
    foreach ($data->companion as &$comp){
        $datos->companionArray[$i] = new Companion();
        $datos->companionArray[$i]->accompanyingFirstName = $comp->accompanyingFirstName;
        $datos->companionArray[$i]->accompanyingLastName = $comp->accompanyingLastName;
        $datos->companionArray[$i]->accompanyingBadgeName = $comp->accompanyingBadgeName;
        $datos->companionArray[$i]->accompanyingToursDay = $comp->accompanyingToursDay;
        if(property_exists($comp, "accompanyingSpecialDietaryPhysical")){
            $datos->companionArray[$i]->specialDietaryPhysical = $comp->accompanyingSpecialDietaryPhysical;
        }
        
        $content = base64_decode($comp->image);
        
        $imagenName = 'acomp'.randomNumber(8).'.'.explode(".", $data->imageName)[1];
        $file = fopen($path.PATH_IMGS.$imagenName , "c");
        fwrite($file, $content);
        fclose($file);
        
        $datos->companionArray[$i]->image = $imagenName ;
        
        $i++;    
    }
}

$datos->bodyguard = ($data->bodyguard == 'true'?1:0);
if($data->bodyguardComp != null && $datos->bodyguard == 1){
    
    $datos->bodyguardAdd->accompanyingFirstName = $data->bodyguardComp->bodyguardFirstName;
    $datos->bodyguardAdd->accompanyingLastName = $data->bodyguardComp->bodyguardLastName;
    $datos->bodyguardAdd->accompanyingBadgeName = $data->bodyguardComp->bodyguardBadgeName;
    $datos->bodyguardAdd->accompanyingToursDay = $data->bodyguardComp->bodyguardToursDay;
    
    if(property_exists($data->bodyguardComp, "bodyguardSpecialDietaryPhysical")){
        $datos->bodyguardAdd->specialDietaryPhysical = $data->bodyguardComp->bodyguardSpecialDietaryPhysical;
    }
    
    $content = base64_decode($data->bodyguardComp->image);
    
    $imagenName = 'bdgu'.randomNumber(8).'.'.explode(".", $data->bodyguardComp->imageName)[1];
    $file = fopen($path.PATH_IMGS.$imagenName , "c");
    fwrite($file, $content);
    fclose($file);
    
    $datos->bodyguardAdd->image = $imagenName ;

}

$datos->commiteeMember = ($data->committeeMember == true?1:0);
if(property_exists($data, "arrivalDetails") && property_exists($data->arrivalDetails,"arrivalDestination")){
    $datos->arrival->arrivalDestination = $data->arrivalDetails->arrivalDestination;
    $datos->arrival->arrivalDate = $data->arrivalDetails->arrivalDate;
    $datos->arrival->arrivalTime = $data->arrivalDetails->arrivalTime;
    $datos->arrival->arrivalDestinationOrigin = $data->arrivalDetails->arrivalDestinationOrigin;
    $datos->arrival->arrivalFlightNumber = $data->arrivalDetails->arrivalFlightNumber;
    $datos->arrival->arrivalDepartureDate = $data->arrivalDetails->arrivalDepartureDate;
    $datos->arrival->arrivalDepartureTime = $data->arrivalDetails->arrivalDepartureTime;
    $datos->arrival->arrivalDepartureFlightNumber = $data->arrivalDetails->arrivalDepartureFlightNumber;

}
$datos->description = $data->ticket->description;
$datos->value= $data->ticket->value;
$datos->total= $data->ticket->total;
if(property_exists($data->ticket, "days")){
    $datos->daysArray= $data->ticket->days;
}else{
    $datos->daysArray= [];
}

$datos->prefix = $data->prefix;
$datos->passportNationality = $data->passportNationality;
$datos->passportNumber = $data->passportNumber;
$datos->firstName = $data->firstName;
if(property_exists($data, "middleName")){
    $datos->middleName = $data->middleName;
}
$datos->passportExpiryDate = $data->passportExpiryDate;
$datos->address1 = $data->address1;
if(isset($data->address2))
    $datos->address2 = $data->address2;
$datos->postalCode = $data->postalCode;
$datos->city = $data->city;
$datos->lastName = $data->lastName ;
if(property_exists($data, "specialDietaryPhysical")){
    $datos->specialDietaryPhysical = $data->specialDietaryPhysical ;
}
$datos->country = $data->country;
$datos->badgeName = $data->badgeName;
$datos->jobTitle = $data->jobTitle;
if(property_exists($data, "jobTitleOther")){
    $datos->jobTitleOhter = $data->jobTitleOther;
}
$datos->delegateEmail = $data->delegateEmail;
$datos->workPone = $data->workPhone;
$datos->email = $data->email;
$datos->mobilePhone = $data->mobilePhone;
$datos->paymentType = $data->paymentType;
if(property_exists($data, "paymentType")){
    $datos->paymentType = $data->paymentType;
}
if(isset($data->organisation))
$datos->organisation = $data->organisation;

$datosDao = new DatosDao($data);
$datosDao->save($datos);
//llega el json exaco como se envio desde la web
//echo $data->address1;
//ACA EL KILOMBO ES METER EN LA BASE TODOS LOS DATOS, despues hay q devolver
//$serviceParticipant->save($data);  no existe esto, pero deberia ser algo asi, donde el service le pasa al dao el objeto, y en el dao esta la magina de meter todo en la base. el service generaria el code que se guarda en la base, y se devuelve para enviar al gateway

if($datos->paymentType == "Online Payment" || $datos->paymentType == "Paypal") {
    echo '{"ode":"'.$datos->code.'","total":"'.$datos->total.'"}';
}else{
    
    $msj = "<center><img src=\"https://iap.follow-develop.com.ar/web/iap_2018.png\" /></center>";
    $msj = $msj. "<br/><br/>";
    $msj = $msj. "<center>";
    $msj = $msj. "<p>The 24rd Annual Conference and General Meeting of the IAP</p>";
    $msj = $msj. "<p>15 - 19 September 2019 in Buenos Aires, Argentina</p>";
    $msj = $msj. "<center>Invoice no. </center>". $datos->code;
    $msj = $msj. "</center>";
    
    $msj = $msj. "<br/><br/>";
    
    if($datos->middleName != null){
        $msj = $msj. "<b>Dear ".$datos->firstName." ".$datos->middleName." ".$datos->lastName."</b>";
    } else {
        $msj = $msj. "<b>Dear ".$datos->firstName." ".$datos->lastName."</b>";
    }
    
    $msj = $msj. "<br/>";
    $msj = $msj. "<p>Thank you very much for your registration for the 23 annual Conference and  General Meeting of the International Association of Prosecutors.</p>";
    $msj = $msj. "<br/>";
    $msj = $msj. "<p>You have chosen bank transfer as your preferred way of payment.</p>";
    $msj = $msj. "<br/>";
    $msj = $msj. "<p><b>Payment information:</b></p>";
    $msj = $msj. "<p>Amount to be transferred: ".$datos->total." EUR</p>";
    
    if(count($datos->daysArray) > 0){
        if ($datos->accompanying != null && $datos->accompanying == 1) {
            $msj = $msj. "<p>The payment covers: Days participation ";
            
            foreach ($datos->daysArray as $day){
                $msj = $msj. " ".$day->day;
            }
            $msj = $msj." and accompanying person.</p>";
        } else {
            $msj = $msj." <p>The payment covers: Days participation";
            foreach ($datos->daysArray as $day){
                $msj = $msj. " ".$day->day;
            }
            $msj = $msj. "</p>";
        }
        
    }else{
        if ($datos->accompanying != null && $datos->accompanying== 1) {
            $msj = $msj. "<p>The payment covers: ".$datos->ticket->description." and accompanying person.</p>";
        } else {
            $msj = $msj. "<p>The payment covers: ".$datos->ticket->description."</p>";
        }
    }
    
    if ($datos->accompanying != null && $datos->accompanying == 1 && (count($datos->companionArray) > 0)) {
        $msj = $msj. "<p>Accompanying Person: ";
        foreach ($datos->companionArray as $companion){
                                       
            $msj=$msj. " ".$companion->accompanyingFirstName." ".$companion->accompanyingLastName;
        }
        
        $msj=$msj. " </p>";
    }
    
    $msj=$msj."<br/>";
    $msj=$msj."<p><b><a style='font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif;'>Please transfer the full amount to the following bank account:</a></b></p>";
    $msj=$msj."<table border='0'>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Recipient:</b></td>";
    $msj=$msj."<td width='70%'>Stichting Treasury International Association of Prosecutors</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Receiving Bank:</b></td>";
    $msj=$msj."<td width='70%'>ABN/AMRO Bank Koningskade 30, P.O. Box 165 ABN/AMRO Bank</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Account no.:</b></td>";
    $msj=$msj."<td width='70%'>42.49.05.345</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Currency</b></td>";
    $msj=$msj."<td width='70%'>:EUR</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>IBAN:</b></td>";
    $msj=$msj."<td width='70%'>NL07 ABNA 0424 9053 45</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>BIC:</b></td>";
    $msj=$msj."<td width='70%'>ABNANL2A</td>";
    $msj=$msj."</tr>";
    $msj=$msj."</table>";
    $msj=$msj."<br/>";
    $msj=$msj."<p><b>Please note the following:</b></p>";
    $msj=$msj."<ul style=\"list-style-type:disc\">";
    $msj=$msj."<li>Please make sure you make a CLEAR REFERENCE to the invoice number or name of participants in the bank remittance.</li>";
    $msj=$msj."<li>Only registrations accompanied by full payment will be accepted. A copy of the receipt of the bank remittance should be sent to the IAP Secretariat ed@iap-association.org.</li>";
    $msj=$msj."<li>Any charges for remittances are to be borne by the participants.</li>";
    $msj=$msj."</ul>";
    $msj=$msj."<br/>";
    $msj=$msj."<p><b>When payment has been successfully processed, the IAP Secretariat will send a voucher (ticket) confirming your participation in the conference. If this letter has not been received within two weeks after your bank transfer, please contact the IAP secretariat.</b></p>";
    $msj=$msj."<br/>";
    $msj=$msj."<p><b>Conference Registration Cancellation Policy</b></p>";
    $msj=$msj."<br/>";
    $msj=$msj."<table border='0'>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Cancellations received before on or before 1 July 2019:</b></td>";
    $msj=$msj."<td width='70%'>100 % of the registration fee will be refunded except from 5% to cover administration expenses.</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Cancellations received on or before 1 August 2019:</b></td>";
    $msj=$msj."<td width='70%'>50 % of the fee will be refunded</td>";
    $msj=$msj."</tr>";
    $msj=$msj."<tr>";
    $msj=$msj."<td width='30%'><b>Cancellations received on or after 2 August 2019:</b></td>";
    $msj=$msj."<td width='70%'>We regret that no refunds will be issued. Please note that substitution of participants is permitted on the basis that a written notice is received before date provided that full details are submitted to the Conference Unit.</td>";
    $msj=$msj."</tr>";
    $msj=$msj."</table>";
    $msj=$msj."<br/>";
    $msj=$msj."<center>Best regards</center>";
    $msj=$msj."<center>The IAP Conference Committee</center>";
    $msj=$msj."<center>Iap-sa2018@npa.gov.za</center>";
    
    $sendMail = new SendmailService();
    $sendMail->sendMail($datos->email,"Confirmation of Pre-Registration for the 24rd Annual Conference and General Meeting of the IAP", $msj);
    
    
    $msj="Dear ".$datos->firstName." ".$datos->lastName."! Thank you so very much for your registration for the 24rd Annual Conference and General Meeting of the International Association of Prosecutors in Buenos Aires, Argentina 15 - 19 September 2019. You will shortly receive an e-mail with full details of your registration. The Conference Committee";

  
    echo '{"response":{"message":"'.$msj.'"}}';
    
}




//echo '{"code":"001052434906","total":"250"}';
//este es el response si puso transferencia

function randomNumber($length) {
    $result = '';
    
    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    
    return $result;
}
?>