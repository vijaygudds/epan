<?php

namespace xepan\cms;

class page_customform extends \xepan\base\Page {
	public $title='Custom Form';

	function init(){
		parent::init();
		$model_cust_form = $this->add('xepan\cms\Model_Custom_Form');

		$model_cust_form->addExpression('total_enquiry')->set(function($m,$q){
			return $this->add('xepan\cms\Model_Custom_FormSubmission')
					    ->addCondition('custom_form_id',$m->getElement('id'))
					    ->count();
		});

		$crud = $this->add('xepan\hr\CRUD');

		if($crud->isEditing()){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				// ->makePanelsCoppalsible()
				->layout([
						'name~'=>'Custom Form Name~c1~12',
						'submit_button_name'=>'Details~c1~6',
						'form_layout'=>'c2~6',
						'custom_form_layout_path~Design Form Layout'=>'c11~12',
						'explanation~'=>'c3~4',
						'recieve_email'=>'Receive Emails~c1~4',
						'recipient_email'=>'c2~8~Comma seperated email ids to receive form details, when new form is submitted',
						'auto_reply'=>'Auto Reply~c1~4',
						'emailsetting_id'=>'c11~8~Send Auto Reply email from this email account',
						'email_subject'=>'c2~12',
						'message_body'=>'c3~12',
						'is_create_lead~Create Lead'=>'Create Lead~c1~4',
						'is_associate_lead~Associate lead to category'=>'c2~8',
						'category'=>'c2~8~And associate with categories',

					]);
			$b = $form->layout->add('Button',null,'explanation')
				->set('Form Layout Hint');
			$b->add('VirtualPage')
			->bindEvent('How to arrange form fields ','click')
			->set([$this,"formLayoutExplanation"]);

			$categories_field = $form->addField('DropDown','category');
			$categories_field->setModel($this->add('xepan\marketing\Model_MarketingCategory'));
			$categories_field->addClass('multiselect-full-width');
			$categories_field->setAttr(['multiple'=>'multiple']);
			$categories_field->setAttr(['style'=>'width:50%']);
			$categories_field->setEmptyText("Please Select");

		}

		$crud->setModel($model_cust_form,['emailsetting_id','name','submit_button_name','form_layout','custom_form_layout_path','total_enquiry','recieve_email','recipient_email','auto_reply','email_subject','message_body','is_create_lead','is_associate_lead'],['name','total_enquiry','recieve_email','auto_reply','is_create_lead','status']);

		if($crud->isEditing()){
			$form = $crud->form;
			$cat_field = $form->getElement('category');

			if($form->isSubmitted()){
				$category_names = $form['category'];

				if(is_array($form['category'])){
					throw new \Exception("Error Processing Request", 1);
					$category_names = implode(",", $form['category']);
				}

				$form->model['lead_category_ids'] = $category_names;
				$form->model->save();
			}

			if($form->model['lead_category_ids'] != null)
				$cat_field->set( explode(",", $form->model['lead_category_ids']))->js(true)->trigger('changed');
		}

		// $crud->grid->add('VirtualPage')
		// 	->addColumn('Fields')
		// 	->set(function($page){
		// 		$form_id = $_GET[$page->short_name.'_id'];

		// 		$field_model = $page->add('xepan\cms\Model_Custom_FormField')->addCondition('custom_form_id',$form_id);

		// 		$crud_field = $page->add('xepan\hr\CRUD');
		// 		$crud_field->setModel($field_model);
		// 		$crud_field->grid->addQuickSearch(['name']);

		// 		if($crud_field->isEditing()){
		// 			$type_field = $crud_field->form->getElement('type');
		// 			$type_field->js(true)->univ()->bindConditionalShow([
		// 				'email'=>['auto_reply']
		// 			],'div.atk-form-row');

		// 		}
		// });
		$crud->grid->addQuickSearch(['name']);
		$crud->grid->removeColumn('status');
		$crud->noAttachment();
	}

	function formLayoutExplanation($page){
		$v = $page->add('View_Info');
		$ht = "'first_name~Field New Cpation'=>'Name Section|panel-type~c1~4',
				<br/>'nick_name'=>'c2~4~closed or any other text as field hint',
				<br/>'last_name'=>'c3~4',
				<br/>'city'=>'Location~c1~4~closed', // closed to make panel default collapsed
				<br/>'state'=>'c2~4',
				<br/>'country'=>'c3~4'";
		$ht .= "<br/><b>Field name is your define field name and space is replaced by _ (underscore)</b>";
		$v->setHtml($ht);
	}
}
