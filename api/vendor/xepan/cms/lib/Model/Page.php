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

class Model_Page extends \xepan\cms\Model_Webpage{
	var $table_alias= "webPage";

	function init(){
		parent::init();

		$this->addExpression('template_path')->set(function($m,$q){
			return $m->refSQL('template_id')->fieldQuery('path');
		});

		$this->addCondition('is_template',false);
	}
}
