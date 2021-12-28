<?php
namespace xepan\marketing;

class page_communicationfor extends \xepan\base\Page{

	public $title = "Communication For";
	function page_index(){
		$model = $this->add('xepan\marketing\Model_Communication_For');
        $crud = $this->add('xepan\hr\CRUD');
        $crud->setModel($model);
	}		
}