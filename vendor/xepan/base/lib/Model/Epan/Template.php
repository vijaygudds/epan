<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Epan_Template extends \xepan\base\Model_Table{

	public $table='xepan_template';

	function init(){
		parent::init();

		$this->addField('name');
		$this->addField('tags')->type('text');
		$this->addField('is_active')->type('boolean')->defaultValue(true);
		$this->addField('description')->type('text');

		$this->hasMany('xepan\base\Epan',null,null,'Epans');

		$this->is([
				'name|required|to_trim|unique'
			]);
        
	}
}
