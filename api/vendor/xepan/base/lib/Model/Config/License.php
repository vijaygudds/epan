<?php

namespace xepan\base;


class Model_Config_License extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'application'=>'Line',
						'key'=>'Text',
						'license'=>'Text',
						'valid_till'=>'DatePicker',
						];
	public $config_key = 'APPLICATION_LICENSE';
	public $application='base';

	function init(){
		parent::init();

		// $this->getField('default_login_page')->defaultValue('login');
		// $this->getField('system_contact_types')->defaultValue('Contact,Customer,Supplier,Employee');
	}


	function isLicenseValid(){
		return ($this['license'] == md5($this['key'].$this['application'].$this['valid_till']) && strtotime($this->app->today) <= strtotime($this['valid_till']));
	}
}