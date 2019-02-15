<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);

include_once $path.'/admin/php/pojos/register/Datos.php';
include_once $path.'/admin/php/pojos/register/Day.php';
include_once $path.'/admin/php/pojos/register/Companion.php';
include_once $path.'/admin/php/pojos/register/ArrivalDetail.php';
include_once $path.'/admin/php/pojos/register/Inscription.php';
include_once $path.'/admin/php/pojos/register/Social.php';
include_once $path.'/admin/php/pojos/register/Request.php';
include_once $path.'/admin/php/pojos/register/Ticket.php';

include_once $path.'/admin/php/filters/register/DatoFilter.php';


class DatosDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	public function updateStatus(Datos &$dato) {
	    $sql = "UPDATE followdevelopcom_iap_site.INSCRIPTIONS
                set STATUS=:status, TRANSACTION=:transaction,TRANSACTION_CODE=:transactionCode where VALUES_ID=:valuesId;";
	    $stmt = $this->conn->prepare($sql);
	    
	    $stmt->bindValue(':status', $dato->status,PDO::PARAM_STR);
	    $stmt->bindValue(':transaction', $dato->transaction,PDO::PARAM_STR);
	    $stmt->bindValue(':transactionCode', $dato->transactionCode,PDO::PARAM_STR);
	    $stmt->bindValue(':valuesId', $dato->id,PDO::PARAM_STR);
	    
