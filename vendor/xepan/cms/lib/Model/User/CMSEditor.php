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

class Model_User_CMSEditor extends \xepan\base\Model_Table{
	public $table='cms_editors';

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\User');
		
		$this->addExpression('username')->set($this->refSQL('user_id')->fieldQuery('username'));
		$this->addExpression('password')->set($this->refSQL('user_id')->fieldQuery('password'));

		$this->addField('can_edit_template')->type('boolean')->defaultValue(true);
		$this->addField('can_edit_page_content')->type('boolean')->defaultValue(true);

	}
}
