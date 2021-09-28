<?php

namespace xepan\base;

class Widget_RecentContacts extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->grid = $this->add('xepan\base\Grid');
	}

	function recursiveRender(){
		$contact = $this->add('xepan\base\Model_Contact');
		$contact->addCondition('status','Active');
		$contact->setOrder('created_at','desc');

		$this->grid->setModel($contact,['name','created_at','created_by','type']);
		$this->grid->addPaginator(10);
		$this->grid->add('H2',null,'grid_buttons')->set('Recent Contacts')->addClass('text-muted');
		$this->grid->removeSearchIcon();

		return parent::recursiveRender();
	}
}