<?php

/**
* description: Phone Numbers for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_Phone extends Model_Contact_Info{

	function init(){
		parent::init();
			
		$this->getElement('head')->enum(['Official','Personal','Mobile']);
		$this->addCondition('type','Phone');

		$this->addHook('beforeSave',[$this,'checkPhoneNo']);
	}

	function checkPhoneNo(){
        $contact = $this->add('xepan\base\Model_Contact');
        
        if($this['contact_id'])
	        $contact->load($this['contact_id']);

		$contactconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'contact_no_duplcation_allowed'=>'DropDown'
							],
					'config_key'=>'contact_no_duplication_allowed_settings',
					'application'=>'base'
			]);
		$contactconfig_m->tryLoadAny();	

		if($contactconfig_m['contact_no_duplcation_allowed'] != 'duplication_allowed'){
	        $contactphone_m = $this->add('xepan\base\Model_Contact_Phone');
	        $contactphone_m->addCondition('id','<>',$this->id);
	        $contactphone_m->addCondition('value',$this['value']);
			
			if($contactconfig_m['contact_no_duplcation_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$contactphone_m->addCondition('contact_type',$this['contact_type']);
		        $contactphone_m->tryLoadAny();
		 	}

	        $contactphone_m->tryLoadAny();
	        
	        if($contactphone_m->loaded())
	            throw $this->exception('This Contact No. Already Used','ValidityCheck')->setField('value');
		}	
    }

}
