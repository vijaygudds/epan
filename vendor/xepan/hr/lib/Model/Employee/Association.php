<?php
namespace xepan\hr;

class Model_Employee_Association extends \xepan\base\Model_Table{
	public $table="employee_group_association";
	public $acl=false;
	
	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Employee','employee_id');
		$this->hasOne('xepan\hr\Employee_Group','group_id');

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('group_id')->fieldQuery('name');
		});
	}
}