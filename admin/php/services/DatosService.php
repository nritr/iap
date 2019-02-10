<?php
include_once $path.'/admin/php/dao/DatosDao.php';
include_once $path.'/admin/php/services/CompanionService.php';
include_once $path.'/admin/php/pojos/register/Datos.php';

class DatosService {

	private $dao;

	
	public function __construct() {
	    $this->dao = new DatosDao();
	}

	public function getById(int $id) {
	    $filter            = new DatoFilter();
	    $filter->idDato    = $id;
	    $list              = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	

	public function load(DatoFilter $filter=null) {
	    if ($filter==null) {
	        $filter = new DatoFilter();
	    }
	    return $this->dao->load($filter);
	}
	
	public function updateInscription(Datos &$dato) {
	    $this->dao->updateStatus($dato);
	}
	
	
	public function getMailMessage(Datos &$participant){
	    
	    //$datoService->updateInscription($participant);
	    $msj = "<div style='font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif;'>";
	    $msj = $msj ."<center><img src=\"https://s3-sa-east-1.amazonaws.com/tv.zencast.iap.content/iap_2018.png\" /></center>";
	    $msj = $msj. "<br/><br/>";
	    
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<center>";
	    $msj = $msj. "<p>The 23rd Annual Conference and General Meeting of the IAP</p>";
	    $msj = $msj. "<p>9 - 13 September 2018 in Johannesburg, South Africa</p>";
	    $msj = $msj. "</center>";
	    
	    
	    
	    
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<center>";
	    $msj = $msj. "<img src=\"https://iap.follow-develop.com.ar/ajax/tobarcode.php?code=".$participant->inscription->code."\" />";
	    $msj = $msj. "</center>";
	    $msj = $msj. "<br/><br/><br/><br/>";
	    $msj = $msj."<b>Dear ".$participant->badgeName."</b>";
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "Thank you very much for your registration for the 23 annual Conference and  General Meeting of the International Association of Prosecutors. This voucher confirms that you completed your registration for the conference and you payment has been processed,";
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "We are delighted to confirm your registration details as follow:";
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<b>Registration Information:</b><br/>";
	    $msj = $msj. "<br/>";
	    $msj = $msj. "Full name: ".$participant->badgeName;
	    $msj = $msj. "<br/>";
	    
	    if ($participant->jobTitleOhter != null ) {
	        $msj = $msj. "Profession/title: ".$participant->jobTitle." [".$participant->jobTitleOhter."]";
	    } else {
	        $msj = $msj. "Profession/title: ".$participant->jobTitle;
	    }
	    $msj = $msj. "<br/>";
	    $msj = $msj. "Country: ".$participant->country;
	    $msj = $msj. "<br/>";
	    $msj = $msj. "IAP Membership Category: ".$participant->attendType;
	    $msj = $msj. "<br/>";
	    
	    if ($participant->specialDietaryPhysical != null) {
	        $msj = $msj. "Special dietary/physical needs: ".$participant->specialDietaryPhysical;
	    }
	    
	    if ($participant->accompanying != null && $participant->accompanying == '1' && (count($participant->companionArray) > 0)) {
	        
	        $msj = $msj. "<br/>";
	        $msj = $msj. "<b>Accompanying persons:</b><br/>";
	        $msj = $msj. "<br/>";
	        foreach ($participant->companionArray as $companion){
	            $msj = $msj. "Name of Acc. Person: ".$companion->accompanyingFirstName." ".$companion->accompanyingLastName;
	        }
	        
	    }
	    
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<b>Payment information</b>:<br/><br/>";
	    $msj = $msj. "Amount paid: ".$participant->ticket->total." EUR";
	    
	    $msj = $msj. "<br/>";
	    if(count($participant->daysArray) > 0){
	        if ($participant->accompanying != null && $participant->accompanying == '1') {
	            $msj = $msj. "<p>The payment covers: Days participation ";
	            
	            foreach ($participant->daysArray as $day){
	                $msj = $msj. " ".$day->day;
	            }
	            $msj = $msj." and accompanying person.</p>";
	        } else {
	            $msj = $msj. "<p>The payment covers: Days participation ";
	            
	            foreach ($participant->daysArray as $day){
	                $msj = $msj. " ".$day->day;
	            }
	        }
	        
	    } else {
	        if ($participant->accompanying != null && $participant->accompanying== '1') {
	            $msj = $msj. "<p>The payment covers: ".$participant->ticket->description." and accompanying person.</p>";
	        } else {
	            $msj = $msj. "<p>The payment covers: ".$participant->ticket->description."</p>";
	        }
	    }
	    
	    
	    if ($participant->daysArray != null && count($participant->daysArray)>0) {
	        
	        $msj = $msj. "<br/>";
	        $msj = $msj. "Days participation:";
	        
	        foreach ($participant->daysArray as $day){
	            $msj = $msj. " ".$day->day;
	        }
	        
	        
	        if ($participant->accompanying != null && $participant->accompanying== '1') {
	            
	            
	            
	            if ($participant->companionArray[0]->days !=null && count($participant->companionArray[0]->days) > 0) {
	                
	                $msj = $msj. "<br/>";
	                $msj = $msj. "Accompanying days participation:";
	                foreach($participant->companionArray[0]->days as $acompDay){
	                    $msj = $msj. " ".$acompDay->day;
	                }
	            }
	        }
	    }
	    
	    
	    
	    $msj = $msj. "<ul style=\"list-style-type:disc\">";
	    
	    
	    if ($participant->president != null && $participant->president == 1){
	        
	        $msj = $msj. "<br/><br/>";
	        $msj = $msj. "<b>Social events:</b>";
	        $msj = $msj. "<br/><br/>";
	        $msj = $msj. "You have registered for the following events:";
	        $msj = $msj. "<li><b>President's Welcome Reception (Sunday 9 September 2018)</b></li>";
	        
	        if (($participant->accompanyingPresident!= null && $participant->accompanyingPresident == 1)) {
	            $msj = $msj. "<br>";
	            $msj = $msj. "<ul style=\"list-style-type:circle\">";
	            $msj = $msj. "<li>Accompanying Person:&nbsp;<b>(+1)</b></li>";
	            $msj = $msj. "</ul>";
	        }
	        $msj = $msj. "<br/>";
	        
	    }
	    
	    if (($participant->conferenceDinner != null && $participant->conferenceDinner == 1)) {
	        
	        
	        $msj = $msj. "<li><b>Conference Dinner (Monday 10 September 2018)</b></li>";
	        
	        if (($participant->accompanyingConferenceDinner != null && $participant->accompanyingConferenceDinner == 1)) {
	            $msj = $msj. "<br>";
	            $msj = $msj. "<ul style=\"list-style-type:circle\">";
	            $msj = $msj. "<li>Accompanying Person:&nbsp;<b>(+1)</b></li>";
	            $msj = $msj. "</ul>";
	        }
	        $msj = $msj. "<br/>";
	    }
	    
	    if (($participant->networkingNight != null  && $participant->networkingNight == 1)) {
	        
	        $msj = $msj. "<li><b>Professional Networking Event Only Conference Participants are entitled to attend the Professional Networking Night. (Tuesday 11 September 2018)</b></li>";
	        $msj = $msj. "<br/>";
	    }
	    
	    if (($participant->farewellParty != null && $participant->farewellParty == 1)) {
	        
	        $msj = $msj. "<li><b>Farewell Party (Thursday 13 September 2018)</b></li>";
	        
	        if (($participant->accompanyingFarewellParty != null && $participant->accompanyingFarewellParty == 1)) {
	            $msj = $msj. "<br>";
	            $msj = $msj. "<ul style=\"list-style-type:circle\">";
	            $msj = $msj. "<li>Accompanying Person:&nbsp;<b>(+1)</b></li>";
	            $msj = $msj. "</ul>";
	        }
	        $msj = $msj. "<br/>";
	    }
	    
	    
	    $msj = $msj. "</ul>";
	    
	    if ($participant->accompanying != null && $participant->accompanying== '1') {
	        
	        
	        if ($participant->companionArray[0]->days !=null && count($participant->companionArray[0]->days) > 0) {
	            
	            $msj = $msj. "<ul style=\"list-style-type:circle\">";
	            
	            foreach ($participant->companionArray[0]->days as $days) {
	                
	                if ($days->day == "09/10/2018") {
	                    $msj = $msj. "<li><b>Accompanying Persons Half Day Tour (Monday 10 September 2018)</b></li>";
	                } else {
	                    $msj = $msj. "<li><b>Accompanying Persons Full Day Tour (Wednesday 12 September 2018)</b></li>";
	                }
	            }
	            
	            $msj = $msj. "</ul>";
	        }
	    }
	    $msj = $msj. "<br>";
	    
	    
	    if ($participant->arrival != null && $participant->arrival->arrivalDestination != null) {
	        
	        
	        $msj = $msj. "<br/><br/>";
	        $msj = $msj. "<b>Arrival and Departure information</b><br/><br/>";
	        $msj = $msj. "Arrival Destination: ".$participant->arrival->arrivalDestination." " ;
	        $msj = $msj. "<br/>";
	        $msj = $msj. "Arrival Date/Time: ".$participant->arrival->arrivalDate.$participant->arrival->arrivalTime." ";
	        $msj = $msj. "<br/>";
	        $msj = $msj. "Flight number: ".$participant->arrival->arrivalFlightNumber." ";
	        $msj = $msj. "<br/>";
	        $msj = $msj. "Departure Date/Time: ".$participant->arrival->arrivalDepartureDate.$participant->arrival->arrivalDepartureTime." ";
	        $msj = $msj. "Flight number: ".$participant->arrival->arrivalDepartureFlightNumber." ";
	    }
	    
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<b>Conference registration Cancellation Policy:</b>";
	    $msj = $msj. "<br/><br/>";
	    $msj = $msj. "<ul style=\"list-style-type:disc\">";
	    $msj = $msj. "<li>Cancellations received before on or before 1 July 2018: 100% of the registration fee will be refunded except from 5% to cover administration expenses.</li>";
	    $msj = $msj. "<li>Cancellations received on or before 27 July 2018: 50% of the fee will be refunded.</li>";
	    $msj = $msj. "<li>Cancellations received hereafter: We regret that not refunds will be issued. Please note that the substitution of participants is permitted on the basis that a written notice is received before date provided that full details are submitted to the conference unit.</li>";
	    $msj = $msj. "</ul>";
	    $msj = $msj. "<br/>";
	    $msj = $msj. "<p><b>Please note:&nbsp;</b>All cancellations must be made in writing to the Conference Secretary and any refund will be made after the Conference.</p>";
	    $msj = $msj. "<br/>";
	    $msj = $msj. "<p>The onsite registration of Participants and distribution of conference material and identification badges will be conducted in Stanton Conference Centre) on Sunday 9 September from 10:00 - 18:00. Please bring this voucher to ensure a smooth and quick registration process.</p>";
	    $msj = $msj. "<p>We look forward welcoming you in  Johannesburg, South Africa in September 2018.</p>";
	    $msj = $msj. "<br/>";
	    $msj = $msj. "<p>Best regards</p>";
	    $msj = $msj. "<p>IAP 2017 Conference Secretariat</p>";
	    $msj = $msj. "<p>Iap-sa2018@npa.gov.za</p>";
	    $msj = $msj. "</div>";
	    echo $msj;
	    
	    
	}
		
}
?>