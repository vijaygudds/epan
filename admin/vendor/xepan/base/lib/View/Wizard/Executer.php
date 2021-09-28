<?php

namespace xepan\base;

class View_Wizard_Executer extends \View{

	function init(){
		parent::init();

		$apps= $this->app->xepan_addons;
		// $apps=['xepan\base','xepan\hr'];
		foreach ($apps as $addon) {
			try{
         		$this->add($addon.'/View_EasySetupWizard');   
			}catch(\Exception $e){
				
			}

		}
	}
}