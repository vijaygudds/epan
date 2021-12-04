<?php

namespace xepan\listing;

class Form_ManageListData extends \Form {
	public $options = [];
	public $list_model;
	public $list_data_model;
	public $listing_layout_model;

	function init(){
		parent::init();	

		$this->setListModel();
		$this->setListLayout();

		$this->list_data_model = $this->list_model->getDataModel();
		
		if($this->options['list_data_set_id']){
			$this->list_data_model->applyListDataSetCondition($this->options['list_data_set_id']);
		}

		$contact = $this->add('xepan\base\Model_Contact');
		$contact->loadLoggedIn();
		if($contact->loaded()){
			$this->list_data_model->addCondition('created_by_id',$contact->id);
		}

		if($this->options['show_data_set_record_only']){
			$this->list_data_model->setOrder('id','asc');
			$this->list_data_model->tryLoadAny();
		}

		
		$fields_in_layout=[];
		if($this->listing_layout_model->loaded()){
			$fields_in_layout = $this->listing_layout_model->getFields();
			$this->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->layout($this->listing_layout_model->getLayoutArray());
		}

		$this->setModel($this->list_data_model,$fields_in_layout);

		// validation applied
		foreach ($this->list_model->fields() as $field) {
			if($this->listing_layout_model->loaded() && !in_array($field->dbColumnName(), $fields_in_layout)){
				continue;
			} 
			
			if($field['field_type'] == "Expression") continue;

			$field_name = $field->dbColumnName();
			
			if($this->hasElement($field_name)){
				$f = $this->getElement($field_name);
				$f->setFieldHint($field['hint']);
				if($field['is_mandatory']){
					$f->validate('required');
				}

				if($field['field_type'] == "Multiselect"){
					$f->set( explode(",",$this->list_data_model[$field_name]));
				}

			}
			// if($field['field_type'] == "Upload"){
			// 	$f = $this->addField('xepan\filestore\Field_File',$field_name,$field['name']);
			// }else{
			// 	$f = $this->addField($field['field_type'],$field_name,$field['name']);
			// }
			// if(in_array($field['field_type'], ['DropDown','radio']) && $values = $field['default_value']){
			// 	$t = explode(",", $values);
			// 	$f->setValueList(array_combine($t, $t));
			// }

		// 	$f->set($this->list_data_model[$field_name]);
		}
		// print_r($fields_in_layout);

		$this->addSubmit($this->options['save_button_caption'])->addClass($this->options['save_button_class']);
		if($this->isSubmitted()){
			// foreach ($this->list_model->fields() as $field) {
			// 	if($this->listing_layout_model->loaded() && !in_array($field->dbColumnName(), $fields_in_layout)){
			// 		continue;
			// 	} 
				
			// 	$field_name = $field->dbColumnName();
			// 	$this->list_data_model[$field_name] = $this[$field_name];
			// }
			$this->save();
			// $this->list_data_model->saveAndUnload();
			$this->js(null,$this->js()->univ()->successMessage('Saved Successfully'))->reload()->execute();
		}
	}


	function setListLayout(){
		return $this->listing_layout_model = $this->add('xepan\listing\Model_ListDataFormLayout')
			// ->tryLoadAny();
			->tryLoad($this->options['listing_layout_id']);
	}

	function setListModel(){
		$this->list_model = $this->add('xepan\listing\Model_List');
		$this->list_model->Load($this->options['listing_id']);
		return $this->list_model;
	}

}