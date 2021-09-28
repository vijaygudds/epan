<?php

namespace xepan\base;


class Model_Config_DuplicateEmailAllowed extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
					'email_duplication_allowed'=>'DropDown'
				];
	public $config_key = 'Email_Duplication_Allowed_Settings';
	public $application='base';

	function init(){
		parent::init();

		$allow_email_permission = array('duplication_allowed' =>'Duplication Allowed',
									 'no_duplication_allowed_for_same_contact_type' =>'No Duplication Allowed For Same Contact Type',
									 'never_duplication_allowed' =>'Never Duplication Allowed');
		$email_allowed_field = $this->getElement('email_duplication_allowed');
		$email_allowed_field->setValueList($allow_email_permission);
	}
}