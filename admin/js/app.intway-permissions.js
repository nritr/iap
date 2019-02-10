var rolMaganer;
function RolMaganer(rol) {
	this.rol 			= rol;
	
	this.isRolAdmin = function () {
		return (this.rol==1);
	}
	this.isRolVentas = function () {
		return (this.rol==2);
	}/*
	this.isRolOperario = function () {
		return (this.rol==3);
	}*/
	
	this.executePermission = function() {
		$("[hidden-on-init]").hide();
		
		if (this.isRolAdmin()) {
			$("[show-admin]").show()
		}
		if (this.isRolVentas()) {
			$("[show-ventas]").show()
		}

		/*if (this.isRolSuper()) {
			$("[hidden-super]").hide()
		}*/
	}
}