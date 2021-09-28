<?php

namespace xepan\cms;

class page_changetemplate extends \xepan\base\Page{
	
	public $title = "Change Theme";
	
	function init(){
		parent::init();

		$this->add('H2')->set('Change Theme Now');
		$temp = $this->add('xepan\cms\Model_Template');

		$grid = $this->add('xepan\hr\Grid',null,null,['view/change-template']);
		$grid->setModel($temp);
		$apply_btn = $grid->addColumn('Button','Apply_Now');

		$apply_btn->js('click')->univ()->alert('TODO');
	}
}