<?php

namespace xepan\marketing;

class Model_Communication_For extends \xepan\base\Model_Table{
	public $table='communication_for';

	public $status=[

		/*'Active',
		'InActive'*/
	];
	public $actions=['All'=>['view','edit','delete','sub_for'/*,'delete_all_lead'*/]];
	public $for_type = "CommunicationFor";

	// public $addOtherInfo=false;
	// public $otherInfoFields=[];	

	function init(){
		parent::init();
		$this->addField('created_by_id')->system(true)->defaultValue($this->app->employee->id);
		$this->addField('name')->sortable(true);
		$this->addField('is_active')->type('boolean')->defaultValue('Active');
		$this->addField('type')->system(true);
		$this->getElement('type')->defaultValue($this->for_type);
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now);
	}
	
	function page_sub_for($page){
		$model = $page->add('xepan\marketing\Model_Communication_SubFor');
		$model->addCondition('for_id',$this->id);
        $crud = $page->add('xepan\hr\CRUD');
        $crud->setModel($model);	

		// throw new \Exception("Error Processing Request", 1);
		
	}
}		
