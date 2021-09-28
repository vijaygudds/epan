<?php

namespace xepan\listing;

class Model_ListDataFormLayout extends \xepan\base\Model_Table{
	
	public $table = 'xepan_listing_list_data_form_layout';

	public $status = ['Published','UnPublished'];
	public $actions = [
					'Active'=>['view','edit','delete'],
					'Inactive'=>['view','edit','delete']
					];

	public $acl_type = "xepan_listing_list_data_form_layout";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\List','list_id');
		$this->addField('name');
		$this->addField('layout')->type('text');
		
		$this->is(['name|to_trim|required']);
		$this->add('dynamic_model\Controller_AutoCreator');	
	}

	function getLayoutArray(){
		$arr=[];
		$lines = explode(",", $this['layout']);
		foreach ($lines as $line) {
			$seg = explode("=>", $line);
			$arr[trim(str_replace("'", "", $seg[0]))] = trim(str_replace("'", "", $seg[1]));
		}
		return $arr;
	}

	function getArray(){
		if(!$this->loaded()) throw new \Exception("Layout model must be loaded");
		
		return explode(",",$this['layout']);
	}

	function getFields(){
		$temp = [];
		foreach ($this->getArray() as $field) {
			$t = trim(str_replace("'", "", explode("~", explode("=>", $field)[0])[0]));
			$temp[$t] = $t;
		}
		return $temp;
	}

}