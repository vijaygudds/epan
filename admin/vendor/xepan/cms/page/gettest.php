<?php

namespace xepan\cms;

class page_gettest extends \Page{
	function init(){
		parent::init();

		$model = $this->add('xepan\cms\Initiator');
		$model->themeApplied($this->app);

	}
}