	    $result = $stmt->execute();
	    $dato->requestId = $this->conn->lastInsertId();
        
	    
	}
	

	public function load(DatoFilter $filter) {

	    $companionService      = new CompanionService();

	    $where    = "";
		if ($filter->idDato>0) {
		    $where.= " AND d.ID=".$filter->idDato;
		}
		if ($filter->passportNumber != '') {
		    
		    $where.= " AND d.PASSPORT_NUMBER=:passportNumber";
		}
		if ($filter->idDato=="" && $filter->passportNumber=="" && $filter->code == '') {
		    $where.= " AND i.code<>'' AND d.EMAIL<>''";
		}
		if ($filter->getDynamicFilter()!="") {
		    $where.= $filter->getDynamicFilter();
		}
		if ($filter->code != '') {
		    $where.= " AND i.CODE=:code";
		}
		if ($filter->existsTicket>0) {
		    $where.= " AND EXISTS (SELECT t.ID FROM TICKET t WHERE d.TICKET_ID=t.ID)";
		}
		$sql = "
            select
            d.ID as DATOS_ID,d.SOCIAL_ID,d.ARRIVAL_DETAILS_ID,d.GRANTING_PARTICIPANT,d.ATTEND,d.ATTEND_TYPE,d.CONTRIBUTE,d.CONTRIBUTE_TYPE,d.ACCOMPANYING,d.
            COMMITTEE_MEMBER,d.PREFIX,d.FIRST_NAME,d.MIDDLE_NAME,d.LAST_NAME,d.PASSPORT_NATIONALITY,d.BADGE_NAME,d.ADDRESS_1,d.ADDRESS_2,d.PASSPORT_NUMBER,
            d.PASSPORT_EXPIRY_DATE,d.POSTAL_CODE,d.CITY,d.COUNTRY,d.JOB_TITLE,d.JOB_TITLE_OTHER,d.ORGANISATION,d.EMAIL,d.
            DELEGATE_EMAIL,d.WORK_PHONE,d.MOBILE_PHONE,d.PAYMENT_TYPE,d.TICKET_ID,d.REQUESTS_ID,d.REGISTER_DATE,
            s.ID as SOCIAL_ID,s.PRESIDENT,s.ACCOMPANYING_PRESIDENT,s.CONFERENCE_DINNER,s.ACCOMPANYING_CONFERENCE_DINNER,s.FAREWELL_PARTY,
            s.ACCOMPANYING_FAREWELL_PARTY,s.NETWORKING_NIGHT,
            r.ID REQUEST_ID, r.LETTER_INVITATION, r.CERTIFICATE_ATTENDANCE,
            ad.ID as AD_ID,ad.ARRIVAL_DESTINATION,ad.ARRIVAL_DATE,ad.ARRIVAL_TIME,ad.ARRIVAL_DESTINATION_ORIGIN,ad.ARRIVAL_FLIGHT_NUMBER,
            ad.ARRIVAL_DEPARTURE_DATE,ad.ARRIVAL_DEPARTURE_TIME,ad.ARRIVAL_DEPARTURE_FLIGHT_NUMBER,
            t.ID as TICKET_ID,t.DESCRIPTION,t.VALUE,t.TOTAL,t.TYPE,".
            " i.STATUS, i.IMAGE, i.ARRIVED, i.CODE, i.TRANSACTION, i.TRANSACTION_CODE ".
            " FROM followdevelopcom_iap_site.DATOS d
            LEFT join followdevelopcom_iap_site.SOCIAL s on d.SOCIAL_ID = s.ID
            LEFT join followdevelopcom_iap_site.ARRIVAL_DETAILS ad on ad.ID = d.ARRIVAL_DETAILS_ID
            LEFT join followdevelopcom_iap_site.TICKET t on t.ID=d.TICKET_ID
            LEFT join followdevelopcom_iap_site.REQUESTS r on r.id = d.REQUESTS_ID ".
           " LEFT JOIN followdevelopcom_iap_site.INSCRIPTIONS i ON i.VALUES_ID=d.ID ".
            " WHERE 1 ".$where.
            " ORDER BY d.ID DESC";

        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	   if ($filter->passportNumber != '') {
            $stmt->bindValue(':passportNumber', $filter->passportNumber	, PDO::PARAM_STR);
        }
        if ($filter->code != '') {
            $stmt->bindValue(':code', $filter->code	, PDO::PARAM_STR);
        }
		$res = $stmt->execute();


		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
			$dato                                        = new Datos();
			$dato->id                                    = $fila['DATOS_ID'];
			$dato->socialId                              = $fila['SOCIAL_ID'];
			$dato->accompanying                           = $fila['ACCOMPANYING'];
			$dato->address1                              = $fila['ADDRESS_1'];
			$dato->address2                              = $fila['ADDRESS_2'];
			$dato->arrival->arrivalDate                  = $fila['ARRIVAL_DATE'];
			$dato->arrival->arrivalDepartureDate         = $fila['ARRIVAL_DEPARTURE_DATE'];
			$dato->arrival->arrivalDepartureFlightNumber = $fila['ARRIVAL_DEPARTURE_FLIGHT_NUMBER'];
			$dato->arrival->arrivalDepartureTime         = $fila['ARRIVAL_DEPARTURE_TIME'];
			$dato->arrival->arrivalDestination           = $fila['ARRIVAL_DESTINATION'];
			$dato->arrival->arrivalDestinationOrigin     = $fila['ARRIVAL_DESTINATION_ORIGIN'];
			$dato->arrival->id                           = $fila['ARRIVAL_DETAILS_ID'];
			$dato->arrival->arrivalFlightNumber          = $fila['ARRIVAL_FLIGHT_NUMBER'];
			$dato->arrival->arrivalTime                  = $fila['ARRIVAL_TIME'];
			
			$dato->inscription->arrived                  = $fila['ARRIVED'];
			$dato->inscription->image                    = $fila['IMAGE'];
			$dato->inscription->status                   = $fila['STATUS'];
			$dato->inscription->code                     = $fila['CODE'];
			$dato->inscription->transaction              = $fila['TRANSACTION'];
			$dato->inscription->transactionCode          = $fila['TRANSACTION_CODE'];
			
			$dato->social->president                     = $fila['PRESIDENT'];
			$dato->social->accompanyingConferenceDinner  = $fila['ACCOMPANYING_CONFERENCE_DINNER'];
			$dato->social->accompanyngPresident          = $fila['ACCOMPANYING_PRESIDENT'];
			$dato->social->accompanyingFarewellParty     = $fila['ACCOMPANYING_FAREWELL_PARTY'];
			$dato->social->networkingNight               = $fila['NETWORKING_NIGHT'];
			$dato->social->conferenceDinner              = $fila['CONFERENCE_DINNER'];
			$dato->social->farewellParty                 = $fila['FAREWELL_PARTY'];
			
			$dato->request->letterInvitation             = $fila['LETTER_INVITATION'];
			$dato->request->certificateAttendance        = $fila['CERTIFICATE_ATTENDANCE'];
			
			$dato->ticket->description                   = $fila['DESCRIPTION'];
			$dato->ticket->days                          = $companionService->loadDays(null,$fila['TICKET_ID']);
			$dato->ticket->total                         = $fila['TOTAL'];
			$dato->ticket->type                          = $fila['TYPE'];
			$dato->ticket->value                         = $fila['VALUE'];
			
			$dato->attend                                = $fila['ATTEND'];
			$dato->attendType                            = $fila['ATTEND_TYPE'];
			$dato->badgeName                             = $fila['BADGE_NAME'];
			
			$dato->city                                  = $fila['CITY'];
			
			$dato->commiteeMember                        = $fila['COMMITTEE_MEMBER'];
			$dato->companionArray                        = $companionService->getByIdDatos($fila['DATOS_ID']);//obtengo los acompañantes
			
			$dato->contribute                            = $fila['CONTRIBUTE'];
			$dato->contributeType                        = $fila['CONTRIBUTE_TYPE'];
			$dato->country                               = $fila['COUNTRY'];
			$dato->daysArray                             = $companionService->loadDays(null,$fila['TICKET_ID']);
			$dato->delegateEmail                         = $fila['DELEGATE_EMAIL'];
			$dato->email                                 = $fila['EMAIL'];
			
			$dato->firstName                             = $fila['FIRST_NAME'];
			$dato->grantingParticipant                   = $fila['GRANTING_PARTICIPANT'];
			
			$dato->jobTitle                              = $fila['JOB_TITLE'];
			$dato->jobTitleOhter                         = $fila['JOB_TITLE_OTHER'];
			$dato->lastName                              = $fila['LAST_NAME'];
			
			$dato->middleName                            = $fila['MIDDLE_NAME'];
			$dato->mobilePhone                           = $fila['MOBILE_PHONE'];
			
			$dato->organisation                          = $fila['ORGANISATION'];
			$dato->passportExpiryDate                    = $fila['PASSPORT_EXPIRY_DATE'];
			$dato->passportNationality                   = $fila['PASSPORT_NATIONALITY'];
			$dato->passportNumber                        = $fila['PASSPORT_NUMBER'];
			$dato->paymentType                           = $fila['PAYMENT_TYPE'];
			$dato->postalCode                            = $fila['POSTAL_CODE'];
			$dato->prefix                                = $fila['PREFIX'];
			
			$dato->requestId                             = $fila['REQUEST_ID'];
			
			$dato->ticketId                              = $fila['TICKET_ID'];
			
			$dato->workPone                              = $fila['WORK_PHONE'];
			$dato->registerDate                          = $fila['REGISTER_DATE'];
			$list[] = $dato;

		}
		return $list;
    }

    public function saveSocial(Datos &$dato) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`SOCIAL`(`ID`,`PRESIDENT`,`ACCOMPANYING_PRESIDENT`,`CONFERENCE_DINNER`,
                `ACCOMPANYING_CONFERENCE_DINNER`,`FAREWELL_PARTY`,`ACCOMPANYING_FAREWELL_PARTY`,`NETWORKING_NIGHT`)
                VALUES(:ID,:PRESIDENT,:ACCOMPANYING_PRESIDENT,:CONFERENCE_DINNER,:ACCOMPANYING_CONFERENCE_DINNER,
                :FAREWELL_PARTY,:ACCOMPANYING_FAREWELL_PARTY,:NETWORKING_NIGHT)";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':ID', $dato->socialId,PDO::PARAM_INT);
        $stmt->bindValue(':PRESIDENT', $dato->president,PDO::PARAM_INT);
        $stmt->bindValue(':ACCOMPANYING_PRESIDENT', $dato->accompanyngPresident,PDO::PARAM_INT);
        $stmt->bindValue(':CONFERENCE_DINNER', $dato->conferenceDinner,PDO::PARAM_INT);
        $stmt->bindValue(':ACCOMPANYING_CONFERENCE_DINNER', $dato->accompanyingConferenceDinner,PDO::PARAM_INT);
        $stmt->bindValue(':FAREWELL_PARTY', $dato->farewellParty,PDO::PARAM_INT);
        $stmt->bindValue(':ACCOMPANYING_FAREWELL_PARTY', $dato->accompanyingFarewellParty,PDO::PARAM_INT);
        $stmt->bindValue(':NETWORKING_NIGHT', $dato->networkingNight,PDO::PARAM_INT);
        
        $result = $stmt->execute();
        $dato->socialId = $this->conn->lastInsertId();
        
    }
    
    public function saveArrivalDetail(Datos &$dato) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`ARRIVAL_DETAILS`
                (`ID`,`ARRIVAL_DESTINATION`,`ARRIVAL_DATE`,`ARRIVAL_TIME`,
                `ARRIVAL_DESTINATION_ORIGIN`,`ARRIVAL_FLIGHT_NUMBER`,`ARRIVAL_DEPARTURE_DATE`,
                `ARRIVAL_DEPARTURE_TIME`,`ARRIVAL_DEPARTURE_FLIGHT_NUMBER`)
                VALUES
                (:ID,:ARRIVAL_DESTINATION,STR_TO_DATE(:ARRIVAL_DATE, '%Y-%m-%d'),:ARRIVAL_TIME,
                :ARRIVAL_DESTINATION_ORIGIN,:ARRIVAL_FLIGHT_NUMBER,STR_TO_DATE(:ARRIVAL_DEPARTURE_DATE, '%Y-%m-%d'),
                :ARRIVAL_DEPARTURE_TIME,:ARRIVAL_DEPARTURE_FLIGHT_NUMBER)";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':ID', $dato->arrivalDetailsId,PDO::PARAM_INT);
        $stmt->bindValue(':ARRIVAL_DESTINATION', $dato->arrival->arrivalDestination,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_DATE', $dato->arrival->arrivalDate,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_TIME', $dato->arrival->arrivalTime,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_DESTINATION_ORIGIN', $dato->arrival->arrivalDestinationOrigin,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_FLIGHT_NUMBER', $dato->arrival->arrivalFlightNumber,PDO::PARAM_INT);
        $stmt->bindValue(':ARRIVAL_DEPARTURE_DATE', $dato->arrival->arrivalDepartureDate,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_DEPARTURE_TIME', $dato->arrival->arrivalDepartureTime,PDO::PARAM_STR);
        $stmt->bindValue(':ARRIVAL_DEPARTURE_FLIGHT_NUMBER', $dato->arrival->arrivalDepartureFlightNumber,PDO::PARAM_INT);
        $result = $stmt->execute();
        $dato->arrivalDetailsId = $this->conn->lastInsertId();
        
    }
    
    
    public function saveTicketDay(Datos &$dato) {
        
        foreach ($dato->daysArray as &$day) {
            $sql = "INSERT INTO `followdevelopcom_iap_site`.`DAYS`
                (`TICKET_ID`,`COMPANION_ID`,`DAY`)
                VALUES(:TICKET_ID,0,STR_TO_DATE(:DAY, '%m/%d/%Y'));";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':TICKET_ID', $dato->ticketId,PDO::PARAM_INT);
            $stmt->bindValue(':DAY', $day,PDO::PARAM_STR);
            $result = $stmt->execute();
        }
        
        
    }
    
    public function saveTicket(Datos &$dato) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`TICKET`
                (`ID`,`DESCRIPTION`,`VALUE`,
                `TOTAL`,`TYPE`)VALUES
                (:ID,:DESCRIPTION,:VALUE,:TOTAL,
                :TYPE);
