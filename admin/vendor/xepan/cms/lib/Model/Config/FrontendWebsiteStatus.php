<?php

namespace xepan\cms;


class Model_Config_FrontendWebsiteStatus extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'site_offline'=>'Line',
						'offline_site_content'=>'xepan\base\RichText',
						'continue_crons'=>'Checkbox',
						'default_login_page'=>'Line',
						'system_contact_types'=>'Text',
						];
	public $config_key = 'FRONTEND_WEBSITE_STATUS';
	public $application='cms';

	function init(){
		parent::init();

		$this->getField('default_login_page')->defaultValue('login');
		$this->getField('system_contact_types')->defaultValue('Contact,Customer,Supplier,Employee');
	}
}