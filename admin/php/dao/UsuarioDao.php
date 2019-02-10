<?php
include_once $path.'/admin/php/pojos/Usuario.php';
include_once $path.'/admin/php/pojos/Rol.php';
include_once $path.'/admin/php/pojos/Region.php';
include_once $path.'/admin/php/filters/UsuarioFilter.php';

class UsuarioDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	public function login (Usuario $usr) {
		$res = array();
		$sql = "SELECT u.id_usuario ".
					" FROM usuarios u ".
					" WHERE u.email=:email AND u.password=DES_ENCRYPT(:password,'".PRIVATE_KEY."') AND u.estado=1";
					
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

		if (!$stmt) {
			echo $this->conn->error;
			return;
		}

		$stmt->bindValue(':email', $usr->email, PDO::PARAM_STR);
		$stmt->bindValue(':password', $usr->password, PDO::PARAM_STR);

		$sucursal = null;
		$res = $stmt->execute();
		$fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
		
		if ($fila) {
		    $filter      = new UsuarioFilter($fila['id_usuario']);
		    $list         = $this->load($filter);
		    return $list[0];
		}
		
		return null;
	}
	

	public function load(UsuarioFilter $filter) {

		$res = [];
		$filterBy="";

		if ($filter==null) {
		    $filter = new UsuarioFilter();
		}
		if ($filter->idRol!=null) {
			$filterBy=" AND u.id_rol=:id_rol ";
		}
		if ($filter->id>0) {
		    $filterBy=" AND u.id_usuario=:id_usuario ";
		}
		if ($filter->idRolIN!=null) {
		    $filterBy=" AND u.id_rol IN (".$filter->idRolIN.") ";
		}

		if ($filter->email!="") {
		    $filterBy.=" AND u.email=:email ";
		}
		if ($filter->codigoInterno!="") {
		    $filterBy.=" AND u.codigo_interno=:codigo ";
		}
		if ($filter->getEstado()>-1 && $filter->getEstado()!="" && $filter->id=="" && $filter->codigoInterno=="") {
		    $filterBy.=" AND u.estado=:estado ";
		}
		
		
		$sql = "SELECT `id_usuario`, u.id_rol, `codigo_interno`, `email`, u.nombre, `password`, `fecha_creacion`, `fecha_update`, `estado`, ".
                " rol.nombre rol ".
                " FROM usuarios u ".
                " INNER JOIN ROL rol ON rol.id_rol=u.id_rol ".
			    " WHERE 1 ".$filterBy." ORDER BY ".$filter->getOrderBy();
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

		if (!$stmt) {
			echo $this->conn->error;
			return;
		}
		if ($filter->getEstado()>-1 && $filter->getEstado()!="" && $filter->id=="" && $filter->codigoInterno=="") {
		    $stmt->bindValue(':estado', $filter->getEstado(), PDO::PARAM_INT);
		}
		if ($filter->id>0) {
		    $stmt->bindValue(':id_usuario', $filter->id, PDO::PARAM_INT);
		}
		if ($filter->idRol!=null) {
			$stmt->bindValue(':id_rol', $filter->idRol, PDO::PARAM_INT);
		}

		if ($filter->email!="") {
		    $stmt->bindValue(':email', $filter->email, PDO::PARAM_STR);
		}
		if ($filter->codigoInterno!="") {
		    $stmt->bindValue(':codigo', $filter->codigoInterno, PDO::PARAM_INT);
		}

		$res = $stmt->execute();

		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
			$usr = new Usuario();
			$usr->idUsuario 		= $fila['id_usuario'];
			$usr->nombre 			= $fila['nombre'];
			$usr->rol 				= new Rol($fila['id_rol'],$fila['rol']);
			$usr->email 			= $fila['email'];
			$usr->password 			= $fila['password'];
			$usr->fechaCreacion		= GeneralUtils::getDateSql4View($fila['fecha_creacion']);
			$usr->fechaUpdate		= GeneralUtils::getDateSql4View($fila['fecha_update']);
			$usr->estado 			= $fila['estado'];
			$usr->codigoInterno     = $fila['codigo_interno'];
			$list[] = $usr;
		}
		return $list;
    }
	
	public function save(Usuario $usr) {

		$sql = "INSERT INTO `usuarios`(`id_rol`, `id_region`, `email`, `nombre`,".
				" `password`, `fecha_creacion`, `estado`,codigo_interno)".
				" VALUES (:id_rol,:id_region,:email,:nombre,DES_ENCRYPT(:password,'".PRIVATE_KEY."'),CURDATE(),1,(FLOOR(RAND() * 99999999) + 10000000))";

	
        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':id_rol'		,$usr->rol->idRol			, PDO::PARAM_INT);
		$stmt->bindValue(':id_region'	,$usr->region->idRegion	    , PDO::PARAM_INT);
		$stmt->bindValue(':email'		,$usr->email				, PDO::PARAM_STR);
		$stmt->bindValue(':nombre'		,$usr->nombre				, PDO::PARAM_STR);
		$stmt->bindValue(':password'	,$usr->password				, PDO::PARAM_STR);


        $result = $stmt->execute();
	}
	
	public function update(Usuario $usr) {

		$sql = "UPDATE usuarios SET id_rol=:id_rol, email=:email, nombre=:nombre,id_region=:id_region, ".
				" fecha_update=CURDATE(), estado=:estado WHERE id_usuario=:id_usuario";

        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':id_rol'		,$usr->rol->idRol			, PDO::PARAM_INT);
		$stmt->bindValue(':email'		,$usr->email				, PDO::PARAM_STR);
		$stmt->bindValue(':nombre'		,$usr->nombre				, PDO::PARAM_STR);
		$stmt->bindValue(':estado'		,$usr->estado				, PDO::PARAM_INT);
		$stmt->bindValue(':id_region'	,$usr->region->idRegion	    , PDO::PARAM_INT);
		$stmt->bindValue(':id_usuario'	,$usr->idUsuario			, PDO::PARAM_INT);

        $result = $stmt->execute();

	}
	
	public function deleteUsuario($id_usuario) {
		$sql = "UPDATE usuarios SET estado=0,fecha_update=CURDATE() WHERE id_usuario=:id_usuario";

        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':id_usuario'	,$id_usuario		, PDO::PARAM_INT);
        $result = $stmt->execute();

	}
	
	public function changePassword($id_usuario,$password) {
	    $sql = "UPDATE usuarios SET password=DES_ENCRYPT(:password,'".PRIVATE_KEY."'),fecha_update=CURDATE() WHERE id_usuario=:id_usuario";

        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':password'	,$password			, PDO::PARAM_STR);
		$stmt->bindValue(':id_usuario'	,$id_usuario		, PDO::PARAM_INT);
		$result = $stmt->execute();
	}
	
}
?>