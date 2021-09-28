<?php

 namespace xepan\cms;

class Model_Custom_FormSubmission extends \Model_Table{

 	public $table = 'custom_form_submission'; 

	function init(){
		parent::init();

		$this->hasOne('xepan\cms\Custom_Form','custom_form_id');
		$this->addField('value')->type('text');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		
		$this->addHook('afterInsert',$this);
	}

	function afterInsert(){
		if(isset($this->app->employee)){
			$this->app->employee->
			addActivity("Enquiry Received On Website",null, null /*Related Contact ID*/,null,null,null);
		}
	}
}
