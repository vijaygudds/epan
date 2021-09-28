<?php

namespace xepan\blog;

class page_cron extends \Page{
	function init(){
		parent::init();

		$post = $this->add('xepan\blog\Model_BlogPost');
		$post->schedule();
	}
}