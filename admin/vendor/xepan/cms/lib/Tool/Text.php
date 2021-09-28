<?php


namespace xepan\cms;


class Tool_Text extends \xepan\cms\View_Tool {
	
	public $runatServer = false;
	public $teplateOverridable=false;

	function defaultTemplate(){
		return ['xepan/tool/text'];
	}
	
}