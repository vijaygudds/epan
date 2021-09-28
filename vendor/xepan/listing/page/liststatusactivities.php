<?php
 
namespace xepan\listing;

class page_liststatusactivities extends \xepan\base\Page {
	public $title='List Data';

	function init(){
		parent::init();

		$this->app->stickyGet('list_id');

		$list_model = $this->add('xepan\listing\Model_List');
		$list_model->load($_GET['list_id']);

		$model = $this->add('xepan\listing\Model_ListingStatusActivity');
		$model->addCondition('list_id',$_GET['list_id']);
		$crud = $this->add('xepan\hr\CRUD');
		$crud->setModel($model,null,['on_status','email_subject','email_send_to_creator','email_send_to_list_data_fields','email_send_to_custom_email_ids','sms_content','sms_send_to_creator','sms_send_to_list_data_fields','sms_send_to_custom_phone_numbers','status','list_dataset']);
		$crud->grid->removeAttachment();
		$crud->grid->removeColumn("status");
		$fields = $list_model->getDataModel()->getActualFields();
		
		if($crud->isEditing()){
			$form = $crud->form;
			$tags = $fields;
			$contact_fields = $this->add('xepan\base\Model_Contact')->getActualFields();
			array_walk($contact_fields,function(&$item){$item = '{$contact_'.$item.'}';});
			array_walk($tags,function(&$item){$item = '{$'.$item.'}';});
			
			$form->add('View')->set(implode(", ", array_merge($tags,$contact_fields)));
			$t = explode(',', $list_model['list_data_status']);
			$f = $form->getElement('on_status');
			$f->validate_values = false;			
			$f->setValueList(array_combine($t, $t));
			
			$fields = array_combine($fields, $fields);

			$f = $form->getElement('email_send_to_list_data_fields')->setAttr('multiple');
			$f->validate_values = false;			
			$f->setValueList($fields);
			$f->set(explode(',', $form->model['email_send_to_list_data_fields']));

			$f = $form->getElement('sms_send_to_list_data_fields')->setAttr('multiple');
			$f->validate_values = false;			
			$f->setValueList($fields);
			$f->set(explode(',', $form->model['sms_send_to_list_data_fields']));			

			$form->getElement('list_dataset_id')->getModel()->addCondition('list_id',$_GET['list_id']);
		}
	}
}

