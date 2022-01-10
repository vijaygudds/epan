<?php

namespace xepan\base;

/**
* 
*/
class Model_Contact_CommunicationReadMessage extends \xepan\base\Model_Table{
	public $table = "communication_read_msg";
	public $from_date;
	public $to_date;
	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','from_id')->defaultValue($this->app->employee->id);
		$this->hasOne('xepan\communication\Communication','communication_id');
		$this->addField('contact_id');
		$this->addField('created_at')->type('date')->defaultValue($this->app->now);
		$this->addField('is_read')->type('boolean')->defaultValue(false);
		$this->addField('reply_need')->type('boolean')->defaultValue(false);
		$this->addField('type');/*FROM TO CC BCC*/
		$this->addField('row');

		$this->addExpression('department_id')->set(function($m,$q){
			$reply_me = $this->add('\xepan\hr\Model_Employee',['table_alias'=>'employeecommnbdept']);
			return $reply_me->addCondition('id',$m->getElement('from_id'))
						->fieldQuery('department_id');

		});
		$this->addExpression('department')->set(function($m,$q){
			$reply_me = $this->add('\xepan\hr\Model_Employee',['table_alias'=>'employeecommnbdept']);
			return $reply_me->addCondition('id',$m->getElement('contact_id'))
						->fieldQuery('department');

		});
		$this->addExpression('to_contact')->set(function($m,$q){
			$reply_me = $this->add('\xepan\hr\Model_Employee',['table_alias'=>'employeecommnbdept']);
			return $reply_me->addCondition('id',$m->getElement('contact_id'))
						->fieldQuery('name');

		});
		// $this->addExpression('created_at')->set(function($m,$q){
		// 	$reply_me = $this->add('xepan\communication\Model_Communication',['table_alias'=>'employeecommnbcred']);
		// 	return $reply_me->addCondition('id',$q->getField('communication_id'))
		// 				->fieldQuery('created_at');
		// });

		$this->addExpression('reply_need_by_me')->set(function($m,$q){
			$reply_me = $this->add('xepan\communication\Model_Communication',['table_alias'=>'employeecommnbyme']);

			return $reply_me->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						// ->addCondition([['from_id',$q->getField('contact_id')],['created_by_id',$q->getField('contact_id')]])
						->addCondition('id',$q->getField('communication_id'))
						->addCondition('created_by_id',$q->getField('from_id'))
						->addCondition('reply_need',true)
						// ->fieldQuery('reply_need')
						->count()
						;
		});
		$this->addExpression('total_send_message')->set(function($m,$q){
			return $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('reply_need',true)
						->count();
		});
		
		$this->addExpression('message')->set(function($m,$q){
			$reply_me = $this->add('xepan\communication\Model_Communication',['table_alias'=>'employeecommnbmsg']);
			return $reply_me->addCondition('id',$m->getElement('communication_id'))
						// ->addCondition('created_at','>=',$this->from_date)
						// ->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->fieldQuery('description');
		});
	}
}