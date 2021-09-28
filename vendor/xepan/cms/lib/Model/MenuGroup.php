<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\cms;

class Model_MenuGroup extends \xepan\base\Model_Table{
	var $table = "menu_group";

	function init(){
		parent::init();
		
		$this->addField('name');
		$this->addField('pages')->type('text')->system(true);

		$this->is(['name|to_trim|required|unique']);

		$this->addhook('beforeSave',function($m){
			$m['pages'] = json_encode($m['pages']);
		});
		
		$this->addhook('afterLoad',function($m){
			$m['pages'] = json_decode($m['pages'],true);
		});

	}
}
