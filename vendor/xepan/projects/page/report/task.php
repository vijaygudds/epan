<?php

namespace xepan\projects;

class page_report_task extends \xepan\base\Page{

	public $title = "Employee Task Report";

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
		$employee_task = $this->add('xepan\projects\Model_EmployeeTask',['from_date'=>$from_date,'to_date'=>$to_date]);
		$employee_task->addCondition('status','Active');
		
		if($from_date){
			$employee_task->from_date = $from_date;
		}
		if($employee_id){
			$employee_task->addCondition('id',$employee_id);
		}
		if($department_id){
			$employee_task->addCondition('department_id',$department_id);
		}

		// adding grid
		$grid = $this->add('xepan\hr\Grid');
		$employee_task->setOrder('name','asc');
		$grid->setModel($employee_task,['name','total_task','self_task','task_assigned_to_me','task_assigned_by_me','pending_task','pending_for_receiving','inProgress_task','pending_for_approval','submitted_task','rejected_task','overdue_task','task_complete_in_deadline','task_complete_after_deadline']);

		$grid->add('misc\Export',['export_fields'=>['name','total_task','self_task','task_assigned_to_me','task_assigned_by_me','pending_for_approval','submitted_task','rejected_task','task_complete_in_deadline','task_complete_after_deadline']]);
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
		// total_task format
		$grid->addFormatter('total_task','template')
			->setTemplate('<a href="#" class="total_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$total_task}</a>','total_task');
		$grid->js('click')->_selector('.total_task')->univ()->frameURL('Employee Total Task',[$this->app->url('./total_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// self task format
		$grid->addFormatter('self_task','template')
			->setTemplate('<a href="#" class="self_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$self_task}</a>','self_task');
		$grid->js('click')->_selector('.self_task')->univ()->frameURL('Employee Self Task',[$this->app->url('./self_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// Pending task format
		$grid->addFormatter('pending_task','template')
			->setTemplate('<a href="#" class="pending_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_task}</a>','pending_task');
		$grid->js('click')->_selector('.pending_task')->univ()->frameURL('Employee Pending Task',[$this->app->url('./pending_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		// Pending task format
		$grid->addFormatter('pending_for_receiving','template')
			->setTemplate('<a href="#" class="pending_for_receiving" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_for_receiving}</a>','pending_for_receiving');
		$grid->js('click')->_selector('.pending_for_receiving')->univ()->frameURL('Employee Pending For Receving Task',[$this->app->url('./pending_for_receiving'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// inProgress task format
		$grid->addFormatter('inProgress_task','template')
			->setTemplate('<a href="#" class="inProgress_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$inProgress_task}</a>','inProgress_task');
		$grid->js('click')->_selector('.inProgress_task')->univ()->frameURL('Employee In Progress Task',[$this->app->url('./inProgress_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// inProgress task format
		$grid->addFormatter('overdue_task','template')
			->setTemplate('<a href="#" class="overdue_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$overdue_task}</a>','overdue_task');
		$grid->js('click')->_selector('.overdue_task')->univ()->frameURL('Employee OverDue Task',[$this->app->url('./overdue_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task assign to me format
		$grid->addFormatter('task_assigned_to_me','template')
			->setTemplate('<a href="#" class="task_assigned_to_me" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$task_assigned_to_me}</a>','task_assigned_to_me');
		$grid->js('click')->_selector('.task_assigned_to_me')->univ()->frameURL('Task assign to me',[$this->app->url('./task_assigned_to_me'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task assign by me format
		$grid->addFormatter('task_assigned_by_me','template')
			->setTemplate('<a href="#" class="task_assigned_by_me" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$task_assigned_by_me}</a>','task_assigned_by_me');
		$grid->js('click')->_selector('.task_assigned_by_me')->univ()->frameURL('Task assign by me',[$this->app->url('./task_assigned_by_me'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// pending_for_approval format
		$grid->addFormatter('pending_for_approval','template')
			->setTemplate('<a href="#" class="pending_for_approval" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$pending_for_approval}</a>','pending_for_approval');
		$grid->js('click')->_selector('.pending_for_approval')->univ()->frameURL('Received Task',[$this->app->url('./pending_for_approval'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// task_complete_in_deadline format
		$grid->addFormatter('task_complete_in_deadline','template')
			->setTemplate('<a href="#" class="task_complete_in_deadline" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$task_complete_in_deadline}</a>','task_complete_in_deadline');
		$grid->js('click')->_selector('.task_complete_in_deadline')->univ()->frameURL('Task Completed in deadline',[$this->app->url('./task_complete_in_deadline'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// task_complete_after_deadline format
		$grid->addFormatter('task_complete_after_deadline','template')
			->setTemplate('<a href="#" class="task_complete_after_deadline" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$task_complete_after_deadline}</a>','task_complete_after_deadline');
		$grid->js('click')->_selector('.task_complete_after_deadline')->univ()->frameURL('Task Completed after deadline',[$this->app->url('./task_complete_after_deadline'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// submitted format
		$grid->addFormatter('submitted_task','template')
			->setTemplate('<a href="#" class="submitted_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$submitted_task}</a>','submitted_task');
		$grid->js('click')->_selector('.submitted_task')->univ()->frameURL('Submitted Task',[$this->app->url('./submitted_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);
		
		// rejected task format
		$grid->addFormatter('rejected_task','template')
			->setTemplate('<a href="#" class="rejected_task" data-employee_id="{$id}" data-from_date="'.$from_date.'" data-to_date="'.$to_date.'">{$rejected_task}</a>','rejected_task');
		$grid->js('click')->_selector('.rejected_task')->univ()->frameURL('Rejected Task',[$this->app->url('./rejected_task'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

	}

	function page_total_task(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'totaltask1']);
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

	function page_self_task(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'selftask1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('created_by_id',$employee_id)
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
	function page_pending_task(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'pendingtask1']);
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
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'pendingtask1']);
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
	function page_inProgress_task(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'pendingtask1']);
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
	function page_overdue_task(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$task =  $this->add('xepan\projects\Model_Task',['table_alias'=>'employee_assign_to_assigntask']);
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

	function page_task_assigned_to_me(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'taskassigntome1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('created_by_id','<>',$employee_id)
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_task_assigned_by_me(){

		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'taskassigntome1'])
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
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'taskassigntome1'])
				->addCondition('created_by_id',$employee_id)
				->addCondition('status','Submitted')
				->addCondition('created_at','>=',$from_date)
				->addCondition('created_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_task_complete_in_deadline(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('task_complete_in_deadline',true)
				->addCondition('completed_at','>=',$from_date)
				->addCondition('completed_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);

	}

	function page_task_complete_after_deadline(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('task_complete_in_deadline',false)
				->addCondition('completed_at','>=',$from_date)
				->addCondition('completed_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','deadline','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
		
	}

	function page_submitted_task(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('submitted_at','>=',$from_date)
				->addCondition('submitted_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	function page_rejected_task(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_Task',['table_alias'=>'task_complete_in_deadline1'])
				->addCondition('assign_to_id',$employee_id)
				->addCondition('rejected_at','>=',$from_date)
				->addCondition('rejected_at','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}
}