<?php
include_once $path.'/admin/php/pojos/Pojo.php';
class ProspectoNormalizado extends  Pojo {

    //SELECT `id`, `id_actividad_laboral`, `id_rela_laboral`, `id_frecuencia_cobro`, `dni_dom`, `id_titularidad_servicio`, `id_vivienda`, `genero` FROM `PROSPECTO_NORMALIZADO` WHERE 1
    /**
     * 
     * @var TipoTrabajo
     */
	public $actividadLaboral;
	/**
	 * 
	 * @var TipoIngresoTope
	 */
	public $relaLaboral;
	/**
	 * 
	 * @var DatoParametrico
	 */
	public $frecuenciaCobro;
	public $dniDom;
	/**
	 * 
	 * @var TitularidadServicio
	 */
	public $titularidadServicio;
	/**
	 * 
	 * @var DatoParametrico
	 */
	public $vivienda;
	public $genero;
	
	public function __construct($id=null,$id_actividad_laboral=null,$id_rela_laboral=null,$id_frecuencia_cobro=null) {
        $this->id                   = $id;
        $this->actividadLaboral     = new TipoTrabajo($id_actividad_laboral);
        $this->relaLaboral          = new TipoIngresoTope($id_rela_laboral);
        $this->frecuenciaCobro      = new DatoParametrico($id_frecuencia_cobro);
        $this->titularidadServicio  = new TitularidadServicio();
        $this->vivienda             = new DatoParametrico();
    }
	
	
}
?>