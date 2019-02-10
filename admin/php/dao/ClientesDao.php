<?php
include_once $path.'/admin/php/pojos/Cliente.php';
include_once $path.'/admin/php/pojos/DatoParametrico.php';
include_once $path.'/admin/php/filters/ClienteFilter.php';


class ClienteDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	

	public function load(ClienteFilter $filter) {

		$userService		= new UsuarioService();
		$usr 				= $userService->getUserLogued();

		$where = "";
		if ($filter->id>0) {
		    $where.= " AND c.id_cliente=:id_cliente ";
		}
		if ($filter->nombre!="") {
		    $where.= " AND c.nombre LIKE :nombre ";
		}
		
		$sql = "SELECT `id_cliente`, c.`id_sucursal`, `codigo`, c.`nombre`, `cuit`, `telefono`, c.`direccion`, `cp`, observacion,".
		  		" `fecha_create`, c.`estado` ,s.nombre suc, c.localidad,c.nombre_contacto,c.email,c.celular,id_lista_precios ".//,dp.nombre lisa_precio
		  		" FROM clientes c ".
		  		" INNER JOIN sucursales s ON c.id_sucursal=s.id_sucursal ".
		  		//" INNER JOIN datos_parametricos dp ON c.id_lista_precios=dp.id_dato_parametrico ".
		  		" WHERE c.estado=:estado AND c.id_sucursal=:id_sucursal ".$where.
		  		" ORDER BY ".$filter->getOrderBy();
		
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));


		$stmt->bindValue(':estado', 		$filter->getEstado()		, PDO::PARAM_INT);
		$stmt->bindValue(':id_sucursal', 	$usr->sucursal->idSucursal	, PDO::PARAM_INT);
		if ($filter->id>0) {
		    $stmt->bindValue(':id_cliente', $filter->id	, PDO::PARAM_INT);
		}
		if ($filter->nombre!="") {
		    $stmt->bindValue(':nombre', '%'.$filter->nombre.'%'	, PDO::PARAM_STR);
		}

		$res = $stmt->execute();
		$list = array();
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    
		    $obj = new Cliente($fila['id_cliente'],$fila['codigo']);
			$obj->nombre 		= $fila['nombre'];
			$obj->nombreContacto= $fila['nombre_contacto'];
			$obj->email         = $fila['email'];
			$obj->cuit 		    = $fila['cuit'];
			$obj->telefono 		= $fila['telefono'];
			$obj->celular 		= $fila['celular'];
			$obj->direccion 	= $fila['direccion'];
			$obj->cp 		    = $fila['cp'];
			$obj->localidad	    = $fila['localidad'];
			$obj->observacion   = $fila['observacion'];
			$obj->estado 		= $fila['estado'];
			$obj->fechaCreate   = $fila['fecha_create'];
			$list[] = $obj;
		}

		return $list;
    }
	
    public function getLastCode() {
        $sql = "SELECT MAX(codigo) max ".
            " FROM clientes c ";
        
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $res = $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
        return $fila['max'];
    }
    
	public function save(Cliente $obj) {

		$userService		= new UsuarioService();
		$usr 				= $userService->getUserLogued();
		
		$sql = "INSERT INTO `clientes`( `id_sucursal`, `codigo`, `nombre`, `cuit`, ".
		          " `nombre_contacto`, `email`, `telefono`, `celular`, `direccion`, `cp`,localidad,id_lista_precios,observacion) ".
		          " VALUES (:id_sucursal,:codigo,:nombre,:cuit,:nombre_contacto,:email,:telefono,:celular,:direccion,:cp,:localidad,:id_lista_precios,:observacion)";

        $stmt = $this->conn->prepare($sql);

		$stmt->bindValue(":id_sucursal"		,$usr->sucursal->idSucursal	, PDO::PARAM_INT);
		$stmt->bindValue(":nombre"			,$obj->nombre			    , PDO::PARAM_STR); 
		$stmt->bindValue(":codigo"		    ,$obj->codigo				, PDO::PARAM_INT);
		$stmt->bindValue(":id_lista_precios",null	, PDO::PARAM_INT);
		$stmt->bindValue(":cuit"		    ,($obj->cuit=="" ? null : $obj->cuit)  , PDO::PARAM_STR); 
		$stmt->bindValue(":nombre_contacto"	,$obj->nombreContacto		, PDO::PARAM_STR); 
		$stmt->bindValue(":direccion"		,$obj->direccion			, PDO::PARAM_STR); 
		$stmt->bindValue(":email"		    ,$obj->email				, PDO::PARAM_STR); 
		$stmt->bindValue(":cp"				,$obj->cp					, PDO::PARAM_STR); 
		$stmt->bindValue(":telefono"		,$obj->telefono				, PDO::PARAM_STR); 
		$stmt->bindValue(":celular"			,$obj->celular				, PDO::PARAM_STR); 
		$stmt->bindValue(":localidad"	    ,$obj->localidad			, PDO::PARAM_STR);
		$stmt->bindValue(":observacion"	    ,$obj->observacion			, PDO::PARAM_STR);
		
		
		
        $result = $stmt->execute();
		//LogUtils::saveLog($stmt->error);
		$id = $this->conn->lastInsertId(); 
		$obj->idCliente = $id;

		return $obj;
	}
	
	public function update(Cliente $obj) {
		$sql = "UPDATE clientes SET nombre=:nombre,`cuit`=:cuit,`nombre_contacto`=:nombre_contacto,`email`=:email,observacion=:observacion,".
		  " `telefono`=:telefono,`celular`=:celular,`direccion`=:direccion,`localidad`=:localidad,`cp`=:cp,id_lista_precios=:id_lista_precios,`estado`=:estado".
		  " WHERE id_cliente=:id_cliente";

        $stmt = $this->conn->prepare($sql);
        $idlistaprecio = (isset($obj->listaPrecios) ? $obj->listaPrecios->idDato : null);
        
        $stmt->bindValue(":id_cliente"		,$obj->idCliente	        , PDO::PARAM_INT);
        $stmt->bindValue(":id_lista_precios",$idlistaprecio	            , PDO::PARAM_INT);
        $stmt->bindValue(":nombre"			,$obj->nombre			    , PDO::PARAM_STR);
        $stmt->bindValue(":cuit"		    ,($obj->cuit=="" ? null : $obj->cuit)  , PDO::PARAM_STR);
        $stmt->bindValue(":nombre_contacto"	,$obj->nombreContacto		, PDO::PARAM_STR);
        $stmt->bindValue(":direccion"		,$obj->direccion			, PDO::PARAM_STR);
        $stmt->bindValue(":email"		    ,$obj->email				, PDO::PARAM_STR);
        $stmt->bindValue(":cp"				,$obj->cp					, PDO::PARAM_STR);
        $stmt->bindValue(":telefono"		,$obj->telefono				, PDO::PARAM_STR);
        $stmt->bindValue(":celular"			,$obj->celular				, PDO::PARAM_STR);
        $stmt->bindValue(":localidad"	    ,$obj->localidad			, PDO::PARAM_STR);
        $stmt->bindValue(":estado"	        ,$obj->estado			    , PDO::PARAM_STR);
        $stmt->bindValue(":observacion"	    ,$obj->observacion			, PDO::PARAM_STR);
        
        $result = $stmt->execute();
        return $obj;
	}
	
	public function delete($id) {
		$sql = "UPDATE clientes SET estado=0 WHERE id_cliente=:id_cliente";

        $stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':id_cliente', $id, PDO::PARAM_INT);

		
        $result = $stmt->execute();

	}
	

	
}
?>