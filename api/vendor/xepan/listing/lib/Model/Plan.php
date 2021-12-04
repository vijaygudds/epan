<?php

namespace xepan\listing;

class Model_Plan extends \xepan\base\Model_Table{
	
	public $table = 'xepan_listing_plan';

	public $status = ['Active','Inactive'];
	public $actions = [
					'Active'=>['view','associate_with_user','deactivate','edit','delete'],
					'Inactive'=>['view','associate_with_user','activate','edit','delete']
				];

	public $acl_type = "xepan_listing_plan";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\List','list_id');
		$this->addField('name');
		$this->addField('valid_for_days');
		$this->addField('number_of_list_detail_allowed');
		$this->addField('status')->enum($this->status)->defaultValue('Active');

		$this->is(['name|to_trim|required']);
		$this->add('dynamic_model\Controller_AutoCreator');	
	}

	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}

	function page_associate_with_user($page){
		$m = $this->add('xepan\listing\Model_ContactPlanAssociation');
		$m->addCondition('plan_id',$this->id);
		$m->addCondition('list_id',$this['list_id']);
		$crud = $page->add('xepan\base\CRUD');
		$crud->setModel($m);
		$crud->grid->addPaginator($ipp = 25);
	}
}