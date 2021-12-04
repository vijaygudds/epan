<?php

namespace xepan\cms;


/**
* 
*/
class Tool_Button extends \xepan\cms\View_Tool{
	public $options=[];
	public $runatServer = false;
	public $templateOverridable=false;

	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return ['xepan/tool/button'];
	}
}