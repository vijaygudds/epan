<?php

/**
* description: epan may have many Email Settings for sending and receiving enails.
* Since xEpan is primarily for cloud multiuser SaaS. Email settings are considered as base
* and included in Epan, not in any top layer Application.
* 
* @author : VIJAY MALI
* @email : vijay.mali552@gmail.com
* 
* 
*/

namespace xepan\hr;

class Model_Employee_Group extends \xepan\base\Model_Table{

	public $table='empgroup';
	public $acl_type="hr_EmployeeGroup";
	
	// public $actions=[
	// 	'Active'=>['view','edit','delete','deactivate'],
	// 	'InActive'=>['view','edit','delete','activate'],
	// ];
	function init(){
		parent::init();
		// TODO : add all required fields for email + can_use_in_mass_emails
		// $this->hasOne('xepan\base\Epan','epan_id');
		// $this->hasOne('xepan\base\Contact','created_by_id')->defaultValue(@$this->app->employee->id);
		$this->addField('name');
		$this->addField('is_active')->type('boolean')->defaultValue(false);


	}
}
