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
		$from_date = $this->app->stickyGET('from_date')?:$this->app->today;
		$to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$row = $this->app->stickyGET('row');
		$lead_cat = $this->app->stickyGET('leadcategory');
		$lead_sub_cat = $this->app->stickyGET('leadsubcategory');

		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~2',
				'employee'=>'c2~3',
				'communication_row'=>'c3~2',
				// 'lead_category'=>'c4~2',
				// 'lead_sub_category'=>'c5~2',
				'FormButtons~&nbsp;'=>'c6~2'
			]);

		$date = $form->addField('DateRangePicker','date_range');
		$set_date = $from_date." to ".$to_date;
		$date->set($set_date);

		$emp_field = $form->addField('xepan\base\Basic','employee');
		$emp_field->setModel('xepan\hr\Model_Employee')->addCondition('status','Active');

		$row_field = $form->addField('xepan\base\DropDown','communication_row');
		$row_field->setValueList(['1'=>'1','2'=>'2','3'=>'3']);
		$row_field->setEmptyText('All');
		// $lead_cat_field = $form->addField('xepan\base\DropDown','lead_category');
		// $lead_cat_field->setModel('xepan\marketing\Model_LeadCategory');
		// $lead_cat_field->setEmptyText('All');
		// $lead_subcat_field = $form->addField('xepan\base\DropDown','lead_sub_category');
		// $lead_subcat_field->setModel('xepan\marketing\Model_LeadSubCategory');
		// $lead_subcat_field->setEmptyText('All');

		$form->addSubmit('Get Details')->addClass('btn btn-primary');

		$emp_model = $this->add('xepan\marketing\Model_Lead',['from_date'=>$from_date,'to_date'=>$to_date,'communication_row'=>$row]);

		// $emp_j = $emp_model->join('employee','created_by_id');
		// $emp_j->addField('department_id');

		if($emp_id){
			$emp_model->addCondition('created_by_id',$emp_id);
		}
		if($from_date){
			$emp_model->from_date = $from_date;
			$emp_model->addCondition('created_at','>=',$from_date);
		}
		if($to_date){
			$emp_model->to_date = $to_date;
			$emp_model->addCondition('created_at','<',$this->api->nextDate($to_date));
		}
		// if($department){
		// 	$emp_model->addCondition('department_id',$department);
		// }
		// if($lead_cat){
		// 	$emp_model->addCondition('lead_cat_id',$lead_cat);
		// }
		// if($lead_sub_cat){
		// 	$emp_model->addCondition('lead_cat_sub_id',$lead_sub_cat);
		// }


		$grid = $this->add('xepan\hr\Grid');//,null,null,['view/report/employee-lead-report-gridview']);
		$grid->setModel($emp_model,['created_by','name','total_communication','address','city','pin_code','created_at','emails_str','contacts_str','last_communication']);
		$grid->add('misc/Export',['export_fields'=>['name','total_lead_created','total_lead_assign_to','total_followup','open_opportunity','qualified_opportunity','needs_analysis_opportunity','quoted_opportunity','negotiated_opportunity','win_opportunity','loss_opportunity']]);
		$grid->addPaginator(50);

		/*total reply want Message*/
		$tc= $this->add('VirtualPage')->set(function($page){
			$lead = $this->app->stickyGET('lead_id');
			$model  = $this->add('xepan\communication\Model_Communication')
						->addCondition('created_at','>=',$_GET['from_date'])
						->addCondition('created_at','<',$this->api->nextDate($_GET['to_date']));
			$model->addCondition(
							$model->dsql()->orExpr()
  								->where('from_id',$lead)
  								->where('to_id',$lead));
			$model->setOrder('created_at','desc');

			$grid = $page->add('xepan\hr\Grid');
			$grid->setModel($model,['from','to','created_at','description']);
			$grid->addPaginator(50);
			$grid->addHook('formatRow',function($g){
				// $g->current_row_html['message'] = $g->model['message'];
			$g->current_row_html['description']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['id']])).'"><span class="btn btn-success">View Message</span></a>';
			});
		});

		$grid->addMethod('format_total_communication',function($g,$f)use($tc){
				// VP defined at top of init function
			$g->current_row_html[$f]= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->frameURL('Total Communication',$g->api->url($tc->getURL(),array('lead_id'=>$g->model->id,'from_date'=>$g->model->from_date,'to_date'=>$g->model->to_date))).'">'.$g->current_row[$f].'</a>';
		});

		$grid->addFormatter('total_communication','total_communication');



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