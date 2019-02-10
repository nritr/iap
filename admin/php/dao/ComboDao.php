<?php
include_once $path.'/admin/php/pojos/Rol.php';
include_once $path.'/admin/php/pojos/ProspectoEstado.php';
include_once $path.'/admin/php/pojos/Localidad.php';
include_once $path.'/admin/php/pojos/TipoTrabajo.php';
include_once $path.'/admin/php/pojos/TipoIngresoTope.php';
include_once $path.'/admin/php/pojos/TitularidadServicio.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappContactoTipo.php';
include_once $path.'/admin/php/filters/ProspectoEstadoFilter.php';
include_once $path.'/admin/php/filters/LocalidadFilter.php';
include_once $path.'/admin/php/filters/RegionFilter.php';
include_once $path.'/admin/php/filters/TipoTrabajoFilter.php';
include_once $path.'/admin/php/filters/TipoIngresoTopeFilter.php';
include_once $path.'/admin/php/filters/TitularidadServicioFilter.php';


class ComboDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	

	public function loadRoles($id_rol=null) {

	    $where    = "";
		$res      = [];
		if ($id_rol>0) {
		    $where.= " AND id_rol=".$id_rol;
		}
        $stmt = $this->conn->prepare("SELECT id_rol, nombre descripcion,tipo_asignacion ".
            " FROM ROL ".
            " WHERE 1 ".$where.
            " ORDER BY nombre",array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

		$res = $stmt->execute();


		$list = array();
		$i=0;
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
			$rol                     = new Rol($fila['id_rol'], $fila['descripcion']);
			$rol->tipoAsignacion     = $fila['tipo_asignacion'];
			$list[$i] = $rol;
			$i++;
		}
		return $list;
    }
	
    public function loadRegiones(RegionFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND r.id_region=".$filter->id;
        }
        if ($filter->nombreLike!="") {
            $where.= " AND r.nombre LIKE '%". $filter->nombreLike ."%'";
        }
        if ($filter->idLocalidad>0) {
            $where.= " AND r.id_region IN (SELECT id_region FROM LOCALIDAD l WHERE l.id_localidad=". $filter->idLocalidad .")";
        }
        $sql = "SELECT `id_region`, `nombre`,poligon ".
            " FROM REGION r ".
            " WHERE 1 ".$where.
            " ORDER BY r.nombre";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $rol            = new Region($fila['id_region'], $fila['nombre']);
            $rol->poligon   = $fila['poligon'];
            $list[] = $rol;
        }
        return $list;
    }
    
    public function loadProspectosEstados(ProspectoEstadoFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND id_prospecto_estado=".$filter->id;
        }
        if ($filter->onlyParents==true) {
            $where.= " AND pe.id_prospecto_estado_padre IS NULL";
        } else if ($filter->idEstadoPadre>0) {
            $where.= " AND id_prospecto_estado_padre=".$filter->idEstadoPadre;
        }
        
        $stmt = $this->conn->prepare("SELECT `id_prospecto_estado`, `id_prospecto_estado_padre`, `nombre`, `orden` ".
                    " FROM PROSPECTO_ESTADO pe ".
                    " WHERE 1 ".$where.
                    " ORDER BY orden",array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                = new ProspectoEstado($fila['id_prospecto_estado'], $fila['nombre'], $fila['id_prospecto_estado_padre']);
            $obj->orden         = $fila['orden'];
            $list[]             = $obj;
        }
        return $list;
    }
    
    public function loadLocalidades(LocalidadFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND l.id_localidad=".$filter->id;
        }
        if ($filter->likeAll!="") {
            $where.= " AND (l.localidad LIKE '%". $filter->likeAll ."%' OR l.partido LIKE '%". $filter->likeAll ."%')";
        }
        
        $sql = "SELECT `id_localidad`, `id_localidad_access`, `id_region`, `localidad`, `zona_entrega`, `partido`, `horario_entrega` ".
            " FROM LOCALIDAD l ".
            " WHERE 1 ".$where.
            " ORDER BY ".$filter->getOrderBy().
            " LIMIT 0,25";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                    = new Localidad($fila['id_localidad'], $fila['localidad'], $fila['partido']);
            $obj->horarioEntrega    = $fila['horario_entrega'];
            $obj->region            = new Region($fila['id_region']);
            $obj->zonaEntrega       = $fila['zona_entrega'];
            $list[]                 = $obj;
        }
        return $list;
    }
    
    public function loadTipoTrabajo(TipoTrabajoFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND tt.id_tipo_trabajo=".$filter->id;
        }
        if ($filter->nombreLike!="") {
            $where.= " AND tt.nombre LIKE '%".$filter->nombreLike."%'";
        }
        $sql = "SELECT `id_tipo_trabajo`, `nombre`, `categoria_trabajo`, `gcia_presunta`, `puntaje_tipo_trabajo`, `tope_tipo_trabajo`, ".
                " `posible_independiente`, `posible_emprendimiento`, `posible_comercio`, `denegar_auto` ".
                " FROM TIPO_TRABAJO tt ".
                " WHERE 1 ".$where.
                " ORDER BY ".$filter->getOrderBy().
                " LIMIT 0,25";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                    = new TipoTrabajo($fila['id_tipo_trabajo'], $fila['nombre']);
            $obj->categoriaTrabajo  = $fila['categoria_trabajo'];
            $obj->gciaPresunta      = $fila['gcia_presunta'];
            $obj->puntajeTipoTrabajo= $fila['puntaje_tipo_trabajo'];
            $obj->denegarAuto       = $fila['denegar_auto'];
            $obj->posibleComercio   = $fila['posible_comercio'];
            $obj->posibleEmprendimiento = $fila['posible_emprendimiento'];
            $obj->posibleIndependiente  = $fila['posible_independiente'];
            $obj->topeTipoTrabajo   = $fila['tope_tipo_trabajo'];
            $list[]                 = $obj;
        }
        return $list;
    }
    
    public function loadTipoIngresoTope(TipoIngresoTopeFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND ti.id_tipo_ingreso_tope=".$filter->id;
        }
        if ($filter->nombreLike!="") {
            $where.= " AND ti.nombre LIKE '%".$filter->nombreLike."%'";
        }
        $sql = "SELECT `id_tipo_ingreso_tope`, `nombre`, `tope`, `tope_especial`, `cond_topeEspecial` ".
                " FROM TIPO_INGRESO_TOPE ti ".
                " WHERE 1 ".$where.
                " ORDER BY ".$filter->getOrderBy().
                " LIMIT 0,25";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                    = new TipoIngresoTope($fila['id_tipo_ingreso_tope'], $fila['nombre']);
            $obj->condTopeEspecial  = $fila['cond_topeEspecial'];
            $obj->tope              = $fila['tope'];
            $obj->topeEspecial      = $fila['tope_especial'];
            $list[]                 = $obj;
        }
        return $list;
    }
    
    public function loadTitularidadServicio(TitularidadServicioFilter $filter) {
        
        $where    = "";
        if ($filter->id>0) {
            $where.= " AND ts.id_titularidad_servicio=".$filter->id;
        }
        if ($filter->titularidadLike!="") {
            $where.= " AND ts.titularidad_servicio LIKE '%".$filter->titularidadLike."%'";
        }
        $sql = "SELECT `id_titularidad_servicio`, `titularidad_servicio`, `puntaje`, `monto_opc`, `relacion` ".
            " FROM TITULARIDAD_SERVICIO ts ".
            " WHERE 1 ".$where.
            " ORDER BY ".$filter->getOrderBy().
            " LIMIT 0,25";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                    = new TitularidadServicio($fila['id_titularidad_servicio'], $fila['titularidad_servicio']);
            $obj->puntaje           = $fila['puntaje'];
            $obj->montoOpc          = $fila['monto_opc'];
            $obj->relacion          = $fila['relacion'];
            $list[]                 = $obj;
        }
        return $list;
    }
    
    public function loadWhtsContactoTipo($id=null) {
        
        $where    = "";
        if ($id>0) {
            $where.= " AND wct.id_whatsapp_contacto_tipo=".$id;
        }

        $sql = "SELECT `id_whatsapp_contacto_tipo`, `descripcion`, `color` ".
            " FROM WHATSAPP_CONTACTO_TIPO wct ".
            " WHERE 1 ".$where.
            " ORDER BY wct.descripcion";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $res = $stmt->execute();
        
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                    = new WhatsappContactoTipo($fila['id_whatsapp_contacto_tipo'], $fila['descripcion'],$fila['color']);
            $list[]                 = $obj;
        }
        return $list;
    }
}
?>