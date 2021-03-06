<?php

namespace xepan\marketing;

class page_leaddetails extends \xepan\base\Page {
	
	public $title ='Lead Details';
	public $breadcrumb=['Home'=>'index','Lead'=>'xepan_marketing_lead','Details'=>'#'];


	function init(){
		parent::init();

		$action = $this->api->stickyGET('action')?:'view';
		$lead= $this->add('xepan\marketing\Model_Lead');

		 
		$lead->addExpression('weekly_communication')->set(function($m,$q){
			$comm = $m->add('xepan/communication/Model_Communication');
			// $comm->addCondition('sent_on','>',date('Y-m-d',strtotime('-8 week')));
			$comm->_dsql()->del('fields');
			$comm->_dsql()->field('count(*) communication_count');
			$comm->_dsql()->field('to_id');
			$comm->_dsql()->group('to_id');

			return $q->expr("(select GROUP_CONCAT(tmp.communication_count) from [sql] as tmp where tmp.to_id = [0])",[$m->getElement('id'),'sql'=>$comm->_dsql()]);
		});

		$lead->tryLoadBy('id',$this->api->stickyGET('contact_id'));

		// ===== Edit as and when required for variour type of contacts 
		// and put taeir respective namespace here 
		// is a hack for now, later we will throw hook to register namespace with type

		if($lead->loaded()){
			if($lead['type']=='Employee') $lead->namespace='xepan\hr';
			if($lead['type']=='Affiliate') $lead->namespace='xepan\hr';
			if($lead['type']=='Customer') $lead->namespace='xepan\commerce';
			if($lead['type']=='Supplier') $lead->namespace='xepan\commerce';
			if($lead['type']=='Warehouse') $lead->namespace='xepan\commerce';
			if($lead['type']=='OutsourceParty') $lead->namespace='xepan\production';
		}

		if($action=="add"){
			$base_validator = $this->add('xepan\base\Controller_Validator');
			// $form = $this->add('Form');
			$form = $this->add('Form',['validator'=>$base_validator],'contact_view_full_width',['form/empty']);
			// $lead->getElement('state_id')->display(['form'=>'xepan\base\Basic']);
			// $lead->getElement('country_id')->display(['form'=>'xepan\base\Basic']);

			$t = $lead->getElement('assign_to_id')->getModel();
			$t->addCondition('type','Employee');
			$ge = $lead->getElement('generated_by_id')->getModel();
			$ge->addCondition('type','Employee');

			$config_m = $this->add('xepan\communication\Model_Config_SubType');
			$config_m->tryLoadAny();
			$form->add('xepan\base\Controller_FLC')
			->makePanelCollepsible()
			->closeOtherPanels()
			->addContentSpot()
			->layout([
					'first_name'=>'General Information~c1~4',
					'last_name'=>'c2~4',
					'organization'=>'c3~4',
					'lead_category'=>'c4~4',
					'lead_sub_category'=>'c5~4',
					'source'=>'c6~4',
					'numbers'=>'c7~4~comma seperated numbers',
					'emails'=>'c8~4~comma seperated emails',
					'occupation'=>'c9~4',
					'generated_by_id~Generated By Employee'=>'c10~6',
					'assign_to_id~Assigned to Employee'=>'c11~6',
					// 'score~Score (Is Lead Positive or Negative)'=>'c11~2~or leave as it is for nutral',
					// 'score_buttons~'=>'c12~3',
					'landmark'=>'Address Info~a1~4',
					'mohla_falla'=>'c8~4',
					'village'=>'c9~4',
					'post_office_wardno'=>'c10~4',
					'tehsil'=>'c11~4',
					'city'=>'c12~4', // closed to make panel default collapsed
					'pin_code'=>'a13~4', // closed to make panel default collapsed
					'country'=>'c14~4',
					'country_id~'=>'c14',
					'state'=>'c15~4',
					'state_id~'=>'c15',
					'remark'=>'c16~12'
				]);
			$lead_cat = $form->addField('DropDown','lead_category');
			// $lead_cat->setEmptytext('Please Select');
			$lead_cat->setModel('xepan\marketing\LeadCategory');
			$lead_subcat=$form->addField('DropDown','lead_sub_category');
			// $lead_subcat->setEmptytext('Please Select');
			$lead_subcat_m = $this->add('xepan\marketing\Model_LeadSubCategory');
			if($this->app->stickyGET('leadcategory_id')){
				$lead_subcat_m->addCondition('category_id',$this->app->stickyGET('leadcategory_id'));
			}
			$lead_subcat->setModel($lead_subcat_m);

			$form->setModel($lead,['first_name','last_name','organization',/*'address',*/'pin_code','landmark','mohla_falla','village','tehsil','post_office_wardno','city','country_id','state_id','remark','source','generated_by_id','assign_to_id','emails_str','contacts_str'],['emails_str','contacts_str','name','organization_name_with_name','source','city','type','score','total_visitor','created_by_id','created_by','generated_by_id','generated_by','assign_to_id','assign_to','assign_at','effective_name','code','organization','existing_associated_catagories','created_at','priority','branch_id'])->setOrder('created_at','desc');
			

			$lead_cat->js('change',[$lead_subcat->js(null,[$lead_subcat->js()->select2('destroy')])->reload(null,null,[$this->app->url(null,['cut_object'=>$lead_subcat->name]),'leadcategory_id'=>$lead_cat->js()->val()])]);

			$emails_field = $form->addField('emails');
			$number_field = $form->addField('numbers');
			$categories_field = $form->addField('xepan\base\DropDown','occupation');
			$categories_field->setModel($this->add('xepan\marketing\Model_MarketingCategory'));
			$categories_field->enableMultiSelect();

			$form->addField('Checkbox','want_to_add_next_lead')->set(true);
			// if($cntry_id = $this->app->stickyGET('country_id')){			
			// 	$state_field->getModel()->addCondition('country_id',$cntry_id);
			// }


			$country_field =  $form->getElement('country_id');
			$country_field->getModel()->addCondition('status','Active');
			$state_field = $form->getElement('state_id');
			$state_field->getModel()->addCondition('status','Active');
			$state_field->dependsOn($country_field);

			// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));



			// $form = $this->add('Form',['validator'=>$base_validator],'contact_view_full_width',['form/empty']);
			// $form->setLayout(['page/leadprofile','contact_view_full_width']);
			// $form->setModel($lead,['first_name','last_name','address','city','country_id','state_id','pin_code','organization','post','website','source','remark','assign_to_id']);
			// $form->addField('line','email_1')->validate('email');
			// $form->addField('line','email_2');
			// $form->addField('line','email_3');
			// $form->addField('line','email_4');
			
			// $form->addField('line','contact_no_1');
			// $form->addField('line','contact_no_2');
			// $form->addField('line','contact_no_3');
			// $form->addField('line','contact_no_4');

			// $country_field =  $form->getElement('country_id');
			// $country_field->getModel()->addCondition('status','Active');
			// $state_field = $form->getElement('state_id');
			// $state_field->getModel()->addCondition('status','Active');
			// $state_field->dependsOn($country_field);
			// if($cntry_id = $this->app->stickyGET('country_id')){			
			// 	$state_field->getModel()->addCondition('country_id',$cntry_id);
			// }

			// $country_field->js('change',$form->js()->atk4_form('reloadField','state_id',[$this->app->url(),'country_id'=>$state_field->js()->val()]));
			// $country_field->js('change',$state_field->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$state_field->name]),'country_id'=>$country_field->js()->val()]));

			// $categories_field = $form->addField('DropDown','category');
			// $categories_field->setModel($this->add('xepan\marketing\Model_MarketingCategory'));
			// $categories_field->addClass('multiselect-full-width');
			// $categories_field->setAttr(['multiple'=>'multiple']);
			// $categories_field->setEmptyText("Please Select");

			$form->addSubmit('Add');

			if($form->isSubmitted()){
				$lead['lead_cat_id'] = $form['lead_category'];
				$lead['lead_cat_sub_id'] = $form['lead_sub_category'];
				if($form['lead_category'] == null)
					$form->displayError('lead_category','Please associate lead with Lead Category');

				if(!$form['source'])
					$form->displayError('source','mandatory');
				
				try{
					$this->api->db->beginTransaction();
					$form->model['address'] = $form['landmark'].", ".$form['mohla_falla'].", ".$form['village'].", ".$form['tehsil'].", ".$form['post_office_wardno'].", ".$form['city'].", ".$form['pin_code'].", ".$form['state'].", ".$form['country'];
					$form->save();
					$new_lead_model = $form->getModel();
						
					$this->app->hook('new_lead_added',[$new_lead_model]);

					foreach (explode(",",$form['numbers']) as $no) {
						if(!$no) continue;
						$lead->addPhone(trim($no),'Official',null,null,null,false);
					}

					foreach (explode(",",$form['emails']) as $email) {
						if(!$email) continue;
						$lead->addEmail(trim($email),'Official',null,null,null,false);
					}

					if($form['email_1']){
						$new_lead_model->checkEmail($form['email_1'],null,'email_1');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_lead_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_1'];
						$email->save();
					}

					if($form['email_2']){
						$new_lead_model->checkEmail($form['email_2'],null,'email_2');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_lead_model->id;
						$email['head'] = "Official";
						$email['value'] = $form['email_2'];
						$email->save();
					}

					if($form['email_3']){
						$new_lead_model->checkEmail($form['email_3'],null,'email_3');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_lead_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_3'];
						$email->save();
					}
					if($form['email_4']){
						$new_lead_model->checkEmail($form['email_4'],null,'email_4');

						$email = $this->add('xepan\base\Model_Contact_Email',['bypass_hook'=>true]);
						$email['contact_id'] = $new_lead_model->id;
						$email['head'] = "Personal";
						$email['value'] = $form['email_4'];
						$email->save();
					}

					// Contact Form
					if($form['contact_no_1']){
						$new_lead_model->checkPhone($form['contact_no_1'],null,'contact_no_1');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_lead_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_1'];
						$phone->save();
					}

					if($form['contact_no_2']){
						$new_lead_model->checkPhone($form['contact_no_2'],null,'contact_no_2');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_lead_model->id;
						$phone['head'] = "Official";
						$phone['value'] = $form['contact_no_2'];
						$phone->save();
					}

					if($form['contact_no_3']){
						$new_lead_model->checkPhone($form['contact_no_3'],null,'contact_no_3');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_lead_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_3'];
						$phone->save();
					}
					if($form['contact_no_4']){
						$new_lead_model->checkPhone($form['contact_no_4'],null,'contact_no_4');

						$phone = $this->add('xepan\base\Model_Contact_Phone',['bypass_hook'=>true]);
						$phone['contact_id'] = $new_lead_model->id;
						$phone['head'] = "Personal";
						$phone['value'] = $form['contact_no_4'];						
						$phone->save();
					}

					// Category Association
					if($form['category']){
						$categories = explode(",",$form['category']);
						foreach ($categories as $key => $cat_id) {
							if(!is_numeric($cat_id))
								continue;

							$cat_asso_model = $this->add('xepan\marketing\Model_Lead_Category_Association');
							$cat_asso_model['lead_id'] = $new_lead_model->id;
							$cat_asso_model['marketing_category_id'] = $cat_id;
							$cat_asso_model['created_at'] = $this->app->now;
							$cat_asso_model->save();
						}
					}					
					$this->api->db->commit();
				}catch(\Exception_StopInit $e){

		        }catch(\Exception $e){
		            $this->api->db->rollback();
		            throw $e;
		        }	

		        if($form['want_to_add_next_lead']){
		        	$form->js(null,$form->js()->reload())->univ()->successMessage('Lead Created Successfully')->execute();
		        }
				$form->js(null,$form->js()->univ()->successMessage('Lead Created Successfully'))->univ()->redirect($this->app->url(null,['action'=>"edit",'contact_id'=>$new_lead_model->id]))->execute();
			}
			// Temporary off view qsp add form 
			// $lead_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\marketing\Model_Lead','view_document_class'=>'xepan\hr\View_Document','page_reload'=>($action=='add')],'contact_view_full_width');
			// $lead_view->document_view->effective_template->del('im_and_events_andrelation');
			// $lead_view->document_view->effective_template->del('email_and_phone');
			// $lead_view->document_view->effective_template->del('avatar_wrapper');
			// $lead_view->document_view->effective_template->del('contact_since_wrapper');
			// $lead_view->document_view->effective_template->del('send_email_sms_wrapper');
			// $lead_view->document_view->effective_template->del('online_status_wrapper');
			// $lead_view->document_view->effective_template->del('contact_type_wrapper');
			// $lead_view->setStyle(['width'=>'50%','margin'=>'auto']);
			$this->template->del('other_details');
		}else{
			$this->template->del('contact_view_full_width');
			$lead_view = $this->add('xepan\base\View_Contact',['acl'=>'xepan\marketing\Model_Lead','view_document_class'=>'xepan\hr\View_Document'],'contact_view');
			$lead_view->setModel($lead);
		}


		$detail = $this->add('xepan\hr\View_Document',['action'=> $action,'id_field_on_reload'=>'contact_id'],'details',['view/details']);
		$detail->setModel($lead,['assign_to','assign_to_id','source','marketing_category','communication','opportunities','remark','weekly_communication'],['assign_to_id','source','remark']);//,'marketing_category_id','communication','opportunities'
		if(($action != 'view')){				
			$detail->form->getElement('assign_to_id')->getModel()->addCondition('type','Employee');
		}

		if($lead->loaded()){
			
			$opportunities_tab = $this->add('xepan\hr\View_Document',['action'=> $action,'id_field_on_reload'=>'contact_id'],'opportunity',['view/opp']);
			$o = $opportunities_tab->addMany('opportunity',null,'opportunity',['grid/addopportunity-grid']);
			if($action != 'view')
				$o->grid->addQuickSearch(['title']);
			$o->setModel($lead->ref('Opportunities'),['title','description','status','assign_to_id','fund','discount_percentage','closing_date'])->setOrder('created_at','desc');

			$activity_view = $this->add('xepan\base\Grid',['no_records_message'=>'No activity found'],'activity',['view/activity/activity-grid']);
			if($action != 'view')
				$activity_view->addQuickSearch(['activity']);
			$activity_view->add('xepan\base\Paginator',null,'Paginator');

			$activity=$this->add('xepan\base\Model_Activity')->setOrder('created_at','desc');
			$activity->addCondition('related_contact_id',$_GET['contact_id']);
			$activity->tryLoadAny();
			$activity_view->setModel($activity);	


			/*
			*	
			*	Lead <=> Category association form
			*	
			*/
			
			$model_assoc_category = $this->add('xepan\marketing\Model_MarketingCategory');

			if($action=='view'){
				$base= $detail;
				
				$asso = $lead->ref('xepan\marketing\Lead_Category_Association');

				$category_assoc_grid = $base->add('xepan\base\Grid',['show_header'=>false],'marketing_category');
				$category_assoc_grid->setModel($asso,['marketing_category'])
							        ->_dsql()->group('marketing_category_id');
			}

			else{

				$base = $detail->form->layout;

				$cat_ass_field = $base->addField('DropDown','ass_cat')->set($lead->getAssociatedCategories());
				$cat_ass_field->setAttr(['multiple'=>'multiple']);
				$cat_ass_field->setModel('xepan\marketing\Model_MarketingCategory');

				$detail->form->onSubmit(function($frm){
					$lead_model = $this->add('xepan\marketing\Model_lead')->load($_GET['contact_id']);	
					$lead_model->removeAssociateCategory();

					$selected_categories = [];
					$selected_categories = explode(',', $frm['ass_cat']);
												
					foreach ($selected_categories as $cat) {
						if(!$cat)
							break;														
						$lead_model->associateCategory($cat);
					}

					$frm->save();
					$frm->js(null,$this->js()->univ()->successMessage('Saved'))->reload()->execute();	
				});
			
			}

		}

		$config_m = $this->add('xepan\base\Model_ConfigJsonModel',
		[
			'fields'=>[
						'activate_lead_api'=>'checkbox',
						'open_lead_external_info_in_iframe'=>'checkbox',
						'external_url'=>'Line',
						],
				'config_key'=>'MARKETING_EXTERNAL_CONFIGURATION',
				'application'=>'marketing'
		]);
		$config_m->tryLoadAny();
		
		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString($config_m['external_url']);

		$external_view = $this->add('GiTemplate');
		$external_view->loadTemplateFromString($config_m['external_url']);
		$external_view->trySet($lead->get());


		if($config_m['activate_lead_api'] AND $config_m['open_lead_external_info_in_iframe'])
			$this->add('View',null,'extra_info')->setElement('iframe')->setAttr(['src'=>$external_view->render(),'width'=>'100%','height'=>'600px']);
		else{		
			$this->template->trySet('extra_info',' ');
			$this->add('View',null,'extra_info_message',null)->set('Please activate lead API from configuration');	
		}
		
		$this->js(true)->_load('jquery.sparkline.min')->_selector('.sparkline')->sparkline('html', ['enableTagOptions' => true, 'chartRangeMin' =>0]);

	}

