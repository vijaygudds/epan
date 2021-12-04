<?php

/**
* description: Model Documet Attachment
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;


class Model_Document_Attachment extends \xepan\base\Model_Table{
	
	public $table='attachment';
	public $acl = false;

	function init(){
		parent::init();
		
		$this->hasOne('xepan\base\Document','document_id');
		$this->add('xepan\filestore\Field_File','file_id');
		$this->addExpression('thumb_url')->set(function($m,$q){
			return $q->expr('[0]',[$m->getElement('file')]);
		});

		$this->addHook('beforeDelete',$this);

		$this->is([
				'file_id|required'
			]);
	}

	function beforeDelete(){
		$this->ref('file_id')->delete();
	}
}
