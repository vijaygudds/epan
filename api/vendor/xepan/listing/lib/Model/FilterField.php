<?php

namespace xepan\listing;

class Model_FilterField extends \xepan\base\Model_Table{
	public $table='list_filter_fields';
	public $status = ['Active','Inactive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'Inactive'=>['view','edit','delete','activate']
				];

	public $acl_type = "Listing\FilterField";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_Filter','list_filter_id');

		$this->hasOne('xepan\listing\Model_Fields','populate_values_from_field_id');
		$this->hasOne('xepan\listing\Model_Fields','filter_effected_field_id');

		$this->addField('name');
		$this->addField('field_type')
					->setValueList(
							array(
								'Number'=>"Number",
								'email'=>'Email',
								'line'=>'Line',
								'text'=> 'Text',
								'password' =>'Password',
								'radio'=>'Radio',
								'checkbox'=>'Checkbox',
								'DropDown'=>'DropDown',
								'DatePicker'=>"DatePicker",
								// 'Upload'=>"Upload",
								'TimePicker'=>"TimePicker",
								"Captcha"=>'Captcha'
							)
						);
		$this->addField('placeholder');
		$this->addField('hint');
		$this->addField('default_value')->type('text');
		$this->addField('operator')->enum(['>','<','>=','<=','=','contains','in']);

		$this->addField('status')->enum(['Active','Inactive'])->defaultValue('Active');
		
		$this->is([
				'name|to_trim|required',
				'list_filter_id|to_trim|required',
				'field_type|to_trim|required'
			]);

		$this->add('dynamic_model\Controller_AutoCreator');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$this['name'] = $this->app->normalizeName($this['name']);
		
		if($this->checkDuplicate()){
			throw $this->exception('name is already taken','ValidityCheck')->setField('name');
		}
	}

	function checkDuplicate(){

		$list = $this->add('xepan\listing\Model_FilterField');
		$list->addCondition('name',$this['name']);
		$list->addCondition('id','<>',$this->id);
		$list->addCondition('list_filter_id',$this['list_filter_id']);

		return $list->count()->getOne();
	}

	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}

	function getDistinctDataValue($list_model){

		$field_model = $this->add('xepan\listing\Model_Fields')->load($this['populate_values_from_field_id']);

		$db_column = $field_model->dbColumnName();
		$db_table = $list_model->getTableName();;

		$data = $this->app->db->dsql()->expr('SELECT DISTINCT('.$db_column.') FROM '.$db_table)->get();
		$list = [];
		foreach ($data as $key => $value) {
			if(!trim($value[$db_column])) continue;
			$list[] = $value[$db_column];
		}

		return $list;
	}
}