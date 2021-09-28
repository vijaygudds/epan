<?php

namespace xepan\base;

class Widget_GlobalActivity extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->report->enableFilterEntity('date_range');
		$this->report->enableFilterEntity('Employee');

		$this->grid = $this->add('xepan\base\Grid');
	}

	function recursiveRender(){		
		$activity = $this->add('xepan\base\Model_Activity');

		if(isset($this->report->employee))
			$activity->addCondition('related_contact_id',$this->report->employee);

		if(isset($this->report->start_date))
			$activity->addCondition('created_at','>',$this->report->start_date);
		if(isset($this->report->end_date))
			$activity->addCondition('created_at','<',$this->app->nextDate($this->report->end_date));

		$this->grid->setModel($activity,['activity','created_at']);
		$this->grid->addPaginator(10);
		$this->grid->add('H2',null,'grid_buttons')->set('Global Activities')->addClass('text-muted');
		$this->grid->removeSearchIcon();

		$this->js(true)->univ()->setInterval($this->grid->js()->reload()->_enclose(),200000);
		
		return parent::recursiveRender();
	}
}