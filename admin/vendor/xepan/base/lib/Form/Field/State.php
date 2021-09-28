<?php

namespace xepan\base;

class Form_Field_State extends  Form_Field_DropDown {

	public $validate_values = false;
	public $id_field=null;
	public $title_field=null;
	public $include_status='Active'; // all, no condition
	public $country_field=null;
	public $country_id=null;

	function init(){
		parent::init();

		$this->setEmptyText('Please Select');
	}

	function setIdField($id_field){
		$this->id_field = $id_field;
		return $this;
	}

	function setTitleField($title_field){
		$this->title_field = $title_field;
		return $this;
	}

	function includeAll(){
		$this->include_status=null;
		return $this;
	}

	function includeStatus($status){
		$this->include_status = $status;
		return $this;
	}

	function dependsOn($country_field){
		$this->country_field = $country_field;
		$this->country_id = $this->app->stickyGET($this->name.'_country_id');
		if($this->country_id) $this->show_input_only = true; // stops lable repetation

		$this->country_field->js('change',[$this->js(null,[$this->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$this->name]),$this->name.'_country_id'=>$this->country_field->js()->val()])]);
		return $this;
	}

	function recursiveRender(){
		$model = $this->add('xepan\base\Model_State');
		if($this->include_status) $model->addCondition('status',$this->include_status);
		if($this->country_id) $model->addCondition('country_id',$this->country_id);

		$this->setModel($model,$this->id_field, $this->title_field);
		return parent::recursiveRender();
	}

}