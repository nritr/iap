<?php

class Cliente {

    public $idCliente;
    public $codigo;
    public $nombre;
    public $cuit;
    public $nombreContacto;
    public $telefono;
    public $celular;
    public $email;
    public $direccion;
    public $localidad;
    public $cp;
    public $observacion;
    public $estado;
    public $fechaCreate;
    private $isEmpresa;
    
    public static $EMPRESA = 1;
    public static $PERSONA = 2;
   
    /**
     * 
     * @var Sucursal
     */
    public $sucursal;
    /**
     * 
     * @var Planta[]
     */
    public $plantas;
	
	
    public function __construct($id=null,$codigo=null) {
	    $this->idCliente=$id;
	    $this->codigo=$codigo;
	}
	
	
}
?>