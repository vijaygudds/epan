<?php

namespace xepan\communication;



class View_CommunicationNew extends \View {
	
	public $allowed_channels = ['email','call','call_sent','call_received','meeting','personal','comment','internal_chat'];

	public $channel_email=true;
	public $channel_sms=true;
	public $channel_call_sent=true;
	public $channel_call_received=true;
	public $channel_meeting=true;
	public $channel_comment=true;
	public $channel_internal_chat=true;

	public $showCommunicationHistory = true;
	public $showAddCommunications = true;
	public $showFilter = true;
	
	public $success_js = null;

	public $contact=null;
	public $edit_communication_id=null;
	public $is_editing = false;

	public $acl_controller = null;

	public $historyLister;
	public $config_subtype;
	public $config_company;

	function init(){
		parent::init();
		$this->template->loadTemplateFromString($this->myTemplate());

		$this->config_subtype = $this->add('xepan\communication\Model_Config_SubType');
		$this->config_subtype->tryLoadAny();

		$this->config_company = $this->add('xepan\base\Model_Config_CompanyInfo');			
		$this->config_company->tryLoadAny();

		$task_subtype_m = $this->add('xepan\projects\Model_Config_TaskSubtype');
		$task_subtype_m->tryLoadAny();
		$this->task_subtype = explode(",",$task_subtype_m['value']);
		$this->task_subtype = array_combine($this->task_subtype, $this->task_subtype);

		$this->app->stickyGET('edit_communication_id');
		$this->edit_vp = $this->add('VirtualPage')
			->set(function($page){
				$id = $_GET['edit_communication_id'];
				
				$config_m = $this->config_subtype;
				$company_m = $this->config_company;

				$form_fields = [
							'communication_sub_type~'.$config_m['sub_type_1_label_name']=>'Edit Communication~c1~4',
							'calling_status~'.$config_m['sub_type_2_label_name']=>'c2~4',
							'sub_type_3~'.$config_m['sub_type_3_label_name']=>'c3~4',
							'created_at'=>'c4~3',
							'from_number'=>'c5~3',
							'to_number'=>'c6~3',
							'employee'=>'c7~3',
							'description'=>'c8~12'
						];

				$m = $this->add('xepan\communication\Model_Communication');
				$m->load($id);
				$comm_model = $this->add('xepan\communication\Model_Communication_Call');
				$comm_model->addCondition('status','Called');
				$comm_model->addCondition('id',$id);
				$comm_model->tryLoadAny();
				if($m['status'] == "Called"){

					$form_fields = [
							'communication_sub_type~'.$config_m['sub_type_1_label_name']=>'Edit Call Received Communication~c1~4',
							'calling_status~'.$config_m['sub_type_2_label_name']=>'c2~4',
							'sub_type_3~'.$config_m['sub_type_3_label_name']=>'c3~4',
							'created_at'=>'c4~3',
							'from_number'=>'c5~3',
							'to_number'=>'c6~3',
							'employee'=>'c7~3',
							'description'=>'c8~12'
						];

				}elseif($m['status'] == "Received"){
					$comm_model = $this->add('xepan\communication\Model_Communication_Call');
					$comm_model->addCondition('status','Received');
					$comm_model->addCondition('id',$id);
					$comm_model->tryLoadAny();

					$form_fields = [
							'communication_sub_type~'.$config_m['sub_type_1_label_name']=>'Edit Call Received Communication~c1~4',
							'calling_status~'.$config_m['sub_type_2_label_name']=>'c2~4',
							'sub_type_3~'.$config_m['sub_type_3_label_name']=>'c3~4',
							'created_at'=>'c1~3',
							'from_number'=>'c2~3',
							'to_number'=>'c3~3',
							'employee'=>'c4~3',
							'description'=>'c5~12'
						];
				}elseif($m['status'] == "Personal"){
					$comm_model = $this->add('xepan\communication\Model_Communication_Personal');
					$comm_model->addCondition('status','Personal');
					$comm_model->addCondition('id',$id);
					$comm_model->tryLoadAny();

					$form_fields = [
							'communication_sub_type~'.$config_m['sub_type_1_label_name']=>'Edit Call Received Communication~c1~4',
							'calling_status~'.$config_m['sub_type_2_label_name']=>'c2~4',
							'sub_type_3~'.$config_m['sub_type_3_label_name']=>'c3~4',
							'created_at'=>'c4~4',
							'employee'=>'c5~4',
							'related_employee'=>'c6~12',
							'description'=>'c7~12'
						];
				}elseif($m['status'] == "Commented"){
					$comm_model = $this->add('xepan\communication\Model_Communication_Comment');
					$comm_model->addCondition('status','Commented');
					$comm_model->addCondition('id',$id);
					$comm_model->tryLoadAny();

					$form_fields = [
							'communication_sub_type~'.$config_m['sub_type_1_label_name']=>'Edit Call Received Communication~c1~4',
							'calling_status~'.$config_m['sub_type_2_label_name']=>'c2~4',
							'sub_type_3~'.$config_m['sub_type_3_label_name']=>'c3~4',
							'created_at'=>'c4~4',
							'employee'=>'c5~4',
							'description'=>'c6~12'
						];
				}

				$contact = $this->add('xepan\base\Model_Contact');
				$contact->load($comm_model['to_id']);
				
				$form = $page->add('Form');
				$form->add('xepan\base\Controller_FLC')
					->makePanelCollepsible()
					->closeOtherPanels()
					->addContentSpot()
					->layout($form_fields);


				$company_number = explode(",", $company_m['mobile_no']);
				$company_number = array_combine($company_number, $company_number);

				$sub_type_array = explode(",",$config_m['sub_type']);
				$sub_type_field = $form->addField('xepan\base\DropDown','communication_sub_type')->setEmptyText("Please Select");
				$sub_type_field->setValueList(array_combine($sub_type_array,$sub_type_array));
				$sub_type_field->set($comm_model['sub_type']);

				$status_array = explode(",",$config_m['calling_status']);
				$status_field = $form->addField('xepan\base\DropDown','calling_status')->setEmptyText('Please Select');
				$status_field->setValueList(array_combine($status_array,$status_array));
				$status_field->set($comm_model['calling_status']);

				$sub_type_3_array = explode(",",$config_m['sub_type_3']);
				$sub_type_3_field = $form->addField('xepan\base\DropDown','sub_type_3')->setEmptyText('Please Select');
				$sub_type_3_field->setValueList(array_combine($sub_type_3_array,$sub_type_3_array));
				$sub_type_3_field->set($comm_model['sub_type_3']);

				$form->addField('DateTimePicker','created_at')->validate('required')->set($comm_model['created_at']);
				
				$employee_field = $form->addField('xepan\hr\Employee','employee')->set($comm_model['from_id']);

				$form->addField('xepan\base\RichText','description')
						->set($comm_model['description']);

				if(isset($form_fields['from_number'])){
					$from_number_field = $form->addField('xepan\base\DropDown','from_number');
					$emp_phones = $this->app->employee->getPhones();
					$emp_phones = array_combine($emp_phones, $emp_phones);
					$from_number_field->setValueList(array_merge(array_filter($company_number),array_filter($emp_phones)));
					$from_number_field->select_menu_options = ['tags'=>true];
					$from_number_field->validate_values = false;

					$from_raw = json_decode($comm_model['from_raw'],true);
					$from_number_field->set($from_raw['number']);
				}

				if(isset($form_fields['to_number'])){

					$phones = [];
					$phones = $contact->getPhones();
					$to_number_field = $form->addField('xepan\base\DropDown','to_number');
					$to_number_field->setValueList(array_combine($phones,$phones));
					$to_number_field->select_menu_options = ['tags'=>true];
					$to_number_field->validate_values = false;

					$to_raw = json_decode($comm_model['to_raw'],true);
					$to_number_field->set($to_raw[0]['number']);
				}

				if($m['status'] == "Personal"){
					$related_employees = $form->addField('dropDown','related_employee');
					$related_employees->addClass('multiselect-full-width')
									->setAttr(['multiple'=>'multiple']);
					$related_employees->setModel('xepan\hr\Model_Employee_Active');
					$related_employees->set($m->getCommunicationRelatedEmployee());
				}

				$form->addSubmit('Update Communication')->addClass('btn btn-primary');

				if($form->isSubmitted()){

					foreach ($form_fields as $key => $value) {
						$comm_model[$key] = $form[$key];
					}
					// $comm_model['created_at'] = $form['created_at'];

					if(isset($form_fields['to_number'])){
						$comm_model->addTo($form['to_number'],$contact['name']);
					}

					if(isset($form_fields['from_number'])){
						$emp = $this->add('xepan\hr\Model_Employee')->load($form['employee']);
						// $comm_model->setFrom($form['from_number'],$emp['name']);
						$to=['name'=>$emp['name'],'number'=>$form['from_number']];
						$comm_model->set('from_raw',$to);
					}
					$comm_model['from_id'] = $form['employee'];
					$comm_model->save();

					$form->js(null,$form->js()->reload())->univ()->successMessage('communication updated')->execute();
				}

			});
		
		$this->acl_controller = $this->add('xepan\hr\Controller_ACL',['based_on_model'=>'xepan\communication\Model_Communication']);

	}

