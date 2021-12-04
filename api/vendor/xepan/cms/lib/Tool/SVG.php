<?php


namespace xepan\cms;


class Tool_SVG extends \xepan\cms\View_Tool {
	
	public $runatServer = false;
	public $templateOverridable = true;

	function defaultTemplate(){
		return ['xepan/tool/svg'];
	}
	
}