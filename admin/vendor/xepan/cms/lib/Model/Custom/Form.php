<?php

namespace xepan\cms;

class Model_Custom_Form extends \xepan\base\Model_Table{

 	public $table = 'custom_form'; 
 	public $status=[
		'Active',
		'InActive'
	];
	// public $acl=false;
	public $actions=[
		'Active'=>['view','edit','delete','enquiry','deactivate','manage_fields'],
		'InActive'=>['view','edit','delete','enquiry','activate','manage_fields'],
	];

	function init(){
		parent::init();

		$this->hasOne('xepan\hr\Post_Email_MyEmails','emailsetting_id');
		$this->addField('name');
		$this->addField('submit_button_name');

		$this->addField('form_layout')->enum(array('stacked','minimal','horizontal','empty'));
		$this->addField('custom_form_layout_path')->type('text')->caption("Form Layout");
			
		$this->addField('recieve_email')->type('boolean');
		$this->addField('recipient_email')->hint('comma separated multiple email ids ');
		
		$this->addField('auto_reply')->type('boolean');
		$this->addField('email_subject');
		$this->addField('message_body')->type('text')->display(['form'=>'xepan\base\RichText']);
		$this->addField('created_at')->defaultValue($this->app->now);
		$this->addField('created_by_id')->defaultValue(@$this->app->employee->id);
		$this->addField('type')->defaultValue('Custom_Form');

		$this->addField('status')->defaultValue('Active');

		$this->addField('is_create_lead')->type('boolean')->caption('Create Lead');
		$this->addField('is_associate_lead')->type('boolean');

		$this->addField('lead_category_ids')->type('text');

		$this->hasMany('xepan\cms\Custom_FormField','custom_form_id');
		
		$this->addHook('afterSave',[$this,'updateJsonFile']);
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("CustomForm : '".$this['name']."' now active, For use on website", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_cms_customform")
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}

	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("CustomForm '".$this['name']."' has deactivated, not available use on website", null/* Related Document ID*/, $this->id /*Related Contact ID*/,null,null,"xepan_cms_customform")
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	function page_enquiry($p){
		$custom_form_sub_m = $p->add('xepan\cms\Model_Custom_FormSubmission');		
		$custom_form_sub_m->addCondition('custom_form_id',$this->id);

		$grid = $p->add('xepan\hr\Grid');
		$grid->setModel($custom_form_sub_m,['value','created_at'])->setOrder('created_at','desc');
		
		$grid->addPaginator(10);

		$grid->addHook('formatRow',function($g){
			$array = json_decode($g->model['value'],true);
			$g->current_row_html['value'] = implode("<br/>",
										array_map(
											    function($k, $v) {
											        return "$k : $v";
											    }, 
											    array_keys($array), 
											      $array
										)
									);
		});
	}

	function page_manage_fields($p){
		$form_id = $this->id;

		$field_model = $p->add('xepan\cms\Model_Custom_FormField')->addCondition('custom_form_id',$form_id);

		$crud_field = $p->add('xepan\hr\CRUD');
		$crud_field->setModel($field_model);
		$crud_field->grid->addQuickSearch(['name']);

		if($crud_field->isEditing()){
			$type_field = $crud_field->form->getElement('type');
			$type_field->js(true)->univ()->bindConditionalShow([
				'email'=>['auto_reply']
			],'div.atk-form-row');

		}
	}


	function updateJsonFile(){

		if(isset($this->app->skipDefaultTemplateJsonUpdate) && $this->app->skipDefaultTemplateJsonUpdate) return;

		$path = $this->api->pathfinder->base_location->base_path.'/websites/'.$this->app->current_website_name."/www/layout";
		if(!file_exists(realpath($path))){
			\Nette\Utils\FileSystem::createDir('./websites/'.$this->app->current_website_name.'/www/layout');
		}

		$forms = $this->add('xepan\cms\Model_Custom_Form')->getRows();
		foreach ($forms as &$form) {
			$fields = $this->add('xepan\cms\Model_Custom_FormField');
			$fields->addCondition('custom_form_id',$form['id']);
			$form['formfields'] = $fields->getRows();
		}

		$file_content = json_encode($forms);
		$fs = \Nette\Utils\FileSystem::write('./websites/'.$this->app->current_website_name.'/www/layout/customform.json',$file_content);
	}

}