";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':ID', $dato->ticketId,PDO::PARAM_INT);
        $stmt->bindValue(':DESCRIPTION', $dato->description,PDO::PARAM_STR);
        $stmt->bindValue(':VALUE', $dato->value,PDO::PARAM_INT);
        $stmt->bindValue(':TOTAL', $dato->total,PDO::PARAM_INT);
        $stmt->bindValue(':TYPE', $dato->type,PDO::PARAM_STR);
        $result = $stmt->execute();
        $dato->ticketId = $this->conn->lastInsertId();
        $this->saveTicketDay($dato);
        
    }
    
    public function saveRequest(Datos &$dato) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`REQUESTS`
                (`ID`,`LETTER_INVITATION`,`CERTIFICATE_ATTENDANCE`)VALUES
                (:ID,:LETTER_INVITATION,:CERTIFICATE_ATTENDANCE);
";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':ID', $dato->requestId,PDO::PARAM_INT);
        $stmt->bindValue(':LETTER_INVITATION', $dato->letterInvitation,PDO::PARAM_STR);
        $stmt->bindValue(':CERTIFICATE_ATTENDANCE', $dato->certificateAttendance,PDO::PARAM_INT);
        $result = $stmt->execute();
        $dato->requestId = $this->conn->lastInsertId();
        
    }
    
    
    public function saveInscription(Datos &$dato) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`INSCRIPTIONS`
                (`CODE`,`STATUS`,`IMAGE`,`ARRIVED`,`VALUES_ID`)VALUES
                (:CODE,:STATUS,:IMAGE,0,:VALUES_ID);";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':CODE', $dato->code,PDO::PARAM_STR);
        $stmt->bindValue(':STATUS', $dato->status,PDO::PARAM_STR);
        $stmt->bindValue(':IMAGE', $dato->image,PDO::PARAM_STR);
        $stmt->bindValue(':VALUES_ID', $dato->id,PDO::PARAM_STR);
        
        $result = $stmt->execute();
        $dato->requestId = $this->conn->lastInsertId();
        
    }
    
    public function saveAccompanyng(Datos &$dato,$bodyGuard) {
        
        foreach ($dato->companionArray as &$comp) {
            $sql = "INSERT INTO `followdevelopcom_iap_site`.`COMPANION`
                    (`ID_DATOS`,`ACCOMPANYING_FIRST_NAME`,`ACCOMPANYING_LAST_NAME`,`ACCOMPANYING_BADGE_NAME`,`ACCOMPANYING_IMAGE`,`SPECIAL_DIET_PHYSICAL`,`IS_BODYGUARD`)
                    VALUES(:ID_DATOS,:ACCOMPANYING_FIRST_NAME,:ACCOMPANYING_LAST_NAME,:ACCOMPANYING_BADGE_NAME,:ACCOMPANYING_IMAGE,:SPECIAL_DIET_PHYSICAL,:IS_BODYGUARD);";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindValue(':ID_DATOS', $dato->id,PDO::PARAM_INT);
            $stmt->bindValue(':ACCOMPANYING_FIRST_NAME', $comp->accompanyingFirstName,PDO::PARAM_STR);
            $stmt->bindValue(':ACCOMPANYING_LAST_NAME', $comp->accompanyingLastName,PDO::PARAM_STR);
            $stmt->bindValue(':ACCOMPANYING_BADGE_NAME', $comp->accompanyingLastName,PDO::PARAM_STR);
            $stmt->bindValue(':ACCOMPANYING_IMAGE', $comp->image,PDO::PARAM_STR);
            $stmt->bindValue(':SPECIAL_DIET_PHYSICAL', $comp->specialDietaryPhysical,PDO::PARAM_STR);
            $stmt->bindValue(':IS_BODYGUARD', $bodyGuard,PDO::PARAM_INT);
            
            $result = $stmt->execute();
            $id = $this->conn->lastInsertId();
            if($comp->accompanyingToursDay != null){
                foreach ($comp->accompanyingToursDay as &$day){
                    $this->saveAccompanyingDay($day,$id);
                }
            }
            
        }
    }
    
    public function saveBodyguard(Datos &$dato,$bodyGuard) {
        
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`COMPANION`
                (`ID_DATOS`,`ACCOMPANYING_FIRST_NAME`,`ACCOMPANYING_LAST_NAME`,`ACCOMPANYING_BADGE_NAME`,`ACCOMPANYING_IMAGE`,`SPECIAL_DIET_PHYSICAL`,`IS_BODYGUARD`)
                VALUES(:ID_DATOS,:ACCOMPANYING_FIRST_NAME,:ACCOMPANYING_LAST_NAME,:ACCOMPANYING_BADGE_NAME,:ACCOMPANYING_IMAGE,:SPECIAL_DIET_PHYSICAL,:IS_BODYGUARD);";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':ID_DATOS', $dato->id,PDO::PARAM_INT);
        $stmt->bindValue(':ACCOMPANYING_FIRST_NAME', $dato->bodyguardAdd->accompanyingFirstName,PDO::PARAM_STR);
        $stmt->bindValue(':ACCOMPANYING_LAST_NAME', $dato->bodyguardAdd->accompanyingLastName,PDO::PARAM_STR);
        $stmt->bindValue(':ACCOMPANYING_BADGE_NAME', $dato->bodyguardAdd->accompanyingLastName,PDO::PARAM_STR);
        $stmt->bindValue(':ACCOMPANYING_IMAGE', $dato->bodyguardAdd->image,PDO::PARAM_STR);
        $stmt->bindValue(':SPECIAL_DIET_PHYSICAL', $dato->bodyguardAdd->specialDietaryPhysical,PDO::PARAM_STR);
        $stmt->bindValue(':IS_BODYGUARD', $bodyGuard,PDO::PARAM_INT);
        
        $result = $stmt->execute();
        $id = $this->conn->lastInsertId();
        if($dato->bodyguardAdd->accompanyingToursDay != null){
            foreach ($dato->bodyguardAdd->accompanyingToursDay as &$day){
                $this->saveAccompanyingDay($day,$id);
            }
        }
    }
    
    
    public function saveAccompanyingDay(&$day,$compId) {
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`DAYS`
            (`TICKET_ID`,`COMPANION_ID`,`DAY`)
            VALUES(0,:COMP_ID,STR_TO_DATE(:DAY, '%m/%d/%Y'));";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':COMP_ID', $compId,PDO::PARAM_INT);
        $stmt->bindValue(':DAY', $day,PDO::PARAM_STR);
        $result = $stmt->execute();
        
    }
    
    public function save(Datos &$dato) {
        $this->saveSocial($dato);
        $this->saveArrivalDetail($dato);
        $this->saveTicket($dato);
        $this->saveRequest($dato);
        $sql = "INSERT INTO `followdevelopcom_iap_site`.`DATOS`
        (`ID`,`SOCIAL_ID`,`ARRIVAL_DETAILS_ID`,`GRANTING_PARTICIPANT`,`ATTEND`,`ATTEND_TYPE`,
        `CONTRIBUTE`,`CONTRIBUTE_TYPE`,`ACCOMPANYING`,`COMMITTEE_MEMBER`,`PREFIX`,`FIRST_NAME`,
        `MIDDLE_NAME`,`LAST_NAME`,`PASSPORT_NATIONALITY`,`BADGE_NAME`,`ADDRESS_1`,`ADDRESS_2`,
        `PASSPORT_NUMBER`,`PASSPORT_EXPIRY_DATE`,`POSTAL_CODE`,`CITY`,`COUNTRY`,`JOB_TITLE`,
        `JOB_TITLE_OTHER`,`ORGANISATION`,`EMAIL`,`DELEGATE_EMAIL`,`WORK_PHONE`,`MOBILE_PHONE`,
        `PAYMENT_TYPE`,`TICKET_ID`,`REQUESTS_ID`,`SPECIAL_DIET_PHYSICAL`,`BODYGUARD`)
        VALUES(:ID ,:SOCIAL_ID ,:ARRIVAL_DETAILS_ID ,:GRANTING_PARTICIPANT ,:ATTEND ,:ATTEND_TYPE ,
            :CONTRIBUTE ,:CONTRIBUTE_TYPE ,:ACCOMPANYING ,:COMMITTEE_MEMBER ,:PREFIX ,:FIRST_NAME ,
            :MIDDLE_NAME ,:LAST_NAME ,:PASSPORT_NATIONALITY ,:BADGE_NAME ,:ADDRESS_1 ,:ADDRESS_2 ,:PASSPORT_NUMBER ,STR_TO_DATE(:PASSPORT_EXPIRY_DATE, '%Y-%m-%d') ,
            :POSTAL_CODE ,:CITY ,:COUNTRY ,:JOB_TITLE ,:JOB_TITLE_OTHER ,:ORGANISATION ,:EMAIL ,:DELEGATE_EMAIL ,
            :WORK_PHONE ,:MOBILE_PHONE ,:PAYMENT_TYPE ,:TICKET_ID ,:REQUESTS_ID,:SPECIAL_DIET_PHYSICAL,:BODYGUARD
        )";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ID', $dato->id,PDO::PARAM_INT);
        $stmt->bindValue(':SOCIAL_ID', $dato->socialId,PDO::PARAM_INT);
        $stmt->bindValue(':ARRIVAL_DETAILS_ID',$dato->arrivalDetailsId,PDO::PARAM_INT);
        $stmt->bindValue(':GRANTING_PARTICIPANT',$dato->grantingParticipant,PDO::PARAM_INT);
        $stmt->bindValue(':ATTEND',$dato->attend,PDO::PARAM_INT);
        $stmt->bindValue(':ATTEND_TYPE',$dato->attendType,PDO::PARAM_STR);
        $stmt->bindValue(':CONTRIBUTE',$dato->contribute,PDO::PARAM_INT);
        $stmt->bindValue(':CONTRIBUTE_TYPE',$dato->contributeType,PDO::PARAM_STR);
        
        $stmt->bindValue(':ACCOMPANYING',$dato->accompanying,PDO::PARAM_INT);
        $stmt->bindValue(':COMMITTEE_MEMBER',$dato->commiteeMember,PDO::PARAM_INT);
        $stmt->bindValue(':PREFIX',$dato->prefix,PDO::PARAM_STR);
        $stmt->bindValue(':FIRST_NAME',$dato->firstName,PDO::PARAM_STR);
        $stmt->bindValue(':MIDDLE_NAME',$dato->middleName,PDO::PARAM_STR);
        $stmt->bindValue(':LAST_NAME',$dato->lastName,PDO::PARAM_STR);
        $stmt->bindValue(':PASSPORT_NATIONALITY',$dato->passportNationality,PDO::PARAM_STR);
        $stmt->bindValue(':BADGE_NAME',$dato->badgeName,PDO::PARAM_STR);
        $stmt->bindValue(':ADDRESS_1',$dato->address1,PDO::PARAM_STR);
        $stmt->bindValue(':ADDRESS_2',$dato->address2,PDO::PARAM_STR);
        $stmt->bindValue(':PASSPORT_NUMBER',$dato->passportNumber,PDO::PARAM_STR);
        $stmt->bindValue(':PASSPORT_EXPIRY_DATE',$dato->passportExpiryDate,PDO::PARAM_STR);
        $stmt->bindValue(':POSTAL_CODE',$dato->postalCode,PDO::PARAM_INT);
        $stmt->bindValue(':CITY',$dato->city,PDO::PARAM_STR);
        $stmt->bindValue(':COUNTRY',$dato->country,PDO::PARAM_STR);
        $stmt->bindValue(':JOB_TITLE',$dato->jobTitle,PDO::PARAM_STR);
        $stmt->bindValue(':JOB_TITLE_OTHER',$dato->jobTitleOhter,PDO::PARAM_STR);
        $stmt->bindValue(':ORGANISATION',$dato->organisation,PDO::PARAM_STR);
        $stmt->bindValue(':EMAIL',$dato->email,PDO::PARAM_STR);
        $stmt->bindValue(':DELEGATE_EMAIL',$dato->delegateEmail,PDO::PARAM_STR);
        $stmt->bindValue(':WORK_PHONE',$dato->workPone,PDO::PARAM_STR);
        $stmt->bindValue(':MOBILE_PHONE',$dato->mobilePhone,PDO::PARAM_STR);
        $stmt->bindValue(':PAYMENT_TYPE',$dato->paymentType,PDO::PARAM_INT);
        $stmt->bindValue(':TICKET_ID',$dato->ticketId,PDO::PARAM_INT);
        $stmt->bindValue(':REQUESTS_ID',$dato->requestId,PDO::PARAM_INT);
        $stmt->bindValue(':SPECIAL_DIET_PHYSICAL',$dato->specialDietaryPhysical,PDO::PARAM_STR);
        $stmt->bindValue(':BODYGUARD',$dato->bodyguard,PDO::PARAM_INT);
        
        $result = $stmt->execute();
        $dato->id = $this->conn->lastInsertId();
        
        $this->saveInscription($dato);
        
        if($dato->accompanying == 1){
            $this->saveAccompanyng($dato,0);
        }
        if($dato->bodyguard == 1){
            $this->saveBodyguard($dato,1);
        }
            
    }
    
}
?>