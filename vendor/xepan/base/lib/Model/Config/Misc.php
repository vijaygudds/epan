<?php

namespace xepan\base;


class Model_Config_Misc extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
								'time_zone'=>'DropDown',
								'admin_restricted_ip'=>'Text',
								
								];
	public $config_key = 'Miscellaneous_Technical_Settings';
	public $application='base';

	function init(){
		parent::init();

		$time_zone_field= $this->getField('time_zone');
		$time_zone_field->setValueList(array_combine(timezone_identifiers_list(),timezone_identifiers_list()));
		$this->getElement('admin_restricted_ip')->hint('Comma seperated IPs');
		// $this->getField('system_contact_types')->defaultValue('Contact,Customer,Supplier,Employee');
	}

}