<?php

namespace xepan\cms;

class Page_cmseditors extends \xepan\base\Page{
	public $title = "CMS Editors";
	function init(){
		parent::init();

		$cmseditors = $this->add('xepan\base\CRUD');
		$cmseditors_mdl = $this->add('xepan\cms\Model_User_CMSEditor');
		$cmseditors->setModel($cmseditors_mdl,['username','user_id','can_edit_template','can_edit_page_content']);
		$cmseditors->grid->addQuickSearch(['username']);
	}
}