<?php

namespace xepan\communication;

class page_report_leadcommunication extends \xepan\base\Page{

	public $title = "Lead Communication Reports";
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
		$emp_id = $this->app->stickyGET('employee_id');
		$this->from_date = $from_date  = $this->app->stickyGET('from_date')?:$this->app->today;
		$this->to_date = $to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$department = $this->app->stickyGET('department');

		$post_model = $this->app->employee->ref('post_id');
		// $lead_cat = $this->app->stickyGET('leadcategory');
		// $lead_sub_cat = $this->app->stickyGET('leadsubcategory');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~2',
				'employee'=>'c2~3',
				// 'department'=>'c3~3',
				// 'lead_category'=>'c4~2',
				// 'lead_sub_category'=>'c5~2',
				'FormButtons~&nbsp;'=>'c6~2'
			]);

		// $date = $form->addField('DateRangePicker','date_range');
		// $set_date = $from_date." to ".$to_date;
		// $date->set($set_date);

		$date = $form->addField('DateRangePicker','date_range');
		$set_date = $this->app->today." to ".$this->app->today;
		if($from_date){
			$set_date = $from_date." to ".$to_date;
			$date->set($set_date);
		}

		$employee_model = $this->add('xepan\hr\Model_Employee')//,['title_field'=>'name_with_post'])
							->addCondition('status','Active');
		// $employee_model->addExpression('name_with_post')->set(function($m,$q){
		// 	return $q->expr('CONCAT_WS("::",[name],[post],[code])',
		// 				[
		// 					'name'=>$m->getElement('name'),
		// 					'post'=>$m->getElement('post'),
		// 					'code'=>$m->getElement('code')
		// 				]
		// 			);
		// });
		$emp_field = $form->addField('xepan\base\Basic','employee');
		// $emp_field->setModel('xepan\hr\Model_Employee')->addCondition('status','Active');
		// $dept_field = $form->addField('xepan\base\DropDown','department');
		// $model_department = $this->add('xepan\hr\Model_Department');
		// $row_field = $form->addField('xepan\base\DropDown','communication_row');
		// $row_field->setValueList(['1'=>'1','2'=>'2','3'=>'3','ALL'=>'More then 3']);
		// $row_field->setEmptyText('All');
		// $lead_cat_field = $form->addField('xepan\base\DropDown','lead_category');
		// $lead_cat_field->setModel('xepan\marketing\Model_LeadCategory');
		// $lead_cat_field->setEmptyText('All');
		// $lead_subcat_field = $form->addField('xepan\base\DropDown','lead_sub_category');
		// $lead_subcat_field->setModel('xepan\marketing\Model_LeadSubCategory');
		// $lead_subcat_field->setEmptyText('All');
		// switch ($post_model['permission_level']) {
		// 	case "Department":
		// 		$model_department->addCondition('id',$this->app->employee['department_id']);
		// 		$dept_field->set($this->app->employee['department_id']);
		// 		$dept_field->setAttr('disabled',true);
		// 		$department = $this->app->employee['department_id'];

		// 		$employee_model->addCondition('department_id',$this->app->employee['department_id']);
		// 		break;
		// 	case ($post_model['permission_level'] == 'Individual' || $post_model['permission_level'] == 'Sibling'):
		// 		$model_department->addCondition('id',$this->app->employee['department_id']);
		// 		$dept_field->set($this->app->employee['department_id']);
		// 		$dept_field->setAttr('disabled',true);
		// 		$department = $this->app->employee['department_id'];

		// 		$employee_model->addCondition('id',$this->app->employee->id);
		// 		$emp_field->set($this->app->employee->id);
		// 		$emp_field->other_field->setAttr('disabled',true);
		// 		$emp_id = $this->app->employee->id;
		// 		break;
		// }

		$emp_field->setModel($employee_model);
		// $dept_field->setModel($model_department);
		// $dept_field->setEmptyText('All');

		$form->addSubmit('Get Details')->addClass('btn btn-primary');

		$emp_model = $this->add('xepan\marketing\Model_Lead',['from_date'=>$from_date,'to_date'=>$to_date/*,'communication_row'=>$row*/]);

		// $emp_model->addExpression('emp_department_id')->set(function($m,$q){
		// 	return $this->add('xepan\hr\Model_Employee')->addCondition('id',$this->app->employee->id)->fieldQuery('department_id');
		// });
		$emp_model->addCondition('total_communication','>',0);
		if($emp_id){
			$emp_model->addCondition('created_by_id',$emp_id);
		}
		// if($from_date){
		// 	$emp_model->from_date = $from_date;
		// 	$emp_model->addCondition('created_at','>=',$from_date);
		// }
		// if($to_date){
		// 	$emp_model->to_date = $to_date;
		// 	$emp_model->addCondition('created_at','<',$this->api->nextDate($to_date));
		// }
		// if($department){
		// 	// throw new \Exception($department, 1);
			
		// 	$emp_model->addCondition('department_id',$department);
		// }


		$grid = $this->add('xepan\hr\Grid');//,null,null,['view/report/employee-lead-report-gridview']);
		


		$grid->setModel($emp_model,['unique_name','total_communication','last_communication']);
		// $grid->add('misc/Export',['export_fields'=>['name','total_lead_created','total_lead_assign_to','total_followup','open_opportunity','qualified_opportunity','needs_analysis_opportunity','quoted_opportunity','negotiated_opportunity','win_opportunity','loss_opportunity']]);
		$grid->addPaginator(1000);

		// $emp_model->_dsql()->group('type');


		/*total reply want Message*/
		// $tc= $this->add('VirtualPage')->set(function($page){
		// 	$from_id = $this->app->stickyGET('from_contact_id');
		// 	$to_id = $this->app->stickyGET('to_contact_id');
		// 	$model  = $this->add('xepan\communication\Model_Communication')
		// 				->addCondition('communication_type','<>','AbstractMessage')
		// 				->addCondition('created_at','>=',$_GET['from_date'])
		// 				->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']));
		// 	// $model->addCondition(
		// 	// 				$model->dsql()->orExpr()
  // 	// 							->where('id',$from_id)
  // 	// 							->where('id',$to_id));
		// 	$model->addCondition('id','<>',$this->app->employee->id);
		// 	$model->setOrder('created_at','desc');
			
		// 	$grid = $page->add('xepan\hr\Grid');
		// 	$grid->setModel($model);//,['from','to','created_at','description']);
		// 	$grid->addPaginator(100);
			
		// 	// $grid->addHook('formatRow',function($g){
		// 	// 	// $g->current_row_html['message'] = $g->model['message'];
		// 	// $g->current_row_html['description']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['id']])).'"><span class="btn btn-success">View Message</span></a>';
		// 	// });
		// });

		
		$tl= $this->add('VirtualPage')->set(function($page){
			$from_id = $this->app->stickyGET('from_contact_id');
			$to_id = $this->app->stickyGET('to_contact_id');
			// throw new \Exception($_GET['from_contact_id'], 1);
			
			$model  = $this->add('xepan\communication\Model_Communication');
			$model->addCondition($model->dsql()->orExpr()
								->where('from_id',$_GET['from_contact_id'])
								->where('to_id',$_GET['from_contact_id'])
							)
						->addCondition('created_at','>=',$this->from_date)
						->addCondition('created_at','<',$this->api->nextDate($this->to_date));
			// $model->addCondition('type','<>','AbstractMessage');
			// $model->addCondition('id',$_GET['to_contact_id']);
			// $model->addCondition('id','<>',$this->app->employee->id);
			$model->setOrder('created_at','desc');
			
			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($model,['from','to','created_at','title','description','created_by','generated_by','communication_type','sub_type','calling_status','sub_type_3','status','communication_for','communication_subfor']);
			$grid->addPaginator(50);
			
			$grid->addHook('formatRow',function($g){
				$g->current_row_html['description'] = $g->model['description'];
			// $g->current_row_html['description']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_communication',function($g,$f)use($tl){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Lead',$g->api->url($tl->getURL(),array('from_contact_id'=>$g->model['id'],'to_contact_id'=>$g->model['to_id'],'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});

		$grid->addFormatter('total_communication','total_communication');




		// $grid->addMethod('format_total_lead',function($g,$f)use($tl){
		// 		// VP defined at top of init function
		// 	$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Lead',$g->api->url($tl->getURL(),array('from_contact_id'=>$g->model['from_id'],'to_contact_id'=>$g->model['to_id'],'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		// });
		// $grid->addFormatter('total_lead','total_lead');

		if($form->isSubmitted()){

			$grid->js()->reload(
							[
								'employee_id'=>$form['employee'],
								'from_date'=>$date->getStartDate()?:0,
								'to_date'=>$date->getEndDate()?:0,
								// 'department'=>$form['department']?:0,
								'row'=>$form['communication_row']?:0,
							]
				)->execute();
		}		

	}
}