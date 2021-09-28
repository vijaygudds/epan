<?php

namespace xepan\listing;

class Model_Fields extends \xepan\base\Model_Table{
	public $table='list_fields';
	public $status = ['Active','Inactive'];
	public $actions = [
					'Active'=>['view','edit','delete','deactivate'],
					'Inactive'=>['view','edit','delete','activate']
					];

	public $acl_type = "Listing\Fields";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_List','list_id');

		$this->addField('name');
		$this->addField('field_type')
					->setValueList(
							array(
								'Number'=>"Number",
								'Decimal'=>"Decimal",
								'Int'=>"Integer",
								'email'=>'Email',
								'line'=>'Line',
								'text'=> 'Text',
								'password' =>'Password',
								'radio'=>'Radio',
								'checkbox'=>'Checkbox',
								'DropDown'=>'DropDown',
								'Multiselect'=>'Multiselect',
								'DatePicker'=>"DatePicker",
								'Upload'=>"Upload",
								'TimePicker'=>"TimePicker",
								"Captcha"=>'Captcha',
								"Expression"=>'Expression'
							)
						);
		$this->addField('default_value')->type("text");
		$this->addField('placeholder');
		$this->addField('hint');

		$this->addField('is_mandatory')->type('boolean')->defaultValue(false);
		$this->addField('is_moderate')->type('boolean');
		$this->addField('is_changable')->type('boolean')->defaultValue(false);
		$this->addField('is_filterable')->type('boolean')->defaultValue(true);

		$this->addField('is_public')->type('boolean')->defaultValue(true);
		$this->addField('is_private')->type('boolean')->defaultValue(false);
		$this->addField('is_premium')->type('boolean')->defaultValue(false);

		$this->addField('status')->enum(['Active','Inactive'])->defaultValue('Active');
		
		$this->is([
				'name|to_trim|required',
				'list_id|to_trim|required',
				'field_type|to_trim|required'
			]);

		$this->add('dynamic_model\Controller_AutoCreator');		

		$this->addHook('beforeSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){
		$this['name'] = $this->app->normalizeName($this['name']);
		
		if($this->checkDuplicate()){
			throw $this->exception('name is already taken','ValidityCheck')->setField('name');						
		}

		$this->updateDB();
	}

	function beforeDelete(){
		$this->deleteColumn();

	}

	function deleteColumn(){
		$query = 'ALTER TABLE '.$this->getTableName().' DROP COLUMN '.$this->dbColumnName().';';
		$this->app->db->dsql()->expr($query)->execute();
	}

	function checkDuplicate(){

		$list = $this->add('xepan\listing\Model_Fields');
		$list->addCondition('name',$this['name']);
		$list->addCondition('id','<>',$this->id);
		$list->addCondition('list_id',$this['list_id']);

		return $list->count()->getOne();
	}

	function getTableName(){

		if(!$this['list']){
			return $this->add('xepan\listing\Model_List')->load($this['list_id'])->getTableName();
		}

		$list_name = $this['list'];
		return $table_name = 'xepan_listing_'.$this->app->normalizeName(strtolower($list_name));
	}

	function dbColumnName(){

		$field_name = $this->app->normalizeName(strtolower($this['name']));
		if($this['field_type'] == "Upload"){
			$field_name .= "_id";
		}
		return trim($field_name);
	}

	function updateDB($is_dirty=false){
		$table_name = $this->getTableName();

		// if isloaded and is dirty ('name'), field_type
		// alter table command
		if(($this->loaded() AND !($this->isDirty('name') || $this->isDirty('field_type'))))
			return;

		$db_field_type = $this->dbFieldType();	

		if($this->loaded() AND ($this->isDirty('name') || $this->isDirty('field_type'))){
			// alter column query 
			if($this->isDirty('name')){
				$old_model = $this->add('xepan\listing\Model_Fields')->load($this->id);
				$old_column_name = $old_model->dbColumnName();

				$query = 'ALTER TABLE '.$table_name.' CHANGE COLUMN '.$old_column_name.' '.$this->dbColumnName().' '.$db_field_type.';';
			}else{
				$query = 'ALTER TABLE '.$table_name.' MODIFY COLUMN '.$this->dbColumnName().' '.$db_field_type.';';
			}
		}else {
			// add column query;
			$query = 'ALTER TABLE `'.$table_name.'` ADD COLUMN `'.$this->dbColumnName().'`  '.$db_field_type.' NULL DEFAULT NULL;';
			// $query = 'ALTER TABLE `'.$table_name.'` ADD `'.$this->dbColumnName().'`  '.$db_field_type.' NULL DEFAULT NULL, ADD `is_mandatory` tinyint(4) NULL DEFAULT NULL, ADD `is_moderate` tinyint(4) NULL DEFAULT NULL,ADD `is_changable` tinyint(4) NULL DEFAULT NULL,ADD `is_filterable` tinyint(4) NULL DEFAULT NULL,ADD `is_public` tinyint(4) NULL DEFAULT NULL,ADD `is_private` tinyint(4) NULL DEFAULT NULL, ADD `is_premium` tinyint(4) NULL DEFAULT NULL;';
		}
		
		$this->app->db->dsql()->expr($query)->execute();
	}

	function dbFieldType(){

		switch ($this['field_type']) {
			case 'Decimal':
				$type = 'decimal(10,2)';
				break;
			case 'Int':
				$type = "int";
				break;
			case 'text':
				$type = "text";
				break;
			case 'checkbox':
				$type = "tinyint(4)";
				break;
			case 'DatePicker':
				$type = "datetime";
				break;
			case 'TimePicker':
				$type = "time";
				break;
			case 'upload':
				$type = "int";
				break;
			case 'Multiselect':
				$type = "text";
				break;
			default:
				$type = "varchar(255)";
				break;
		}

		return $type;
	}

	function modelFieldType(){

		switch ($this['field_type']) {
			case 'Number':
				$type = 'number';
				break;
			case 'text':
				$type = "text";
				break;
			case 'checkbox':
				$type = "boolean";
				break;
			case 'DatePicker':
				$type = "datetime";
				break;
			case 'TimePicker':
				$type = "time";
				break;
			default:
				$type = "string";
				break;
		}

		return $type;
	}


	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}

}