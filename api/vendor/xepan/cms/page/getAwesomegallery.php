<?php

namespace xepan\cms;

class page_getAwesomegallery extends \Page{
	function init(){
		parent::init();

		$c = $this->add('xepan\cms\Model_CarouselCategory');

		$rows = $c->getRows(['id','name']);
		$option = "<option value='0'>Please Select </option>";
		foreach ($rows as $row) {
			$option .= "<option value='".$row['name']."'>".$row['name']."</option>";
		}

		echo $option;
		exit;
	}
}