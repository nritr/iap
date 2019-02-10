<?php
include_once $path.'/admin/php/pojos/Area.php';
include_once $path.'/admin/php/pojos/whatsapp/Whatsapp.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappCuenta.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappContacto.php';
include_once $path.'/admin/php/pojos/whatsapp/WhatsappContactoTipo.php';
include_once $path.'/admin/php/filters/WhatsappFilter.php';


class WhatsappDao {


	private $conn;
	
	public function __construct() {

        $this->conn =DB_Connect::connect();
	}
	
	public function loadContacts(WhatsappFilter $filter) {
	    
	    $where = "";
	    $whereJoinCuenta = "";
	    if ($filter->telefonoLike!="") {
	        $where.= " AND telefono LIKE :telefono";
	    }
	    if ($filter->textoLike!="") {
	        $where.= " AND (texto LIKE :texto OR wc.nombre LIKE :texto)";
	    }
	    if ($filter->noLeidos==1) {
	        $where.= " AND EXISTS (SELECT lw.idRtaWhatsapp ".
	                   " FROM rtaMensajesWhatsapp lw ".
	                   " INNER JOIN WHATSAPP_CUENTA lwcu ON lwcu.id_whatsapp_cuenta=lw.id_whatsapp_cuenta AND lwcu.id_area IN (SELECT lrra.id_area FROM REL_ROL_AREA lrra WHERE lrra.id_rol=:id_rol) ". 
	                   " WHERE lw.telefono=w.telefono ".
	                   " AND lw.fecha_leido_chat IS NULL ".
	                   " AND fromMe=0)";
	    }
	    if ($filter->idRol>0) {
	        $whereJoinCuenta .= " AND wcu.id_area IN (SELECT lrra.id_area FROM REL_ROL_AREA lrra WHERE lrra.id_rol=:id_rol)";
	    }
	    $sql = "SELECT `telefono`, `nombreWhatsapp` , MAX(w.fecha) fecha, ".
	            "  (SELECT COUNT(lw.idRtaWhatsapp) ".
	                   " FROM rtaMensajesWhatsapp lw ".
	                   " INNER JOIN WHATSAPP_CUENTA lwcu ON lwcu.id_whatsapp_cuenta=lw.id_whatsapp_cuenta AND lwcu.id_area IN (SELECT lrra.id_area FROM REL_ROL_AREA lrra WHERE lrra.id_rol=:id_rol) ".
	                   " WHERE lw.telefono=w.telefono ".
	                   " AND lw.fecha_leido_chat IS NULL ".
	                   " AND fromMe=0 ) no_leidos, ".
	                   " SUBSTRING((SELECT wl.texto FROM rtaMensajesWhatsapp wl WHERE wl.telefono=w.telefono AND wl.idRtaWhatsapp IN (SELECT MAX(wll.idRtaWhatsapp) FROM rtaMensajesWhatsapp wll WHERE wll.telefono=w.telefono)),1,45) last_msg, ".
	                   " wc.nombre,wc.id_whatsapp_contacto, ".
	                   " wct.color,wct.descripcion,wct.id_whatsapp_contacto_tipo ".
                " FROM rtaMensajesWhatsapp w ".
                " LEFT JOIN WHATSAPP_CONTACTO wc ON wc.nro_telefono=w.telefono ".
                " LEFT JOIN WHATSAPP_CONTACTO_TIPO wct ON wct.id_whatsapp_contacto_tipo=wc.id_tipo ".
                " INNER JOIN WHATSAPP_CUENTA wcu ON wcu.id_whatsapp_cuenta=w.id_whatsapp_cuenta ".$whereJoinCuenta.
                " WHERE 1 ".$where.
                " GROUP by w.telefono ".
                " ORDER by ".$filter->getOrderBy().
                " LIMIT 0,100";
	    //die( $sql);
	    $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	    
	    if ($filter->telefonoLike!="") {
	        $stmt->bindValue(':telefono'     , '%'.$filter->telefonoLike.'%'      , PDO::PARAM_STR);
	    }
	    if ($filter->textoLike!="") {
	        $stmt->bindValue(':texto'     , '%'.$filter->textoLike.'%'      , PDO::PARAM_STR);
	    }
	    if ($filter->idRol>0) {
	        $stmt->bindValue(':id_rol'     , $filter->idRol      , PDO::PARAM_INT);
	    }
	    $res = $stmt->execute();
	    $list = [];
	    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
	        
	        $obj = new Whatsapp(null,$fila['telefono'],null,null);
	        $obj->nombreWhatsapp     = $fila['nombreWhatsapp'];
	        $obj->fecha              = GeneralUtils::humanTiming (strtotime($fila['fecha'])) ;
	        $obj->noLeidos           = $fila['no_leidos'];
	        $obj->whatsappContacto   = new WhatsappContacto($fila['id_whatsapp_contacto'],$fila['nombre'],$fila['id_whatsapp_contacto_tipo']);
	        $obj->whatsappContacto->tipo->descripcion  = $fila['descripcion'];
	        $obj->whatsappContacto->tipo->color        = $fila['color'];
	        $obj->lastMsg            = $fila['last_msg'];
	        $list[] = $obj;
	    }
	    
