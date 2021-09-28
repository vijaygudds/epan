<?php

namespace xepan\cms;

class page_configuration extends \xepan\base\Page{
	public $title = "Configuration";

	function init(){
		parent::init();

		$config_m = $this->add('xepan\cms\Model_Config_FrontendWebsiteStatus');
		$config_m->add('xepan\hr\Controller_ACL');
		$config_m->tryLoadAny();

		$form = $this->add('Form');
		$form->addField('Dropdown','put_site_offline')->setValueList([true=>'Yes',false=>'No'])->setEmptyText('Please select a value')->set($config_m['site_offline']);
		$form->addField('xepan\base\RichText','offline_content')->set($config_m['offline_site_content']);
		$form->addField('Checkbox','continue_crons')->set($config_m['continue_crons']);
		$form->addField('default_login_page')->set($config_m['default_login_page']);
		$form->addField('system_contact_types')->set($config_m['system_contact_types']);
		$form->addSubmit('Save');

		if($form->isSubmitted()){
			$config_m['site_offline'] = $form['put_site_offline'];
			$config_m['offline_site_content'] = $form['offline_content'];
			$config_m['continue_crons'] = $form['continue_crons'];
			$config_m['default_login_page'] = $form['default_login_page'];
			$config_m['system_contact_types'] = preg_replace('/\s+/', '', $form['system_contact_types']);
			$config_m->save();

			$form->js()->univ()->successMessage('Saved')->execute();
		}
	}
}