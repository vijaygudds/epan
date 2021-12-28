<?php

namespace xepan\marketing;

class Model_LeadSubCategory extends \xepan\base\Model_Table{
	public $table = "lead_subcategory";

	public $status=[];
	public $actions=[
		'*'=>[
			'add',
			'view',
			'edit',
			'delete'
		]
	];
	public $acl=false;

	function init(){
		parent::init();
		
		$this->addField('created_by_id')->system(true)->defaultValue($this->app->employee->id);
		$this->addField('name');
		$this->hasOne('xepan\marketing\LeadCategory','category_id');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
	}	
}