	function defaultTemplate(){
		return ['page/leadprofile'];
	}

	function render(){
		// $this->app->jui->addStaticInclude('jquery.easypiechart.min');
		parent::render();
	}	

	function checkPhoneNo($phone_id,$phone_value,$contact_id,$form){

		 $contact = $this->add('xepan\base\Model_Contact');
        
        if($contact_id)
	        $contact->load($contact_id);

		$contactconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'contact_no_duplcation_allowed'=>'DropDown'
							],
					'config_key'=>'contact_no_duplication_allowed_settings',
					'application'=>'base'
			]);
		$contactconfig_m->tryLoadAny();	

		if($contactconfig_m['contact_no_duplcation_allowed'] != 'duplication_allowed'){
	        $contactphone_m = $this->add('xepan\base\Model_Contact_Phone');
	        $contactphone_m->addCondition('id','<>',$phone_id);
	        $contactphone_m->addCondition('value',$phone_value);
			
			if($contactconfig_m['contact_no_duplcation_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$contactphone_m->addCondition('contact_type',$contact['contact_type']);
		        $contactphone_m->tryLoadAny();
		 	}

	        $contactphone_m->tryLoadAny();
	        
	        if($contactphone_m->loaded())
	        	for ($i=1; $i <=4 ; $i++){ 
	        		if($phone_value == $form['contact_no_'.$i])
			        	$form->displayError('contact_no_'.$i,'Contact No. Already Used');
	        	}
		}	
    }

    function checkEmail($email_id,$email_value,$contact_id,$form){

    	$contact = $this->add('xepan\base\Model_Contact');
        
        if($contact_id)
	        $contact->load($contact_id);

		$emailconfig_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'email_duplication_allowed'=>'DropDown'
							],
					'config_key'=>'Email_Duplication_Allowed_Settings',
					'application'=>'base'
			]);
		$emailconfig_m->tryLoadAny();

		if($emailconfig_m['email_duplication_allowed'] != 'duplication_allowed'){
	        $email_m = $this->add('xepan\base\Model_Contact_Email');
	        $email_m->addCondition('id','<>',$email_id);
	        $email_m->addCondition('value',$email_value);
			
			if($emailconfig_m['email_duplication_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$email_m->addCondition('contact_type',$contact['contact_type']);
			}
	        
	        $email_m->tryLoadAny();
	        
	        if($email_m->loaded()){
	        	for ($i=1; $i <=4 ; $i++){ 
	        		if($email_value == $form['email_'.$i])
			        	$form->displayError('email_'.$i,'Email Already Used');
	        	}
	        }    
		}

    }
}