	    return $list;
	}

	public function findMessageEquals(WhatsappFilter $filter) {
	    
	    $where = "";

	    
	    $sql = "SELECT `idRtaWhatsapp`, `id_whatsapp_cuenta`, `telefono`, `nombreWhatsapp`, `texto`, `leido`, `fromMe`, `fecha_envio_api`, `fecha_chat`, `fecha`, `instance`, `dto`, `idChatApi`, `json_response`".
                " FROM rtaMensajesWhatsapp w ".
                " WHERE w.telefono=:telefono ".
                " AND w.leido=-1".
                " AND w.texto=:texto".
                " AND w.fromMe=1 ".
                " HAVING max(w.fecha)=w.fecha";
	    
	    $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	    
        $stmt->bindValue(':telefono'     , $filter->telefono    , PDO::PARAM_INT);
        $stmt->bindValue(':texto'        , $filter->texto       , PDO::PARAM_STR);

	    
	    $res = $stmt->execute();
	    $list = [];
	    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
	        
	        $obj = new Whatsapp($fila['idChatApi'],$fila['telefono'],$fila['texto'],$fila['id_whatsapp_cuenta']);
	        $obj->idRtaWhatsapp      = $fila['idRtaWhatsapp'];
	        $obj->nombreWhatsapp     = $fila['nombreWhatsapp'];
	        $obj->leido              = $fila['leido'];
	        $obj->fromMe             = $fila['fromMe'];
	        $obj->fechaEnvioApi      = $fila['fecha_envio_api'];
	        $obj->fechaChat          = $fila['fecha_chat'];
	        $obj->fecha              = $fila['fecha'];
	        $obj->instance           = $fila['instance'];
	        $obj->dto                = $fila['dto'];
	        $obj->jsonResponse       = $fila['json_response'];
	        
	        $list[] = $obj;
	    }
	    
	    return $list;
	}
	
	public function load(WhatsappFilter $filter) {


		$where = "";
		$whereJoinCuenta = "";
		if ($filter->id>0) {
		    $where.= " AND w.idRtaWhatsapp=:idRtaWhatsapp ";
		} 
		if ($filter->idChatApi!="") {
		    $where.= " AND idChatApi=:idChatApi";
		}
		if ($filter->telefono!="") {
		    $where.= " AND telefono=:telefono";
		}
		if ($filter->idRol>0) {
		    $whereJoinCuenta .= " AND wc.id_area IN (SELECT lrra.id_area FROM REL_ROL_AREA lrra WHERE lrra.id_rol=:id_rol)";
		}

		$sql = "SELECT `idRtaWhatsapp`,telefono,`nombreWhatsapp`,`texto`,`leido`,`fromMe`,`fecha_envio_api`,`fecha_chat`,w.instance,`dto`,`idChatApi`,`json_response`,fecha,w.id_whatsapp_cuenta,fecha_leido_chat,".
                " a.id_area, a.nombre, a.color, ".
                " wc.tel_asignado, wc.cuenta ".
		        " FROM ".
		          " rtaMensajesWhatsapp w ".
		          " INNER JOIN WHATSAPP_CUENTA wc ON wc.id_whatsapp_cuenta=w.id_whatsapp_cuenta ".$whereJoinCuenta.
		          " LEFT JOIN AREA a ON a.id_area=wc.id_area ".
		  		" WHERE 1 ".$where.
		  		//" GROUP by DATE_FORMAT(w.fecha, '%Y%m%d') ".
		  		" ORDER by DATE_FORMAT(w.fecha, '%Y%m%d'),a.id_area,wc.tel_asignado,w.fecha";
		//echo $sql;
        $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
		if ($filter->id>0) {
		    $stmt->bindValue(':idRtaWhatsapp' , $filter->id	          , PDO::PARAM_INT);
		}
		if ($filter->idChatApi!="") {
		    $stmt->bindValue(':idChatApi'     , $filter->idChatApi    , PDO::PARAM_INT);
		}
		if ($filter->telefono!="") {
		    $stmt->bindValue(':telefono'     , $filter->telefono      , PDO::PARAM_INT);
		}
		if ($filter->idRol>0) {
		    $stmt->bindValue(':id_rol'     , $filter->idRol      , PDO::PARAM_INT);
		}
		$res = $stmt->execute();
		$list = [];
		while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		    
		    $obj = new Whatsapp($fila['idChatApi'],$fila['telefono'],$fila['texto'],$fila['id_whatsapp_cuenta']);
			$obj->idRtaWhatsapp      = $fila['idRtaWhatsapp'];
			$obj->nombreWhatsapp     = $fila['nombreWhatsapp'];
			$obj->leido              = $fila['leido'];
			$obj->fromMe             = $fila['fromMe'];
			$obj->fechaEnvioApi      = $fila['fecha_envio_api'];
			$obj->fechaChat          = $fila['fecha_chat'];
			$obj->fecha              = $fila['fecha'];
			$obj->instance           = $fila['instance'];
			$obj->dto                = $fila['dto'];
			$obj->jsonResponse       = $fila['json_response'];
			$obj->fechaLeidoChat     = $fila['fecha_leido_chat'];
			$obj->whatsappCuenta->area->idArea   = $fila['id_area'];
			$obj->whatsappCuenta->area->nombre   = $fila['nombre'];
			$obj->whatsappCuenta->area->color    = $fila['color'];
			$obj->whatsappCuenta->telAsignado    = $fila['tel_asignado'];
			$obj->whatsappCuenta->cuenta         = $fila['cuenta'];
			$list[] = $obj;
		}

		return $list;
    }
	
    
	public function save(Whatsapp $obj) {

	
		$sql = "INSERT INTO rtaMensajesWhatsapp( telefono, nombreWhatsapp, texto, leido, fromMe, fecha_envio_api, fecha_chat,  instance, dto, idChatApi, json_response,id_whatsapp_cuenta ) ".
		          " VALUES (:telefono, :nombreWhatsapp, :texto, :leido, :fromMe, :fecha_envio_api, :fecha_chat,  :instance, :dto, :idChatApi, :json_response,:id_whatsapp_cuenta )";

        $stmt               = $this->conn->prepare($sql);

        $idWhatCu           = (isset($obj->whatsappCuenta->idWhatsappCuenta) ? $obj->whatsappCuenta->idWhatsappCuenta : null);
		$stmt->bindValue(":telefono"		,$obj->telefono             , PDO::PARAM_INT);
		$stmt->bindValue(":nombreWhatsapp"	,$obj->nombreWhatsapp	    , PDO::PARAM_STR); 
		$stmt->bindValue(":texto"		    ,$obj->texto				, PDO::PARAM_STR);
		$stmt->bindValue(":leido"           ,$obj->leido                , PDO::PARAM_INT);
		$stmt->bindValue(":fromMe"          ,$obj->fromMe               , PDO::PARAM_INT);
		$stmt->bindValue(":fecha_envio_api" ,$obj->getFechaEnvioApiSql(), PDO::PARAM_STR);
		$stmt->bindValue(":fecha_chat"      ,$obj->getFechaChatSql()    , PDO::PARAM_STR);
		$stmt->bindValue(":instance"        ,$obj->instance             , PDO::PARAM_INT);
		$stmt->bindValue(":dto"             ,$obj->dto                  , PDO::PARAM_INT);
		$stmt->bindValue(":idChatApi"       ,$obj->idChatApi            , PDO::PARAM_STR);
		$stmt->bindValue(":json_response"   ,$obj->jsonResponse         , PDO::PARAM_STR);
		$stmt->bindValue(":id_whatsapp_cuenta",$idWhatCu                , PDO::PARAM_INT);
		
        $result               = $stmt->execute();
		$id                   = $this->conn->lastInsertId(); 
		$obj->idRtaWhatsapp   = $id;

		return $obj;
	}
	
	public function update(Whatsapp $obj) {
	    $sql = "UPDATE rtaMensajesWhatsapp SET nombreWhatsapp=:nombreWhatsapp,leido=:leido,fecha_envio_api=:fecha_envio_api,instance=:instance,dto=:dto,idChatApi=:idChatApi,json_response=:json_response,fecha=:fecha ".
		  " WHERE idRtaWhatsapp=:idRtaWhatsapp";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":idRtaWhatsapp"   ,$obj->idRtaWhatsapp    	        , PDO::PARAM_INT);
        $stmt->bindValue(":nombreWhatsapp"  ,$obj->nombreWhatsapp          	    , PDO::PARAM_STR);
        $stmt->bindValue(":leido"			,$obj->leido                        , PDO::PARAM_INT);
        $stmt->bindValue(":fecha_envio_api"	,$obj->getFechaEnvioApiSql()        , PDO::PARAM_STR);
        $stmt->bindValue(":fecha"	        ,$obj->fecha                        , PDO::PARAM_STR);
        $stmt->bindValue(":instance"	    ,$obj->instance		                , PDO::PARAM_INT);
        $stmt->bindValue(":dto"	            ,$obj->dto		                    , PDO::PARAM_INT);
        $stmt->bindValue(":idChatApi"	    ,$obj->idChatApi		            , PDO::PARAM_STR);
        $stmt->bindValue(":json_response"	,$obj->jsonResponse		            , PDO::PARAM_STR);

        $result = $stmt->execute();
	}

	public function marcarLeido(int $nro) {
	    $sql = "UPDATE rtaMensajesWhatsapp SET fecha_leido_chat=NOW() ".
	   	    " WHERE telefono=:telefono";
	    
	    $stmt = $this->conn->prepare($sql);
	    $stmt->bindValue(":telefono"   ,$nro 	        , PDO::PARAM_INT);
	    $result = $stmt->execute();
	}

	public function saveContact(WhatsappContacto $obj) {
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
	
	public function loadCuenta($id=null,$instance=null,$idRol=null) {
	    
	    
	    $where = "";
	    if ($id>0) {
	        $where.= " AND c.id_whatsapp_cuenta=:id_whatsapp_cuenta ";
	    }
	    if ($instance>0) {
	        $where.= " AND c.instance=:instance";
	    }
	    if ($idRol>0) {
	        $where.= " AND c.id_area IN (SELECT id_area FROM REL_ROL_AREA rral WHERE rral.id_rol=:id_rol)";
	    }
	    
	    $sql = "SELECT `id_whatsapp_cuenta`, c.id_area,c.cuenta, `instance`, `url`, `token`, `tel_asignado`, `owner`, ".
	   	       " a.nombre ".
	           " FROM WHATSAPP_CUENTA c ".
	           " INNER JOIN AREA a ON a.id_area=c.id_area ".
	           " WHERE 1 ".$where;

	    $stmt = $this->conn->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	    
	    if ($id>0) {
	        $stmt->bindValue(':id_whatsapp_cuenta' , $id	          , PDO::PARAM_INT);
	    }
	    if ($instance>0) {
	        $stmt->bindValue(':instance'     , $instance    , PDO::PARAM_INT);
	    }
	    if ($idRol>0) {
	        $stmt->bindValue(':id_rol'     , $idRol    , PDO::PARAM_INT);
	    }
	    $res = $stmt->execute();
	    $list = [];
	    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
	        
	        $obj = new WhatsappCuenta($fila['id_whatsapp_cuenta'],$fila['cuenta'],$fila['id_area']);
	        $obj->owner                = $fila['owner'];
	        $obj->telAsignado          = $fila['tel_asignado'];
	        $obj->token                = $fila['token'];
	        $obj->url                  = $fila['url'];
	        $obj->instance             = $fila['instance'];
	        $obj->area->nombre         = $fila['nombre'];
	        $list[] = $obj;
	    }
	    
	    return $list;
	}
}
?>