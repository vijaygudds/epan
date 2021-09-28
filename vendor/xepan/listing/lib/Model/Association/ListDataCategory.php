<?php

namespace xepan\listing;

class Model_Association_ListDataCategory extends \xepan\base\Model_Table{
	
	public $table = 'listing_category_list_data_association';
	public $acl=false;
	
	function init(){
		parent::init();

		$this->hasOne('xepan\listing\List','list_id');
		$this->hasOne('xepan\listing\Category','list_category_id');
		$this->addField('list_data_id');

		$this->add('dynamic_model\Controller_AutoCreator');
	}
}