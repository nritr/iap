<?php
include_once $path.'/admin/php/dao/ProspectoDao.php';
include_once $path."/admin/php/services/LogHistoricoService.php";
include_once $path."/admin/php/services/ComboService.php";

class ProspectoService {

	private $dao;
	
	public function __construct() {
	    $this->dao = new ProspectoDao();
	}

	
	public function getById(int $id) {
	    $filter = new ProspectoFilter($id);
	    $list = $this->dao->load($filter);
	    if (count($list)>0) {
	       return $list[0];
	    }
	    return null;
	}
	
	public function load(ProspectoFilter $filter) {
	    return $this->dao->load($filter);
	}
	

	public function saveUpdate(Prospecto $obj,int $tipoLog=null) {
	    
	    if ($obj->idProspecto>0) {
	        $this->dao->update($obj);
	        if ($tipoLog>0) {
	            $usrService    = new UsuarioService();
	            $logService    = new LogHistoricoService();
	            $usr           = $usrService->getUserLogued();
	            $log           = new LogHistorico(null,$tipoLog,$usr->idUsuario);
	            $log->prospecto->idProspecto = $obj->idProspecto;
	            $logService->save($log);
	        }
		} else {
		    //return $this->dao->save($obj);
		}
	}
	public function normalizar(Prospecto $obj) {
	    
	    if ($obj->prospectoNormalizado->getId()=="") {
	        return $this->dao->normalizar($obj);
	    } else {
	        //return $this->dao->save($obj);
	    }
	}
	public function topemonto($salario,$sexo,$dni_dom,$edad,$lazo,$bnemp,$id_vivienda,$id_trabajo) {
	    $config            = GeneralUtils::getConfigIni();
	    $puntaje           = 0;
	    $montobase         = 0;
	    $montodirecto      = 0;
	    $topemonto         = 0;
	    $multiplicador     = 0;
	    $topebase          = 0;
	    $ret               = [];
	    $comboService      = new ComboService();
	    $filter            = new TipoTrabajoFilter();
	    $filter->id        = $id_trabajo;
	    $trabajo           = $comboService->loadTipoTrabajo($filter)[0];
	    
	    if ($trabajo->denegarAuto==1) {
	        $ret['msg']    = "Trabajo no válido. Denegar por requisitos";
	        $ret['result'] = -1;
	        return $ret;
	    }
	    if ($bnemp==TipoIngresoTope::$ID_BLANCO && $salario<$config['topemonto_min_ing_blanco']) {
	        $ret['msg']    = "Requisitos insuficientes. Denegar";
	        $ret['result'] = -1;
	        return $ret;
	    }
	    if ($bnemp==TipoIngresoTope::$ID_NEGRO && $salario<$config['topemonto_min_ing_negro']) {
	        $ret['msg']    = "Requisitos insuficientes. Denegar";
	        $ret['result'] = -1;
	        return $ret;
	    }
	    
	    if ($sexo==SEXO_FEMENINO) {
	        $puntaje = $puntaje + 0.5;
	        if ($edad >= 21 && $edad <= 25) {
	            $puntaje = $puntaje - 1;
	        } else if ($edad >= 26 && $edad <= 30) {
	            $puntaje = $puntaje - 0.5;
	        } else if ($edad >= 31 && $edad <= 34) {
	            //no se modifia puntaje
	        } else if ($edad>=35) {
	            $puntaje = $puntaje + 1;
	        }
	    } else {
	        if ($edad >= 21 && $edad <= 26) {
	            $puntaje = $puntaje - 1;
	        } else if ($edad >= 27 && $edad <= 34) {
	            $puntaje = $puntaje - 0.5;
	        } else if ($edad >= 35 && $edad <= 39) {
	            //no se modficia
	        } else if ($edad >= 40) {
	            $puntaje = $puntaje + 1;
	        }
	    }
	    
	    if ($dni_dom==DNI_EN_DOM) {
	        //no accion
	    } else {
	        $puntaje = $puntaje - 0.5;
	    }
	    
	    if ($bnemp==TipoIngresoTope::$ID_BLANCO) {
	        $puntaje = $puntaje + 0.5;
	    }
	    
	    if ($lazo == 1) {
	        $puntaje = $puntaje - 0.5;
	    } else if ($lazo == 2) {
	        $puntaje = $puntaje;//no hace nada
	    } else if ($lazo == 3) {
	        $puntaje = $puntaje + 0.5;
	    } else if ($lazo == 4) {
	        $puntaje = $puntaje + 1.5;
	    }
	    
	    if ($id_vivienda == DatoParametrico::$VIVIENDA_ALQUILA) {
	        $puntaje = $puntaje - 1.5;
	    } else {
	        $puntaje = $puntaje + 0.5;
	    }
	    /*-- DEFINIR MONTO DIRECTO Y BASE --*/
	    if ($bnemp == TipoIngresoTope::$ID_NEGRO || $bnemp == TipoIngresoTope::$ID_MONO_C1 || $bnemp == TipoIngresoTope::$ID_EMPRENDEDOR_C1) {
	        $montobase = 4000;
	        $topebase  = 4000;
	    }
	    
	    if ($bnemp == TipoIngresoTope::$ID_BLANCO) {
	        $topebase  = 9000;
	        
	        if ($id_vivienda == DatoParametrico::$VIVIENDA_ALQUILA) {
	            $multiplicador = 0.2;
	        } else {
	            if ($puntaje >= 2.5) {
	                $multiplicador = 0.35;
	            } else if ($puntaje < 2.5 && $puntaje > 1.5) {
	                $multiplicador = 0.3;
	            } else if ($puntaje <= 1.5) {
	                $multiplicador = 0.25;
	            }
	        }
	        $montobase = $salario * $multiplicador;
	        $montobase = $this->roundMontoBase($montobase);
	    } else if ($bnemp == TipoIngresoTope::$ID_MON_C2_PROF) {
	        $montobase     = 6000;
	        $topebase      = 7000;
	        if ($puntaje >= 1.5) {
	           $montobase = $montobase + 1000;
	        } else if ($puntaje <= -1.5) {
	            $montobase = $montobase - 1000;
	        }
	    } else if ($bnemp == TipoIngresoTope::$ID_COM_C1) {
	        $montobase = 5000;
	        $topebase  = 5000;
	        if ($puntaje <= -1.5) {
	            $montobase = $montobase - 1000;
	        }
	    } else if ($bnemp == TipoIngresoTope::$ID_EMPREND_C2) {
	        $topebase = 5000;
	        if ($id_vivienda==DatoParametrico::$VIVIENDA_ALQUILA) {
	           $montobase = 4000;
	        } else {
	           $montobase = 5000;
	        }
	    } else if ($bnemp == TipoIngresoTope::$ID_COM_C2) {
	        $topebase = 12000;
	        
	        if ($id_vivienda == DatoParametrico::$VIVIENDA_ALQUILA) {
	           $montobase = 8000;
	        } else {
	           $montobase = 12000;
	        }
	    }
	    
	    if ($montobase >= $topebase) {
	       $topemonto = $topebase;
	    } else if ($montobase <= 4000) {
	       $topemonto = 4000;
	    } else {
	        $topemonto = $topemonto + $montobase;
	    }
	    return $topemonto;
	}
	
	private function roundMontoBase($monto) {
	    $res = substr($monto, strlen($monto)-3,3);
	    $res1 = substr($monto, 0,strlen($monto)-3);
	    if ($res<=600) {
	        return $res1."000";
	    } 
	    return ($res1+1)."000";
	}
}
?>