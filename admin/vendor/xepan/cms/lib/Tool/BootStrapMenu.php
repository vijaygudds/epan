<?php

namespace xepan\cms;

class Tool_BootStrapMenu extends \xepan\cms\View_Tool{
	public $runatServer = false;
	public $teplateOverridable=true;
	
	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return ['xepan/tool/bootstrap-menu'];
	}
}