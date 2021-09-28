<?php

namespace xepan\cms;

class page_getwebpage extends \Page{
	function init(){
		parent::init();

		$page = $this->add('xepan\cms\Model_Page')
    		->addCondition('is_active',true)
    	;
        $drop_down = "";
        // $drop_down = '<ul class="dropdown-menu">';
        foreach ($page as $p) {
            $url = $this->app->url(str_replace(".html", "", $p['path']));
            $drop_down .= '<li><a href="'.(string)$url.'">'.$p['name'].'</a></li>';
        }
        // $drop_down .= "</ul>";
        echo $drop_down;
		exit;
	}
}