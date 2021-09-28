<?php

namespace xepan\listing;

class Model_ContactPlanAssociation extends \xepan\base\Model_Table{
	
	public $table = 'xepan_listing_contact_plan_association';

	public $status = ['All'];
	public $actions = [
				'All'=>['view','edit','delete']
			];
	public $acl_type = "xepan_listing_contact_plan_association";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\List','list_id');
		$this->hasOne('xepan\listing\Plan','plan_id');
		$this->hasOne('xepan\listing\Contact','contact_id');

		$this->addField('number_of_list_detail_allowed')->type('number')->system(true);
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('start_date')->type('datetime');
		$this->addField('end_date')->type('datetime');

		$this->addExpression('plan_status')->set(function($m,$q){
			return $m->add('xepan\listing\Model_Plan')->addCondition('id',$m->getElement('plan_id'))->fieldQuery('status');
		});
		// $this->add('dynamic_model\Controller_AutoCreator');
		$this->addHook('beforeSave',[$this,'updatePlanValue']);

		$this->is([
				'plan_id|required',
				'contact_id|required'
			]);
	}

	function updatePlanValue(){
		
		if(!$this->loaded()){
			$plan = $this->add('xepan\listing\Model_Plan');
			$plan->load($this['plan_id']);

			$this['created_at'] = $this->app->now;
			if(!$this['start_date'])
				$this['start_date'] = $this->app->now;
			if(!$this['end_date'])
				$this['end_date'] = date('Y-m-d H:i:s', strtotime($this->app->now. ' + '.$plan['valid_for_days'].' days'));
			
			$this['number_of_list_detail_allowed'] = $plan['number_of_list_detail_allowed'];
		}
	}

}