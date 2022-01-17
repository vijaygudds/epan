<?php

namespace xepan\projects;

class page_report_followup extends \xepan\base\Page{

	public $title = "Employee Follow Up Report";

	function page_index(){
		// parent::init();
		
		// sticky get the variable
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$department_id = $this->app->stickyGET('department_id');
		$employee_id = $this->app->stickyGET('employee_id');

		// setting up from and to date
		if(!$from_date)
			$from_date = $this->app->today;
		if(!$to_date)
			$to_date = $this->api->nextDate($this->app->today);

		// adding form
		$form = $this->add('Form',null,null,['form/empty']);
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~3',
				'employee'=>'c2~3',
				'department'=>'c3~3',
				'FormButtons~&nbsp;'=>'c4~3'
			]);

		$date = $form->addField('DateRangePicker','date_range');
		$set_date = $this->app->today." to ".$this->app->today;
		if($from_date){
			$set_date = $from_date." to ".$to_date;
			$date->set($set_date);
		}

		$emp_field = $form->addField('xepan\base\Basic','employee');
		$emp_field->setModel('xepan\projects\Model_Employee')->addCondition('status','Active');

		$dept_field = $form->addField('DropDown','department')
				->setEmptyText('Please Select Department');
		$dept_field->setModel('xepan\hr\Model_Department');
		$form->addSubmit('Get Details');
		
		// adding model
		$employee_followup = $this->add('xepan\projects\Model_EmployeeFollowup',['from_date'=>$from_date,'to_date'=>$to_date]);
		$employee_followup->addCondition('status','Active');
		
		if($from_date){
			$employee_followup->from_date = $from_date;
		}
		if($employee_id){
			$employee_followup->addCondition('id',$employee_id);
		}
		if($department_id){
			$employee_followup->addCondition('department_id',$department_id);
		}

		// adding grid
		$grid = $this->add('xepan\hr\Grid');
		$employee_followup->setOrder('name','asc');
		$grid->setModel($employee_followup,['name','total_followup','self_followup','followup_assigned_to_me','followup_assigned_by_me','pending_followup','pending_for_receiving','inProgress_followup','pending_for_approval','submitted_followup','rejected_followup','overdue_followup','followup_complete_in_deadline','followup_complete_after_deadline']);

		$grid->add('misc\Export',['export_fields'=>['name','total_followup','self_followup','followup_assigned_to_me','followup_assigned_by_me','pending_for_approval','submitted_followup','rejected_followup','followup_complete_in_deadline','followup_complete_after_deadline']]);
		// handling form submission
		if($form->isSubmitted()){
			$grid->js()->reload(
							[
								'employee_id'=>$form['employee'],
								'department_id'=>$form['department'],
								'from_date'=>$date->getStartDate()?:0,
								'to_date'=>$date->getEndDate()?:0
							]
						)->execute();
		}

		$grid->addPaginator($ipp=100);
		//virtual page formats for
		// total_followup format
		$grid->addFormatter('total_followup','template')
			->setTemplate('<a href="#" class="total_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$total_followup}</a>','total_followup');
		$grid->js('click')->_selector('.total_followup')->univ()->frameURL('Employee Total FollowUp',[$this->app->url('./total_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// self task format
		$grid->addFormatter('self_followup','template')
			->setTemplate('<a href="#" class="self_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$self_followup}</a>','self_followup');
		$grid->js('click')->_selector('.self_followup')->univ()->frameURL('Employee Self FollowUp',[$this->app->url('./self_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// Pending task format
		$grid->addFormatter('pending_followup','template')
			->setTemplate('<a href="#" class="pending_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_followup}</a>','pending_followup');
		$grid->js('click')->_selector('.pending_followup')->univ()->frameURL('Employee Pending FollowUp',[$this->app->url('./pending_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		// Pending task format
		$grid->addFormatter('pending_for_receiving','template')
			->setTemplate('<a href="#" class="pending_for_receiving" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_for_receiving}</a>','pending_for_receiving');
		$grid->js('click')->_selector('.pending_for_receiving')->univ()->frameURL('Employee Pending For Receving FollowUp',[$this->app->url('./pending_for_receiving'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// inProgress task format
		$grid->addFormatter('inProgress_followup','template')
			->setTemplate('<a href="#" class="inProgress_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$inProgress_followup}</a>','inProgress_followup');
		$grid->js('click')->_selector('.inProgress_followup')->univ()->frameURL('Employee In Progress FollowUp',[$this->app->url('./inProgress_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// inProgress task format
		$grid->addFormatter('overdue_followup','template')
			->setTemplate('<a href="#" class="overdue_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$overdue_followup}</a>','overdue_followup');
		$grid->js('click')->_selector('.overdue_followup')->univ()->frameURL('Employee OverDue Followup',[$this->app->url('./overdue_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task assign to me format
		$grid->addFormatter('followup_assigned_to_me','template')
			->setTemplate('<a href="#" class="followup_assigned_to_me" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$followup_assigned_to_me}</a>','followup_assigned_to_me');
		$grid->js('click')->_selector('.followup_assigned_to_me')->univ()->frameURL('FollowUp assign to me',[$this->app->url('./followup_assigned_to_me'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task assign by me format
		$grid->addFormatter('followup_assigned_by_me','template')
			->setTemplate('<a href="#" class="followup_assigned_by_me" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$followup_assigned_by_me}</a>','followup_assigned_by_me');
		$grid->js('click')->_selector('.followup_assigned_by_me')->univ()->frameURL('FollowUp assign by me',[$this->app->url('./followup_assigned_by_me'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// pending_for_approval format
		$grid->addFormatter('pending_for_approval','template')
			->setTemplate('<a href="#" class="pending_for_approval" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_for_approval}</a>','pending_for_approval');
		$grid->js('click')->_selector('.pending_for_approval')->univ()->frameURL('Received Followup',[$this->app->url('./pending_for_approval'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// task_complete_in_deadline format
		$grid->addFormatter('followup_complete_in_deadline','template')
			->setTemplate('<a href="#" class="followup_complete_in_deadline" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$followup_complete_in_deadline}</a>','followup_complete_in_deadline');
		$grid->js('click')->_selector('.followup_complete_in_deadline')->univ()->frameURL('FollowUp Completed in deadline',[$this->app->url('./followup_complete_in_deadline'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task_complete_after_deadline format
		$grid->addFormatter('followup_complete_after_deadline','template')
			->setTemplate('<a href="#" class="followup_complete_after_deadline" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$followup_complete_after_deadline}</a>','followup_complete_after_deadline');
		$grid->js('click')->_selector('.followup_complete_after_deadline')->univ()->frameURL('FollowUp Completed after deadline',[$this->app->url('./followup_complete_after_deadline'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// submitted format
		$grid->addFormatter('submitted_followup','template')
			->setTemplate('<a href="#" class="submitted_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$submitted_followup}</a>','submitted_followup');
		$grid->js('click')->_selector('.submitted_followup')->univ()->frameURL('Submitted FollowUp',[$this->app->url('./submitted_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// rejected task format
		$grid->addFormatter('rejected_followup','template')
			->setTemplate('<a href="#" class="rejected_followup" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$rejected_followup}</a>','rejected_followup');
		$grid->js('click')->_selector('.rejected_followup')->univ()->frameURL('Rejected FollowUp',[$this->app->url('./rejected_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

	}

	function page_total_followup(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'totaltask1']);
				$model->addCondition(
							$model->dsql()->orExpr()
  								->where('assign_to_id',$employee_id)
  								->where('created_by_id',$employee_id))
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_self_followup(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'selftask1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('created_by_id',$employee_id)
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
	function page_pending_followup(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'pendingtask1']);
				$model->addCondition(
							$model->dsql()->orExpr()
  								->where('assign_to_id',$employee_id)
  								->where('created_by_id',$employee_id)
  							)
						->addCondition('status','Pending')
						->addCondition('created_at','>=',$from_date)
						->addCondition('created_at','<',$this->api->nextDate($to_date))

				// ->addCondition('assign_to_id',$employee_id)
				// ->addCondition('created_by_id',$employee_id)
				// ->addCondition('created_at','>=',$from_date)
				// ->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','created_by','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
	function page_pending_for_receiving(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'pendingtask1']);
				$model->addCondition(
							$model->dsql()->orExpr()
  								->where('received_at',null)
  								->where('received_at','>','created_at')
  							)
						->addCondition('assign_to_id',$employee_id)
						->addCondition('status','Assigned')
						->addCondition('created_at','>=',$from_date)
						->addCondition('created_at','<',$this->api->nextDate($to_date))

				// ->addCondition('assign_to_id',$employee_id)
				// ->addCondition('created_by_id',$employee_id)
				// ->addCondition('created_at','>=',$from_date)
				// ->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','created_by','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
	function page_inProgress_followup(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'pendingtask1']);
				$model->addCondition(
							$model->dsql()->orExpr()
  								->where('assign_to_id',$employee_id)
  								->where('created_by_id',$employee_id)
  							)
						->addCondition('status','Inprogress')
						->addCondition('created_at','>=',$from_date)
						->addCondition('created_at','<',$this->api->nextDate($to_date))

				// ->addCondition('assign_to_id',$employee_id)
				// ->addCondition('created_by_id',$employee_id)
				// ->addCondition('created_at','>=',$from_date)
				// ->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','created_by','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
	function page_overdue_followup(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$task =  $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'employee_assign_to_assigntask']);
			$task->addCondition('status',['Pending','Inprogress','Assigned','Submitted']);
		   //  	 	->addCondition($task->dsql()->orExpr()
		   //  		->where('assign_to_id',$employee_id)
		   //  		->where($task->dsql()->andExpr()
					// ->where('created_by_id',$employee_id)
					// ->where('assign_to_id',null)));
			$task->addCondition('assign_to_id',$employee_id);			
			$task->addCondition('deadline','<',$this->app->now);			
			$task->addCondition('status','<>','Completed');
			$task->addCondition('created_at','>=',$from_date);
			$task->addCondition('created_at','<',$this->api->nextDate($to_date));
					;		
			$grid = $this->add('xepan\hr\Grid');
			$grid->setModel($task,['task_name','created_by','assign_to_','description','starting_date','deadline','estimate_time','status','priority','received_at','comment_count']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_followup_assigned_to_me(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'taskassigntome1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('created_by_id','<>',$employee_id)
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_followup_assigned_by_me(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'taskassigntome1'])
				->addCondition('assign_to_id','<>',$employee_id)
				->addCondition('created_by_id',$employee_id)
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_pending_for_approval(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'taskassigntome1'])
				->addCondition('created_by_id',$employee_id)
				->addCondition('status','Submitted')
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_followup_complete_in_deadline(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('task_complete_in_deadline',true)
				->addCondition('completed_at','>=',$from_date)
				->addCondition('completed_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);

	}

	function page_followup_complete_after_deadline(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('task_complete_in_deadline',false)
				->addCondition('completed_at','>=',$from_date)
				->addCondition('completed_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','deadline','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
		
	}

	function page_submitted_followup(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('submitted_at','>=',$from_date)
				->addCondition('submitted_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_rejected_followup(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('rejected_at','>=',$from_date)
				->addCondition('rejected_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
}