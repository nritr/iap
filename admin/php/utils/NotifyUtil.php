<?php

class NotifyUtil {

    private $request;
    public function __construct() {
        $this->request = new RequestUtil();
        
    }
    public function setNotify(Notify $notify) {
        $this->request->setSession("notify", serialize($notify));
    }
    
    /**
     * 
     * @return Notify|NULL
     */
    public function getNotify(): ?Notify {
        $notify = $this->request->getSession("notify");
        if ($notify) {
            $this->request->unsetSessionVar("notify");
            return unserialize($notify);
        }
        return null;
    }
	
}

class Notify {
    
    public $type;
    public $text;
    public $place;
    
    public function __construct($text,$type="success",$place="top center") {
        $this->text = $text;
        $this->type = $type;
        $this->place= $place;
    }
}
?>