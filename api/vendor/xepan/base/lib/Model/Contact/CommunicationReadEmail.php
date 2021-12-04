<?php

namespace xepan\base;

/**
* 
*/
class Model_Contact_CommunicationReadEmail extends \xepan\base\Model_Table{
	public $table = "communication_read_emails";
	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','contact_id');
		$this->hasOne('xepan\communication\Communication','communication_id');
		$this->addField('is_read')->type('boolean')->defaultValue(false);
		$this->addField('type');/*FROM TO CC BCC*/
		$this->addField('row');
	}
}