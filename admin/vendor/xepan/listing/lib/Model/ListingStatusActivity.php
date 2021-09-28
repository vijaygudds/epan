<?php

namespace xepan\listing;

class Model_ListingStatusActivity extends \xepan\base\Model_Table{

	public $table='listing_status_activity';
	public $status = ['Active','Inactive'];
	public $actions = [
					'Active'=>['view','deactivate','edit','delete'],
					'Inactive'=>['view','activate','edit','delete']
				];
	public $acl_type = "Listing\ListingStatusActivity";

	function init(){
		parent::init();

		$this->hasOne('xepan\listing\Model_List','list_id');
		$this->hasOne('xepan\listing\Model_ListDataSet','list_dataset_id')->caption('Not Send If This Condition Match');

		$this->addField('on_status')->display(array('form'=>'xepan\base\NoValidateDropDown'));
		$this->addField('email_subject');
		$this->addField('email_body')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$this->addField('sms_content')->type('text');

		$this->addField('email_send_to_creator')->type('boolean');
		$this->addField('email_send_to_list_data_fields')->display(array('form'=>'xepan\base\NoValidateDropDown'));
		$this->addField('email_send_to_custom_email_ids')->type('text');

		$this->addField('sms_send_to_creator')->type('boolean');
		$this->addField('sms_send_to_list_data_fields')->display(array('form'=>'xepan\base\NoValidateDropDown'));
		$this->addField('sms_send_to_custom_phone_numbers')->type('text');

		$company_m = $this->add('xepan\base\Model_Config_CompanyInfo');
			$company_m->tryLoadAny();

		$this->getElement('email_send_to_custom_email_ids')->defaultValue($company_m['company_email']);
		$this->getElement('sms_send_to_custom_phone_numbers')->defaultValue($company_m['mobile_no']);

		$this->addField('status')->enum($this->status)->defaultValue('Active');

		$this->add('dynamic_model\Controller_AutoCreator');
	}

	function deactivate(){
		$this['status'] = 'Inactive';
		$this->save();
	}

	function activate(){
		$this['status'] = 'Active';
		$this->save();
	}
}