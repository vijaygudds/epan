<?php

namespace xepan\base;

class Model_EpanCategoryAssociation extends \xepan\base\Model_Table{
	public $table='epan_category_association';
	public $acl = false;
	public $title_field = "epan";

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Model_Epan','epan_id');
		$this->hasOne('xepan\base\Model_EpanCategory','epan_category_id');

		$this->addHook('beforeSave',$this);
	}

	function beforeSave(){
		$m = $this->add('xepan\base\Model_EpanCategoryAssociation');
		$m->addCondition('epan_id',$this['epan_id']);
		$m->addCondition('epan_category_id',$this['epan_category_id']);
		$m->addCondition('id','<>',$this->id);
		$m->tryLoadAny();
		if($m->loaded()){
			throw $this->Exception($this['name'].' Epan already associate with this category','ValidityCheck')->setField('epan_id');
		}
	}

}
