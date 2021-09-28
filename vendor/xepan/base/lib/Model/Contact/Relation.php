<?php

/**
* description: Relations for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_Relation extends Model_Contact_Info{

	function init(){
		parent::init();
			
		$this->getElement('head')->enum(['Father','Mother','Other']);
		$this->addCondition('type','Relation');
	}
}
