<?php


namespace xepan\base;


class Form_ContactOtherInfo extends \Form {

	public $contact=null;
	function init(){
		parent::init();
	}
		

	function setModel($model,$fields){
		$this->contact = $model->ref('contact_id');

		$other_fields_model = $this->add('xepan\base\Model_Config_ContactOtherInfo');
		$other_fields_model->addCondition('for',$this->contact['type']);

		foreach ($other_fields_model as $m) {
			
			if(!$m['name']) continue;
			$field = $this->addField($m['type'],$m['name']);
			if($m['type']=='DropDown'){
				$field->setValueList(array_combine(explode(",", $m['possible_values']), explode(",", $m['possible_values'])))->setEmptyText('Please Select');
			} 

			$existing = $this->add('xepan\base\Model_Contact_Other')
				->addCondition('contact_id',$this->contact->id)
				->addCondition('head',$m['name'])
				->tryLoadAny();
			$field->set($existing['value']);

			if($m['conditional_binding']){
				$field->js(true)->univ()->bindConditionalShow(json_decode($m['conditional_binding'],true),'div.atk-form-row');
			}

			if($m['is_mandatory']){
				$field->validate('required');
			}

		}
		return;
	}

	function update(){

		$other_fields_model = $this->add('xepan\base\Model_Config_ContactOtherInfo');
		$other_fields_model->addCondition('for',$this->contact['type']);
		
		if($this->isSubmitted()){
			foreach ($other_fields_model as $m) {
				if(!$m['name']) continue;

				$existing = $this->add('xepan\base\Model_Contact_Other')
					->addCondition('contact_id',$this->contact->id)
					->addCondition('head',$m['name'])
					->tryLoadAny();
				$existing['value'] = $this[$m['name']];
				$existing->save();
			}

			return true;

		}
	}
}