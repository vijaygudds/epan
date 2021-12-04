<?php
 
namespace xepan\blog;

class page_blogpostcategory extends \xepan\base\Page {
	public $title='Blog Post Categories';

	function init(){
		parent::init();

		$blog_category_model = $this->add('xepan\blog\Model_BlogPostCategory');
		$blog_category_model->add('xepan\blog\Controller_SideBarStatusFilter');

		$crud = $this->add('xepan\hr\CRUD',null,null,['view/post/category']);
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'name'=>'Blog Post Category~c1~6',
						'order'=>'c2~2',
						'status'=>'c3~2',
						'FormButtons~&nbsp;'=>'c4~2', // closed to make panel default collapsed
						
					]);
		}
		$crud->setModel($blog_category_model,['name','order','status']);
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(10);


	}
}

