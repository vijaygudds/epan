<?php

namespace xepan\listing;

class Model_MyList extends \xepan\base\Model_Table{
	public $table = 'list_my_list';
	public $status = ['All'];
	public $actions = [
					'All'=>['view','edit','delete'],
				];
	public $acl_type = "Listing\MyList";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_Contact','contact_id');
		$this->hasOne('xepan\listing\Model_List','list_id');
		$this->addField('list_data_id');
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->system(true);
				
	}
}