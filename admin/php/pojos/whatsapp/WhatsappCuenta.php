<?php
//`id_whatsapp_cuenta`, `id_area`, `instance`, `url`, `token`, `tel_asignado`, `owner` 
class WhatsappCuenta {

    public $idWhatsappCuenta;
    /**
     * 
     * @var Area
     */
    public $area;
    public $cuenta;
    public $url;
    public $instance;
    public $token;
    public $telAsignado;
    public $owner;

	
    public function __construct($idWhatsappCuenta=null,$cuenta=null,$id_area=null) {
        $this->idWhatsappCuenta = $idWhatsappCuenta;
        $this->area             = new Area($id_area);
        $this->cuenta           = $cuenta;
	}

}
?>