<?php
include_once $path.'/admin/php/pojos/Pojo.php';

class PlanCuota extends Pojo {

    //SELECT `id_plan_cuota`, `id_plan_access`, `monto_plan`, `frecuencia_plan`, `cuotas`, `valor_cuota`, `cant_meses`, `interes`, `nombre`, `habilitado`, `id_planes_sql` 
    //FROM `PLAN_CUOTA` WHERE 1

    public $idPlanAccess;
    public $montoPlan;
    public $frecuenciaPlan;
    public $cuotas;
    public $valorCuota;
    public $cantMeses;
    public $interes;
    public $nombre;
    public $habilitado;
    public $idPlanesSql;
	
	public function __construct($id=null,$id_plan_access=null) {
		$this->id                 = $id;
		$this->idPlanAccess       = $id_plan_access;
	}
	
	
}
?>