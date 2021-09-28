<?php

namespace xepan\base;

class Model_Branch extends \xepan\base\Model_Table{
	public $table = "branch";
	public $status=['Active','InActive'];
	
	public $actions = [
		'Active' => ['view','edit','delete','deactivate'],
		'InActive' => ['view','edit','delete','activate']
	];

	public $acl_type = "Branch";

	function init(){
		parent::init();

		$this->hasOne('xepan\base\ContactCreatedBY','created_by_id')->defaultValue(@$this->app->employee->id)->system(true);
		$this->addField('name')->sortable(true);
		$this->addField('status')->defaultValue('Active');

		$this->addExpression('type')->set('"Branch"');
		$this->is([
				'name|unique|to_trim|required'
			]);
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Branch : '".$this['name']."' Acivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->saveAndUnload();
	}

	function deactivate(){
		$this['status'] = 'InActive';
		$this->app->employee
            ->addActivity("Branch : '".$this['name']."'  has been deactivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('activate','InActive',$this);
		$this->saveAndUnload();
	}

}