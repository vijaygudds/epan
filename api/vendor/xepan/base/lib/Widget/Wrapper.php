<?php

namespace xepan\base;


class Widget_Wrapper extends \View {
	
	function init(){
		parent::init();

	}

	function defaultTemplate(){
		return ['view\widgetwrapper'];
	}
}