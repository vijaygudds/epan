<?php

namespace xepan\webwidgets;

class page_getcategory extends \Page{
	function init(){
		parent::init();

		$c = $this->add('xepan\webwidgets\Model_GalleryCategory')
				->addCondition('status','Active');

		$rows = $c->getRows(['id','name']);
		$option = "";
		foreach ($rows as $row) {
			$option .= "<option value='".$row['id']."'>".$row['name']."</option>";
		}

		echo $option;
		exit;
	}
}