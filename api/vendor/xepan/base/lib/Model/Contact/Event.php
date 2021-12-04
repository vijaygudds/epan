<?php

/**
* description: Events for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_Event extends Model_Contact_Info{

	function init(){
		parent::init();
			
		$this->getElement('head')->enum(['DOB','Anniversary']);
		$this->addCondition('type','Event');
	}
}
