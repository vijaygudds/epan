<?php

namespace xepan\listing;

class Model_Filter extends \xepan\base\Model_Table{
	public $table = 'list_filter';
	public $status = ['All'];
	public $actions = [
					'All'=>['view','fields','edit','delete']
				];
	public $acl_type = "Listing\ListFilter";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_List','list_id');
		$this->addField('name');
		$this->addField('layout')->type('text');
		// $this->addField('status')->enum(['All']);

		$this->is(['name|to_trim|required']);

		$this->hasMany('xepan\listing\Model_FilterField','list_filter_id');

		$this->add('dynamic_model\Controller_AutoCreator');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		if($this->checkDuplicate()){
			throw $this->exception('name is already taken','ValidityCheck')->setField('name');						
		}
	}

	function checkDuplicate(){
		$list = $this->add('xepan\listing\Model_Filter');
		$list->addCondition('name',$this['name']);
		$list->addCondition('id','<>',$this->id);
		return $list->count()->getOne();
	}

	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}
	
	function getField(){
		return $this->add('xepan\listing\Model_FilterField')->addCondition('list_filter_id',$this->id);
	}

	function page_fields($page){
		$model = $this->add('xepan\listing\Model_FilterField');
		$model->addCondition('list_filter_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$form = $crud->form;
		$form->add('xepan\base\Controller_FLC')
        ->showLables(true)
        ->addContentSpot()
        ->makePanelsCoppalsible(true)
        ->layout([
        		'name'=>'Filter Field Details~c1~6',
        		'field_type'=>'c2~6',
        		'placeholder'=>'c11~4',
        		'hint'=>'c12~4',
				'status'=>'c13~4',

        		'default_value'=>'Field Value~c21~6',
        		'populate_values_from_field_id'=>'c22~6',
        		
        		'operator'=>'Apply Condition~c31~6',
        		'filter_effected_field_id'=>'c32~6',

		]);

		$crud->setModel($model);
        if($crud->isEditing()){
	        $form->getElement('populate_values_from_field_id')->getModel()->addCondition('list_id',$this['list_id']);
	        $form->getElement('filter_effected_field_id')->getModel()->addCondition('list_id',$this['list_id']);
        }
		$crud->grid->addPaginator(25);

	}

	function page_manage_layouts($page){
		$m = $this->add('xepan\listing\Model_ListDataFormLayout');
		$m->addCondition('list_id',$this->id);
		$crud = $page->add('xepan\base\CRUD');
		$crud->setModel($m);

		$crud->form->add('View')->set(implode(", ",$this->getDataModel()->getActualFields()));
	}
	
	function getLayoutArray(){
		$arr = [];
		$lines = explode(",", $this['layout']);
		foreach ($lines as $line) {
			$seg = explode("=>", $line);
			$arr[trim(str_replace("'", "", $seg[0]))] = trim(str_replace("'", "", $seg[1]));
		}
		return $arr;
	}	
}