<?php

namespace xepan\base;

/**
* 
*/
class Model_RuleGroup extends \xepan\base\Model_Table
{
	public $table='rule_group';

	public $status=['All'];

	public $actions = ['All'=>['view','edit','delete']];

	public $acl='xepan\base\Model_Rules';
	
	function init()
	{
		parent::init();

		$this->addField('name');

		$this->hasOne('xepan\base\Contact','created_by_id');

		$this->hasMany('xepan\base\Rules','rulegroup_id');

		$this->addHook('beforeDelet',[$this,'checkRulesInGroup']);

		$this->is([
			'name|to_trim|required|len|>3'
		]);
	}

	function checkRulesInGroup(){
		if($this->ref('xepan\base\Rules')->count()->getOne() > 0)
			throw $this->exception('This Rule Group contains Rules, cannot delete');
	}

}