<?php

namespace xepan\webwidgets;

class page_testimonial extends \xepan\base\Page{
	public $title = "Testimonials";

	function init(){
		parent::init();
		
		$testimonials_crud = $this->add('xepan\hr\CRUD',null,null,['grid\testimonials']);
		$testimonials_crud->setModel('xepan\webwidgets\Model_Testimonial',['customer_name','title','description','file_id'],['customer_name','title','description','file','status']);	
	}
}