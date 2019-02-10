<?php
include_once $path.'/admin/php/pojos/DatoParametrico.php';
include_once $path.'/admin/php/pojos/CategoriaParametrica.php';
include_once $path.'/admin/php/filters/DatoParametricoFilter.php';


class DatosParametricosDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	public function load(DatoParametricoFilter $filter) {
		$where = "";
		if ($filter->id>0) {
			$where .= " AND dp.id_dato_parametrico=:id_dato_parametrico ";
		}
		if ($filter->idCategoria>0) {
			$where .= " AND dp.id_categoria_parametrica=:id_categoria_parametrica ";
		}
		if ($filter->tipo>0) {
			$where .= " AND cp.tipo=:tipo ";
		}
		if ($filter->idDatoParametricoPadre>0) {
		    $where .= " AND dp.id_dato_parametrico_padre=:id_dato_parametrico_padre ";
		}

		$where .= " AND dp.estado=".$filter->getEstado();
		
		$sql = "SELECT id_dato_parametrico, dp.id_categoria_parametrica, dp.nombre, dp.estado ,".
		      " cp.nombre categoria,cp.tipo, dp.valor,dp.id_dato_parametrico_padre ".
				" FROM datos_parametricos dp ".
				" INNER JOIN categorias_parametricas cp ON cp.id_categoria_parametrica=dp.id_categoria_parametrica ".
				" WHERE 1 ".$where.
				" ORDER BY ".$filter->getOrderBy();
				

        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));


		if ($filter->id>0) {
			$stmt->bindValue(':id_dato_parametrico', $filter->id , PDO::PARAM_INT);
		}
		if ($filter->idCategoria>0) {
			$stmt->bindValue(':id_categoria_parametrica', $filter->idCategoria , PDO::PARAM_INT);
		}
		if ($filter->tipo>0) {
			$stmt->bindValue(':tipo', $filter->tipo , PDO::PARAM_INT);
		}
		if ($filter->idDatoParametricoPadre>0) {
		    $stmt->bindValue(':id_dato_parametrico_padre', $filter->idDatoParametricoPadre , PDO::PARAM_INT);
		}


		$res = $stmt->execute();
		$list = array();
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
			$obj = new DatoParametrico($fila['id_dato_parametrico'],$fila['nombre']);
			$obj->categoria			= new CategoriaParametrica($fila['id_categoria_parametrica'],$fila['categoria']);
			$obj->estado			= $fila['estado'];
			$obj->valor			    = $fila['valor'];
			$obj->estado			= $fila['estado'];
			$obj->tipo				= $fila['tipo'];
			$obj->idDatoPadre		= $fila['id_dato_parametrico_padre'];
			$list[] = $obj;

		}

		return $list;
    }
	

	public function loadCategorias(DatoParametricoFilter $filter) {
		$where = "";
		if ($filter->id>0) {
			$where .= " AND cp.id_categoria_parametrica=:id_categoria_parametrica ";
		}
		if ($filter->tipo>0) {
			$where .= " AND cp.tipo=:tipo ";
		}

		$sql = "SELECT cp.id_categoria_parametrica, nombre, tipo ".
				" FROM categorias_parametricas cp ".
				" WHERE 1 ".$where.
				" ORDER BY ".$filter->getOrderBy();
				
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));


		if ($filter->id>0) {
			$stmt->bindValue(':id_categoria_parametrica', $filter->id , PDO::PARAM_INT);
		}
		if ($filter->tipo>0) {
			$stmt->bindValue(':tipo', $filter->tipo , PDO::PARAM_INT);
		}


		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
			$obj = new DatoParametrico($fila['id_categoria_parametrica'],$fila['nombre']);
			$obj->tipo				= $fila['tipo'];
			$list[] = $obj;

		}

		return $list;
    }

    /**
     * 
     * @param DatoParametrico $obj
     * @return int
     */
	public function save(DatoParametrico $obj) {

		$sql = "";
		if ($obj->idDato==null) {
			$sql = "INSERT INTO `datos_parametricos`( `id_categoria_parametrica`, `id_dato_parametrico_padre`, `nombre`, `valor`) VALUES ".
			     " (:id_categoria_parametrica,:id_dato_parametrico_padre,:nombre,:valor)";
		} else {
			$sql = "UPDATE datos_parametricos SET nombre=:nombre,valor=:valor,estado=:estado,id_dato_parametrico_padre=:id_dato_parametrico_padre ".
			     " WHERE id_dato_parametrico=:id_dato_parametrico ";
		}

        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(":nombre"					  ,$obj->nombre		, PDO::PARAM_STR); 
		$stmt->bindValue(":valor"				      ,$obj->valor		, PDO::PARAM_STR);
		$stmt->bindValue(":id_dato_parametrico_padre" ,$obj->idDatoPadre, PDO::PARAM_INT);
		
		if ($obj->idDato==null) {
		    $stmt->bindValue(":id_categoria_parametrica"  ,$obj->categoria->idCategoriaParametrica , PDO::PARAM_INT);
		} else {
		    $stmt->bindValue(":id_dato_parametrico"		  ,$obj->idDato		, PDO::PARAM_INT);
		    $stmt->bindValue(":estado"				      ,$obj->estado		, PDO::PARAM_INT);
		}
        $result = $stmt->execute();
        
        if ($obj->idDato==null) {
            $obj->idDato = $this->conn->lastInsertId();
        }
        
        
        return $obj->idDato;

	}
	

	
}
?>