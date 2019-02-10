<?php
include_once $path.'/admin/php/pojos/Pojo.php';
include_once $path.'/admin/php/pojos/ProspectoNormalizado.php';
include_once $path.'/admin/php/pojos/PlanCuota.php';

class LogHistorico extends Pojo {

    //SELECT `id`, `id_tipo`, `id_prospecto`, `id_usuario`, `fecha` FROM `LOG_HISTORICO` WHERE 1
    /**
     * 
     * @var DatoParametrico
     */
	public $tipo;
	/**
	 * 
	 * @var Prospecto
	 */
	public $prospecto;
	/**
	 * 
	 * @var Usuario
	 */
	public $usuario;
	
	public $fecha;
	
	public static $ID_TIPO_PROSPECTO_LLAMADO       = 19;
	public static $ID_TIPO_PROSPECTO_VOLV_LLAMAR   = 20;
	public static $ID_TIPO_PROSPECTO_VOLV_URG      = 21;
	public static $ID_TIPO_PROSPECTO_NORMALIZACION = 22;
	public static $ID_TIPO_ELECCION_PLAN           = 23;
	public static $ID_TIPO_CAMBIO_ESTADO           = 24;
	
	public function __construct($id=null,$id_tipo=null,$id_usuario=null) {
		$this->id         = $id;
		$this->tipo       = new DatoParametrico($id_tipo);
		$this->usuario    = new Usuario($id_usuario);
		$this->prospecto  = new Prospecto();
	}
	
	
}
?>