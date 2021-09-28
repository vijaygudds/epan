<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\base;


class page_configurationmode extends \xepan\base\Page {
	
	function init(){
		parent::init();
		$this->app->memorize('configuration_mode',!$this->app->recall('configuration_mode',false));
		$this->app->redirect('/');
	}
}
