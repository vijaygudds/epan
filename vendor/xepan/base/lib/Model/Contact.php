<?php

/**
* description: Contact serves as Base model for all models that relates to any human contact
* Let it be lead, customer, supplier or any other contact in any application.
* This contact model stores all basic possible details in this table and leave specific implementation
* for Model extending this Model by joining other tables
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Contact extends \xepan\base\Model_Table{
	public $table='contact';

	public $status=[];
	public $actions=[];
	public $type = "Contact";
	public $contact_type = "Contact";

	public $addOtherInfo=false;
	public $otherInfoFields=[];

	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\base\ContactCreatedBY','created_by_id');
		$this->hasOne('xepan\base\ContactAssignedTo','assign_to_id');
		$this->hasOne('xepan\base\User',null,'username');
		$this->hasOne('xepan\base\Country','country_id')->display(array('form' => 'xepan\base\Country'));
		$this->hasOne('xepan\base\State','state_id')->display(array('form' => 'xepan\base\State'));
		$this->hasOne('xepan\base\Branch','branch_id')->defaultValue(@$this->app->branch->id);
		$this->hasOne('xepan\marketing\Model_LeadCategory','lead_cat_id');
		$this->hasOne('xepan\marketing\Model_LeadSubCategory','lead_cat_sub_id');
		$this->addField('type');
		$this->getElement('type')->defaultValue($this->type);
		
		$this->addField('first_name');
		$this->addField('last_name');

		$this->addField('address')->type('text');
		$this->addField('city');
		$this->addField('pin_code');
		$this->addField('status')->enum($this->status)->mandatory(true)->system(true);

		$this->addField('organization');
		$this->addField('post')->caption('Post');
		$this->addField('website');
		$this->addField('code')->system(true)->defaultValue('general-lead');
		
		$this->addField('source');
		$this->addField('remark')->type('text');
		$this->addField('score')->defaultValue(0)->sortable(true);
		$this->addField('last_communication_before_days')->defaultValue(0)->sortable(true);
		
		$this->addField('created_at')->type('datetime')->defaultValue(@$this->app->now)->sortable(true);
		$this->addField('updated_at')->type('datetime')->defaultValue(@$this->app->now);
		$this->addField('assign_at')->type('datetime')->defaultValue(@$this->app->now)->sortable(true);
		
		$this->addField('search_string')->type('text')->system(true)->defaultValue(null);
		$this->addField('freelancer_type')->enum(['Public','Company','Not Applicable'])->defaultValue('Not Applicable');
		$this->addField('related_with');
		$this->addField('related_id')->type('int');

		$this->addField('tag')->type('text')->display(['form'=>'DropDown']);

		$this->add('xepan/filestore/Field_Image',['name'=>'image_id','deref_field'=>'thumb_url'])->allowHTML(true);

		$this->addExpression('name')->set($this->dsql()->expr('CONCAT([0]," ",[1])',[$this->getElement('first_name'),$this->getElement('last_name')]))->sortable(true);

		$this->addExpression('effective_name',function($m,$q){
			return $q->expr('IF(ISNULL([organization_name]) OR trim([organization_name])="" ,[contact_name],[organization_name])',
						[
							'contact_name'=>$m->getElement('name'),
							'organization_name'=>$m->getElement('organization')
						]
					);
		});

		$this->addExpression('name_with_type',function($m,$q){
			return $q->expr('CONCAT_WS("::",[name],[organization],[type],[code])',
						[
							'name'=>$m->getElement('name'),
							'organization'=>$m->getElement('organization'),
							'type'=>$m->getElement('type'),
							'code'=>$m->getElement('code')
						]
					);
		});

		$this->hasMany('xepan\base\Contact_Email',null,null,'Emails');
		$this->hasMany('xepan\base\Contact_Phone',null,null,'Phones');
		$this->hasMany('xepan\base\Contact_Relation',null,null,'Relations');
		$this->hasMany('xepan\base\Contact_IM',null,null,'IMs');
		$this->hasMany('xepan\base\Contact_Event',null,null,'Events');
		$this->hasMany('xepan\base\Contact_Other',null,null,'OtherContactInfos');
		$this->hasMany('xepan\base\Contact_CommunicationReadEmail','contact_id',null,'UnreadEmails');		
		
		if($this->addOtherInfo){
			$emp_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
			$emp_other_info_config_m->addCondition('for',$this['type']);
			// $emp_other_info_config_m->tryLoadAny();
			// $other_fields = array_column($emp_other_info_config_m->getRows(), 'name');
			
			foreach ($emp_other_info_config_m->config_data as $data) {
				if($data['for'] != $this->contact_type) continue;

				$ot_fields = $data['name'];
				if(!trim($ot_fields)) continue;
				
				// $ot_fields = strtolower($ot_fields);
				$normalize_name = $this->app->normalizeName($ot_fields);
				$this->otherInfoFields[$normalize_name] = $ot_fields;

				$this->addExpression($normalize_name)->set(function($m,$q)use($ot_fields){
					return $m->refSQL('OtherContactInfos')->addCondition('head',$ot_fields)->fieldQuery('value');
				});
			}
		}

		$this->addExpression('emails_str')->set(function($m,$q){
			$x = $m->add('xepan\base\Model_Contact_Email',['table_alias'=>'emails_str']);
			return $x->addCondition('contact_id',$q->getField('id'))
						->addCondition('is_active',true)
						->addCondition('is_valid',true)
						->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR "<br/>")',[$x->getElement('value')]));
		})->allowHTML(true)->sortable(true);

		$this->addExpression('unique_name',function($m,$q){
			return $q->expr("CONCAT([0],' : [',IFNULL([1],''),'] - [',[2],'] - [', IFNULL([3],''),']')",
					[
						$m->getElement('name'),
						$m->getElement('organization'),
						$m->getElement('type'),
						$m->getElement('code')
					]);
		
		});

		$this->addExpression('contacts_str')->set(function($m,$q){
			$x = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'contacts_str']);
			return $x->addCondition('contact_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR "<br/>")',[$x->getElement('value')]));
		})->allowHTML(true);

		$this->addExpression('contacts_comma_seperated')->set(function($m,$q){
			$x = $m->add('xepan\base\Model_Contact_Phone',['table_alias'=>'contacts_str']);
			return $x->addCondition('contact_id',$q->getField('id'))->_dsql()->del('fields')->field($q->expr('group_concat([0] SEPARATOR ", ")',[$x->getElement('value')]));
		})->allowHTML(true);

		$this->addExpression('online_status')->set(function($m,$q){
			return '"online"'; // or ideal or offline
		});

		$this->addExpression('scope')->set(function($m,$q){
			return $m->refSQL('user_id')->fieldQuery('scope');
		});

		$this->addHook('beforeSave',function($m){
			$m['updated_at']=$this->app->now;
			if($m->isDirty('assign_to_id')) $m['assign_at'] = $this->app->now;
		});

		$this->addHook('beforeDelete',[$this,'memoriseUserID']);
		$this->addHook('afterDelete',[$this,'removeAssociatedUser']);
		$this->addHook('beforeDelete',[$this,'deleteContactEmails']);
		$this->addHook('beforeDelete',[$this,'deleteContactPhones']);
		$this->addHook('beforeDelete',[$this,'deleteContactRelations']);
		$this->addHook('beforeDelete',[$this,'deleteContactIMs']);
		$this->addHook('beforeDelete',[$this,'deleteContactEvents']);
		$this->addHook('afterSave',[$this,'updateContactCode']);
				
		$this->addHook('afterSave',[$this,'contact_category_association']);

		$this->is([
				// 'epan_id|required',
				'first_name|to_trim|to_upper_words|required',
				'last_name|to_trim|to_upper_words',
				'user_id|unique_in_epan',
				'type|to_trim|required'
			]);
	}

	function contact_category_association(){		
		$this->app->hook('contact_save',[$this]);
	}

	function memoriseUserID(){
		$this->app->memorize('user_id_4_removed_customer',$this['user_id']);
	}

	function removeAssociatedUser(){
		$user = $this->add('xepan\base\Model_User')->addCondition('id',$this->app->recall('user_id_4_removed_customer',0))->tryLoadAny();
		if($user->loaded()) $user->delete();
		$this->app->forget('user_id_4_removed_customer');
	}

	function updateContactCode(){
		if(!$this->loaded()) throw new \Exception($this['type'] ." Model Must be Loaded", 1);
			$type = $this['type'];
			$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
			$company_m->tryLoadAny();

			$company_info = $this->app->epan['name'];
			$owner_code = $company_m['company_code']?:substr($company_info, 0,3);

			$code = "CON";
			switch ($type) {
					case 'Employee':
							$code = $owner_code."EMP";
						break;
					case 'Customer':
							$code = $owner_code."CUS";
						break;
					case 'Supplier':
							$code = $owner_code."SUP";
						break;
					case 'OutsourceParty':
							$code = $owner_code."OUT";
						break;
					case 'Lead':
							$code = $owner_code."LEA";
						break;
					case 'Warehouse':
							$code = $owner_code."WAR";
						break;
					case 'Affiliate':
							$code = $owner_code."AFF";
						break;
					default:
						//Code...							
						break;
			}			
			$this['code'] = $code.$this->id;
			$this->save();

	}

	function deleteContactEmails(){
		$this->ref('Emails')->deleteAll();
	}
	function deleteContactPhones(){
		$this->ref('Phones')->deleteAll();
	}
	function deleteContactRelations(){
		$this->ref('Relations')->deleteAll();
	} 
	function deleteContactIMs(){
		$this->ref('IMs')->deleteAll();
	}
	function deleteContactEvents(){
		$this->ref('Events')->deleteAll();
	}

	function deactivateContactEmails($contact_id){		
		$contact_info = $this->add('xepan\base\Model_Contact_Info');
		$contact_info->addCondition('contact_id',$contact_id);
		$contact_info->addCondition('type','Email');

		foreach ($contact_info as $info){
			$info['is_active'] = false;
			$info->save();
		}
	}

	function page_communication($page){	
		$this->app->stickyGET('comm_type');
		
		$tabs = $page->add('TabsDefault');
        $communication_tab = $tabs->addTab('Communication');
        $followup_tab = $tabs->addTab('Followups');

		$comm = $communication_tab->add('xepan\communication\View_Communication');
		$comm->setCommunicationsWith($this);
		
		$this->app->hook('communication_rendered',[$this->id,$followup_tab]);
	}

	//load Logged In check for the user of contact loaded or not, 
	//mainly used  for online contact account
	function loadLoggedIn($type=null,$return_contact=false){
		if($this->loaded()) $this->unload();
		if(!$this->api->auth->isLoggedIn()) return false;
		
		$this->addCondition('user_id',$this->api->auth->model->id);
		if($type)
			$this->addCondition('type',$type);

		$this->tryLoadAny();
		if(!$this->loaded()) return false;
		if($return_contact) return $this;
		return true;
	}

	function addEmail($email,$head='Official',$active=true,$valid=true,$field=null,$validate=true){
		$email = trim($email);
		if($email=='') return;

		if($validate && !$this->checkEmail($email,null,$field))
			throw new \Exception("$email already exists",1);
			
		return $this->add('xepan\base\Model_Contact_Email')
			->set('contact_id',$this->id)
			->set('head',$head)
			->set('value',$email)
			->set('is_active',$active)
			->set('is_valid',$valid)
			->save();

	}

	function getEmails($all=false){
		if(!$this->loaded())
			return [];
		$emails = $this->ref('Emails')->_dsql();
		
		if(!$all) $emails->where('is_active',true)->where('is_valid',true);

		$emails = $emails->del('fields')->field('value')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($emails)),false);
	}

	function addPhone($number,$head='Official',$active=true,$valid=true,$field=null,$validate=true){
		$number = trim($number);

		if($number=='') return;
		if($validate && !$this->checkPhone($number,null,$field))
			throw new \Exception("$number already exists",1);

		return $this->add('xepan\base\Model_Contact_Phone')
			->set('contact_id',$this->id)
			->set('head',$head)
			->set('value',$number)
			->set('is_active',$active)
			->set('is_valid',$valid)
			->save();

	}

	function getPhones(){
		if(!$this->loaded())
			return [];

		$emails = $this->ref('Phones')
								->_dsql()->del('fields')->field('value')->getAll();
		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($emails)),false);	
	}

	function user(){
		return $this->ref('user_id');
	}

	function updateUser($user_name,$password=null,$scope="WebsiteUser",$status="Active"){
		
		if(!$this['user_id']){
			$user_model = $this->add('xepan\base\Model_User');
		}else
			$user_model = $this->user();

		$user_model->addCondition('username',$user_name);
		$user_model->tryLoadAny();
		
		$user_model['status'] = $status;
		$user_model['scope'] = $scope;
		$user_model->save();

		if(!$this['first_name'])
			$this['first_name'] = $user_name;
		$this['user_id'] = $user_model->id;
		$this->save();
		
		if($password)
			$user_model->updatePassword($password);

		return $user_model;
	}

	function page_manage_score($p){		
		$score_view = $p->add('View_Info')->set('Current Score : '.$this['score'])->addClass('panel panel-default panel-heading xepan-push');
		$form = $p->add('Form')->addClass('xepan-push-small');
		$form->addField('score');
		$form->addField('DropDown','do_what')->setValueList(['increase'=>'increase','decrease'=>'decrease']);
		$form->addSubmit('Update Score')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$this->manage_score($form['do_what'],$form['score']);
			return $this->app->page_action_result = $this->app->js(true,$p->js()->univ()->closeDialog())->univ()->successMessage('Done');
		}
	}

	function manage_score($do_what,$score){		
		if($score % 10 != 0)
			throw new \Exception("Score Should Be Multiple Of 10");

		$model_point_system = $this->add('xepan\base\Model_PointSystem');
		$model_point_system->addCondition('contact_id',$this->id);

		if($do_what == 'increase')
			$model_point_system['score'] += $score;
		else
			$model_point_system['score'] -= $score;
		
		$model_point_system->save();
	}

	function checkContactInfo($type,$value,$contact=null,$field=null){
		if(!$contact) $contact = $this;
		if(!$contact->loaded()) 
			throw $this->exception('Cannot check as contact is not loaded');

		if($type=='Email'){

			$config_m = $this->add('xepan\base\Model_ConfigJsonModel',
				[
					'fields'=>[
								'email_duplication_allowed'=>'DropDown'
								],
						'config_key'=>'Email_Duplication_Allowed_Settings',
						'application'=>'base'
				]);
			$config_m->tryLoadAny();
			$config_field ='email_duplication_allowed';
		}elseif($type=='Phone'){
			$config_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'contact_no_duplcation_allowed'=>'DropDown'
							],
					'config_key'=>'contact_no_duplication_allowed_settings',
					'application'=>'base'
			]);
			$config_m->tryLoadAny();
			$config_field ='contact_no_duplcation_allowed';
		}

		if($config_m[$config_field] === 'duplication_allowed') return true;

		$other_values = $this->add('xepan\base\Model_Contact_'.$type);
		$other_values->addCondition('value',$value);
		$other_values->addCondition('contact_id','<>',$contact->id);

		if($config_m[$config_field] == 'no_duplication_allowed_for_same_contact_type'){
			$other_values->addCondition('contact_type',$contact['type']);
		}
			
		$other_values->tryLoadAny();
		
		if($field && $other_values->loaded())
			throw $this->exception($type.' ('.$value.')'.' Already used','ValidityCheck')->setField($field);
		
		return !$other_values->loaded();

	}

	function checkEmail($email,$contact=null,$field=null){
		return $this->checkContactInfo('Email',$email,$contact,$field);
	}

	function checkPhone($phone,$contact=null,$field=null){
		return $this->checkContactInfo('Phone',$phone,$contact,$field);
	}


// ======================= OLD FUNCTIONS CAN BE REMOVED ONCE STABLE VERSION RELEASED

	function checkEmail_remove($email,$value,$model,$obj){
		$contact = $this->add('xepan\commerce\Model_'.$model['type']);
        if($model->id)
	        $contact->load($model->id);

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
	        if($email->id)
	        $email_m->addCondition('id','<>',$email_id);
	        $email_m->addCondition('value',$value);
			
			if($emailconfig_m['email_duplication_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$email_m->addCondition('contact_type',$email['head']);
			}
	        $email_m->tryLoadAny();
	        if($email_m->loaded()){
	            throw $this->exception('This Email Already Used','ValidityCheck')->setField($value);
	        }

		}
	}

	function checkPhoneNo_removed($contactm,$value,$model,$form){
		
		$contact = $this->add('xepan\commerce\Model_'.$model['type']);
        if($model->id)
	        $contact->load($model->id);

		// Contact No Setting
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
	        $contact_m = $this->add('xepan\base\Model_Contact_Email');
	        if($contactm->id)
	        $cotnact_m->addCondition('id','<>',$contactm_id);
	        $contact_m->addCondition('value',$value);
			
			if($contactconfig_m['contact_no_duplcation_allowed'] == 'no_duplication_allowed_for_same_contact_type'){
				$contact_m->addCondition('contact_type',$contactm['head']);
			}
	        $contact_m->tryLoadAny();
	        if($contact_m->loaded()){
	            throw $this->exception('This Email Already Used','ValidityCheck')->setField($value);
	        }

		}	
	}

	function addOtherInfoToForm($form){
    	// load contact other info configuration model for employee only
    	// loop for all fields
    		// check field type
    			// if dropdown then add dropdown
    			// if line then add line type field
    			// if datePicker then add datepicker type field
    			// if mandatory apply validation

 		$contact_other_info_config_m = $this->add('xepan\base\Model_Config_ContactOtherInfo');
		$contact_other_info_config_m->addCondition('for',$this->contact_type);

		foreach($contact_other_info_config_m->config_data as $of) {
			if($of['for'] != $this->contact_type ) continue;

			if(!$of['name']) continue;

			$field_name = $this->app->normalizeName($of['name']);
			$field = $form->addField($of['type'],$field_name,$of['name']);
			if($of['type']== 'DropDown'){
				$data_array = array_combine(explode(",", $of['possible_values']), explode(",", $of['possible_values']));
				$data_array  = array_map('trim',$data_array);
				$field->setValueList($data_array)->setEmptyText('Please Select');
			}

			if($of['conditional_binding']){
				$field->js(true)->univ()->bindConditionalShow(json_decode($of['conditional_binding'],true),'div.atk-form-row');
			}

			if($of['is_mandatory']){
				$field->validate('required');
			}

		}

    }
}