<?php
include_once $path.'/admin/php/pojos/PlanCuota.php';
include_once $path.'/admin/php/filters/PlanCuotaFilter.php';


class PlanCuotaDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	

	public function load(PlanCuotaFilter $filter) {


		$where    = "";
		$groupBy  = "";
		if ($filter->id>0) {
		    $where.= " AND p.id_plan_cuota=:id_plan_cuota ";
		}
		if ($filter->cuotas>0) {
		    $where.= " AND p.cuotas=:cuotas";
		}
		if ($filter->frecuenciaPlan>0) {
		    $where.= " AND p.frecuencia_plan=:frecuencia_plan ";
		}
		if ($filter->montoPlanMax>0) {
		    $where .= " AND p.monto_plan<=:monto_plan";
		}
		if ($filter->montoPlan>0) {
		    $where .= " AND p.monto_plan=:monto_plan";
		}
		if ($filter->groupBy!="") {
		    $groupBy .= $filter->groupBy;
		}
		$where.= " AND p.habilitado=1 ";
		$sql = "SELECT `id_plan_cuota`, `id_plan_access`, `monto_plan`, `frecuencia_plan`, `cuotas`, `valor_cuota`, `cant_meses`, `interes`, `nombre`, `habilitado`, `id_planes_sql` ".
                    " FROM PLAN_CUOTA p WHERE 1 ".$where.
                    ($groupBy!="" ? " GROUP BY ".$groupBy : "").
                    " ORDER BY ".$filter->getOrderBy();
		//echo $sql;
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if ($filter->id>0) {
            $stmt->bindValue(':id_prospecto', $filter->id	, PDO::PARAM_INT);
        }
        if ($filter->cuotas>0) {
            $stmt->bindValue(':cuotas', $filter->cuotas	, PDO::PARAM_INT);
        }
        if ($filter->frecuenciaPlan>0) {
            $stmt->bindValue(':frecuencia_plan', $filter->frecuenciaPlan	, PDO::PARAM_INT);
        }
        if ($filter->montoPlanMax>0) {
            $stmt->bindValue(':monto_plan', $filter->montoPlanMax	, PDO::PARAM_INT);
        }
        if ($filter->montoPlan>0) {
            $stmt->bindValue(':monto_plan', $filter->montoPlan	, PDO::PARAM_INT);
        }
		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    
		    $obj                      = new PlanCuota($fila['id_plan_cuota'],$fila['id_plan_access']);
		    $obj->cantMeses           = $fila['cant_meses'];
		    $obj->cuotas              = $fila['cuotas'];
		    $obj->frecuenciaPlan      = $fila['frecuencia_plan'];
		    $obj->habilitado          = $fila['habilitado'];
		    $obj->idPlanesSql         = $fila['id_planes_sql'];
		    $obj->interes             = $fila['interes'];
		    $obj->montoPlan           = $fila['monto_plan'];
		    $obj->nombre              = $fila['nombre'];
		    $obj->valorCuota          = $fila['valor_cuota'];
			
			$list[] = $obj;
		}

		return $list;
    }
	
    /*
	public function save(Rodado $obj) {

		$userService		= new UsuarioService();
		$usr 				= $userService->getUserLogued();
		
		$sql = "INSERT INTO GP_RODADO ( `id_sucursal`, `patente`, `modelo`, `conductor`) ".
		          " VALUES (:id_sucursal, :patente, :modelo, :conductor)";

        $stmt = $this->conn->prepare($sql);

		$stmt->bindValue(":id_sucursal"		,$usr->sucursal->idSucursal	, PDO::PARAM_INT);
		$stmt->bindValue(":patente"			,$obj->patente			    , PDO::PARAM_STR); 
		$stmt->bindValue(":modelo"		    ,$obj->modelo				, PDO::PARAM_INT);
		$stmt->bindValue(":conductor"       ,$obj->conductor            , PDO::PARAM_STR);
		
        $result = $stmt->execute();
		$id = $this->conn->lastInsertId(); 
		$obj->idRodado = $id;

		return $obj;
	}*/
	
	/*public function update(Prospecto $obj) {
		$sql = "UPDATE `PROSPECTO` SET id_estado=:id_estado,messenger_id=:messenger_id,".
		      " celular_normalizado=:celular_normalizado,dni_normalizado=:dni_normalizado,normalizado=:normalizado,ult_llamado=:ult_llamado,prox_llamado=:prox_llamado,cant_llamados=:cant_llamados ".
		      " WHERE id_prospecto=:id_prospecto";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_prospecto"        ,$obj->idProspecto    	            , PDO::PARAM_INT);
        $stmt->bindValue(":id_estado"           ,$obj->estado->idProspectoEstado    , PDO::PARAM_INT);
        $stmt->bindValue(":messenger_id"        ,$obj->messengerId			        , PDO::PARAM_STR);
        $stmt->bindValue(":celular_normalizado" ,$obj->celularNormalizado           , PDO::PARAM_STR);
        $stmt->bindValue(":dni_normalizado"     ,$obj->dniNormalizado		        , PDO::PARAM_INT);
        $stmt->bindValue(":normalizado"         ,$obj->normalizado		            , PDO::PARAM_INT);
        $stmt->bindValue(":ult_llamado"         ,$obj->ultLlamado                   , PDO::PARAM_STR);
        $stmt->bindValue(":cant_llamados"       ,$obj->cantLlamados                 , PDO::PARAM_INT);
        $stmt->bindValue(":prox_llamado"        ,$obj->proxLlamado                  , PDO::PARAM_STR);

        $result = $stmt->execute();
	}*/

	

	
}
?>