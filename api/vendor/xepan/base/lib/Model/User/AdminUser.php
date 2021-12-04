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

class Model_User_AdminUser extends Model_User{

	function init(){
		parent::init();
		
		$this->addCondition('type','AdminUser');

	}
}
