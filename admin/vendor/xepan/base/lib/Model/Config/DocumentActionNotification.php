<?php

namespace xepan\base;

class Model_Config_DocumentActionNotification extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
						'for'=>'xepan\base\DropDownNormal',
						'on_status'=>'xepan\base\NoValidateDropDownNormal',
						'sms_content'=>'Text',
						'sms_send_from'=>'DropDown',
						'email_subject'=>'line',
						'email_body'=>'xepan\base\RichText',
						'email_send_from'=>'DropDown',
						// 'send_to_all_employee_of_post'=>'DropDown',
						// 'send_to_employees'=>'Text',
						'email_send_to_custom_email_ids'=>'Text',
						'sms_send_to_custom_mobile_no'=>'Text',
						'sms_send_to_related_contact'=>'Checkbox',
						'email_send_to_related_contact'=>'Checkbox',
						'send_document_as_attachment'=>'Checkbox',
						'related_contact_field'=>'Hidden'
					];
	public $config_key = 'DOCUMENT_ACTION_NOTIFICATION';
	public $application = 'base';

	function init(){
		parent::init();

		$for_field = $this->getElement('for');
		$for_field->hint('Document Type like Quotation/SalesOrder/SalesInvoice/PurhcaseOrder etc');

		// $email_body = $this->getElement('email_body')->display(['form'=>'xepan\base\RichText']);
		$this->getElement('sms_send_from')->setModel('xepan\communication\Model_Communication_SMSSetting');
		$this->getElement('email_send_from')->setModel($this->add('xepan\communication\Model_Communication_EmailSetting')->addCondition('is_active',true));
		// $this->getElement('send_to_all_employee_of_post')->setModel($this->add('xepan\hr\Model_Post')->addCondition('status','Active'));
		// $this->getElement('send_to_employees')
		// 			->display(['form'=>'DropDown'])
		// 			->setModel($this->add('xepan\hr\Model_Employee')->addCondition('status','Active'));


		$this->add('Controller_Validator');
		
		$this->is(
			[
				'for|required',
				'status|required'
			]);
	}

}