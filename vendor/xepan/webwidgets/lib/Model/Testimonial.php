<?php

namespace xepan\webwidgets;

class Model_Testimonial extends \xepan\base\Model_Table{
	public $table = "testimonial";
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
		
		$this->hasOne('xepan\hr\Model_Employee','created_by_id')->defaultValue($this->app->employee->id);
		
		$this->addField('customer_name');
		$this->addField('title');
		$this->addField('description')->type('text');
		
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
		$this->addField('type');
		$this->addField('status')->enum($this->status)->defaultValue('Active');

		$this->addCondition('type','Testimonial');
		
		$this->add('xepan\filestore\Field_File','file_id');

		$this->addExpression('thumb_url')->set(function($m,$q){
			return $q->expr('[0]',[$m->getElement('file')]);
		});
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("Testimonial : '".$this['title']."' Activated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("Testimonial : '".$this['title']."' is deactivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,null)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}
}