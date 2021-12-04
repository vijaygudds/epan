<?php

namespace xepan\blog;

class page_getcategory extends \Page{


	function page_index(){
		// parent::init();

		$c = $this->add('xepan\blog\Model_BlogPostCategory')
				->addCondition('status','Active');

		$rows = $c->getRows(['id','name']);
		$option = "";
		foreach ($rows as $row) {
			$option .= "<option value='".$row['id']."'>".$row['name']."</option>";
		}
		
		echo $option;
		exit;
	}

	function page_group(){

		$model = $this->add('xepan\blog\Model_CategoryGroup');
		$option = "<option value='0'>Please Select </option>";
		foreach ($model as $fil){
			$option .= "<option value='".$fil['id']."'>".$fil['name']."</option>";
		}

		echo $option;
		exit;	
	}
}