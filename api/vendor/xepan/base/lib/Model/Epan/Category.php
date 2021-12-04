<?php

/**
* description: Epan Category are used only when installing xEpan as multi user based ERPaaS or Other
* Multi User based system.
* For Single use installation this is irrelevent. 
* @author : Gowrav Vishwakarma
* 
*/

namespace xepan\base;

class Model_Epan_Category extends \xepan\base\Model_Table{
	public $table='epan_category';

	function init(){
		parent::init();

		$this->addField('name')->mandatory(true)->hint('Identification for Category');
		$this->hasMany('Epan');
		
		$this->is([
				'name|unique|to_trim|required'
			]);
		
		$this->addHook('beforeDelete',$this);
	}


	function beforeDelete($m){
		$epan_count = $m->ref('Epan')->count()->getOne();
		
		if($epan_count)
			throw $this->exception('Cannot Delete,first delete Epan`s');	
	}
}
