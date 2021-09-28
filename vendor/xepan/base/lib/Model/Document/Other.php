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

class Model_Document_Other extends Model_Table{
	
	public $table='document_info';
	public $acl='parent';

	public $for=null;

	public $bypass_hook=false;

	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan');

		$this->hasOne('xepan\base\Document');

		$this->addField('head');		
		$this->addField('value');
		$this->addField('type');

		// THIS SHOULD BE ON PAGE : NO CRUD : FORM WITH ALL HEADS VISIBLE WITH BIND CONDITIONAL VALUES
		// $document_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
		// if($this->for){
		// 	$document_other_info_config_m->addCondition('for',$this->for);
		// }
		// $document_other_info_config_m->tryLoadAny();
			
		// $this->getElement('head')->enum(array_map('trim',explode(",",$document_other_info_config_m['name'])));

		$this->addField('is_active')->type('boolean')->defaultValue(true);
		$this->addField('is_valid')->type('boolean')->defaultValue(true); // Mark false if found hard bounced

		$this->is([
				'head|required'
		]);

		$this->addExpression('document_type')->set(function($m,$q){
			return $m->refSQL('document_id')->fieldQuery('type');
		});
		$this->addHook('beforeSave',$this);
	}

	function beforeSave($m){		
    	$this->app->hook('document_info',[$this,$this->bypass_hook]);    	
	}

}
