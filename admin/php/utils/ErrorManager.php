<?php

class ErrorManager {

    /**
     * 
     * @var Exception
     */
    private $ex;
    private $data;
    public function __construct(Exception $ex=null) {
        $this->ex=$ex;
    }
    
    public function getCode() {
        if ($this->ex!=null)
            return $this->ex->getCode();
        return 0;
    }
    
    public function getMessage() {
        if ($this->ex!=null)
            return $this->ex->getMessage();
        return null;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    private function getData() {
        return ($this->data == "" ? "null" : $this->data);
    }
    public function getJsonResponse($encode=true) {

        $status = ((string)$this->ex==null ? "success" : "fail");
        $ret = "{".
                "\"status\":\"".$status."\", ".
                "\"error\": ".
                        "{".
                            "\"code\":\"".$this->getCode()."\",".
                            "\"message\":\"".$this->getMessage()."\"".
                        "},".
                "\"data\":".($encode==true ? json_encode($this->getData()) : $this->getData()).
                "}";
        return $ret;
    }
}
?>