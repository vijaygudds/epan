<?php

namespace xepan\listing;

class Model_Contact extends \xepan\base\Model_Contact{
	var $table_alias = "listingContact";
	public $title_field = "name_with_user_name";
	function init(){
		parent::init();


		$this->addExpression('name_with_user_name')->set(function($m,$q){
			return $q->expr('CONCAT(IFNULL([0],"")," ",IFNULL([1],"")," :: ",IFNULL([2],""))',
				[
					$m->getElement('first_name'),
					$m->getElement('last_name'),
					$m->getElement('user')
				]);
		});

	}

	function createContact($app,$contact_detail=[],$user){
		$this->addCondition('user_id',$user['id']);
		$this->tryLoadAny();

		$this['first_name'] = $contact_detail['first_name'];
		$this['last_name'] = $contact_detail['last_name'];
		$this->save();

		if(filter_var($user['username'], FILTER_VALIDATE_EMAIL)){
			$email = $this->add('xepan\base\Model_Contact_Email');
			$email->addCondition('contact_id',$this->id);
			$email->addCondition('value',$user['username']);
			$email->tryLoadAny();
			
			$email['head'] = 'Official';
			$email->save();
		}
		
		if(isset($contact_detail['mobile_no']) && $contact_detail['mobile_no']){
			$phone = $this->add('xepan\base\Model_Contact_Phone');
			$phone->addCondition('contact_id',$this->id);
			$phone->addCondition('value',$contact_detail['mobile_no']);
			$phone->tryLoadAny();
			
			$phone['head'] = 'Official';
			$phone->save();
		}
	}
}