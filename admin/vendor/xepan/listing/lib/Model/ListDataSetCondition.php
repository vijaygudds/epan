<?php

namespace xepan\listing;

class Model_ListDataSetCondition extends \xepan\base\Model_Table{

	public $table='list_data_set_condition';
	public $status = ['All'];
	public $actions = [
					'All'=>['view','edit','delete'],
				];
	public $acl_type = "Listing\ListDataSetCondition";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_ListDataSet','list_data_set_id');
		$this->hasOne('xepan\listing\Model_Fields','filter_effected_field_id');
		$this->addField('operator')->enum(['>','<','>=','<=','=','contains','in','!=']);
		$this->addField('value');
		
		$this->add('dynamic_model\Controller_AutoCreator');

	}
}