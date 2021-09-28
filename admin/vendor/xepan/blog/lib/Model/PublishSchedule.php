<?php

namespace xepan\blog;

class Model_PublishSchedule extends \xepan\base\Model_Table{
	public $table = "publish_schedule";
	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\blog\BlogPost');
		$this->addField('date')->display(['form'=>'DateTimePicker'])->type('datetime');
		$this->addField('is_posted')->defaultValue(0);
	}
}