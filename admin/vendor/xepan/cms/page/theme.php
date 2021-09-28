<?php

namespace xepan\cms;

class page_theme extends \xepan\base\Page{
	public $title = "Themes";

	public $epan_template;

	function init(){
		parent::init();
		if($this->app->is_admin) return;
		$this->add('xepan\cms\View_Theme',['epan_template'=>$this->epan_template]);
	}
}