<?php
 
namespace xepan\listing;

class page_test extends \xepan\base\Page {
	public $title='Listing Filters';

	function init(){
		parent::init();

		$asso = $this->add('xepan\listing\Model_Association_ListDataCategory');
		$asso->addCondition('list_id',1);
		$asso->addCondition('list_data_id',7);
		$this->app->print_r($asso->getRows(),true);
	}
}

