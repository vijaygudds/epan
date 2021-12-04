<?php

namespace xepan\base;

/**
* 
*/
class Model_RulesOption extends \xepan\base\Model_Table
{
	public $table='rule_options';
	public $acl = 'xepan\base\Model_Rules';
	function init()
	{
		parent::init();

		$this->hasOne('xepan\base\Rules','rule_id');
		$this->addField('name');
		$this->addField('description')->type('text')->display(['form'=>'xepan\base\RichText']);
		$this->addField('score_per_qty')->caption('Score Per Unit');

		$this->addExpression('name_with_score')->set('CONCAT(name," [",score_per_qty,"]")');

		$this->hasMany('xepan\base\PointSystem','rule_option_id');
	}
}