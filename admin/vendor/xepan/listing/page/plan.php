<?php
 
namespace xepan\listing;

class page_plan extends \xepan\base\Page {
	public $title='Listing Plan';

	function init(){
		parent::init();

		$listid = $this->app->stickyGET('list_id');

		$model = $this->add('xepan\listing\Model_Plan');
		$model->addCondition('list_id',$listid);
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model);
	}
}

