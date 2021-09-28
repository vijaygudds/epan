<?php

namespace xepan\base;

class View_EasySetupWizard extends \View{
	
	function init(){
		parent::init();
		
		/**************************************************************************
			Country Setup Wizard
		**************************************************************************/	
		if($_GET[$this->name.'_set_countries']){
			$this->api->db->dsql()->expr(file_get_contents(realpath(getcwd().'/vendor/xepan/base/countriesstates.sql')))->execute();
			$this->js(true)->reload();
		}

		$isDone = false;
		
		$action = $this->js()->reload([$this->name.'_set_countries'=>1]);

		if($this->add('xepan\base\Model_Country')->count()->getOne() > 0){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You already have countries populated, visit page ? <a href="'. $this->app->url('xepan_communication_general_countrystate')->getURL().'"> click here to go </a>');
		}

		$country_view = $this->add('xepan\base\View_Wizard_Step');

		$country_view->setAddOn('Application - Base')
			->setTitle('Country & State database not populated yet')
			->setMessage('Populate country and states.')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);

		/**************************************************************************
			Time Zone Set Up Wizard
		**************************************************************************/	
		if($_GET[$this->name.'_time_zone']){
			
			$this->js(true)->univ()->frameURL("Time Zone",$this->app->url('xepan_communication_generalsetting'));
		}

		$isDone = false;
		
		$action = $this->js()->reload([$this->name.'_time_zone'=>1]);
		$misc_m = $this->add('xepan\base\Model_Config_Misc');
		
		$misc_m->tryLoadAny();	

		if($misc_m['time_zone']){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You already have updated the time zone, visit page ? <a href="'. $this->app->url('xepan_communication_generalsetting')->getURL().'"> click here to go </a>');
		}	

		$time_zone_view = $this->add('xepan\base\View_Wizard_Step');

		$time_zone_view->setAddOn('Application - Base')
			->setTitle('Update Your Time Zone')
			->setMessage('Please Update your time zone accoding your country. For update time zone click here and Go to tab of Misc Setting')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);

		/**************************************************************************
			Email Duplication Allowed Configuration Set Up Wizard
		**************************************************************************/	
		if($_GET[$this->name.'_email_duplication_allowed']){
			$this->js(true)->univ()->frameURL("Email Duplication Allowed Configuration",$this->app->url('xepan_communication_generalsetting'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_email_duplication_allowed'=>1]);
		$email_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'email_duplication_allowed'=>'DropDown'
							],
					'config_key'=>'Email_Duplication_Allowed_Settings',
					'application'=>'base'
			]);
		$email_m->tryLoadAny();	

		if($email_m['email_duplication_allowed']){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You already have updated the Email_Duplication_Allowed_Settings, visit page ? <a href="'. $this->app->url('xepan_communication_generalsetting')->getURL().'"> click here to go </a>');
		}	

		$email_view = $this->add('xepan\base\View_Wizard_Step');

		$email_view->setAddOn('Application - Base')
			->setTitle('Update Email Duplication Allowed Configuration')
			->setMessage('Update settings accoding your organization norms. For update settings click here and Go to tab of Duplicate Emails')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);

		/**************************************************************************
			Contact No. Duplication Allowed Configuration Set Up Wizard
		**************************************************************************/	
		if($_GET[$this->name.'_contact_no_duplcation_allowed']){
			$this->js(true)->univ()->frameURL("Contact No. Duplication Allowed Configuration",$this->app->url('xepan_communication_generalsetting'));
		}

		$isDone = false;

		$action = $this->js()->reload([$this->name.'_contact_no_duplcation_allowed'=>1]);
		$contactno_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'contact_no_duplcation_allowed'=>'DropDown'
							],
					'config_key'=>'contact_no_duplication_allowed_settings',
					'application'=>'base'
			]);
		$contactno_m->tryLoadAny();	

		if($contactno_m['contact_no_duplcation_allowed']){
			$isDone = true;
			$action = $this->js()->univ()->dialogOK("Already have Data",' You already have updated the contact numbers duplication allowed settings, visit page ? <a href="'. $this->app->url('xepan_communication_generalsetting')->getURL().'"> click here to go </a>');
		}	

		$contact_no_view = $this->add('xepan\base\View_Wizard_Step');

		$contact_no_view->setAddOn('Application - Base')
			->setTitle('Update Contact Number Duplication Allowed Configuration')
			->setMessage('Update settings accoding your organization norms. For update settings click here and Go to tab of Duplicate Conatct')
			->setHelpMessage('Need help ! click on the help icon')
			->setHelpURL('#')
			->setAction('Click Here',$action,$isDone);
	}
}