	function filter(){

		if($start_date = $this->app->stickyGET('start_date')){
			$this->model->addCondition('created_at','>=',$start_date);
		}

		if($end_date = $this->app->stickyGET('end_date')){
			$this->model->addCondition('created_at','<',$this->app->nextDate($end_date));
		}

		if($related_contact_id = $this->app->stickyGET('related_contact_id')){
			$this->model->addCondition([
								['from_id',$related_contact_id],
								['to_id',$related_contact_id]
							]);
		}

		if($comm_type = $this->app->stickyGET('communication_type')){
			$this->model->addCondition('communication_type',explode(",", $comm_type));
		}

		if($direction = $this->app->stickyGET('direction')){
			$this->model->addCondition('direction',$direction);
		}

		if($search = $this->app->stickyGET('search_string')){
			$this->model->addExpression('Relevance')
					->set('MATCH(title,description,communication_type) AGAINST ("'.$search.'")');
			$this->model->addCondition('Relevance','>',0);
 			$this->model->setOrder('Relevance','Desc');
		}
	}

	function setCommunicationsWith($contact){
		$this->contact = $contact;

		$this->contact_emails = $this->contact->getEmails();
		$this->contact_phones = $this->contact->getPhones();
		$this->employee_emails = $this->app->employee->getEmails();
		$this->employee_phones = $this->app->employee->getPhones();

		$communication = $this->add('xepan\communication\Model_Communication');
		$communication->addCondition([['from_id',$contact->id],['to_id',$contact->id],['related_contact_id',$contact->id]]);
		$communication->setOrder('created_at','desc');

		return $this->setModel($communication);
	}

