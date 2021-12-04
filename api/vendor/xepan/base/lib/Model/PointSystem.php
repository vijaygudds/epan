<?php

namespace xepan\base;

/**
* 
*/
class Model_PointSystem extends \xepan\base\Model_Table
{
	public $table='point_system';
	
	function init()
	{
		parent::init();

		$this->hasOne('xepan\base\Contact','created_by_id')->defaultValue(@$this->app->employee->id);
		$this->hasOne('xepan\base\Rules','rule_id');
		$this->hasOne('xepan\base\RulesOption','rule_option_id');
		$this->addField('contact_id')->defaultValue(0);
		$this->addField('timesheet_id')->defaultValue(0);
		$this->addField('qty')->defaultValue(0);
		$this->addField('score')->defaultValue(0);
		$this->addField('remarks');
		$this->addField('landing_campaign_id')->defaultValue(0);
		$this->addField('landing_content_id')->defaultValue(0);
		$this->addField('created_at')->type('datetime')->defaultValue(@$this->app->now);

		$this->addExpression('created_at_date')->set('DATE(created_at)')->type('date');

		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){

		if($this->isDirty('qty') && $this->loaded()){
			$old_score = $this->newInstance()->load($this->id)->get('score');
			
			$contact_m = $this->add('xepan\base\Model_Contact');
			$contact_m->addCondition('id',$this['contact_id']);
			$contact_m->tryLoadAny();

			if($contact_m->loaded()){
				$contact_m['score'] = $contact_m['score'] - $old_score;
				$contact_m->save();
			}
		}

		if($this['rule_option_id'])
			$this['score']= $this['qty'] * $this->ref('rule_option_id')->get('score_per_qty');

		if($this['rule_option_id'] && !$this['rule_id'])
			$this['rule_id'] = $this->ref('rule_option_id')->get('rule_id');
	}

	function afterSave($m){
		$contact_m = $this->add('xepan\base\Model_Contact');
		$contact_m->addCondition('id',$m['contact_id']);
		$contact_m->tryLoadAny();

		if($contact_m->loaded()){
			$contact_m['score'] = $contact_m['score'] + $m['score'];
			$contact_m->save();
		}
	}

	function beforeDelete($m){
		$contact_m = $this->add('xepan\base\Model_Contact');
		$contact_m->addCondition('id',$m['contact_id']);
		$contact_m->tryLoadAny();

		if($contact_m->loaded()){
			$contact_m['score'] = $contact_m['score'] - $m['score'];
			$contact_m->save();
		}
	}
}