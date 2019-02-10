<?php
//`id_whatsapp_contacto_tipo`, `id_tipo`, `id_region`, `nro_telefono`, `fecha`
class WhatsappContactoTipo {

    public $idWhatsappContactoTipo;
    public $descripcion;
    public $color;
	
    public function __construct($id=null,$nombre=null,$color=null) {
        $this->idWhatsappContactoTipo   = $id;
        $this->descripcion              = $nombre;
        $this->color                    = $color;
	}

}
?>