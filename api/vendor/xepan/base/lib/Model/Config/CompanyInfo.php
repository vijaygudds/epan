<?php

namespace xepan\base;


class Model_Config_CompanyInfo extends \xepan\base\Model_ConfigJsonModel{
	public $fields =[
								'company_name'=>"Line",
								'company_code'=>"Line",
								'company_owner'=>"Line",
								'mobile_no'=>"Line",
								'company_email'=>"Line",
								'company_address'=>"Line",
								'company_pin_code'=>"Line",
								'company_description'=>"xepan\base\RichText",
								'company_logo_absolute_url'=>"Line",
								'company_twitter_url'=>"Line",
								'company_facebook_url'=>"Line",
								'company_google_url'=>"Line",
								'company_linkedin_url'=>"Line",
								];
	public $config_key = 'COMPANY_AND_OWNER_INFORMATION';
	public $application='communication';

	function init(){
		parent::init();

		// $this->getField('default_login_page')->defaultValue('login');
		// $this->getField('system_contact_types')->defaultValue('Contact,Customer,Supplier,Employee');
	}

}