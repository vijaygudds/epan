<?php

namespace xepan\communication;
/**
 * 
 */
// class Model_Communication_CommunicationActivity extends \xepan\hr\Model_Employee{
class Model_Communication_CommunicationActivity extends \xepan\communication\Model_Communication{
	public $communication_type;
	public $communication_subtype;
	public $communication_action;
	public $communication_result;
	public $contact;
	public $communication_for;
	public $communication_subfor;
	public $from_date;
	public $to_date;

	function init(){
		parent::init();	

		$this->addExpression('total_communication')->set(function($m,$q){
		$ttl_com = $this->add('xepan\communication\Model_Communication',['table_alias'=>'totalcom'])
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('communication_type','<>',['AbstractMessage'])
						->addCondition('created_at','<',$this->api->nextDate($this->to_date));
				if($this->communication_type)		
						$ttl_com->addCondition('communication_type',$this->communication_type);
				if($this->communication_subtype)		
						$ttl_com->addCondition('sub_type',$this->communication_subtype);
				if($this->communication_result)		
						$ttl_com->addCondition('calling_status',$this->communication_result);
				if($this->communication_action)		
						$ttl_com->addCondition('sub_type_3',$this->communication_action);
				if($this->communication_for)		
						$ttl_com->addCondition('communication_for_id',$this->communication_for);
				if($this->communication_subfor)		
						$ttl_com->addCondition('communication_subfor_id',$this->communication_subfor);

				if($this->contact)	
					$ttl_com->addCondition('created_by_id',$this->contact);

			return $ttl_com->count();
		})->sortable(true);

		$this->addExpression('total_communication_withoutphoneattand')->set(function($m,$q){
		$ttl_com = $this->add('xepan\communication\Model_Communication',['table_alias'=>'totalcom'])
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('communication_type','<>',['AbstractMessage'])
						->addCondition('created_at','<',$this->api->nextDate($this->to_date));
				if($this->communication_type)		
						$ttl_com->addCondition('communication_type',$this->communication_type);
				if($this->communication_subtype)		
						$ttl_com->addCondition('sub_type',$this->communication_subtype);
				// if($this->communication_result)		
						$ttl_com->addCondition('calling_status','<>','PHONE ATTEND');
				if($this->communication_action)		
						$ttl_com->addCondition('sub_type_3',$this->communication_action);
				if($this->communication_for)		
						$ttl_com->addCondition('communication_for_id',$this->communication_for);
				if($this->communication_subfor)		
						$ttl_com->addCondition('communication_subfor_id',$this->communication_subfor);

				if($this->contact)	
					$ttl_com->addCondition('created_by_id',$this->contact);

			return $ttl_com->count();
		})->caption('total_communication');
	}
}
