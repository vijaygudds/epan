<?php

namespace xepan\cms;

class Page_robottxt extends \Page{
	public $title = "Robot TXT Generator";
	function init(){
		parent::init();
		
		header('Content-Type:text/plain');

		if(file_exists(getcwd().'/websites/'.$this->app->current_website_name.'/www/robots.txt')){
			echo file_get_contents(getcwd().'/websites/'.$this->app->current_website_name.'/www/robots.txt');
			exit(0);
		}

		$default=[];
		$default[] = "User-agent: *";
		$default[] = "Disallow: /admin/";
		$default[] = "Disallow: /vendor/";
		$default[] = "Allow: /websites/*/www/";

		echo implode("\n\r", $default);
		exit(0);
	}
}