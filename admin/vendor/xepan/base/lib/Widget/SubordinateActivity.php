<?php

namespace xepan\base;

class Widget_SubordinateActivity extends \xepan\base\Widget{
	function init(){
		parent::init();
		
		$this->report->enableFilterEntity('date_range');
		$this->report->enableFilterEntity('Employee');

		$this->grid = $this->add('xepan\base\Grid');
	}

	function recursiveRender(){

		$descendants = $this->app->employee->ref('post_id')->descendantPosts();
		$activity = $this->add('xepan\base\Model_Activity');
		$activity->addExpression('post')->set(function($m,$q){
			$employee = $this->add('xepan\hr\Model_Employee');
			$employee->addCondition('id',$m->getElement('contact_id'));
			$employee->setLimit(1);
			return $employee->fieldQuery('post_id');
		});
		$activity->addCondition('post',array_unique($descendants));

		if(isset($this->report->employee))
			$activity->addCondition('related_contact_id',$this->report->employee);

		if(isset($this->report->start_date))
			$activity->addCondition('created_at','>',$this->report->start_date);
		if(isset($this->report->end_date))
			$activity->addCondition('created_at','<',$this->app->nextDate($this->report->end_date));

		$this->grid->setModel($activity,['activity','created_at']);
		$this->grid->addPaginator(10);
		
		$this->grid->add('H2',null,'grid_buttons')->set('Subordinates Activities')->addClass('text-muted');
		$this->grid->removeSearchIcon();

		return parent::recursiveRender();
	}
}