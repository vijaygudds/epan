<?php

namespace xepan\webwidgets;

class Initiator extends \Controller_Addon {
	public $addon_name = 'xepan_webwidgets';

	function setup_admin(){
		
		$this->routePages('xepan_webwidgets');
		$this->addLocation(array(
				'template'=>'templates',
				'js'=>'templates/js',
				'css'=>'templates/css')
			)
		->setBaseURL('../vendor/xepan/webwidgets/');

		$m = $this->app->cms_menu;
		$m->addItem([' WebWidgets','icon'=>' fa fa-th-large'],'xepan_webwidgets_webwidgets');

		return $this;
	}

	function setup_frontend(){
		$this->routePages('xepan_webwidgets');
		$this->addLocation(array(
				'template'=>'templates',
				'js'=>'templates/js',
				'css'=>'templates/css')
		)
		->setBaseURL('./vendor/xepan/webwidgets/');

		if($this->app->isEditing){	
			$this->app->exportFrontEndTool('xepan\webwidgets\Tool_Testimonials','webwidgets');
			$this->app->exportFrontEndTool('xepan\webwidgets\Tool_Gallery','webwidgets');
		}

		return $this;	
	}

	function resetDB(){
	}
}