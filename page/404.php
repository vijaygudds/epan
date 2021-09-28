<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

class page_404 extends \Page {
	public $title='404- No page found';

	function init(){
		parent::init();
		header('HTTP/1.0 404 Not Found');
	}

	function defaultTemplate(){		
		if(file_exists(getcwd().'/websites/'.$this->app->current_website_name.'/www/404.html')){			
			return ['404'];
		}
		else{
			echo "404- Page not found";
			exit();
			return parent::defaultTemplate();
		}
	}
}
