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

class page_contact_profile extends \Page {
	public $title='Contact Profile';

	function init(){
		parent::init();

		$contact = $this->add('xepan\base\Model_Contact')->load($this->api->stickyGET('id'));

		$d = $this->add('xepan\base\View_Document',
				[
					'action'=>$this->api->stickyGET('action')?:'view', // add/edit
					'id_fields_in_view'=>'["all"]/["post_id","field2_id"]',
					'allow_many_on_add' => false, // Only visible if editinng,
					'view_template' => ['view/profile']
				]
			);
		$d->setModel($contact,null,['first_name','last_name']);
		
		$emails_crud  = $d->addMany(
			$contact->ref('Emails'),
			$view_class='xepan\base\Grid',$view_options=null,$view_spot='Emails',$view_defaultTemplate=['view/profile','Emails'],$view_fields=null,
			$class='xepan\base\xCRUD',$options=null,$spot='Emails',$defaultTemplate=null,$fields=null
			);

		
	}
}
