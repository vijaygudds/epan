<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_contactdetail extends \xepan\base\Page {
	public $title='Contact Detail';
	public $breadcrumb=['Home'=>'index','Contact'=>'xepan_base_contact','Details'=>'#'];

	function init(){
		parent::init();

		$contact = $this->add('xepan\base\Model_Contact')->load($this->api->stickyGET('contact_id'));

		$contact_view = $this->add('xepan\base\View_Contact');
		$contact_view->setModel($contact);

		
	}
}
