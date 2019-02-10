<?php
include_once $path.'/admin/php/pojos/Prospecto.php';
include_once $path.'/admin/php/pojos/ProspectoEstado.php';
include_once $path.'/admin/php/pojos/ProspectoNormalizado.php';
include_once $path.'/admin/php/pojos/PlanCuota.php';
include_once $path.'/admin/php/filters/ProspectoFilter.php';


class ProspectoDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	

	public function load(ProspectoFilter $filter) {


		$where = "";
		if ($filter->id>0) {
		    $where.= " AND p.id_prospecto=:id_prospecto ";
		}
		if ($filter->idEstado>0) {
		    $where.= " AND p.id_estado IN (SELECT id_prospecto_estado FROM PROSPECTO_ESTADO lpe WHERE id_prospecto_estado_padre=:id_estado) ";
		}
		if ($filter->idSubEstado>0) {
		    $where.= " AND p.id_estado=:idSubEstado ";
		}
		if ($filter->idOrigen>0) {
		    $where.= " AND p.id_origen=:id_origen ";
		}
		if ($filter->idEstadoIN!="") {
		    $where.= " AND p.id_estado IN (". $filter->idEstadoIN .") ";
		}
		if ($filter->nombreLike!="") {
		    $where.= " AND (p.nombre LIKE :nombreLike OR p.nombre_fb LIKE :nombreLike)";
		}
		if ($filter->telefonoLike!="") {
		    $where.= " AND (p.celular LIKE :telefonoLike OR p.celular_normalizado LIKE :telefonoLike OR p.dni LIKE :telefonoLike)";
		}
		if ($filter->volverLlamarOrNull==1) {
		    $where.="AND (p.prox_llamado is null OR p.prox_llamado<=NOW())";
		}
		if ($filter->llamadoPend==1) {
		    $where.="AND (p.prox_llamado>NOW())";
		}
		if ($filter->propuestaEnviada==1) {
		    $where.="AND (pn.id IS NOT NULL)";
		}
		$sql = "SELECT `id_prospecto`, `id_estado`, `id_origen`, p.nombre,p.apellido, p.genero,`nombre_fb`, `messenger_id`,  `celular`, `celular_normalizado`, `edad`, `dni`,dni_normalizado, dni_coincide,`dni_normalizado`, ".
                " `localidad`, `oficio`, vivienda,contrato, `servicio_anombrede`, `blanco_negro`, `salario`, `normalizado`, `horario_preferencia`, `fecha`, ".
                " p.ult_llamado,DATEDIFF(NOW(),p.ult_llamado) ult_llamado_screen_dias ,DATE_FORMAT(p.ult_llamado, '%H:%i') ult_llamado_screen_horario,".
                " p.prox_llamado,DATEDIFF(NOW(),p.prox_llamado) prox_llamado_screen_dias ,DATE_FORMAT(p.prox_llamado, '%H:%i') prox_llamado_screen_horario,".
                " p.cant_llamados, p.id_plan_cuota,".
                " pe.nombre nom_estado,pe.id_prospecto_estado_padre,pe.nombre_abreviado, ".
                " pep.nombre nom_estado_padre, pep.id_prospecto_estado,pep.nombre_abreviado nombre_abreviado_padre, ".
                " pn.id id_prospecto_normalizado, pn.id_actividad_laboral, pn.id_rela_laboral, pn.id_frecuencia_cobro, pn.dni_dom, pn.id_titularidad_servicio, pn.id_vivienda, pn.genero ".
                " FROM PROSPECTO p ".
                " INNER JOIN PROSPECTO_ESTADO pe ON pe.id_prospecto_estado=p.id_estado ".
                " LEFT JOIN PROSPECTO_ESTADO pep ON pep.id_prospecto_estado=pe.id_prospecto_estado_padre ".
                " LEFT JOIN PROSPECTO_NORMALIZADO pn ON pn.id=p.id_prospecto_normalizado ".
                " WHERE 1 ".$where.
                " ORDER BY ".$filter->getOrderBy().
                " LIMIT 0,100";
		//die ($sql);
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if ($filter->id>0) {
            $stmt->bindValue(':id_prospecto', $filter->id	, PDO::PARAM_INT);
        }
        if ($filter->idEstado>0) {
            $stmt->bindValue(':id_estado', $filter->idEstado	, PDO::PARAM_INT);
        }
        if ($filter->idSubEstado>0) {
            $stmt->bindValue(':idSubEstado', $filter->idSubEstado	, PDO::PARAM_INT);
        }
        if ($filter->idOrigen>0) {
            $stmt->bindValue(':id_origen', $filter->idOrigen	, PDO::PARAM_INT);
        }
        if ($filter->nombreLike!="") {
            $stmt->bindValue(':nombreLike', '%'.$filter->nombreLike.'%'	, PDO::PARAM_STR);
        }
        if ($filter->telefonoLike!="") {
            $stmt->bindValue(':telefonoLike', '%'.$filter->telefonoLike.'%'	, PDO::PARAM_STR);
        }

		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    
		    $obj = new Prospecto($fila['id_prospecto'],$fila['nombre'],$fila['nombre_fb'],$fila['id_estado'],$fila['id_prospecto_normalizado']);
		    $obj->apellido           = $fila['apellido'];
		    $obj->genero             = $fila['genero'];
		    $obj->nombreAbreviado    = $fila['blanco_negro'];
			$obj->blancoNegro        = $fila['blanco_negro'];
			$obj->celular            = $fila['celular'];
			$obj->celularNormalizado = $fila['celular_normalizado'];
			$obj->dni                = $fila['dni'];
			$obj->dniCoincide        = $fila['dni_coincide'];
			$obj->dniNormalizado     = $fila['dni_normalizado'];
			$obj->edad               = $fila['edad'];
			$obj->estado             = new ProspectoEstado($fila['id_estado'],$fila['nom_estado']);
			$obj->estado->nombreAbreviado                = $fila['nombre_abreviado'];
			$obj->estado->estadoPadre                    = new ProspectoEstado($fila['id_prospecto_estado'],$fila['nom_estado_padre']);
			$obj->estado->estadoPadre->nombreAbreviado   = $fila['nombre_abreviado_padre'];
			$obj->fecha              = $fila['fecha'];
			$obj->horarioPreferencia = $fila['horario_preferencia'];
			$obj->localidad          = $fila['localidad'];
			$obj->messengerId        = $fila['messenger_id'];
			$obj->normalizado        = $fila['normalizado'];
			$obj->oficio             = $fila['oficio'];
			$obj->vivienda           = $fila['vivienda'];
			$obj->contrato           = $fila['contrato'];
			$obj->salario            = $fila['salario'];
			$obj->servicioAnombrede  = $fila['servicio_anombrede'];
			$obj->ultLlamado         = $fila['ult_llamado'];
			if ($fila['ult_llamado']!="") {
			    $obj->ultLlamadoScreen   = Prospecto::diffDateScreen($fila['ult_llamado_screen_dias'], $fila['ult_llamado_screen_horario']);
			}
			$obj->proxLlamado        = $fila['prox_llamado'];
			if ($fila['prox_llamado']!="") {
			    $obj->proxLlamadoScreen  = Prospecto::diffDateScreen($fila['prox_llamado_screen_dias'], $fila['prox_llamado_screen_horario']);
			}
			$obj->cantLlamados       = $fila['cant_llamados'];
			$obj->plan->id           = $fila['id_plan_cuota'];
			if ($fila['id_prospecto_normalizado']!="") {
			    //pn.id id_prospecto_normalizado, pn.id_actividad_laboral, pn.id_rela_laboral, pn.id_frecuencia_cobro, pn.dni_dom, pn.id_titularidad_servicio, pn.id_vivienda, pn.genero ".
			    $obj->prospectoNormalizado->actividadLaboral->idTipoTrabajo  = $fila['id_actividad_laboral'];
			    $obj->prospectoNormalizado->dniDom                           = $fila['dni_dom'];
			    $obj->prospectoNormalizado->frecuenciaCobro->idDato          = $fila['id_frecuencia_cobro'];
			    $obj->prospectoNormalizado->genero                           = $fila['genero'];
			    $obj->prospectoNormalizado->relaLaboral->idTipoIngresoTope   = $fila['id_rela_laboral'];
			    $obj->prospectoNormalizado->titularidadServicio->idTitularidadServicio = $fila['id_titularidad_servicio'];
			    $obj->prospectoNormalizado->vivienda->idDato                 = $fila['id_vivienda'];
			}
			
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
	
	public function update(Prospecto $obj) {
		$sql = "UPDATE `PROSPECTO` SET id_estado=:id_estado,messenger_id=:messenger_id,nombre=:nombre,apellido=:apellido,".
		      " celular_normalizado=:celular_normalizado,dni_normalizado=:dni_normalizado,normalizado=:normalizado,ult_llamado=:ult_llamado,".
		      " prox_llamado=:prox_llamado,cant_llamados=:cant_llamados,id_prospecto_normalizado=:id_prospecto_normalizado,id_plan_cuota=:id_plan_cuota ".
		      " WHERE id_prospecto=:id_prospecto";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_prospecto"                ,$obj->idProspecto    	                 , PDO::PARAM_INT);
        $stmt->bindValue(":id_prospecto_normalizado"    ,$obj->prospectoNormalizado->getId()     , PDO::PARAM_INT);
        
        $stmt->bindValue(":id_estado"           ,$obj->estado->idProspectoEstado    , PDO::PARAM_INT);
        $stmt->bindValue(":messenger_id"        ,$obj->messengerId			        , PDO::PARAM_STR);
        $stmt->bindValue(":nombre"              ,$obj->nombre   			        , PDO::PARAM_STR);
        $stmt->bindValue(":apellido"            ,$obj->apellido   			        , PDO::PARAM_STR);
        $stmt->bindValue(":celular_normalizado" ,$obj->celularNormalizado           , PDO::PARAM_STR);
        $stmt->bindValue(":dni_normalizado"     ,$obj->dniNormalizado		        , PDO::PARAM_INT);
        $stmt->bindValue(":normalizado"         ,$obj->normalizado		            , PDO::PARAM_INT);
        $stmt->bindValue(":ult_llamado"         ,$obj->ultLlamado                   , PDO::PARAM_STR);
        $stmt->bindValue(":cant_llamados"       ,$obj->cantLlamados                 , PDO::PARAM_INT);
        $stmt->bindValue(":prox_llamado"        ,$obj->proxLlamado                  , PDO::PARAM_STR);
        $stmt->bindValue(":id_plan_cuota"       ,$obj->plan->getId()                , PDO::PARAM_INT);
        
        $result = $stmt->execute();
	}

	public function normalizar(Prospecto $obj) {
	    $sql = "INSERT INTO PROSPECTO_NORMALIZADO (id_actividad_laboral, id_rela_laboral, id_frecuencia_cobro, dni_dom, id_titularidad_servicio, id_vivienda,genero) VALUES ".
	    " (:id_actividad_laboral, :id_rela_laboral, :id_frecuencia_cobro, :dni_dom, :id_titularidad_servicio, :id_vivienda,:genero)";
	    
	    $normalizado = $obj->prospectoNormalizado;
	    $stmt = $this->conn->prepare($sql);
	    $stmt->bindValue(":id_actividad_laboral"   , $normalizado->actividadLaboral->idTipoTrabajo     , PDO::PARAM_INT);
	    $stmt->bindValue(":id_rela_laboral"        , $normalizado->relaLaboral->idTipoIngresoTope      , PDO::PARAM_INT);
	    $stmt->bindValue(":id_frecuencia_cobro"    , $normalizado->frecuenciaCobro->idDato	           , PDO::PARAM_INT);
	    $stmt->bindValue(":dni_dom"                , $normalizado->dniDom           			       , PDO::PARAM_INT);
	    $stmt->bindValue(":id_titularidad_servicio", $normalizado->titularidadServicio->idTitularidadServicio , PDO::PARAM_INT);
	    $stmt->bindValue(":id_vivienda"            , $normalizado->vivienda->idDato                   , PDO::PARAM_INT);
	    $stmt->bindValue(":genero"                 , $normalizado->genero                             , PDO::PARAM_INT);

	    
	    $result = $stmt->execute();
	    
	    return  $this->conn->lastInsertId(); 
	}

	
}
?>