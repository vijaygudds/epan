<?php
 
namespace xepan\listing;

class page_configuration extends \xepan\base\Page {
	public $title='Listing Configuration';

	function init(){
		parent::init();

		$this->config_m = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
						'list_id'=>'line',
						'list_data_download_layout'=>'xepan\base\RichText'
					],
				'config_key'=>'Xepan_Listing_Configuration',
				'application'=>'xepan\listing'
			]);
		$this->config_m->tryLoadAny();
	}

	function page_index(){
		$tab = $this->add('Tabs');
		$tab->addTabUrl('./download','List Data Download Layout');
	}

	function page_download(){
		$form = $this->add('Form');
		$form->setModel($this->config_m);
		$form->addSubmit('Submit');
		if($form->isSubmitted()){
			$form->save();
			$form->js(null,$form->js()->reload())
				->univ()->successMessage('updated successfully')
				->execute();
		}
	}
}

