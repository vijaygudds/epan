<?php
 
namespace xepan\listing;

class page_listdataset extends \xepan\base\Page {
	public $title='List Data Set';

	function init(){
		parent::init();

		$list_id =  $this->app->stickyGET('list_id');
		$m = $this->add('xepan\listing\Model_ListDataSet');
		$m->addCondition('list_id',$list_id);
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($m);
	}
}

