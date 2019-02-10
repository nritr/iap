<?php
include_once $path.'/admin/php/pojos/register/Companion.php';



class CompanionDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	

	public function load(DatoFilter $filter) {


		$where = "";
		if ($filter->idDato>0) {
		    $where.= " AND c.ID_DATOS=".$filter->idDato;
		}

		$sql = "SELECT `ID`, `ID_DATOS`, `ACCOMPANYING_FIRST_NAME`, `ACCOMPANYING_LAST_NAME`, `ACCOMPANYING_BADGE_NAME` ".
	               " FROM COMPANION c ".
	               " WHERE 1 ".$where;
		
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));


		
		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    $obj                          = new Companion();
		    //TODO: FALTA LA IMAGEN DE LOS ACOMPAÑANTES
			$obj->accompanyingBadgeName   = $fila['ACCOMPANYING_BADGE_NAME'];
			$obj->accompanyingFirstName    = $fila['ACCOMPANYING_FIRST_NAME'];
			$obj->accompanyingLastName    = $fila['ACCOMPANYING_LAST_NAME'];
			$obj->id                      = $fila['ID'];
			$obj->days                    = $this->loadDays($fila['ID']);
			$list[]                       = $obj;
		}

		return $list;
    }
	
    public function loadDays($id_companion=null,$id_ticket=null) {

        $where="";
        if ($id_companion>0) {
            $where.= " AND COMPANION_ID=".$id_companion;
        }
        if ($id_ticket>0) {
            $where.= " AND TICKET_ID=".$id_ticket;
        }
        $sql = "SELECT `ID`, `TICKET_ID`, `COMPANION_ID`, DATE_FORMAT(DAY,'%d/%m/%Y') AS DAY  ".
                " FROM `DAYS` WHERE 1 ".$where;
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        
        
        $res = $stmt->execute();
        $list = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $obj                          = new Day();
            //TODO: FALTA LA IMAGEN DE LOS ACOMPAÑANTES
            $obj->companionId           = $fila['COMPANION_ID'];
            $obj->day                   = $fila['DAY'];
            $obj->ticketId              = $fila['TICKET_ID'];
            $obj->id                    = $fila['ID'];
            $list[] = $obj;
        }
        
        return $list;
    }
    /*
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
	}*/
	
	
}
?>