<?php
 
namespace xepan\listing;

class page_filter extends \xepan\base\Page {
	public $title='Listing Filters';

	function init(){
		parent::init();

		$list_id = $this->app->stickyGET('list_id');

		$model = $this->add('xepan\listing\Model_Filter');
		$model->addCondition('list_id',$list_id);
		
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model);
		$crud->grid->addPaginator(10);

		if($crud->isEditing('edit')){
			$form = $crud->form;
			$form->add('View')->set(implode(", ",array_column($model->ref('xepan\listing\Model_FilterField')->getRows(), 'name')));
		}
	}
}

