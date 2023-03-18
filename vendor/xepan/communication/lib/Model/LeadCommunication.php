<?php

namespace xepan\communication;  

class Model_LeadCommunication extends \xepan\marketing\Model_Lead{
	public $from_date;
	public $to_date;
	public $communication_row;
	public $communicationfor;
	public $communicationsubfor;
	function init(){
		parent::init();
		// if(!$this->from_date || !$this->to_date) throw new \Exception("must pass from date and to date");
		// throw new \Exception($this->from_date, 1);
		
		

		$this->addExpression('total_communication')->set(function($m,$q){
			$com_m = $m->add('xepan\communication\Model_Communication',['table_alias'=>'totalleads']);
			$com_m->addCondition($com_m->dsql()->orExpr()
								->where('from_id',$q->getField('id'))
								->where('to_id',$q->getField('id'))
							)
						->addCondition('communication_type','<>','AbstractMessage')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			if($this->communicationfor)
				$com_m->addCondition('communication_for_id',$this->communicationfor);
			if($this->communicationsubfor)
				$com_m->addCondition('communication_subfor_id',$this->communicationsubfor);
			// $com_m->addCondition('type','<>','AbstractMessage');			
			// $com_m->setOrder('id','desc');
			// ->setLimit(1);
			// $com_m->tryLoadAny();
			return $com_m->count();								
		});

		$this->addExpression('lead_communication_created_by')->set(function($m,$q){
			$lead_c = $m->add('xepan\communication\Model_Communication',['table_alias'=>'leadscrea']);
			$lead_c->addCondition($lead_c->dsql()->orExpr()
								->where('from_id',$q->getField('id'))
								->where('to_id',$q->getField('id'))
							)
						->addCondition('communication_type','<>','AbstractMessage')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))->setLimit(1);
			// $com_m->addCondition('type','<>','AbstractMessage');			
			// $com_m->setOrder('id','desc');
			// ->setLimit(1);
			// $com_m->tryLoadAny();
			return $lead_c->fieldQuery('created_by');
		});
		$this->addExpression('lead_communication_for')->set(function($m,$q){
			$lead_c = $m->add('xepan\communication\Model_Communication',['table_alias'=>'leadscrea']);
			if($this->communicationfor)
				$lead_c->addCondition('communication_for_id',$this->communicationfor);
			
			$lead_c->addCondition($lead_c->dsql()->orExpr()
								->where('from_id',$q->getField('id'))
								->where('to_id',$q->getField('id'))
							)
						->addCondition('communication_type','<>','AbstractMessage')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->setOrder('id','desc')
						->setLimit(1);

			// $com_m->addCondition('type','<>','AbstractMessage');			
			// ->setLimit(1);
			// $com_m->tryLoadAny();
			return $lead_c->fieldQuery('communication_for');
		});
		$this->addExpression('lead_communication_sub_for')->set(function($m,$q){
			$lead_c = $m->add('xepan\communication\Model_Communication',['table_alias'=>'leadscrea']);
			if($this->communicationfor)
				$lead_c->addCondition('communication_for_id',$this->communicationfor);
			if($this->communicationsubfor)
				$lead_c->addCondition('communication_subfor_id',$this->communicationsubfor);
			$lead_c->addCondition($lead_c->dsql()->orExpr()
								->where('from_id',$q->getField('id'))
								->where('to_id',$q->getField('id'))
							)
						->addCondition('communication_type','<>','AbstractMessage')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->setOrder('id','desc')
						->setLimit(1);
			// $com_m->addCondition('type','<>','AbstractMessage');			
			// ->setLimit(1);
			// $com_m->tryLoadAny();
			return $lead_c->fieldQuery('communication_subfor');
		});


		// $this->addExpression('last_communication')->set(function($m,$q){
		// 	$last_commu = $m->add('xepan\communication\Model_Communication');
		// 	$last_commu->addCondition(
		// 					$last_commu->dsql()->orExpr()
		// 						->where('from_id',$q->getField('id'))
		// 						->where('to_id',$q->getField('id'))
		// 					)
		// 				->addCondition('created_at','>=',$this->from_date)
		// 				->addCondition('created_at','<',$this->api->nextDate($this->to_date))
		// 				->setOrder('id','desc')
		// 				->setLimit(1);
		// 	return $q->expr('DATE_FORMAT([0],"%M %d, %Y")',[$last_commu->fieldQuery('created_at')]);
		// });


	} 
}
