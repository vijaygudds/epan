<?php

namespace xepan\base;


class Model_Config_DocumentOtherInfo extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'for'=>'Line',
						'name'=>"Line",
						'type'=>"DropDown",
						'possible_values'=>"Text",
						'is_mandatory'=>'CheckBox',
						'conditional_binding'=>'Text'
					];
	public $config_key = 'Document_Other_Info_Fields';
	public $application='base';

	function init(){
		parent::init();
		$this->getElement('type')->enum(['Line','Text','DropDown','DatePicker']);
		$this->getElement('conditional_binding')->hint('Enter sperated lines for fields \n{"Value A":["Field B", "Field C","Field D"],"Value B":["Field A", "Field F"]}');
		$this->getElement('possible_values')->hint('Comma Seperated Values For DropDown type');
		$this->getElement('for')->setValueList(['Quotation'=>'Quotation','SalesOrder'=>'SalesOrder','SalesInvoice'=>'SalesInvoice','PurchaseOrder'=>'PurchaseOrder','PurchaseInvoice'=>'PurchaseInvoice','Item'=>'Item']);

		$this->addHook('beforeSave',function($m){
			$this['conditional_binding'] = trim($this['conditional_binding']);
			if($this['conditional_binding']){
				json_decode($this['conditional_binding']);
				if(json_last_error() !== JSON_ERROR_NONE){
					throw $this->exception('Not a valid JSON','ValidityCheck')
								->setField('conditional_binding')
								->addMoreInfo('Error',json_last_error_msg());
				}
			}
			$this['name'] = $this->app->normalizeName($this['name']);
		},[],4);

	}

}