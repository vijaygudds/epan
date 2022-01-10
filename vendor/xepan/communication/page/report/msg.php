<?php

namespace xepan\communication;

class page_report_msg extends \xepan\base\Page{

	function init(){
		parent::init();
		$id = $this->app->stickyGET('communication_id');
		$m = $this->add('xepan\communication\Model_Communication');
		$m->load($id);
		$v = $this->add('View');
		$v->setHtml($m['description']);
	}
	
}