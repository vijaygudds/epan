<?php

namespace xepan\communication;

class page_report_employeemsgcommunication extends \xepan\base\Page{

	public $title = "Employee Communication Message Reports";
	public $sub_type_1_fields;
	public $sub_type_1_norm_unnorm_array=[];
	public $sub_type_2_fields;
	public $sub_type_2_norm_unnorm_array=[];
	public $sub_type_3_fields;
	public $sub_type_3_norm_unnorm_array=[];
	public $communication_fields;
	public $communication_type_value = ['Call'=>'Call','Meeting'=>'Meeting','TeleMarketing'=>'TeleMarketing'];
	public $config_m;

	function init(){
		parent::init();
		$from_date = $this->app->stickyGET('from_date');
		$to_date = $this->app->stickyGET('to_date');
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~3',
				'employee'=>'c2~3',
				'department'=>'c3~3',
				'FormButtons~&nbsp;'=>'c4~3'
			]);
		$department = $this->app->stickyGET('department');

		$post_model = $this->app->employee->ref('post_id');	

		$date = $form->addField('DateRangePicker','date_range');
		$set_date = $this->app->today." to ".$this->app->today;
		if($from_date){
			$set_date = $from_date." to ".$to_date;
			$date->set($set_date);	
		}

		$emp_field = $form->addField('xepan\base\Basic','employee');
		$emp_field->setModel('xepan\projects\Model_EmployeeCommunicationActivity');
		$dept_field = $form->addField('DropDown','department')->setEmptyText('Please Select Department');
		$model_department = $this->add('xepan\hr\Model_Department');
		$form->addSubmit('Get Details');	

		$employee_comm = $this->add('xepan\projects\Model_EmployeeCommunicationActivity',['from_date'=>$from_date?:$this->app->today,'to_date'=>$to_date?:$this->api->nextDate($this->app->today)]);

		switch ($post_model['permission_level']) {
			case "Department":
				$model_department->addCondition('id',$this->app->employee['department_id']);
				$dept_field->set($this->app->employee['department_id']);
				$dept_field->setAttr('disabled',true);
				$department = $this->app->employee['department_id'];

				$employee_comm->addCondition('department_id',$this->app->employee['department_id']);
				break;
			case ($post_model['permission_level'] == 'Individual' || $post_model['permission_level'] == 'Sibling'):
				$model_department->addCondition('id',$this->app->employee['department_id']);
				$dept_field->set($this->app->employee['department_id']);
				$dept_field->setAttr('disabled',true);
				$department = $this->app->employee['department_id'];

				$employee_comm->addCondition('id',$this->app->employee->id);
				$emp_field->set($this->app->employee->id);
				$emp_field->other_field->setAttr('disabled',true);
				$emp_id = $this->app->employee->id;
				break;
		}
		$dept_field->setModel($model_department);

		if($_GET['from_date']){
			$employee_comm->from_date = $_GET['from_date'];
		}		
		if($_GET['employee_id']){
			$employee_comm->addCondition('id',$_GET['employee_id']);
		}
		if($_GET['department_id']){
			$employee_comm->addCondition('department_id',$_GET['department_id']);
		}

		$grid = $this->add('xepan\hr\Grid',null,null,['view/report/employee-comm-msg-report']);
		$grid->setModel($employee_comm);
		$grid->addPaginator(50);
		$grid->add("misc\Export");

		/*total reply want Message*/
		$send_msg= $this->add('VirtualPage')->set(function($page){
			$employee_id = $this->app->stickyGET('employee_id');
			$communication = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['from_date']))
						->addCondition('created_by_id',$employee_id);

			$msg = $page->add('xepan\base\Model_Contact_CommunicationReadMessage')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']))
						->addCondition('from_id',$employee_id)
						// ->addCondition('communication_id',$communication['id'])
						// ->addCondition('reply_need_by_me',true)
						// ->count();
					;		
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($msg,['from','to_contact','created_at','department','message']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['message']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['communication_id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_reply_want',function($g,$f)use($send_msg){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Message Reply To Me',$g->api->url($send_msg->getURL(),array('employee_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});

		$grid->addFormatter('total_reply_want','total_reply_want');

		/*total relpy given message`s*/
		$send_email= $this->add('VirtualPage')->set(function($page){

			$employee_id = $this->app->stickyGET('employee_id');
			$communication = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['from_date']))
						->addCondition('created_by_id',$employee_id);

			$msg = $page->add('xepan\base\Model_Contact_CommunicationReadMessage')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']))
						->addCondition('contact_id',$employee_id)
						// ->addCondition('communication_id',$communication['id'])
						// ->addCondition('reply_need_by_me',true)
						// ->count();
					;
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($msg,['from','to_contact','created_at','department','message']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['message']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['communication_id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_reply_given',function($g,$f)use($send_email){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Message Reply By Me',$g->api->url($send_email->getURL(),array('employee_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});
		$grid->addFormatter('total_reply_given','total_reply_given');

		/*total read message`s*/
		$read_msg= $this->add('VirtualPage')->set(function($page){

			$employee_id = $this->app->stickyGET('employee_id');
			$communication = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['from_date']))
						->addCondition('created_by_id',$employee_id);

			$msg = $page->add('xepan\base\Model_Contact_CommunicationReadEmail')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']))
						->addCondition('contact_id',$employee_id)
						// ->addCondition('communication_id',$communication['id'])
						->addCondition('is_read',true)
						// ->count();
					;
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($msg);//,['from','contact','created_at','department','message']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['message']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['communication_id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_read_msg',function($g,$f)use($read_msg){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Read Message',$g->api->url($read_msg->getURL(),array('employee_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});
		$grid->addFormatter('total_read_msg','total_read_msg');

		/*total UNread message`s*/
		$unread_msg= $this->add('VirtualPage')->set(function($page){

			$employee_id = $this->app->stickyGET('employee_id');
			$communication = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['from_date']))
						->addCondition('created_by_id',$employee_id);

			$msg = $page->add('xepan\base\Model_Contact_CommunicationReadEmail')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']))
						->addCondition('contact_id',$employee_id)
						// ->addCondition('communication_id',$communication['id'])
						->addCondition('is_read',false)
						// ->count();
					;
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($msg);//,['from','contact','created_at','department','message']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['message']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['communication_id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_unread_msg',function($g,$f)use($unread_msg){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Unread Message',$g->api->url($unread_msg->getURL(),array('employee_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});
		$grid->addFormatter('total_unread_msg','total_unread_msg');

		/*total Send Message*/
		$send_msg= $this->add('VirtualPage')->set(function($page){

			$employee_id = $this->app->stickyGET('employee_id');
			$msg = $page->add('xepan\communication\Model_Communication_MessageSent')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']))
						->addCondition('created_by_id',$employee_id)
						// ->count();
					;		
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($msg,['from','to','title','description','status']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['description']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model->id])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_send_message',function($g,$f)use($send_msg){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Send Message',$g->api->url($send_msg->getURL(),array('employee_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});

		$grid->addFormatter('total_send_message','total_send_message');

		if($form->isSubmitted()){
			

			$form->js()->univ()->redirect($this->app->url(),[
								'employee_id'=>$form['employee'],
								'department_id'=>$form['department'],
								'from_date'=>$date->getStartDate()?:0,
								'to_date'=>$date->getEndDate()?:0
							]
							
						)->execute();
		}

	}

	// function page_index(){


	// 	$emp_id = $this->app->stickyGET('employee_id');
	// 	$this->from_date = $from_date  = $this->app->stickyGET('from_date')?:$this->app->today;
	// 	$this->to_date = $to_date = $this->app->stickyGET('to_date')?:$this->app->today;
	// 	$department = $this->app->stickyGET('department');

	// 	$post_model = $this->app->employee->ref('post_id');
		
	// 	$form = $this->add('Form');
	// 	$form->add('xepan\base\Controller_FLC')
	// 		->makePanelsCoppalsible(true)
	// 		->layout([
	// 			'date_range'=>'Filter~c1~2',
	// 			'employee'=>'c2~3',
	// 			'department'=>'c3~3',
	// 			// 'communication_type'=>'c3~2',
	// 			'FormButtons~&nbsp;'=>'c4~2'
	// 		]);

	// 	$date = $form->addField('DateRangePicker','date_range');
	// 	$set_date = $this->app->today." to ".$this->app->today;
	// 	if($from_date){
	// 		$set_date = $from_date." to ".$to_date;
	// 		$date->set($set_date);
	// 	}
		
	// 	$employee_model = $this->add('xepan\hr\Model_Employee',['title_field'=>'name_with_post'])
	// 						->addCondition('status','Active');
	// 	$employee_model->addExpression('name_with_post')->set(function($m,$q){
	// 		return $q->expr('CONCAT_WS("::",[name],[post],[code])',
	// 					[
	// 						'name'=>$m->getElement('name'),
	// 						'post'=>$m->getElement('post'),
	// 						'code'=>$m->getElement('code')
	// 					]
	// 				);
	// 	});	

	// 	$emp_field = $form->addField('xepan\base\Basic','employee');
				
	// 	$dept_field = $form->addField('xepan\base\DropDown','department');
	// 	$model_department = $this->add('xepan\hr\Model_Department');




	// 	switch ($post_model['permission_level']) {
	// 		case "Department":
	// 			$model_department->addCondition('id',$this->app->employee['department_id']);
	// 			$dept_field->set($this->app->employee['department_id']);
	// 			$dept_field->setAttr('disabled',true);
	// 			$department = $this->app->employee['department_id'];

	// 			$employee_model->addCondition('department_id',$this->app->employee['department_id']);
	// 			break;
	// 		case ($post_model['permission_level'] == 'Individual' || $post_model['permission_level'] == 'Sibling'):
	// 			$model_department->addCondition('id',$this->app->employee['department_id']);
	// 			$dept_field->set($this->app->employee['department_id']);
	// 			$dept_field->setAttr('disabled',true);
	// 			$department = $this->app->employee['department_id'];

	// 			$employee_model->addCondition('id',$this->app->employee->id);
	// 			$emp_field->set($this->app->employee->id);
	// 			$emp_field->other_field->setAttr('disabled',true);
	// 			$emp_id = $this->app->employee->id;
	// 			break;
	// 	}

	// 	$emp_field->setModel($employee_model);
	// 	$dept_field->setModel($model_department);
	// 	$dept_field->setEmptyText('All');
	// 	// grid
	// 	$grid = $this->add('xepan\hr\Grid');
		
	// 	$form->addSubmit('Get Details')->addClass('btn btn-primary');
		
	// 	// record model
	// 	$emp_model = $this->add('xepan\base\Model_Contact_CommunicationReadMessage',['from_date'=>$from_date,'to_date'=>$to_date]);




	// 	if($emp_id){
	// 		$emp_model->addCondition('from_id',$emp_id);
	// 	}
	// 	if($from_date){
	// 		$emp_model->from_date = $this->from_date;
	// 	}
	// 	if($to_date){
	// 		$emp_model->to_date = $this->to_date;
	// 	}
	// 	if($department){
	// 		$emp_model->addCondition('department_id',$department);
	// 	}


		

	// 	$grid->setModel($emp_model);//,$model_field_array);
	// 	$order = $grid->addOrder();
	// 	$grid->addpaginator(10);
	// 	if($form->isSubmitted()){
	// 		$grid->js()->reload(
	// 				[
	// 					'employee_id'=>$form['employee'],
	// 					'from_date'=>$date->getStartDate()?:0,
	// 					'to_date'=>$date->getEndDate()?:0,
	// 					'department'=>$form['department']
	// 				]
	// 		)->execute();
	// 	}

		
	// 	$grid->js(true)->_load('jquery.sparkline.min');

	// }

	

}