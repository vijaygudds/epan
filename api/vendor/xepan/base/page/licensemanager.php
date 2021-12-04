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

class page_licensemanager extends \Page {
	public $title='xEpan Application License Manager';

	function init(){
		parent::init();

		$this->app->stickyGET('application');
		$lm= $this->add('xepan\base\Model_Config_License');

		$crud = $this->add('xepan\base\CRUD');
		$crud->setModel($lm,['application','key','license','valid_till'],['id','application','key','license','valid_till']);
	}
}
