<?php

namespace xepan\base;

/**
* 
*/
class Model_Rules extends \xepan\base\Model_Table
{
	public $table='rules';

	public $status=['Active','InActive'];

	public $actions = [
		'Active'=>['view','edit','delete','de_activate'],
		'InActive'=>['view','edit','delete','activate']
	];

	public $acl_type='xepan\base\Model_Rules';
	
	function init()
	{
		parent::init();

		$this->hasOne('xepan\base\Contact','created_by_id');
		$this->hasOne('xepan\base\RuleGroup','rulegroup_id');

		$this->addField('name');

		$this->addField('status')->enum($this->status)->defaultValue('Active');

		$this->hasMany('xepan\base\RulesOption','rule_id');
		$this->hasMany('xepan\base\PointSystem','rule_id');

		$this->is([
			'rulegroup_id|required'
		]);
	}

}