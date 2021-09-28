<?php
 
namespace xepan\listing;

class page_field extends \xepan\base\Page {
	public $title='Listing Fields';

	function init(){
		parent::init();

		$listing_list_model = $this->add('xepan\listing\Model_Fields');
		// $listing_list_model->add('xepan\listing\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD');

		$crud->setModel($listing_list_model);
		$crud->grid->addQuickSearch(['name']);
		// $crud->grid->addPaginator(10);
	}
}

