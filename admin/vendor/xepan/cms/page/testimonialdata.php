<?php

namespace xepan\cms;

class page_testimonialdata extends \Page{

	function page_getcategory(){
		$model = $this->add('xepan\cms\Model_TestimonialCategory');
		$model->addCondition('status','Active');
		$option = "<option value='0'>Please Select</option>";
		foreach ($model as $cat_model) {
			$option .= "<option value='".$cat_model['id']."'>".$cat_model['name']."</option>";
		}

		echo $option;
		exit;
	}
}