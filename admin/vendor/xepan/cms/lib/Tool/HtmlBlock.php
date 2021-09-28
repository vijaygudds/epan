<?php

namespace xepan\cms;


/**
* 
*/
class Tool_HtmlBlock extends \xepan\cms\View_Tool{
	public $options=[];
	public $runatServer = false;
	public $templateOverridable=false;

	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return ['xepan/tool/htmlblock'];
	}
}