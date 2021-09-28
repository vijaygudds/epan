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

class Model_Template extends \xepan\cms\Model_Webpage{
	var $table_alias= "webTemplate";

	function init(){
		parent::init();

		$this->addCondition('is_template',true);
	}
}
