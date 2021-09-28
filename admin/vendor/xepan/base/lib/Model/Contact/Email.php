<?php

/**
* description: Emails for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_Email extends Model_Contact_Info{
	public $skip_email_check=false;
	
	function init(){
		parent::init();
			
		$this->getElement('head')->enum(['Official','Personal']);
		$this->addCondition('type','Email');
		$this->is(['value|to_trim|required|email']);
		// if(!$this->skip_email_check)
		// 	$this->addHook('beforeSave',[$this,'checkEmail']);
	}

	function checkEmail(){
		

        $contact = $this->add('xepan\base\Model_Contact');
        
        if($this['contact_id'])
	        $contact->load($this['contact_id']);

		$emailconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'email_duplication_allowed'=>'DropDown'
							],
					'config_key'=>'Email_Duplication_Allowed_Settings',
					'application'=>'base'
			]);
		$emailconfig_m->tryLoadAny();

		if($emailconfig_m['email_duplication_allowed'] != 'duplication_allowed'){
	        $email_m = $this->add('xepan\base\Model_Contact_Email');
	        $email_m->addCondition('id','<>',$this->id);
	        $email_m->addCondition('value',$this['value']);
			
			if($emailconfig_m['email_duplication_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$email_m->addCondition('contact_type',$this['contact_type']);
			}
	        
	        $email_m->tryLoadAny();
	        
	        if($email_m->loaded())
	            throw $this->exception('This Email Already Used','ValidityCheck')->setField('value');

		}	
    }

}
