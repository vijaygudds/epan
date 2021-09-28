<?php

namespace xepan\base;

class Model_EpanCategory extends \xepan\base\Model_Table{
	public $table='epan_category';
	public $status = ['Active','InActive'];
	public $actions = [
			'Active'=>['view','edit','delete','associate_epan','deactivate'],
			'InActive'=>['view','edit','delete','activate']
		];
	public $acl_type = "epan_category";

	function init(){
		parent::init();
				
		$this->addField('name');
		$this->addField('status')->defaultValue('Active');

		$this->hasMany('xepan\base\EpanCategoryAssociation','epan_category_id');

		$this->addExpression('epans')->set($this->refSQL('xepan\base\EpanCategoryAssociation')->count());
		$this->is([
				'name|to_trim|required|unique'
			]);
	}

	function deactivate(){
		$this['status'] = "InActive";
		$this->save();
	}

	function activate(){
		$this['status'] = "Active";
		$this->save();
	}

	function page_associate_epan($page){

		$m = $this->add('xepan\base\Model_EpanCategoryAssociation');
		$m->addCondition('epan_category_id',$this->id);

		$crud = $page->add('xepan\hr\CRUD');
		$crud->setModel($m,['epan_id'],['epan']);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn('action');
	}

}
