<?php

namespace xepan\base;


class Model_Config_DuplicateContactNoAllowed extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
				'contact_no_duplcation_allowed'=>'DropDown'
			];
	public $config_key = 'contact_no_duplication_allowed_settings';
	public $application='base';

	function init(){
		parent::init();

		$allow_contactno_permission = array('duplication_allowed' =>'Duplication Allowed',
									 'no_duplication_allowed_for_same_contact_type' =>'No Duplication Allowed For Same Contact Type',
									 'never_duplication_allowed' =>'Never Duplication Allowed');
		$email_allowed_field = $this->getElement('contact_no_duplcation_allowed');
		$email_allowed_field->setValueList($allow_contactno_permission);
	}
}