<?php

/**
* description: ATK Page
* 
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_documentactionnotification extends \Page {
	public $title = 'Document Action Notification';
	public $datalist = [];
	public $crud;

	function init(){
		parent::init();

		foreach ($this->app->xepan_app_initiators as $app_init) {
			if($app_init->hasMethod('documentActionData')){
				$data = $app_init->documentActionData();
				$this->datalist = array_merge($this->datalist,$data);
			}
		}

		$model = $this->add('xepan\base\Model_Config_DocumentActionNotification');
		$model->getElement('for')->enum(array_keys($this->datalist));
		$this->crud = $crud = $this->add('xepan\hr\CRUD');


		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->makePanelCollepsible(true)
				->layout([
						'for~Notification For Document'=>'Document Status Notification~c1~6',
						'on_status'=>'c2~6',
						'sms_content'=>'SMS Details~c3~4',
						'sms_send_from'=>'c4~4',
						'sms_send_to_related_contact~&nbsp;'=>'c4~4',
						'sms_send_to_custom_mobile_no'=>'c5~4',
						'email_subject'=>'Email Detail~c6~8',
						'email_body'=>'c6~8',
						'email_send_from'=>'c7~4',
						'email_send_to_custom_email_ids'=>'c7~4',
						'send_document_as_attachment~&nbsp;'=>'c7~4',
						'email_send_to_related_contact~&nbsp;'=>'c7~4',
						// 'send_to_all_employee_of_post'=>'Notification Send to Contact~c8~4',
						// 'send_to_employees'=>'c9~8'
					]);

			$field_view = $form->add('View');
		}
		$crud->setModel($model);

		if($crud->isEditing()){

			$selected_for = $_GET['document_for'];
			$form = $crud->form;
			if($crud->isEditing('edit') && !isset($_GET['document_for'])){
				$selected_for = $crud->model['for'];
				$field_view->set(implode(", ", $this->datalist[$selected_for]['fields']));
			}

			// $form->getElement('send_to_employees')->enableMultiSelect();
			$for_field = $form->getElement('for');
			$status_field = $form->getElement('on_status');

			if(isset($this->datalist[$selected_for]['related_contact_field']))
				$related_contact_field = $form->getElement('related_contact_field')->set($this->datalist[$selected_for]['related_contact_field']);

			$for_field->js('change',
				[
					$form->js()->atk4_form('reloadField','on_status',[$this->app->url(null,['cut_object'=>$status_field->name]),'document_for'=>$for_field->js()->val()]),
					$field_view->js()->reload(['document_for'=>$for_field->js()->val()])
				]
			);
			// $for_field->js('change',[$this->js(null,[$status_field->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$status_field->name]),'document_for'=>$for_field->js()->val()])]);
			
			if($selected_for){
				$status_array = [];
				if(isset($this->datalist[$selected_for]['status'])) $status_array = $this->datalist[$selected_for]['status'];
				$status_field->setValueList(array_combine($status_array,$status_array));
				
				$field_view->set(implode(", ", $this->datalist[$selected_for]['fields']));
			}

		}

		$crud->grid->removeColumn('id');
		$crud->grid->removeColumn('email_body');
		$crud->grid->removeColumn('sms_send_from');
		$crud->grid->removeColumn('email_send_from');
		$crud->grid->removeColumn('related_contact_field');
		$crud->grid->addFormatter('sms_content','Wrap');
		$crud->grid->addFormatter('email_subject','Wrap');
	}
}
