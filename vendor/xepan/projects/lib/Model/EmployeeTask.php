<?php

namespace xepan\projects;

class Model_EmployeeTask extends \xepan\projects\Model_Employee{
	public $from_date;
	public $to_date;

	function init(){
		parent::init();
		
		if(!$this->from_date || !$this->to_date) throw new \Exception("must pass from date and to date");

		$this->addExpression('total_task')->set(function($m,$q){
			$ttl_task = $this->add('xepan\projects\Model_Task',['table_alias'=>'totaltask'])
						// ->addCondition('assign_to_id',$q->getField('id'))
						// ->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			$ttl_task->addCondition(
							$this->dsql()->orExpr()
  								->where('assign_to_id',$q->getField('id'))
  								->where('created_by_id',$q->getField('id'))
  							);
			return $ttl_task->count();
		})->sortable(true);

		$this->addExpression('self_task')->set(function($m,$q){

			return $this->add('xepan\projects\Model_Task',['table_alias'=>'self_task'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		})->sortable(true);

		$this->addExpression('task_assigned_to_me')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'task_assigned_to_me'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('created_by_id','<>',$q->getField('id'))
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;

		})->sortable(true);
		
		$this->addExpression('pending_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'task_assigned_by_me'])
						->addCondition(
							$this->dsql()->orExpr()
  								->where('assign_to_id',$q->getField('id'))
  								// ->where('created_by_id',$q->getField('id'))
  							)
						->addCondition('status','Pending')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		})->sortable(true);
		$this->addExpression('pending_for_receiving')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'task_assigned_by_me'])
						->addCondition(
							$this->dsql()->orExpr()
  								->where('received_at',null)
  								// ->where('received_at','>','created_at')
  							)
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('status','Assigned')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		})->sortable(true);
		$this->addExpression('inProgress_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'task_assigned_by_me'])
						->addCondition(
							$this->dsql()->orExpr()
  								->where('assign_to_id',$q->getField('id'))
  								->where('created_by_id',$q->getField('id'))
  							)
						->addCondition('status','Inprogress')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		})->sortable(true);	

		$this->addExpression('overdue_task')->set(function($m,$q){
			$task =  $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask']);
			$task->addCondition('status',['Pending','Inprogress','Assigned','Submitted']);
		   //  	 	->addCondition($task->dsql()->orExpr()
		   //  		->where('assign_to_id',$q->getField('id'))
		   //  		->where($task->dsql()->andExpr()
					// ->where('created_by_id',$q->getField('id'))
					// ->where('assign_to_id',null)));
			$task->addCondition('assign_to_id',$q->getField('id'));			
			$task->addCondition('deadline','<',$this->app->now);			
			$task->addCondition('status','<>','Completed');
			$task->addCondition('created_at','>=',$this->from_date);
			$task->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			return $task->count();		
		});

		$this->addExpression('task_assigned_by_me')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'task_assigned_by_me'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('assign_to_id','<>',$q->getField('id'))
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		})->sortable(true);

		$this->addExpression('pending_for_approval')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'received_task'])
						->addCondition('created_by_id',$q->getField('id'))
						->addCondition('status','Submitted')
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		});

		$this->addExpression('task_complete_in_deadline')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'taskindeadline'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('task_complete_in_deadline',true)
						->addCondition('status','Completed')
						->addCondition('completed_at','>=',$this->from_date)
						->addCondition('completed_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		});

		$this->addExpression('task_complete_after_deadline')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'taskafterdeadline'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('task_complete_in_deadline',false)
						->addCondition('status','Completed')
						->addCondition('completed_at','>=',$this->from_date)
						->addCondition('completed_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		});

		$this->addExpression('submitted_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'submitted_task'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('status','Submitted')
						->addCondition('submitted_at','>=',$this->from_date)
						->addCondition('submitted_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		});

		$this->addExpression('rejected_task')->set(function($m,$q){
			return $this->add('xepan\projects\Model_Task',['table_alias'=>'rejected_task'])
						->addCondition('assign_to_id',$q->getField('id'))
						->addCondition('rejected_at','>=',$this->from_date)
						->addCondition('rejected_at','<',$this->api->nextDate($this->to_date))
						->count()
						;
		});


		$this->addExpression('ideal_time')->set(function($m,$q){
			return "'0'";
		});
	
	}
}