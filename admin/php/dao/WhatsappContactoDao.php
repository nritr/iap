<?php
include_once $path.'/admin/php/pojos/whatsapp/Whatsapp.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappCuenta.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappContacto.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappContactoTipo.php';
include_once $path.'/admin/php/filters/WhatsappContactoFilter.php';


class WhatsappContactoDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	public function load(WhatsappContactoFilter $filter) {


		$where = "";
		if ($filter->id>0) {
		    $where.= " AND wc.id_whatsapp_contacto=:id_whatsapp_contacto ";
		} 

		if ($filter->nombreLike!="") {
		    $where.= " AND wc.nombre LIKE :nombre";
		}

		$sql = "SELECT `id_whatsapp_contacto`, `id_tipo`, `id_region`, `nro_telefono`, `nombre`, `fecha` ".
		        " FROM WHATSAPP_CONTACTO wc ".
		  		" WHERE 1 ".$where.
		  		" ORDER BY ".$filter->getOrderBy();
		
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
		if ($filter->id>0) {
		    $stmt->bindValue(':id_whatsapp_contacto' , $filter->id	          , PDO::PARAM_INT);
		}
		if ($filter->nombreLike!="") {
		    $stmt->bindValue(':nombre'     , "%".$filter->nombreLike."%"    , PDO::PARAM_STR);
		}
		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    
		    $obj          = new WhatsappContacto($fila['id_whatsapp_contacto'],$fila['nombre'],$fila['id_tipo'],$fila['id_region'],$fila['nro_telefono']);
			$obj->fecha   = $fila['fecha'];
			
			$list[] = $obj;
		}

		return $list;
    }
	
    
	
	public function update(Whatsapp $obj) {
	    $sql = "UPDATE rtaMensajesWhatsapp SET nombreWhatsapp=:nombreWhatsapp,leido=:leido,fecha_envio_api=:fecha_envio_api,instance=:instance,dto=:dto,idChatApi=:idChatApi,json_response=:json_response ".
		  " WHERE idRtaWhatsapp=:idRtaWhatsapp";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":idRtaWhatsapp"   ,$obj->idRtaWhatsapp    	        , PDO::PARAM_INT);
        $stmt->bindValue(":nombreWhatsapp"  ,$obj->nombreWhatsapp          	    , PDO::PARAM_STR);
        $stmt->bindValue(":leido"			,$obj->leido                        , PDO::PARAM_INT);
        $stmt->bindValue(":fecha_envio_api"	,$obj->getFechaEnvioApiSql()        , PDO::PARAM_STR);
        $stmt->bindValue(":instance"	    ,$obj->instance		                , PDO::PARAM_INT);
        $stmt->bindValue(":dto"	            ,$obj->dto		                    , PDO::PARAM_INT);
        $stmt->bindValue(":idChatApi"	    ,$obj->idChatApi		            , PDO::PARAM_STR);
        $stmt->bindValue(":json_response"	,$obj->jsonResponse		            , PDO::PARAM_STR);

        $result = $stmt->execute();
	}

	public function save(WhatsappContacto $obj) {
	    $sql = "INSERT INTO WHATSAPP_CONTACTO (id_tipo, id_region, nro_telefono, nombre) ".
	   	    " VALUES (:id_tipo, :id_region, :nro_telefono, :nombre)";
	    
	    $stmt               = $this->conn->prepare($sql);
	    
	    $stmt->bindValue(":id_tipo"		      ,$obj->tipo->idWhatsappContactoTipo     , PDO::PARAM_INT);
	    $stmt->bindValue(":id_region"	      ,$obj->region->getId()	              , PDO::PARAM_INT);
	    $stmt->bindValue(":nro_telefono"	  ,$obj->nroTelefono				      , PDO::PARAM_INT);
	    $stmt->bindValue(":nombre"            ,$obj->nombre                           , PDO::PARAM_STR);
	    
	    $result                    = $stmt->execute();
	    $id                        = $this->conn->lastInsertId();
	    $obj->idWhatsappContacto   = $id;
	    
	    return $obj;
	}
}
?>