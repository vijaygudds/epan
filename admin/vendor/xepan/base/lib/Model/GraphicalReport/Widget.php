<?php


namespace xepan\base;

class Model_GraphicalReport_Widget extends \xepan\base\Model_Table{

	public $table='graphical_report_widget';
	function init(){
		parent::init();

		$this->hasOne('xepan\base\GraphicalReport','graphical_report_id');
		$this->addField('name');
		$this->addField('col_width')->enum([1,2,3,4,5,6,7,8,9,10,11,12]);
		$this->addField('order')->defaultValue(1000);
		$this->addField('is_active')->type('boolean')->defaultValue(true);
		
		// allowed to which post :: hasmany or json {"12","23"} like id search  
		$this->addField('class_path');
	}
}