<?php


namespace xepan\cms;

/**
* 
*/
class Tool_PopupToolTip extends \xepan\cms\View_Tool{
	
	public $options=[];
	public $runatServer = false;
	public $templateOverridable=false;
	
	function init(){
		parent::init();
	}
	function defaultTemplate(){
		return ['view/tool/popup-tool-tip'];
	}
}