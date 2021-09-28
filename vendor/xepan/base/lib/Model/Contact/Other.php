<?php

/**
* description: IM for Contact
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact_Other extends Model_Contact_Info{

	public $for=null;

	function init(){
		parent::init();

		// $contact_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
		// if($this->for){
		// 	$contact_other_info_config_m->addCondition('for',$this->for);
		// }
			
		$this->getElement('head');//->enum(array_map('trim',array_column($contact_other_info_config_m->getRows(),'name')));
		$this->addCondition('type','Other');
	}
}
