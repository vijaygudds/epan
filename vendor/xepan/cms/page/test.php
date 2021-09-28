<?php

namespace xepan\cms;

class Page_test extends \xepan\base\Page{
	public $title = "test";
	function init(){
		parent::init();

		// $this->app->hook('ThemeApplied');
		$this->add('View')->set('Test page');
	}
}