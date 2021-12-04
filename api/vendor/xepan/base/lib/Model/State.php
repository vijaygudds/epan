<?php

namespace xepan\base;

class Model_State extends \xepan\base\Model_Table{

	public $table="state";
	public $acl=true;
	public $status=[
	'Active',
	'InActive'
	];
	public $actions=[
		'Active'=>['view','edit','delete','deactivate'],
		'InActive'=>['view','edit','delete','activate']
	];

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Country','country_id');
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue(@$this->app->employee->id);

		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		
		$this->addField('name');		
		$this->addField('type');
		$this->addField('abbreviation');
		$this->addCondition('type','State');
		$this->addHook('beforeDelete',$this);

		$this->addExpression('country_status')->set($this->refSQL('country_id')->fieldQuery('status'));

		$this->is([
				'name|to_trim|required|unique_in_epan'
			]);
	}

	function beforeDelete($m){
		if($m['name'] === 'All'){
			throw new \Exception("Cannot delete states together, please delete states individually");
		}		
	}

	//activate State
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("State : '".$this['name']."' now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate State
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("State : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}
}