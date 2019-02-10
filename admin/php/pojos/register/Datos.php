<?php
class Datos {
    public function __construct() {        $this->arrival          = new ArrivalDetail();        $this->companionArray   = [];        $this->inscription      = new Inscription();        $this->social           = new Social();        $this->request          = new Request();        $this->ticket           = new Ticket();    }    
    /*NICO: estan en inscription, las otras status e image que tmb estan en inscription no las toque porq las usas, asi no rompo nada.     * public $code;
    */
    public $transaction;
    public $transactionCode;
    public $status;
    public $image;
    //public $arrived; NICO: saque esta propiedad que no esas, y wsta en Inscription
    
//ID as DATOS_ID, SOCIAL_ID, COMPANION_ID, ARRIVAL_DETAILS_ID, GRANTING_PARTICIPANT, ATTEND, ATTEND_TYPE, CONTRIBUTE, CONTRIBUTE_TYPE, ACCOMPANYING, 
//COMMITTEE_MEMBER, PREFIX, FIRST_NAME, MIDDLE_NAME, LAST_NAME, PASSPORT_NATIONALITY, BADGE_NAME, ADDRESS_1, ADDRESS_2,
//PASSPORT_NUMBER, PASSPORT_EXPIRY_DATE, POSTAL_CODE, CITY, COUNTRY, JOB_TITLE, JOB_TITLE_OTHER, ORGANISATION, EMAIL,
//DELEGATE_EMAIL, WORK_PHONE, MOBILE_PHONE, PAYMENT_TYPE, TICKET_ID, REQUESTS_ID

   
   public $socialId;
   public $companionId;
   public $arrivalDetailsId;
   public $grantingParticipant;
   public $attend;
   public $attendType;
   public $contribute;
   public $contributeType;
   public $accompanying;
   public $commiteeMember;
   public $prefix;
   public $firstName;
   public $middleName;
   public $lastName;
   public $passportNationality;
   public $badgeName;
   public $address1;
   public $address2;
   public $passportNumber;
   public $passportExpiryDate;
   public $postalCode;
   public $city;
   public $country;
   public $specialDietaryPhysical;
   public $jobTitle;
   public $jobTitleOhter;
   public $organisation;
   public $email;
   public $delegateEmail;
   public $workPone;
   public $mobilePhone;
   public $paymentType;
   public $ticketId;
   public $requestId;
   
   //social
   //ID, PRESIDENT, ACCOMPANYING_PRESIDENT, CONFERENCE_DINNER, ACCOMPANYING_CONFERENCE_DINNER, FAREWELL_PARTY,
   //ACCOMPANYING_FAREWELL_PARTY, NETWORKING_NIGHT
   
   public $id;
   public $president;
   public $accompanyngPresident;
   public $conferenceDinner;
   public $accompanyingConferenceDinner;
   public $farewellParty;
   public $accompanyingFarewellParty;
   public $networkingNight;
   
   //Request
   //ID, LETTER_INVITATION, CERTIFICATE_ATTENDANCE
   //public $requestId;
   public $letterInvitation;
   public $certificateAttendance;
      /**    *     * @var Companion[]    */
   public $companionArray;      /**    *     * @var Inscription    */   public $inscription;      /**    *    * @var ArrivalDetail    */   public $arrival;      /**    *     * @var Social    */   public $social;      /**    *     * @var Request    */   public $request;      /**    *     * @var Ticket    */
   public $ticket;
   public $description;
   public $value;
   public $total;
   public $type;
   public $daysArray;
      public $registerDate;   public function getImageName() { 
       return PATH_IMGS.$this->image;
   }
}