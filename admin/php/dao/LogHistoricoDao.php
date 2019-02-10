<?php
include_once $path.'/admin/php/pojos/LogHistorico.php';
include_once $path.'/admin/php/pojos/Prospecto.php';
include_once $path.'/admin/php/filters/LogHistoricoFilter.php';


class LogHistoricoDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	

	public function load(LogHistoricoFilter $filter) {


		$where = "";
		if ($filter->id>0) {
		    $where.= " AND lh.id=:id ";
		}
		if ($filter->idTipo>0) {
		    $where.= " AND lh.id_tipo=:id_tipo ";
		}
		if ($filter->idProspecto>0) {
		    $where.= " AND lh.id_prospecto=:id_prospecto ";
		}
		
		$sql = "SELECT lh.id, lh.id_tipo, lh.id_prospecto, lh.id_usuario, lh.fecha, ".
		  		  " p.id_prospecto,p.nombre nom_pros, ".
		  		  " dp.nombre nom_tipo, ".
		  		  " u.nombre nom_usr ".
		          " FROM LOG_HISTORICO lh ".
		          " LEFT JOIN PROSPECTO p ON p.id_prospecto=lh.id_prospecto ".
                  " INNER JOIN datos_parametricos dp ON dp.id_dato_parametrico=lh.id_tipo ".
                  " INNER JOIN usuarios u ON u.id_usuario=lh.id_usuario ".
                  " WHERE 1 ".$where.
		  		  " ORDER BY ".$filter->getOrderBy();
		
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));


		if ($filter->id>0) {
		    $stmt->bindValue(':id', $filter->id	, PDO::PARAM_INT);
		}
		if ($filter->idTipo>0) {
		    $stmt->bindValue(':id_tipo', $filter->idTipo	, PDO::PARAM_INT);
		}
		if ($filter->idProspecto>0) {
		    $stmt->bindValue(':id_prospecto', $filter->idProspecto	, PDO::PARAM_INT);
		}
		
		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    $obj                          = new LogHistorico($fila['id'],$fila['id_tipo'],$fila['id_usuario']);
			$obj->prospecto->idProspecto  = $fila['id_prospecto'];
			$obj->prospecto->nombre       = $fila['nom_pros'];
			$obj->tipo->nombre            = $fila['nom_tipo'];
			$obj->usuario->nombre         = $fila['nom_usr'];
			$obj->fecha                   = $fila['fecha'];
			$obj->fechaScreen             = GeneralUtils::getDateSql4View($fila['fecha']);
			$list[] = $obj;
		}

		return $list;
    }
	
    
	public function save(LogHistorico $obj) {

	
		$sql = "INSERT INTO LOG_HISTORICO (id_tipo, id_prospecto, id_usuario) VALUES ".
		          " (:id_tipo, :id_prospecto, :id_usuario)";

        $stmt = $this->conn->prepare($sql);

		$stmt->bindValue(":id_tipo"		    ,$obj->tipo->idDato	             , PDO::PARAM_INT);
		$stmt->bindValue(":id_prospecto"	,$obj->prospecto->getId()		 , PDO::PARAM_INT); 
		$stmt->bindValue(":id_usuario"		,$obj->usuario->idUsuario	     , PDO::PARAM_INT);
		
        $result = $stmt->execute();

		$id = $this->conn->lastInsertId(); 
		$obj->id = $id;

		return $obj;
	}
	
	
}
?>