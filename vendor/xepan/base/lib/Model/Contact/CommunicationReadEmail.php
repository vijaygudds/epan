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
		$this->addField('created_at')->type('date')->defaultValue($this->app->now);
		$this->addField('type');/*FROM TO CC BCC*/
		$this->addField('row');
		
		$this->addExpression('message')->set(function($m,$q){
			$reply_me = $this->add('xepan\communication\Model_Communication',['table_alias'=>'employeecommnbmsg']);
			return $reply_me->addCondition('id',$m->getElement('communication_id'))
						// ->addCondition('created_at','>=',$this->from_date)
						// ->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->fieldQuery('description');
		});
	}
}