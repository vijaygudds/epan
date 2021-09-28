<?php

namespace xepan\blog;

class page_blogcategorygroup extends \xepan\base\Page{
	public $title = "Blog Category Group";

	function init(){
        parent::init();

		$model = $this->add('xepan\blog\Model_CategoryGroup');
        $crud = $this->add('xepan\hr\CRUD');
        
        if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelsCoppalsible(true)
				->layout([
					'name'=>'Category Group~c1~12',

				]);

			foreach ( $this->add('xepan\blog\Model_BlogPostCategory') as $cat) {
			 	$field = $form->addField('checkbox',$this->app->normalizeName($cat['name']),$cat['name']);
			}
		}


		$crud->addHook('formSubmit',function($c,$cf){
			$temp = $cf->getAllFields();
			$cf->model['name'] = $temp['name'];
			unset($temp['name']);
			$cf->model['categories'] = $temp;
			$cf->model->save();
			return true; // do not proceed with default crud form submit behaviour
		});
		
		$crud->setModel($model);
		$crud->grid->removeAttachment();
		if($crud->isEditing('edit')){
			$form = $crud->form;
			foreach ( $this->add('xepan\blog\Model_BlogPostCategory') as $cat) {
				$cat_name = $this->app->normalizeName($cat['name']);
			 	$field = $form->getElement($cat_name);
			 	if(isset($crud->model['categories'][$cat_name]) && $crud->model['categories'][$cat_name])
			 		$field->set(true);
			}
		}
    }
}