<?php
 
namespace xepan\listing;

class page_list extends \xepan\base\Page {
	public $title='Listing List';

	function init(){
		parent::init();

		$listing_list_model = $this->add('xepan\listing\Model_List');

		// $listing_list_model->add('xepan\listing\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD');
		$form = $crud->form;
        $form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        // ->makePanelsCoppalsible(true)
        ->layout([
                'name'=>'List Details~c1~6',
                'status'=>'c2~6',

            ]);

		$crud->setModel($listing_list_model,['name','list_data_status','data_count','status']);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->removeAttachment();
		$crud->grid->addColumn('data_count');

		$crud->grid->addHook('formatRow',function($g){
			$ld = $this->add('xepan\listing\Model_ListData',['listing'=>$g->model->id]);
			$g->current_row['data_count'] = $ld->count()->getOne();
		});

		// $crud->grid->addPaginator(10);

		
	}
}

