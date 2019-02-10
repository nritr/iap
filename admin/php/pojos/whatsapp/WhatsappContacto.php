<?php
//`id_whatsapp_contacto`, `id_tipo`, `id_region`, `nro_telefono`, `fecha`
class WhatsappContacto {

    public $idWhatsappContacto;
    /**
     * 
     * @var WhatsappContactoTipo
     */
    public $tipo;
    /**
     * 
     * @var Region
     */
    public $region;
    public $nroTelefono;
    public $nombre;
    public $fecha;

	
    public function __construct(int $id=null,$nombre=null,int $id_tipo=null,$id_region=null,int $nro=null) {
        $this->idWhatsappContacto = $id;
        $this->nombre               = $nombre;
        $this->tipo                 = new WhatsappContactoTipo($id_tipo);
        $this->region               = new Region($id_region);
        $this->nroTelefono          = $nro;
	}
	
	public function getId() {
	    return ($this->idWhatsappContacto=="" ? null : $this->idWhatsappContacto);
	}
}
?>