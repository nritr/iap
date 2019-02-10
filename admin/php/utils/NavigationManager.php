<?php

class NavigationManager {

    private $path;
    
    public function __construct(string $path = null) {
        $this->path = $path;
    }
    
    public function errorPage(string $msg,string $url_back) {
        $skin 			= new SkinManager("/admin/html/error.html");
        $skinCommon 	= new SkinCommons();
        $skin->addVariable("TXT_INCLUDE_HEAD",$skinCommon->getHead());
        $skin->addVariable("TXT_INCLUDE_BODY_HEAD",$skinCommon->getBodyHead());
        $skin->addVariable("TXT_INCLUDE_NAVIGATION_PANEL",$skinCommon->getNavigationPanel());
        $skin->addVariable("TXT_INCLUDE_FOOTER",$skinCommon->getFooter());
        
        $skin->addVariable("TXT_ERROR", $msg);
        $skin->addVariable("TXT_URL_BACK", $url_back);
        return $skin->getSkin();
    }
	

}

?>