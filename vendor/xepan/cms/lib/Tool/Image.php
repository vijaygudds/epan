<?php


namespace xepan\cms;


class Tool_Image extends \xepan\cms\View_Tool {
	
	public $runatServer = false;
	public $templateOverridable = false;

	function defaultTemplate(){
		return ['xepan/tool/image'];
	}
	
}