<?php

namespace xepan\blog;

class Tool_Search extends \xepan\cms\View_Tool{
	public $options = [
					'search_reasult_url'=>''
					];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;
		
		$search_result_page = $this->options['search_reasult_url'];
		if(!$search_result_page){
			$this->add('View_Warning')->set('Please add a search result page in options');
			return;
		}

		$form = $this->add('Form',null,null,['form/empty']);
		$form_field = $form->addField('line','search');

		if($form->isSubmitted()){
			$url = $this->api->url($search_result_page);
			$url->arguments = ['search'=>$form['search']];
			$form->api->redirect($url);
		}
	}
}