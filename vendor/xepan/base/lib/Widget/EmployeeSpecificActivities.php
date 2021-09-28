<?php

namespace xepan\base;

class Widget_EmployeeSpecificActivities extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->addClass('panel panel-default panel-body');

		$this->report->enableFilterEntity('date_range');
		$this->report->enableFilterEntity('Employee');

		$contact_id = $this->app->employee->id;
		if(isset($this->report->employee))
			$contact_id = $this->report->employee;
		
		$from_date = $this->app->now;
		$to_date = $this->app->now;
		if(isset($this->report->start_date)){
			$from_date = $this->report->start_date;	
			$to_date = 	$this->report->end_date;
		}

		
		$related_person_id = $this->app->stickyGET('related_person_id');
		$department_id = $this->app->stickyGET('department_id');
		$communication_type = $this->app->stickyGET('communication_type');

		$this->form = $form = $this->add('Form');
		$this->form->addField('xepan\base\Basic','related_person','Related Person')->setModel($this->add('xepan\base\Model_Contact'));
		$dept_field = $this->form->addField('Dropdown','department','Department');
		$dept_field->setModel($this->add('xepan\hr\Model_Department'));
		$dept_field->setEmptyText('Please select');
		$this->form->addField('Dropdown','communication_type','Communication Type')->setValueList(['Email'=>'Email','Call'=>'Call','TeleMarketing'=>'TeleMarketing','Personal'=>'Personal','SMS'=>'SMS'])->setEmptyText('Please select a communication type');
		$this->form->addSubmit("FILTER")->addClass('btn-primary');

		$this->activity_view = $this->add('xepan\base\View_Activity',['paginator_count'=>10,'self_activity'=>false,'pass_descendants_condition'=>'yes','from_date'=>$from_date,'to_date'=>$to_date,'contact_id'=>$contact_id,'related_person_id'=>$related_person_id,'department_id'=>$department_id,'communication_type'=>$communication_type]);	
	}

	function recursiveRender(){

		if($this->form->isSubmitted()){				
			$this->form->js(null,$this->activity_view->js()->reload([
							'related_person_id'=>$this->form['related_person'],
							'document_id'=>$this->form['document'],
							'department_id'=>$this->form['department'],
							'communication_type'=>$this->form['communication_type'],
							'self_activity'=>$this->form['show_my_activity']
						]))->univ()->successMessage('wait ... ')->execute();

		}
		return parent::recursiveRender();
	}
}