	function setCommunicationsRelatedToDocument($document){

	}


	function setModel($model){
		if($model->loaded()) $this->is_editing = true;
		return parent::setModel($model);
	}

	function showCommunicationHistory($show){
		$this->showCommunicationHistory= $show;
	}

	function showAddCommunications($show){
		$this->showAddCommunications = $show;
	}

	function addChannels($channels){
		if(is_array($channels)){
			foreach ($channels as $ch) {
				$this->addChannels($ch);
			}
			return;
		}

		if(!in_array(strtolower($channels), $this->channels)) 
			throw $this->exception('Unknown channel')->addMoreInfo('Available Channels ',implode(", ", $this->channels))->addMoreInfo('Provided Channel',$channels);
			
		switch (strtolower($channels)) {
			case 'email':
				$this->channel_email = true;
				break;
			case 'sms':
				$this->channel_sms = true;
				break;
			case 'call':
				$this->channel_call_sent = true;
				$this->channel_call_received = true;
				break;
			case 'call_sent':
				$this->channel_call_sent = true;
				break;
			case 'call_received':
				$this->channel_call_received = true;
				break;
			case 'meeting':
			case 'personal':
				$this->channel_meeting = true;
				break;
			case 'comment':
				$this->channel_comment = true;
				break;
			case 'internal_chat':
				$this->channel_internal_chat = true;
				break;
		}
	}

	function addTopBar(){

		if($this->acl_controller->hasMethod('canAdd') &&  !$this->acl_controller->canAdd()) return;

			$this->manageCalled();

		// if($this->showFilter){
		// 	$this->addFilter();
		// }
	}

	function addSuccessJs($js){
		$this->success_js = $js;
	}

	function addCommunicationHistory(){
		// $communication = $this->model;

		// $this->historyLister = $lister=$this->add('xepan\communication\View_Lister_NewCommunication',['contact_id'=>$this->contact->id],null,null);
		// if($this->app->stickyGET('communication_filter')){
		// 	$this->filter();
		// }

		// $lister->setModel($communication)->setOrder(['created_at desc','id desc']);
		// $p = $lister->add('xepan\base\Paginator',null,'Paginator');
		// $p->setRowsPerPage($this->ipp = 10);

		// $lister->js('click',$this->js()->univ()->frameURL('Edit Communication',[$this->app->url($this->edit_vp->getURL()),'edit_communication_id'=>$this->js()->_selectorThis()->data('id')]))
		// 	->_selector('.do-view-edit-communication');
	}


