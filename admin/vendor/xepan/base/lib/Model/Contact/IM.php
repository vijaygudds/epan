<?php

/**
* description: IM for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_IM extends Model_Contact_Info{

	function init(){
		parent::init();
			
		$this->getElement('head')->enum(['Skype','Yahoo','ICM','WhatsApp']);
		$this->addCondition('type','IM');
	}
}
