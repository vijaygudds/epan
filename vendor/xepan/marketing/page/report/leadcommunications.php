<?php

/**
 *
 *  
 */
namespace xepan\marketing;
class page_report_leadcommunications extends \xepan\base\Page{

	public $title = "Lead Communications Report`s";

	function init(){
		parent::init();
		$lead_id = $this->app->stickyGET('lead_id');
		$from_date = $this->app->stickyGET('from_date')?:$this->app->today;
		$to_date = $this->app->stickyGET('to_date')?:$this->app->today;
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelsCoppalsible(true)
			->layout([
				'date_range'=>'Filter~c1~2',
				'lead'=>'c2~3',
				// 'communication_date'=>'c3~2',
				// 'communication_with'=>'c4~2',
				// 'call_from'=>'c5~2',
				// 'call_to'=>'c6~2',
				// 'description'=>'c7~2',
				'FormButtons~&nbsp;'=>'c6~2'
			]);

		$date = $form->addField('DateRangePicker','date_range');
		$set_date = $from_date." to ".$to_date;
		$date->set($set_date);

		$lead_field = $form->addField('xepan\base\Basic','lead');
		$lead_field->setModel('xepan\marketing\Model_Lead')->addCondition('status','Active');

		$form->addSubmit('Get Details')->addClass('btn btn-primary');

		$communication_m = $this->add('xepan\communication\Model_Communication',['from_date'=>$from_date,'to_date'=>$to_date]);
		$communication_m->addCondition('communication_type','<>','AbstractMessage');

		
		if($_GET['filter']){
			if($lead_id){
				// throw new \Exception($lead_id, 1);
				
				$communication_m->addCondition('to_id',$lead_id);
			}
			if($from_date){
				$communication_m->from_date = $from_date;
				$communication_m->addCondition('created_at','>=',$from_date);
			}
			if($to_date){
				$communication_m->to_date = $to_date;
				$communication_m->addCondition('created_at','<',$this->api->nextDate($to_date));
			}
				
		}else{
			$communication_m->addCondition('id',-1);
		}	

		$grid = $this->add('xepan\hr\Grid');
		$grid->setModel($communication_m,array('from','to','created_at','to_contact_str','title','description'));
		$grid->addPaginator(50);
		$grid->addHook('formatRow',function($g){
			$g->current_row_html['description'] = $g->model['description'];
			// $g->current_row_html['description']= '<a href="javascript:void(0)" onclick="'.$g->js()->univ()->newWindow($this->app->url('xepan_communication_report_msg',['communication_id'=>$g->model['id']])).'"><span class="btn btn-success">View Message</span></a>';
		});

		if($form->isSubmitted()){
			$grid->js()->reload(
							[
								'lead_id'=>$form['lead'],
								'from_date'=>$date->getStartDate()?:0,
								'to_date'=>$date->getEndDate()?:0,
								'filter'=>1
							]
				)->execute();
		}
	}	
}