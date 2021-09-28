<?php

namespace xepan\cms;


/**
* 
*/
class Tool_ImageWithDescription extends \xepan\cms\View_Tool{
	public $options=[];
	public $runatServer = false;
	public $templateOverridable=false;
	
	function init(){
		parent::init();
	}
	function defaultTemplate(){
		return ['view/tool/image-with-description'];
	}

}