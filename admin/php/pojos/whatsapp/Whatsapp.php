<?php
//`idRtaWhatsapp`, `telefono`, `nombreWhatsapp`, `texto`, `leido`, `fromMe`, `fchEnvio`, `instance`, `dto`, `idChatApi`
class Whatsapp {

    public $idRtaWhatsapp;
    /**
     * 
     * @var WhatsappCuenta
     */
    public $whatsappCuenta;
    /**
     * 
     * @var WhatsappContacto
     */
    public $whatsappContacto;
    public $telefono;
    public $nombreWhatsapp;
    public $texto;
    public $leido;
    public $fromMe;
    public $fechaEnvioApi;
    public $fechaChat;
    public $fecha;
    public $instance;
    public $dto;
    public $idChatApi;
    public $jsonResponse;
    public $fechaLeidoChat;

    public static $LEIDO_ENVIADO_CHAT       = "-1";
    public static $LEIDO_ENVIADO_CHAT_API   = "0";
    public static $LEIDO_VISTO              = "2";
    public static $LEIDO_ENTREGADO          = "1";
	
    public function __construct($idChatApi=null,$telefono=null,$msg=null,$idWhatsCuenta=null) {
        $this->idChatApi       = $idChatApi;
	    $this->telefono        = $telefono;
	    $this->texto           = $msg;
	    $this->whatsappCuenta  = new WhatsappCuenta($idWhatsCuenta);
	}
	
	public function getFechaEnvioApiSql() {
	    return GeneralUtils::getDateSql($this->fechaEnvioApi);
	}
	
	public function getFechaChatSql() {
	    return GeneralUtils::getDateSql($this->fechaChat);
	}
	
	public function getFechaSql() {
	    return GeneralUtils::getDateSql($this->fecha);
	}
	
	public function getFechaEnvioApiView() {
	    return GeneralUtils::getDateSql4View($this->fechaEnvioApi);
	}
	
	public function getFechaChatView() {
	    return GeneralUtils::getDateSql4View($this->fechaChat);
	}
	
	public function getFechaView() {
	    return GeneralUtils::getDateSql4View($this->fecha);
	}
}
?>