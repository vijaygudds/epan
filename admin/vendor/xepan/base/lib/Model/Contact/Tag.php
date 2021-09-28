<?php

namespace xepan\base;

class Model_Contact_Tag extends \xepan\base\Model_ConfigJsonModel{
	public  $fields	= [
					'name'=>"Line"
				];

	public $config_key = 'XEPAN_BASE_CONTACT_TAG';
	public $application = 'base';

	function init(){
		parent::init();

		$this->addHook('beforeSave',[$this,'sanitize'],[],4);
	}

	function sanitize(){
		$this['name'] = "`".str_replace('`', "", trim($this['name']))."`";

		$tag = $this->add('xepan\base\Model_Contact_Tag');
		$tag->addCondition('name',$this['name']);
		if($this->loaded())
			$tag->addCondition("id",'!=',$this->id);
		$tag->tryLoadAny();
		
		if($tag->loaded()){
			throw $this->exception('name already exists','ValidityCheck')->setField('name');
		}
	}

	function getAllTag(){
		$tag_model = $this->add('xepan\base\Model_Contact_Tag');
		$all_tag = array_column($tag_model->config_data, 'name');
		return $all_tag = array_combine($all_tag, $all_tag);
	}
}