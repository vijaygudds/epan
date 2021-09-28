<?php

/**
* description: xEpan Login layout
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Layout_Login extends \Layout_Basic {
	function init(){
		parent::init();
		$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
		// $company_m->add('xepan\hr\Controller_ACL');
		$company_m->tryLoadAny();

		$this->template->trySet('company_name',$company_m['company_name']);
	}

	function defaultTemplate(){
		return ['layout/xepanlogin'];
	}
}