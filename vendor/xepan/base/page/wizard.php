<?php

namespace xepan\base;

class page_wizard extends \xepan\base\Page {
	public $title='Quick Setup Wizard';

	function init(){
		parent::init();

		$this->add('xepan\base\View_Wizard_Executer');
	}
}
