<?php

namespace xepan\base;

class Model_Country extends \xepan\base\Model_Table{

	public $table="country";
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
		
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue(@$this->app->employee->id);

		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');

		$this->addField('name');
		$this->addField('iso_code');

		$this->addField('type');
		$this->addCondition('type','Country');
		
		$this->hasMany('xepan\base\State','country_id');
		
		$this->addHook('afterInsert',$this);
		$this->addHook('beforeDelete',$this);

		$this->is([
			'name|to_trim|required|unique_in_epan'
		]);
	}

	function afterInsert($model,$id){
		$state = $this->add('xepan\base\Model_State');
		$state['country_id'] = $id;
		$state['created_by_id'] = $this->app->employee->id;
		$state['name'] = 'All';		
		$this['type'] = 'State';
		$this['abbreviation'] = 'All';
		$state->save();
	}

	function beforeDelete($m){
		if($m['name'] === 'All'){
			throw new \Exception("Cannot delete countries together, please delete countries individually");
		}		
	}

	//activate Country
	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Country : '".$this['name']."' now active", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	//deactivate Country
	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Country : '". $this['name'] ."' has been deactivated", null /*Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_commerce_customerdetail&contact_id=".$this->id."")
            ->notifyWhoCan('activate','InActive',$this);
		return $this->save();
	}
}