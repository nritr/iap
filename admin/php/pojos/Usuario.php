<?php

class Usuario {

    public $idUsuario;
	public $codigoInterno;
	public $nombre;
	/**
	 * @var Rol
	 */
	public $rol;
	/**
	 * @var Region
	 */
	public $region;
	public $email;
	public $password;
	public $fechaCreacion;
	public $fechaUpdate;
	public $estado;


	public function __construct($id=null,$nombre=null,$id_region=null,$id_rol=null) {
		$this->idUsuario  = $id;
		$this->nombre     = $nombre;
		$this->rol        = new Rol($id_rol);
		$this->region     = new Region($id_region);
	}

	/*public function isAvatar() {
		return ($this->pathAvatarRand && $this->pathAvatarExtension ? 1 : 0);
	}*/
	
	/*public function getAvatar() {
		if (!$this->isAvatar()) {
			return GeneralConf::$avatar_anonima_usuario;
		} else {
			return GeneralConf::$UploadDirectoryWeb . "thumb_".$this->pathAvatarRand.$this->pathAvatarExtension;
		}
	}
	public function getAvatarBig() {
		if (!$this->isAvatar()) {
			return GeneralConf::$foto_anonima_usuario;
		} else {
			return GeneralConf::$UploadDirectoryWeb . "thumb_".$this->pathAvatarRand.$this->pathAvatarExtension;
		}
	}*/

}
?>