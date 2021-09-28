<?php

namespace xepan\base;

class Form_Field_Contact extends  Form_Field_Basic {

	public $id_field=null;
	public $title_field=null;
	public $include_status='Active'; // all, no condition

	function setIdField($id_field){
		$this->id_field = $id_field;
	}

	function setTitleField($title_field){
		$this->title_field = $title_field;
	}

	function includeAll(){
		$this->include_status=null;
	}

	function includeStatus($status){
		$this->include_status = $status;
	}


	function recursiveRender(){
		$contact = $this->add('xepan\base\Model_User');
		if($this->include_status) $contact->addCondition('status',$this->include_status);
		$this->setModel($contact,$this->id_field, $this->title_field);
		parent::recursiveRender();
	}
}