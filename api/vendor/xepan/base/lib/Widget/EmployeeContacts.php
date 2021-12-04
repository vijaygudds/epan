<?php

namespace xepan\base;

class Widget_EmployeeContacts extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->report->enableFilterEntity('date_range');
		$this->report->enableFilterEntity('Employee');

		$this->grid = $this->add('xepan\base\Grid');
		$this->grid->add('View',null,'grid_buttons')->setHtml('<b>Contacts Added</b>');
		$this->grid->removeSearchIcon();
	}

	function recursiveRender(){
		$contact_m = $this->add('xepan\base\Model_Contact');
		
		if(isset($this->report->start_date))
			$contact_m->addCondition('created_at','>=',$this->report->start_date);
		else
			$contact_m->addCondition('created_at','>=',$this->app->today);

		if(isset($this->report->end_date))
			$contact_m->addCondition('created_at','<=',$this->app->nextDate($this->report->end_date));
		else
			$contact_m->addCondition('created_at','<=',$this->app->nextDate($this->app->today));

		if(isset($this->report->employee))
			$contact_m->addCondition('created_by_id',$this->report->employee);

		$this->grid->setModel($contact_m,['effective_name','type','created_at']);
		$this->grid->addPaginator(20);

		parent::recursiveRender();
	}
}