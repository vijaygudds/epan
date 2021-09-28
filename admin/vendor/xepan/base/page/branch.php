<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/


namespace xepan\base;

class page_branch extends \xepan\base\Page {
	
	public $title='Branch Management';

	function init(){
		parent::init();
        

        $model = $this->add('xepan\base\Model_Branch');
        $crud = $this->add('xepan\hr\CRUD');
        $crud->setModel($model,['name','action','status']);
        $crud->grid->addPaginator(25);
        $crud->grid->removeAttachment();
        $crud->grid->addQuickSearch(['name']);
	}
}
