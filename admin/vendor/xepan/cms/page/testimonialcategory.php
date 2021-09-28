<?php 

namespace xepan\cms;

class Page_testimonialcategory extends \xepan\base\Page{
	public $title = "Testimonial";
	function init(){
		parent::init();

		
		$model = $this->add('xepan\cms\Model_TestimonialCategory');
		$crud = $this->add('xepan\hr\CRUD'); 
		$crud->setModel($model);
		
	}
}