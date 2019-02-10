<?php
/*
@autor: Ignacio Sclar
@fecha: Mayo del 2013
@version: 1.1
*/
class SkinManager {

	var $htmlFile = "";
	var $content = "";
	var $loops = array();
	var $vars = array();
	var $ifzone = array();
	public function __construct($file) {
		$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
		$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
		$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);
		$this->htmlFile = $path . $file;
	}
	
	private function readFile() {
		if(file_exists($this->htmlFile) && is_file($this->htmlFile)) {
			$this->content = file_get_contents($this->htmlFile);
		} else {
			die('Error: The file '.$this->htmlFile.' does not exist!');
		}
	}
	
	public function addLoop($loop) {
		array_push ($this->loops,$loop);
	}
	
	public function addVariable($name,$val) {
		$this->vars[$name]=$val;
	}
	
	public function addIfZone($name,$val) {
		$this->ifzone[$name]=$val;
	}
	
	public function getSkin() {
		$this->readFile();
		$this->process();
		return $this->content;
	}
	
	private function process() {
		foreach ($this->vars as $key => $val) {
			$this->content = str_replace('$'.$key.'$',$val,$this->content);
		}
		foreach ($this->ifzone as $key => $val) {
			$this->content = $this->replaceZone($key,$this->content,($val == 1 ? $this->getContentZone($key,$this->content) : ''));
		}
		foreach ($this->loops as $value) {
			$content = $this->getContentZone($value->getName(),$this->content);
			$loop_result = $this->processLoop($value,$content);
			$this->content = $this->replaceZone($value->getName(),$this->content,$loop_result);
		}
	}
	
	private function getContentZone($name,$content) {
		$ini = strrpos($content, '$INI_'.$name.'$')+strlen('$INI_'.$name.'$');
		$len = strrpos($content, '$END_'.$name.'$') - strrpos($content, '$INI_'.$name.'$')-strlen('$INI_'.$name.'$')-1;
		if ($ini>0 && $len>0) {
			return substr ( $content ,$ini,$len);
		}
		return "";
	}
	
	private function replaceZone($name,$content,$replace) {
		$ini = strrpos($content, '$INI_'.$name.'$');
		$len = strrpos($content, '$END_'.$name.'$') - strrpos($content, '$INI_'.$name.'$') + strlen('$END_'.$name.'$');
		if ($ini>0 && $len>0) {
			return substr_replace($content,$replace,$ini,$len);
		}
		return $content;
	}
	
	private function processLoop($loop,$content) {
		$result = "";
		for ($i=0;$i<$loop->getCount();$i++) {
			$local_content = $content;
			$arr = $loop->getItemData($i);
			foreach ($arr as $key => $val) {
				$local_content = str_replace('$'.$key.'$',$val,$local_content);
			}
			$arr = $loop->getItemIfZones($i);
			foreach ($arr as $key => $val) {
				$local_content = $this->replaceZone($key,$local_content,($val == 1 ? $this->getContentZone($key,$local_content) : ''));
			}
			$arr = $loop->getItemSubLoops($i);
			foreach ($arr as $key => $value) {
				$contentSubLoop = $this->getContentZone($value->getName(),$local_content);
				$loop_result = $this->processLoop($value,$contentSubLoop);
				$local_content = $this->replaceZone($value->getName(),$local_content,$loop_result);
			}
			$result .= $local_content;
		}
		return $result;
	}
}

class SkinLoop {
	var $name = "";
	var $list = array();
	var $cols = array();
	var $if_zone = array();
	var $sub_loop = array();
	const IF_ZONE = 'ifzone';
	const SUB_LOOP = 'subloop';
	
	public function getName() {
		return $this->name;
	}
	public function __construct($name) {
		$this->name=$name;
	}
	
	public function closeRow() {
		if (sizeof($this->cols)>0) {
			array_push ($this->list,$this->cols);
		}
		$this->cols = array();
	}
	
	public function addData($var,$value) {
		$this->cols[$var]=$value;
	}
	
	public function addIfZone($name,$value) {
		if (!isset($this->cols[self::IF_ZONE])) {
			$this->cols[self::IF_ZONE] = array();
		}
		$this->cols[self::IF_ZONE][$name]=$value;
	}
	public function addSubLoop($subloop) {
		if (!isset($this->cols[self::SUB_LOOP])) {
			$this->cols[self::SUB_LOOP] = array();
		}
		array_push ($this->cols[self::SUB_LOOP],$subloop);
	}
	
	public function getCount() {
		return sizeof($this->list);
	}
	
	public function getItemData($ind) {
		$res = array();
		$keys = array_keys($this->list[$ind]);
		foreach ($keys as $key) {
			if ($key!=self::IF_ZONE && $key!=self::SUB_LOOP) {
				$res[$key] = $this->list[$ind][$key];
			}
		}
		return $res;
	}
	
	public function getItemIfZones($ind) {
		$res = array();
		$keys = array_keys($this->list[$ind]);
		foreach ($keys as $key) {
			if ($key==self::IF_ZONE) {
				foreach ($this->list[$ind][$key] as $key1 => $val1) {
					$res[$key1] = $val1;
				}
				
			}
		}
		return $res;
	}
	
	public function getItemSubLoops($ind) {
		$res = array();
		$keys = array_keys($this->list[$ind]);
		foreach ($keys as $key) {
			if ($key==self::SUB_LOOP) {
				foreach ($this->list[$ind][$key] as $key1 => $val1) {
					$res[$key1] = $val1;
				}
				
			}
		}
		return $res;
	}
}
?>