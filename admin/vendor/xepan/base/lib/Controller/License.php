<?php


// add following line in setup_admin or setup_frontend or where you need to check to make application license dependent
// if(!$this->add('xepan\base\Controller_License')->check(__NAMESPACE__)) return;

namespace xepan\base;

class Controller_License extends \AbstractController {

	function init(){
		parent::init();
	}
	
	function check($application){
		$allowed = false;
		
		$this->app->stickyGET('application');
		$lm= $this->add('xepan\base\Model_Config_License');
		$lm->addCondition('application',$application);
		$lm->tryLoadAny();
		$allowed = $lm->isLicenseValid();

		if(!$allowed){
			$btn = $this->app->page_top_right_button_set->addButton(explode("\\", $application)[1].' Lisence Expired');
			$btn->addClass('btn btn-danger');

			$btn->js('click')->univ()->location($this->app->url('xepan_base_licensemanager'));

		}

		return $allowed;
	}

}