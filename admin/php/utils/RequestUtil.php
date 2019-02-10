<?php

class RequestUtil {

    private $arrayValues;
    public function __construct($array=null) {
        $this->arrayValues = $array;
    }
    
	public function getPost($post,$default=null) {
		if (isset($_POST[$post])) {
			if (is_array($_POST[$post])) {
				$result = array();
				$req = $_POST[$post];
				foreach($req as $key => $value) {
					$result[] = GeneralUtils::replaceSpecialChar($value);
				}
				return $result;
			}
			return GeneralUtils::replaceSpecialChar($_POST[$post]);
		} else {
		    return $default;
		}
	}
	
	public function getRequest($req) {
		if (isset($_REQUEST[$req])) {
			if (is_array($_REQUEST[$req])) {
				$result = array();
				$req = $_REQUEST[$req];
				foreach($req as $key => $value) {
					$result[] = GeneralUtils::replaceSpecialChar($value);
				}
				return $result;
			}
			return GeneralUtils::replaceSpecialChar($_REQUEST[$req]);
		} else {
			return null;
		}
	}
	
	public function getRequestOrCookie($req,$screen="",$cleanIfPost=false) {
	    $val = "";
	    if ($_SERVER['REQUEST_METHOD']=='GET') {
	        $val = isset($_GET[$req]) ? $_GET[$req] : null;
	    } else {
	        $val = isset($_POST[$req]) ? $_POST[$req] : null;
	        if ($cleanIfPost && $val=="") {
	            $this->setCookie($req."_".$screen, null);
	        }
	    }
	    if ($val!="") {
	        if (is_array($val)) {
	            $result = array();
	            $req = $val;
	            foreach($req as $key => $value) {
	                $result[] = GeneralUtils::replaceSpecialChar($value);
	            }
	            return $result;
	        }
	        $this->setCookie($req."_".$screen, $val);
	        return GeneralUtils::replaceSpecialChar($val);
	    } else {
	        if (!($cleanIfPost==true && $_SERVER['REQUEST_METHOD']=="POST")) {
	           $tmp = $this->getCookie($req."_".$screen);
	        }
	        if ($tmp!="") {
	            return $tmp;
	        }
	        return null;
	    }
	}
	
	public function getGet($get,$default = null) {
		if (isset($_GET[$get])) {
			if (is_array($_GET[$get])) {
				$result = array();
				$req = $_GET[$get];
				foreach($req as $key => $value) {
					$result[] = GeneralUtils::replaceSpecialChar($value);
				}
				return $result;
			}
			return GeneralUtils::replaceSpecialChar($_GET[$get]);
		} else {
			return $default;
		}
	}
	
	public function getCookie($var,$default=null) {
		if (isset($_COOKIE[$var])) {
			if (is_array($_COOKIE[$var])) {
				$result = array();
				$req = $_COOKIE[$var];
				foreach($req as $key => $value) {
					$result[] = ($value);
				}
				return $result;
			}
			return ($_COOKIE[$var]);
		} else {
			return $default;
		}
	}
	
	public function getSession($var,$default = null) {
		if (isset($_SESSION[$var])) {
			return $_SESSION[$var];
		} else {
			return $default;
		}
	}
	
	public function setSession($var,$value) {
		$_SESSION[$var] = $value;
	}
	
	public function unsetSessionVar($var) {
	    unset($_SESSION[$var]);
	}
	
	public function setCookie($var,$value) {
	    setcookie($var, $value, time() + (86400 * 30), "/");
	}
	
	public function getArrayValue($param) {
	    if ($this->arrayValues=="") {
	        return null;
	    }
	    return (isset($this->arrayValues[$param]) ? $this->arrayValues[$param] : null);
	}
}
?>