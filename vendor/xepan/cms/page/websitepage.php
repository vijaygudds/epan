<?php

namespace xepan\cms;

class page_websitepage extends \xepan\base\Page{
	public $title = "Website Pages and Templates";
	function init(){
		parent::init();

		$tab = $this->add('Tabs');
		$temp_tab = $tab->addTab('Template');
		$page_tab = $tab->addTab('Page');

		// Website Template
		$template = $temp_tab->add('xepan\cms\Model_Template');
		$crud = $temp_tab->add('xepan\hr\CRUD');
		$crud->setModel($template,['name','path','is_muted']);

		// Website Pages
		$page = $page_tab->add('xepan\cms\Model_Page');
		$crud = $page_tab->add('xepan\hr\CRUD');
		$crud->setModel($page,['template','template_id','name','path','is_muted']);
	}
}