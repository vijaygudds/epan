<?php

/**
* description: xEpan CMS Page runner. 
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;

use \tburry;


class page_cms extends \Page {
	public $title='';

	public $dom;

	public $spots=1;
	public $page_requested=null;

	function init(){
		parent::init();

		if($this->app->xepan_cms_page['is_secure']){
			$allowed = false;
			if($this->app->auth->model->loaded()) $allowed= true;
			
			if(trim($this->app->xepan_cms_page['secure_only_for'])){
				$contact = $this->add('xepan\base\Model_Contact');
				$contact->loadLoggedIn();

				if(!in_array($contact['type'], explode(",", trim($this->app->xepan_cms_page['secure_only_for'])))) $allowed = false;
			}

			if(!$allowed){
				$config_m = $this->add('xepan\cms\Model_Config_FrontendWebsiteStatus');
				$config_m->tryLoadAny();
				$this->app->redirect($this->app->url($config_m['default_login_page']?:'xepan_cms_login'));
			}

		}

		if($this->page_requested){
			$this->app->template->trySet('title',$this->page_requested);
		}

		// $this->api->addHook('post-init',[$this,'createSpots']);		
		// $this->api->addHook('post-init',[$this,'renderServerSideComponents']);

		$this->add('xepan\cms\Controller_ServerSideComponentManager');
		
		if($this->app->isEditing){
			$this->api->addHook('pre-render',[$this,'createEditingEnvironment']);			
		}
	}


	function createEditingEnvironment(){
        $this->app->add('xepan\cms\View_ToolBar');
	}
}
