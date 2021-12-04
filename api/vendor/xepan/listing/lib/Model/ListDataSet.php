<?php

namespace xepan\listing;

class Model_ListDataSet extends \xepan\base\Model_Table{
	public $table = 'list_data_set';
	public $status = ['All'];
	public $actions = [
					'All'=>['view','conditions','edit','delete'],
				];
	public $acl_type = "Listing\ListDataSet";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_List','list_id');
		$this->addField('name');

		$this->is(['name|to_trim|required']);

		$this->hasMany('xepan\listing\Model_ListDataSetCondition','list_data_set_id');
		$this->add('dynamic_model\Controller_AutoCreator');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if($this->checkDuplicate()){
			throw $this->exception('name is already taken','ValidityCheck')->setField('name');						
		}
	}

	function checkDuplicate(){
		$list = $this->add('xepan\listing\Model_ListDataSet');
		$list->addCondition('name',$this['name']);
		$list->addCondition('id','<>',$this->id);
		return $list->count()->getOne();
	}
	
	function getCondition(){
		return $this->add('xepan\listing\Model_ListDataSetCondition')
				->addCondition('list_data_set_id',$this->id);
	}

	function page_conditions($page){
		$model = $this->add('xepan\listing\Model_ListDataSetCondition');
		$model->addCondition('list_data_set_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$form = $crud->form;
		// $form->add('xepan\base\Controller_FLC')
  //       ->showLables(true)
  //       ->addContentSpot()
  //       ->makePanelsCoppalsible(true)
  //       ->layout([
  //       		'name'=>'Filter Field Details~c1~6',
  //       		'field_type'=>'c2~6',
  //       		'placeholder'=>'c11~4',
  //       		'hint'=>'c12~4',
		// 		'status'=>'c13~4',

  //       		'default_value'=>'Field Value~c21~6',
  //       		'populate_values_from_field_id'=>'c22~6',
        		
  //       		'operator'=>'Apply Condition~c31~6',
  //       		'filter_effected_field_id'=>'c32~6',

		// ]);

		$crud->setModel($model);
        if($crud->isEditing()){
	        $form->getElement('filter_effected_field_id')->getModel()->addCondition('list_id',$this['list_id']);
        }
		$crud->grid->addPaginator(25);

	}
}