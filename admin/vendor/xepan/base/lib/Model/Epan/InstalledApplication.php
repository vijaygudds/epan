<?php

/**
* description: InstalledApplication defines installed xEpan Applications for any Epan.
* Based on Free/Shareware or Paid user can access application. 
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;


class Model_Epan_InstalledApplication extends \xepan\base\Model_Table{
	public $table='installed_application';

	public $acl = false;

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\base\Application');

		$this->addField('installed_on')->type('datetime')->defaultValue($this->api->now);
		$this->addField('valid_till')->type('datetime')->defaultValue($this->api->now);

		$this->addExpression('application_namespace')->set($this->refSQL('application_id')->fieldQuery('namespace'));

		$this->addField('is_active')->type('boolean')->defaultValue(true);
		$this->hasMany('xepan\hr\EmployeeDepartmentalAclAssociation','installed_app_id');
		$this->addField('is_hidden')->type('boolean')->defaultValue(false); // TODO set based on actual condition, FREE, TIME BASED, LICENSE VALID etc.
		$this->addExpression('is_valid')->set(true); // TODO set based on actual condition, FREE, TIME BASED, LICENSE VALID etc.
		// $this->add('dynamic_model\Controller_AutoCreator');

	}
}
