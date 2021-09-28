<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;


class page_login extends \Page {
	public $title='Login page For CMS Editors';

	function init(){
		parent::init();

		$login_tool = $this->add('xepan\base\Tool_UserPanel',['options'=>['show_footer'=>false,'layout'=>'login_view','login_success_url'=>'index']],'login');		
	}

	function defaultTemplate(){
		return ['view\tool\login'];
	}
}
