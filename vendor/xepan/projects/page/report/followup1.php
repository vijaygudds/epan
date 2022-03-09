<?php

namespace xepan\projects;

class page_report_followup1 extends \xepan\base\Page{

	public $title = "Employee UpComing FollowUp";

	function page_index(){
		// parent::init();
		
		// sticky get the variable
		$start_date = $this->app->stickyGET('start_date');
		$end_date = $this->app->stickyGET('end_date');
		$department_id = $this->app->stickyGET('department_id');
		$employee_id = $this->app->stickyGET('employee_id');

		// setting up from and to date
		if(!$start_date)
			$start_date = $this->app->today;
		if(!$end_date)
			$end_date = date("Y-m-d",strtotime(date("Y-m-d",strtotime($this->app->today)). " +7 DAY"));

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

		$date = $form->addField('DateRangePicker','date_range')->setStartDate($start_date)
                ->setEndDate($end_date)
                ->getFutureDatesSet()
                ->getBackDatesSet(false);
		$set_date = $this->app->today." to ".$this->app->today;
		if($start_date){
			$set_date = $start_date." to ".$end_date;
			$date->set($set_date);
		}

		$emp_field = $form->addField('xepan\base\Basic','employee');
		$emp_field->setModel('xepan\projects\Model_Employee')->addCondition('status','Active');

		$dept_field = $form->addField('DropDown','department')
				->setEmptyText('Please Select Department');
		$dept_field->setModel('xepan\hr\Model_Department');
		$form->addSubmit('Get Details');
		
		// adding model
		$employee_followup = $this->add('xepan\projects\Model_EmployeeFollowup',['start_date'=>$start_date,'end_date'=>$end_date]);
		$employee_followup->addCondition('status','Active');
		$employee_followup->addCondition('upComing_followup','>',0);
		
		if($start_date){
			$employee_followup->start_date = $start_date;
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
		$grid->setModel($employee_followup,['name','upComing_followup']);

		$grid->add('misc\Export',['export_fields'=>['name','upComing_followup']]);
		// handling form submission
		if($form->isSubmitted()){
			$grid->js()->reload(
							[
								'employee_id'=>$form['employee'],
								'department_id'=>$form['department'],
								'start_date'=>$date->getStartDate()?:0,
								'end_date'=>$date->getEndDate()?:0
							]
						)->execute();
		}

		$grid->addPaginator($ipp=100);
		//virtual page formats for
		// upComing_followup format
		$grid->addFormatter('upComing_followup','template')
			->setTemplate('<a href="#" class="upComing_followup" data-employee_id="{$id}" data-from_date="'.$start_date.'" data-to_date="'.$end_date.'">{$upComing_followup}</a>','upComing_followup');
		$grid->js('click')->_selector('.upComing_followup')->univ()->frameURL('Employee Total FollowUp',[$this->app->url('./upComing_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);

		// self task format
		// $grid->addFormatter('self_followup','template')
		// 	->setTemplate('<a href="#" class="self_followup" data-employee_id="{$id}" data-from_date="'.$start_date.'" data-to_date="'.$end_date.'">{$self_followup}</a>','self_followup');
		// $grid->js('click')->_selector('.self_followup')->univ()->frameURL('Employee Self FollowUp',[$this->app->url('./self_followup'),'employee_id'=>$grid->js()->_selectorThis()->data('employee_id'),'from_date'=>$grid->js()->_selectorThis()->data('from_date'),'to_date'=>$grid->js()->_selectorThis()->data('to_date')]);


	}

	function page_upComing_followup(){
		$from_date = $_GET['from_date'];
		$to_date = $_GET['to_date'];
		$employee_id = $_GET['employee_id'];		

		$grid = $this->add('xepan\base\Grid');
		$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'totaltask1']);
				$model->addCondition(
							$model->dsql()->orExpr()
  								->where('assign_to_id',$employee_id)
  								->where('created_by_id',$employee_id))
				->addCondition('starting_date','>=',$from_date)
				->addCondition('starting_date','<',$this->api->nextDate($to_date))
				;
		$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
		$grid->addPaginator($ipp=25);
		$grid->addQuickSearch(['task_name']);
	}

	// function page_self_followup(){

	// 	$from_date = $_GET['from_date'];
	// 	$to_date = $_GET['to_date'];
	// 	$employee_id = $_GET['employee_id'];		

	// 	$grid = $this->add('xepan\base\Grid');
	// 	$model = $this->add('xepan\projects\Model_FollowUp',['table_alias'=>'selftask1'])
	// 			->addCondition('assign_to_id',$employee_id)
	// 			->addCondition('created_by_id',$employee_id)
	// 			->addCondition('created_at','>=',$from_date)
	// 			->addCondition('created_at','<',$this->api->nextDate($to_date))
	// 			;
	// 	$grid->setModel($model,['task_name','assign_to','created_at','starting_date','status']);
	// 	$grid->addPaginator($ipp=25);
	// 	$grid->addQuickSearch(['task_name']);
	// }
}