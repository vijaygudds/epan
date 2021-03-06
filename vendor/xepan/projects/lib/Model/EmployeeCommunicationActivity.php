<?php

namespace xepan\projects;

class Model_EmployeeCommunicationActivity extends \xepan\hr\Model_Employee{
	public $from_date;
	public $to_date;
	public $communication_type;
	public $communication_subtype;
	public $communication_action;
	public $communication_result;
	public $contact;
	public $communication_for;
	public $communication_subfor;
	function init(){
		parent::init();
		// echo "string".$this->from_date;
		$this->addCondition('status','Active');
		$this->addExpression('assign_to_pending_task')->set(function($m,$q){
			// return $q->getField('id');
			$task = $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask']);
				return 	$task->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('assign_to_id','<>',null)
						->addCondition('status','Pending')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});

		$this->addExpression('assign_to_inprogress_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('assign_to_id','<>',null)
						->addCondition('status','Inprogress')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});
		$this->addExpression('assign_to_complete_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('assign_to_id','<>',null)
						->addCondition('status','Completed')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});

		$this->addExpression('assign_by_pending_task')->set(function($m,$q){
			// return $q->getField('id');
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_by_id','<>',null)
						->addCondition('status','Pending')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});

		$this->addExpression('assign_by_inprogress_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_by_id','<>',null)
						->addCondition('status','Inprogress')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});
		$this->addExpression('assign_by_complete_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_by_id','<>',null)
						->addCondition('status','Completed')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});

		$this->addExpression('overdue_task')->set(function($m,$q){
			$task =  $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask']);
			$task->addCondition('status',['Pending','Inprogress','Assigned'])
		    	 	->addCondition($task->dsql()->orExpr()
		    		->where('assign_to_id',$q->getField('id'))
		    		->where($task->dsql()->andExpr()
					->where('created_by_id',$q->getField('id'))
					->where('assign_to_id',null)));
			$task->addCondition('deadline','<',$this->app->now);			
			$task->addCondition('status','<>','Completed');
			$task->addCondition('created_at','>=',$this->from_date);
			$task->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			return $task->count();		
		});

		// $this->addExpression('total_received_message')->set(function($m,$q){
		// 	return $this->add('xepan\communication\Model_Communication_MessageReceived')
		// 				->addCondition('to_id',$q->getField('id'))
		// 				->count();

		// });
		$this->addExpression('total_send_message')->set(function($m,$q){
			return $this->add('xepan\communication\Model_Communication_MessageSent')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('created_by_id',$q->getField('id'))
						->count();
		});
		$this->addExpression('total_received_msg')->set(function($m,$q){

			$rec_msg =  $this->add('xepan\communication\Model_Communication');
			
			$rec_msg->addCondition([
							['to_raw','like','%"'.$q->getField('id').'"%'],
							// ['cc_raw','like','%"'.$q->getField('id').'"%']
							]);
			$rec_msg->addCondition('communication_type','AbstractMessage')
					->addCondition('created_at','>=',$this->from_date)
					->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			return $rec_msg->count();
		});
		$this->addExpression('total_reply_want')->set(function($m,$q){
			$comm = $this->add('xepan\communication\Model_Communication',['table_alias'=>'emprplywant']);

				return $comm->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('created_by_id',$m->getElement('id'))
						->addCondition('reply_need',true)
						->count()
						;
			// return $this->add('xepan\base\Model_Contact_CommunicationReadMessage',['table_alias'=>'empcoredmsgwant'])
			// 			->addCondition('created_at','>=',$this->from_date)
			// 			->addCondition('created_at','<',$this->api->nextDate($this->to_date))
			// 			->addCondition('from_id',$q->getField('id'))
			// 			->addCondition('communication_id',$comm['id'])
			// 			->count();
		});
		$this->addExpression('total_reply_given')->set(function($m,$q){
			$comm = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						// ->addCondition('created_by_id',$q->getField('id'))
						->addCondition('reply_need',true)
						->tryLoadAny()
						;
			// return	$comm->count();		
			return $this->add('xepan\base\Model_Contact_CommunicationReadMessage',['table_alias'=>'empcoredmsggivn'])
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('contact_id',$q->getField('id'))
						// ->addCondition('communication_id',$comm['id'])
						->count();
		});

		$this->addExpression('total_read_msg')->set(function($m,$q){
				// $comm = $this->add('xepan\communication\Model_Communication',['table_alias'=>'empcomread'])
				// 		->addCondition('created_at','>=',$this->from_date)
				// 		->addCondition('created_at','<',$this->api->nextDate($this->to_date))
				// 		->addCondition('created_by_id',$q->getField('id'))
				// 		->tryLoadAny()
				// 		;
				return $this->add('xepan\base\Model_Contact_CommunicationReadEmail')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('contact_id',$q->getField('id'))
						->addCondition('type','<>','From')
						->addCondition('is_read',true)
						->count();
		});
		$this->addExpression('total_unread_msg')->set(function($m,$q){
				// $comm = $this->add('xepan\communication\Model_Communication')
				// 		->addCondition('created_at','>=',$this->from_date)
				// 		->addCondition('created_at','<',$this->api->nextDate($this->to_date))
				// 		->addCondition('created_by_id',$q->getField('id'))
				// 		->tryLoadAny()
				// 		;
				return $this->add('xepan\base\Model_Contact_CommunicationReadEmail')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->addCondition('contact_id',$q->getField('id'))
						->addCondition('type','<>','From')
						->addCondition('is_read',false)
						->count();
		});



		// $this->addExpression('total_received_emails')->set(function($m,$q){
		// 	return $this->add('xepan\communication\Model_Communication_Email_Received')
		// 				->addCondition('to_id',$q->getField('id'))
		// 				->count();

		// });
		$this->addExpression('total_send_emails')->set(function($m,$q){
			return $this->add('xepan\communication\Model_Communication_Email_Sent')
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count();
		});

		$this->addExpression('total_communication')->set(function($m,$q){
		$ttl_com = $this->add('xepan\communication\Model_Communication',['table_alias'=>'totalcom'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('communication_type','<>','AbstractMessage')
						->addCondition('created_at','>=',$this->from_date)
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

				// if($this->contact)	

			return $ttl_com->count();
		})->sortable(true);

		
			// $this->addExpression('total_received_emails');
		// $this->addExpression('total_send_emails');
		
		// $this->addExpression('running_task')->set(function($m,$q){
		// 	return $this->add('xepan\projects\Model_Timesheet',['table_alias'=>'running_task'])
		// 				->addCondition('endtime',null)
		// 				->addCondition('employee_id',$q->getField('id'))
		// 				->setOrder('starttime','desc')
		// 				->setLimit(1)
		// 				->fieldQuery('task');
		// })->sortable(true);


		// $this->addExpression('running_task_id')->set(function($m,$q){
		// 	return $this->add('xepan\projects\Model_Timesheet',['table_alias'=>'running_task'])
		// 				->addCondition('endtime',null)
		// 				->addCondition('employee_id',$q->getField('id'))
		// 				->setOrder('starttime','desc')
		// 				->setLimit(1)
		// 				->fieldQuery('task_id');
		// })->sortable(true);

		// $this->addExpression('running_task_since')->set(function($m,$q){
		// 	return $this->add('xepan\projects\Model_Timesheet',['table_alias'=>'running_task'])
		// 				->addCondition('endtime',null)
		// 				->addCondition('employee_id',$q->getField('id'))
		// 				->setOrder('starttime','desc')
		// 				->setLimit(1)
		// 				->fieldQuery('duration');
		// })->sortable(true);

		// $this->addExpression('project')->set(function($m,$q){
		// 	$p=$this->add('xepan\projects\Model_Project');
		// 	$task_j = $p->join('task.project_id');
		// 	$task_j->addField('task_id','id');
		// 	$p->addCondition($q->expr('[0]=[1]',[$p->getElement('task_id'),$m->getField('running_task_id')]));
		// 	return $p->fieldQuery('name');
		// })->sortable(true);

		// $this->hasMany('xepan\projects\Task','assign_to_id');

		// $this->addExpression('pending_tasks_count')->set(function ($m,$q){
		// 	return $m->refSQL('xepan\projects\Task')
		// 				->addCondition('status','Pending')
		// 				->count();
		// })->sortable(true);



		// $this->addExpression('performance')->set("'Todo'");
	}
}