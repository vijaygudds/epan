<?php


namespace xepan\cms;


class Tool_Icon extends \xepan\cms\View_Tool {
	
	public $runatServer = false;
	public $teplateOverridable=false;	

	function defaultTemplate(){
		return ['xepan/tool/icon'];
	}
}