<?php

namespace xepan\cms;

class page_sefconfig extends \xepan\base\Page{
	public $title = "SEF Config";

	function init(){
		parent::init();

		$tabs = $this->add('Tabs');

		$basic_tab = $tabs->addTab('Basic Information');
		$advanced_tab = $tabs->addTab('Advanced Router Mapping');

		$config = $basic_tab->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'enable_sef'=>'checkbox',
							'page_list'=>'text'
						],
					'config_key'=>'SEF_Enable',
					'application'=>'cms'
		]);
		// $config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();

		$form = $basic_tab->add('Form');

		$enable_sef_form_layout['enable_sef']='Enable SEF~c1~12';
		$basic_tab->app->hook('sef-config-form-layout',[&$enable_sef_form_layout]);
		
		$form->add('xepan\base\Controller_FLC')
			->addContentSpot()
			->layout($enable_sef_form_layout);
		$form->addField('checkbox','enable_sef')->set($config['enable_sef']);

		$basic_tab->app->hook('sef-config-form',[$form, $config['page_list']]);

		$form->addSubmit('save')->addClass('btn btn-primary');

		if($form->isSubmitted()){
			$config['enable_sef'] = $form['enable_sef'];
			$config['page_list'] = $form->get();
			$config->save();
			$form->js(null,$form->js()->reload())->univ()->successMessage('Saved Successfully')->execute();
		}

		// Advanced tab

		$config = $advanced_tab->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'expression'=>'line',
							'page_name'=>'line',
							'param'=>'text'
						],
					'config_key'=>'SEF_List',
					'application'=>'cms'
		]);

		// $config->add('xepan\hr\Controller_ACL');
		$config->tryLoadAny();
		
		$crud = $advanced_tab->add('xepan\hr\CRUD',['entity_name'=>" SEF List"]);
		// $crud->add_button = "sd";
		$crud->setModel($config,['expression','page_name','param']);
		$crud->form->getElement('param')->setFieldHint('comma(,) seperated multiple GET Param, GEt Param for Blog: blog_post_code . and SEF Url work like www.domain.com/user_define_page_name/GET_Param_1/GET_Param_2');
	}
}