	function manageCalled(){
		$m = $this->add('xepan\communication\Model_Communication');
		if($this->edit_communication_id){	
			$m->load($this->edit_communication_id);
		}	
		$config_m = $this->add('xepan\communication\Model_Config_SubType');
		$config_m->tryLoadAny();
		$form = $this->add('Form');
		$form->add('xepan\base\Controller_FLC')
			->makePanelCollepsible()
			->closeOtherPanels()
			->addContentSpot()
			->layout([
				'communication_type'=>'Initial Communication~x1~12~open',
					'sub_type~'.$config_m['sub_type_1_label_name']?:"Product/ Service/ Related To"=>'x2~4',
					'calling_status~'.$config_m['sub_type_2_label_name']?:"Communication Result"=>'x3~4',
					'sub_type_3~'.$config_m['sub_type_3_label_name']?:"Communication Remark"=>'x4~4',
					'communication_for'=>'x5~4',
					'communication_sub_for'=>'x6~4',
					'call_direction'=>'x7~4',
					'meeting_direction'=>'x8~4',
					'email_to'=>'x41~12',
					'cc_mails'=>'x42~12',
					'bcc_mails'=>'x43~12',
					// 'title'=>'x8~12',
					'body'=>'x9~12',
					'from_email'=>'x10~6',
					'from_phone'=>'x11~6',
					'from_person'=>'x12~6',
					'called_to'=>'x13~6',
					'from_number'=>'x14~6',
					'sms_to'=>'x15~6',
					'sms_settings'=>'x16~6',
					'follow_up'=>'f1~12',
					'followup_assign_to'=>'f2~4',
					'starting_at'=>'f3~3',
					'followup_type'=>'f31~3',
					'existing_schedule'=>'f32~2',
					'description'=>'f4~12',
					'set_reminder'=>'r1~12',
					'remind_via'=>'r2~6',
					'notify_to'=>'r3~6',
					'reminder_time'=>'r4~4',
					// 'force_remind'=>'r5~12',
					'snooze_duration'=>'r6~4',
					'remind_unit'=>'r7~4',
					'score~Score (Is Lead Positive or Negative)'=>'c11~2~or leave as it is for nutral',
					'score_buttons~'=>'c12~3',
			]);


		$config_m = $this->config_subtype;
		$company_m = $this->config_company;
		
		$company_number = explode(",", $company_m['mobile_no']);
		$company_number = array_combine($company_number, $company_number);

		// SCORE BUTTONS START
		$score_field = $form->addField('hidden','score')->set('0');
		$set = $form->layout->add('ButtonSet',null,'score_buttons');
		$up_btn = $set->add('Button')->set('+10')->addClass('btn');
		$down_btn = $set->add('Button')->set('-10')->addClass('btn');
		$up_btn->js('click',[$score_field->js()->val(10),$down_btn->js()->removeClass('btn-danger'),$this->js()->_selectorThis()->addClass('btn-success')]);
		$down_btn->js('click',[$score_field->js()->val(-10),$up_btn->js()->removeClass('btn-success'),$this->js()->_selectorThis()->addClass('btn-danger')]);

		$sub_type_array = explode(",",$config_m['sub_type']);
		$for_m = $this->add('xepan\marketing\Model_Communication_For');
		$subfor_m = $this->add('xepan\marketing\Model_Communication_SubFor');
		if($this->edit_communication_id){
			$for_m->addCondition('id',$m['communication_for_id']);
			$subfor_m->addCondition('id',$m['communication_subfor_id']);
		}
		$for_type_field = $form->addField('Dropdown','communication_for')->validateNotNull();
		// $for_type_field->setEmptyText('Please select communication For');
		$for_type_field->setModel($for_m);
		
		$subfor_type_field = $form->addField('DropDown','communication_sub_for')->validateNotNull();
		// $subfor_type_field->setEmptyText('Please select communication Sub For');
		$subfor_type_field->setModel($subfor_m);
			
		if($this->app->stickyGET('for_id')){
			$subfor_m->addCondition('for_id',$this->app->stickyGET('for_id'));
		}

		$for_type_field->js('change',[$subfor_type_field->js(null,[$subfor_type_field->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$subfor_type_field->name]),'for_id'=>$for_type_field->js()->val()])]);

		// $com_purpose = $form->addField('DropDown','commmunication_purpose');
		// $com_purpose->setEmptyText('Please Select Communication Purpose');
		// $com_purpose->setValueList(['LOAN'=>'LOAN','LEGAL'=>'LEGAL','MARKETING'=>'MARKETING','RECOVERY'=>'RECOVERY','BIKEAUCTION'=>'BIKEAUCTION']);

		$type_field = $form->addField('dropdown','communication_type')->set($m['communication_type']);
		$type_field->setEmptyText('Please select communication By');
		$type_field->setValueList([/*'Email'=>'Email',*/'Call'=>'Call','Meeting'=>'Meeting','FollowupCall'=>'Followup Call','NotCommunicated'=>'Not Communicated'/*,'TeleMarketing'=>'TeleMarketing','Personal'=>'Personal','Comment'=>'Comment','SMS'=>'SMS'*/]);

		$sub_type_field = $form->addField('dropdown','sub_type')->set($m['sub_type'])->validateNotNull();
		$sub_type_field->setEmptyText('Please Select');
		$sub_type_field->setValueList(array_combine($sub_type_array,$sub_type_array));

		$calling_status_array = explode(",",$config_m['calling_status']);
		$calling_status_field = $form->addField('dropdown','calling_status')->set($m['calling_status'])->setEmptyText('Please Select');
		$calling_status_field->setValueList(array_combine($calling_status_array,$calling_status_array));

		$sub_type_3_array = explode(",",$config_m['sub_type_3']);
		$sub_type_3_field = $form->addField('DropDown','sub_type_3')->set($m['sub_type_3']);//->setEmptyText('Please Select');
		$sub_type_3_field->setValueList(array_combine($sub_type_3_array,$sub_type_3_array));

		$status_field = $form->addField('dropdown','call_direction')->set($m['status']);
		$status_field->setValueList(['Called'=>'Called (Out)','Received'=>'Received (In)'])->setEmptyText('Please Select');
		$status_field = $form->addField('dropdown','meeting_direction')->set($m['status']);
		$status_field->setValueList(['Meeting'=>'Meeting','Not Meet'=>'Not Meet'])->setEmptyText('Please Select');

		$email_to_field = $form->addField('email_to');
		$cc_email_field = $form->addField('cc_mails');
		$bcc_email_field = $form->addField('bcc_mails');

		$form->addField('hidden','title');
		$form->addField('xepan\base\RichText','body')->set($m['description']);

		$from_email=$form->addField('dropdown','from_email')->setEmptyText('Please Select From Email');
		$my_email = $form->add('xepan\hr\Model_Post_Email_MyEmails');
		$from_email->setModel($my_email);

		// $form->addField('line','from_phone');

		$from_number_field = $form->addField('xepan\base\DropDown','from_phone');
		$this->employee_phones = $this->app->employee->getPhones();
		$emp_phones = $this->employee_phones;
		$emp_phones = array_combine($emp_phones, $emp_phones);

		$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');			
		$company_m->tryLoadAny();
		$company_number = explode(",", $company_m['mobile_no']);
		$company_number = array_combine($company_number, $company_number);

		$from_number_field->setValueList(array_filter($company_number)+array_filter($emp_phones));
		$from_number_field->select_menu_options = ['tags'=>true];
		$from_number_field->validate_values = false;

		$emp_field = $form->addField('DropDown','from_person');
		$emp_model = $this->add('xepan\hr\Model_Employee');			
		$emp_field->setModel($emp_model);
		$emp_field->set($this->app->employee->id);
		$phones = $this->contact_phones;
		$called_to_field = $form->addField('xepan\base\DropDown','called_to');
		$called_to_field->select_menu_options=['tags'=>true];
		$called_to_field->validate_values=false;
		// $called_to_field->setAttr(['multiple'=>'multiple']);
		$called_to_field->setValueList(array_combine($phones,$phones));



		$form->addField('line','from_number');
		$form->addField('line','sms_to');
		$form->addField('DropDown','sms_settings')->setModel('xepan\communication\Model_Communication_SMSSetting');

		$follow_up_field = $form->addField('checkbox','follow_up','Add Followup');
		$starting_date_field = $form->addField('DateTimePicker','starting_at');
		$starting_date_field->js(true)->val('');
		$assign_to_field = $form->addField('DropDown','followup_assign_to');
		$assign_to_field->setModel('xepan\hr\Model_Employee')->addCondition('status','Active');
		$assign_to_field->set($this->app->employee->id);
		$description_field = $form->addField('text','description');
		
		$followup_type = $form->addField('DropDown','followup_type')->setValueList($this->task_subtype)->setEmptyText('Please Select ...');

		$set_reminder_field = $form->addField('checkbox','set_reminder');
		$remind_via_field = $form->addField('DropDown','remind_via')->setValueList(['Email'=>'Email','SMS'=>'SMS','Notification'=>'Notification'])->setAttr(['multiple'=>'multiple'])->setEmptyText('Please Select A Value');
		$notify_to_field = $form->addField('DropDown','notify_to')->setAttr(['multiple'=>'multiple'])->setEmptyText('Please select a value');
		$notify_to_field->setModel('xepan\hr\Model_Employee')->addCondition('status','Active');
		$reminder_time  = $form->addField('DateTimePicker','reminder_time');
		$reminder_time->js(true)->val('');

		// $force_remind_field = $form->addField('checkbox','force_remind','Enable Snoozing [Repetitive Reminder]');
		$snooze_field = $form->addField('snooze_duration');
		$remind_unit_field = $form->addField('DropDown','remind_unit')->setValueList(['Minutes'=>'Minutes','hours'=>'Hours','day'=>'Days'])->setEmptyText('Please select a value');
		
		$form->layout->add('xepan\projects\View_EmployeeFollowupSchedule',['employee_field'=>$assign_to_field,'date_field'=>$starting_date_field,'follow_type_field'=>$followup_type],'existing_schedule');

		$set_reminder_field->js(true)->univ()->bindConditionalShow([
			true=>['remind_via','notify_to','reminder_time','force_remind','snooze_duration','remind_unit']
		],'div.col-md-1,div.col-md-2,div.col-md-3,div.col-md-4,div.col-md-6,div.col-md-12');

			// $emails_field->js('change',$email_to_field->js()->val($emails_field->js()->val()));
			// $number_field->js('change','
			// 	$("#'.$called_to_field->name.'").html("");
			// 	$.each($("#'.$number_field->name.'").val().split(","), function(index,item){
			// 		// console.log($("#'.$number_field->name.'").val());
			// 		$("#'.$called_to_field->name.'").append($("<option/>", {
			// 	        value: item, text: item
			// 	    }));
			// 	});
			// 	$("#'.$called_to_field->name.'").trigger("change");
			// ');
			// $.each($('.$called_to_field.').val().split(","),function(item,index){$.create("option", {"value": '"+item+"'}, "").appendTo('#mySelect');})');

		$follow_up_field->js(true)->univ()->bindConditionalShow([
			true=>['follow_up_type','task_title','starting_at','followup_assign_to','description','set_reminder','followup_type','existing_schedule']
		],'div.col-md-1,div.col-md-2,div.col-md-3,div.col-md-4,div.col-md-6,div.col-md-12');

		$type_field->js(true)->univ()->bindConditionalShow([
			''=>[],
			'Email'=>['sub_type','calling_status','sub_type_3','email_to','cc_mails','bcc_mails','title','body','from_email','email_to','cc_mails','bcc_mails'],
			'Call'=>['sub_type','calling_status','sub_type_3','title','body','from_phone','from_person','called_to','notify_email','notify_email_to','status','calling_status','call_direction','communication_for','communication_sub_for'],
			'FollowupCall'=>['sub_type','calling_status','sub_type_3','title','body','from_phone','from_person','called_to','notify_email','notify_email_to','status','calling_status','call_direction','communication_for','communication_sub_for'],
			'Received'=>['sub_type','calling_status','sub_type_3','title','body','from_phone','from_person','called_to','notify_email','notify_email_to','status','calling_status','call_direction','communication_for','communication_sub_for'],
			'Meeting'=>['sub_type','meeting_direction','sub_type_3','title','body','from_phone','from_person','called_to','notify_email','notify_email_to','status','calling_status','meeting_direction','communication_for','communication_sub_for'],
			'NotCommunicated'=>['calling_status'],
			'TeleMarketing'=>['sub_type','calling_status','sub_type_3','title','body','from_phone','called_to'],
			'Personal'=>['sub_type','calling_status','sub_type_3','title','body','from_person'],
			'Comment'=>['sub_type','calling_status','sub_type_3','title','body','from_person'],
			'SMS'=>['sub_type','calling_status','sub_type_3','title','body','from_number','sms_to','sms_settings']
		],'div.col-md-1,div.col-md-2,div.col-md-3,div.col-md-4,div.col-md-6,div.col-md-12');

		$form->addSubmit('Create Communication')->addClass('btn btn-primary');

		// $form->layout->add('xepan\projects\View_EmployeeFollowupSchedule',['employee_field'=>$assigned_to,'date_field'=>$followup_on,'follow_type_field'=>$followup_type],'existing_schedule');

		if($form->isSubmitted()){
			// throw new \Exception($form['communication_type'], 1);
			
			if($form['communication_type'] === "NotCommunicated"){
				// $form['calling_status'] ="EMI ALL READY DEPOSITED";
				if(!$form['calling_status']){
						$form->displayError('calling_status','Communication Result must be filled');
				}
				$form['communication_for'] = "0";	
				$form['communication_sub_for'] = "0";
			}else{	
				if(!$form['sub_type']){
					$form->displayError('sub_type','Sub type must be filled');
				}
				if($form['communication_type'] === 'Meeting'){
					if(!$form['meeting_direction']){
						$form->displayError('meeting_direction','Meeting Direction must be filled');
					}
				}else{
					if(!$form['call_direction']){
						$form->displayError('call_direction','Called Direction must be filled');
					}
				}
				
				if(!$form['calling_status']){
						$form->displayError('calling_status','Communication Result must be filled');
				}

				if($form['calling_status'] === 'PHONE ATTEND'){
					if(!$form['body']){
						$form->displayError('body',' Communication Description is Required');
					}	
				}
				
				$subfor = $this->add('xepan\marketing\Model_Communication_SubFor');
				if($form['communication_sub_for']){
					$subfor->load($form['communication_sub_for']);
				}
				$form['title'] = $subfor['name']. ' - ' .$form['sub_type']. ' - ' . $form['calling_status'];


				// if(!$form['body']) $form->displayError('body','Please specify content');
				// if(!$form['communication_for']) $form->displayError('communication_for','Please specify Communication For');
				// if(!$form['communication_sub_for']) $form->displayError('communication_sub_for','Please specify Communication For');
				switch ($form['communication_type']) {
					case 'Email':
						if(!$form['title']) $form->displayError('title','Please specify title');
						if(!$form['email_to']) $form->displayError('email_to','Please specify "Email To" Value');
						foreach (explode(",", $form['email_to']) as $e) {
							if (!filter_var(trim($e), FILTER_VALIDATE_EMAIL)) {
								$form->displayError('email_to',$e.' is not an valid Email');
							}
						}
						if($form['cc_mails']){
							foreach (explode(",", $form['cc_mails']) as $e) {
								if (!filter_var(trim($e), FILTER_VALIDATE_EMAIL)) {
									$form->displayError('cc_mails',$e.' is not an valid Email');
								}
							}	
						}
						if($form['bcc_mails']){
							foreach (explode(",", $form['bcc_mails']) as $e) {
								if (!filter_var(trim($e), FILTER_VALIDATE_EMAIL)) {
									$form->displayError('bcc_mails',$e.' is not an valid Email');
								}
							}	
						}
						if(!$form['from_email']) $form->displayError('from_email','Please specify "From Email" value');
						break;
					case "Call":
						if(!$form['call_direction']) $form->displayError('call_direction','Please specify "Call Direction"');
						if(!$form['from_phone']) $form->displayError('from_phone','From Phone must not be empty');
						if(!$form['called_to']) $form->displayError('called_to','Called to  must not be empty');
						break;
					case "Meeting":
						if(!$form['meeting_direction']) $form->displayError('meeting_direction','Please specify "Meeting Direction"');
						// if(!$form['from_phone']) $form->displayError('from_phone','From Phone must not be empty');
						// if(!$form['called_to']) $form->displayError('called_to','Called to  must not be empty');
						break;	
							// case "SMS":
							// 	if(!$form['sms_to']) $form->displayError('sms_to','Please specify "sms_to" value');
							// 	if(!$form['sms_settings']) $form->displayError('sms_settings','Please specify "sms_settings"');
							// 	break;
						default:
						# code...
						break;
				}
			}
			$commtype = $form['communication_type'];
			// $this->contact = $m;
						
			$communication = $this->add('xepan\communication\Model_Communication_'.$commtype);
			// $communication = $this->add('xepan\communication\Model_Communication_Call');

			$communication['from_id']=$form['from_person'];
			$communication['communication_type']=$form['communication_type'];
			$communication['to_id']= $this->contact->id;
			$communication['sub_type']=$form['sub_type'];
			$communication['calling_status']=$form['calling_status'];
			$communication['sub_type_3']=$form['sub_type_3'];
			$communication['score']=$form['score'];
			$communication['communication_for_id'] = $form['communication_for'];
			$communication['communication_subfor_id'] = $form['communication_sub_for'];	

			switch ($commtype) {
				case 'TeleMarketing':
					$communication['from_id']=$form['from_person'];
					$communication['status'] = 'Called';	
					$_to_field='called_to';
				case 'Call':
					$send_settings = $form['from_phone'];
					if($form['call_direction']=='Received'){
						$_to_field='from_phone';
						$communication['from_id']=$this->contact->id;
						$communication['to_id']=$form['from_person']; // actually this is to person this time
						$communication['direction']='In';
						$communication['satus']='Received';
						$communication->setFrom($form['from_phone'],$this->contact['name']);
					}else{					
						$communication['from_id']=$form['from_person']; // actually this is to person this time
						$communication['to_id']=$this->contact->id;
						$communication['direction']='Out';
						$communication['satus']='Called';
						$employee_name=$this->add('xepan\hr\Model_Employee')->load($form['from_person'])->get('name');
						$communication->setFrom($form['from_phone'],$employee_name);
						$_to_field='called_to';
					}
					break;
				case 'FollowupCall':
				$send_settings = $form['from_phone'];
				if($form['status']=='Received'){
						// echo "string". $this->contact->id. "<br/>";
						// echo "string". $this['from_person']. "<br/>";
						$communication['from_id']=$this->contact->id;
						$communication['to_id']=$form['from_person']; // actually this is to person this time
						$_to_field='from_phone';
						$communication['direction']='In';
						$communication['status']='Received';
						$employee_name=$this->add('xepan\hr\Model_Employee')->load($form['from_person'])->get('name');
						$communication->setFrom($form['from_phone'],$this->contact['name']);
						// $communication->addTo($this['from_phone'],$employee_name);
					}else{					
						$communication['from_id']=$form['from_person']; // actually this is to person this time
						$communication['to_id']=$this->contact->id;
						$communication['direction']='Out';
						$communication['status']='Called';
						$employee_name=$this->add('xepan\hr\Model_Employee')->load($form['from_person'])->get('name');
						$communication->setFrom($form['from_phone'],$employee_name);
						// $communication->addTo($this['from_phone'],$this->contact['name']);
					$_to_field='called_to';
					}
				// throw new \Exception("Error Processing Request", 1);
				// $communication['status']=$this['status'];

				// if($this['notify_email']){
				// 	if(!$this['notify_email_to'])
				// 		$this->displayError('notify_email_to','Notify Email is required');
					
				// 	$send_settings = $this->add('xepan\communication\Model_Communication_EmailSetting');
				// 	$send_settings->tryLoad($this['from_email']?:-1);
				// }
				break;	
				case 'Meeting':
					$send_settings = $form['from_phone'];
					$employee_name=$this->add('xepan\hr\Model_Employee')->load($form['from_person'])->get('name');
					$_to_field=$this->contact['name'];
					if($form['meeting_direction']=='Meeting'){
						
						$communication['from_id']=$this->contact->id;
						$communication['to_id']=$form['from_person']; // actually this is to person this time
						$communication['direction']='Meet';
						$communication['status']='Meet';
						$communication->setFrom($form['from_phone'],$employee_name);
					}else{					
						$communication['from_id']=$form['from_person']; // actually this is to person this time
						$communication['to_id']=$this->contact->id;
						$communication['direction']='Not Meet';
						$communication['status']='Not Meet';
						$communication->setFrom($form['from_phone'],$employee_name);
					}	

					
					// $communication['status']=$form['status'];
					// $_to_field='called_to';

					break;
			}

			if($form['score']){
				$model_point_system = $this->add('xepan\base\Model_PointSystem');
				$model_point_system['contact_id'] = $this->contact->id;
				$model_point_system['score'] = $form['score'];
				$model_point_system->save();
			}	

			// Followup 
			if($form['follow_up']){
				$model_task = $this->add('xepan\projects\Model_Task');
				$model_task['type'] = 'Followup';
				$model_task['task_name'] = 'Followup '. $this->contact['name_with_type'];
				$model_task['created_by_id'] = $this->app->employee->id;
				$model_task['starting_date'] = $form['starting_at'];
				$model_task['assign_to_id'] = $form['followup_assign_to'];
				$model_task['description'] = $form['description'];
				$model_task['related_id'] = $this->contact->id;
				$model_task['sub_type'] = $form['followup_type'];
						
				if($form['set_reminder']){
					$model_task['set_reminder'] = true;
					$model_task['reminder_time'] = $form['reminder_time'];
					$model_task['remind_via'] = $form['remind_via'];
					$model_task['notify_to'] = $form['notify_to'];
							
					if($form['force_remind']){
						$model_task['snooze_duration'] = $form['snooze_duration'];
						$model_task['remind_unit'] = $form['remind_unit'];

					}
				}
				$model_task->save();
			}

		$communication->setSubject($form['title']);
		$communication->setBody($form['body']);
		if($_to_field){
			foreach (explode(',',$form[$_to_field]) as $to) {
				$communication->addTo($this->contact['name'],trim($to));
			}			
		}
			if($form['bcc_mails']){
				foreach (explode(',',$form['bcc_mails']) as $bcc) {
						if( ! filter_var(trim($bcc), FILTER_VALIDATE_EMAIL))
							$form->displayError('bcc_mails',$bcc.' is not a valid email');
					$communication->addBcc($bcc);
				}
			}

			if($form['cc_mails']){
				foreach (explode(',',$form['cc_mails']) as $cc) {
						if( ! filter_var(trim($cc), FILTER_VALIDATE_EMAIL))
							$form->displayError('cc_mails',$cc.' is not a valid email');
					$communication->addCc($cc);
				}
			}

			if($form->hasElement('date')){
				$communication['created_at'] = $form['date'];
			}

			if(isset($send_settings)){
						
				$communication->send($send_settings);			
			}else{
				$communication['direction']='Out';
				$communication->save();
			}
			// $form->js(null,[$this->app->js(null,$this->success_js))->univ()->closeDialog()->reload()->univ()->successMessage('Communication added')->execute();
			$form->js(null,$this->success_js)->reload()->univ()->successMessage('Communication added')->execute();		
		}	
	}

	

	function addFilter(){
		$form = $this->add('Form',null,'filter');

		$form->add('xepan\base\Controller_FLC')
			->makePanelCollepsible()
			->closeOtherPanels()
			->addContentSpot()
			->layout([
					'date_range'=>'Filter~c1~6~closed',
					'related_contact'=>'c2~6',
					'communication_type'=>'c3~6',
					'direction'=>'c4~2',
					'search'=>'c5~4',
					'FormButtons~<br/>'=>'c6~12'
				]);

	    $fld_date_range = $form->addField('DateRangePicker','date_range')
            // ->setStartDate('2016-04-07')
            // ->setEndDate('2016-04-30')
            ->showTimer(15)
            ->getBackDatesSet() // or set to false to remove
            // ->getFutureDatesSet() // or skip to not include
            ;
        $fld_contact = $form->addField('xepan\base\Contact','related_contact');
		$fld_contact->includeAll();

		$fld_type = $form->addField('xepan\base\DropDown','communication_type');
		$fld_type->setValueList(['Email'=>'Email','Called'=>'Called','Received'=>'Received','TeleMarketing'=>'TeleMarketing','Personal'=>'Personal','Comment'=>'Comment','SMS'=>'SMS','Newsletter'=>'Newsletter','Support'=>'Support']);
		$fld_type->setAttr(['multiple'=>'multiple']);

		$fld_direction = $form->addField('xepan\base\DropDown','direction');
		$fld_direction->setValueList(['In'=>'In','Out'=>'Out']);
		$fld_direction->setEmptyText('Please Select');

		$form->addField('search');
		$form->addSubmit('Filter')->addClass('btn btn-primary btn-block');
		
		if($form->isSubmitted()){
			$this->historyLister->js()->reload([
					'communication_filter'=>1,
					'start_date'=>$fld_date_range->getStartDate(),
					'end_date'=>$fld_date_range->getEndDate(),
					'related_contact_id'=>$form['related_contact'],
					'communication_type'=>$form['communication_type'],
					'direction'=>$form['direction'],
					'search_string'=>$form['search']
				])->execute();
		}		
	}

	function recursiveRender(){
		if($this->showCommunicationHistory) 
			$this->addCommunicationHistory();
		else
			$this->historyLister = $this->add('View')->setElement('span');

		if($this->showAddCommunications) $this->addTopBar();
		parent::recursiveRender();
	} 

	function myTemplate(){
		$template='
			<div id="{$_name}" class="{$class}">
				<div class="communication-top-bar">
					<div class="row main-box" style="padding-top:15px;">
						<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
							{$filter}
						</div>
						<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
							<div class="btn-group btn-group-justified" role="group" aria-label="Communication Action">
								{$icons}
							</div>
						</div>
					</div>
				</div>
				{$Content}
			</div>
		';
		return $template;
	}
}