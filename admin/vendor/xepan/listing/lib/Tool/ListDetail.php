<?php

namespace xepan\listing;

class Tool_ListDetail extends \xepan\cms\View_Tool{
	public $options = [
					'listing_id'=>0,
					'show_public_fields'=>true,
					'show_private_fields'=>true,
					'show_protected_fields'=>true,
					'allow_private_message'=>true,
					'allow_review'=>true,
					'allow_rating'=>true,
					'show_social_share'=>false,
					'custom_template'=>null,
					'show_detail_if_permitted'=>false
				];
	
	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		$list_id = $this->app->stickyGET('list_data_id');

		if($message = $this->requiredOptionMessage()){
			$this->add('View_Warning')->addClass('alert alert-warning')->set($message);
			return;
		}

		$this->listing_model = $this->add('xepan\listing\Model_List');
		$this->listing_model->load($this->options['listing_id']);

		$this->listdata_model = $listdata_model = $this->add('xepan\listing\Model_ListData',['listing'=>$this->options['listing_id']]);
		$this->listdata_model->tryLoad($list_id);

		if(!$this->listdata_model->loaded()){
			$this->add('View')->set('No Record Found ');
			$this->template->tryDel("detail_wrapper");
			return;
		}
		
		if($this->options['show_detail_if_permitted'] AND !$this->listdata_model->isPermitted()){
			$this->add('View_Warning')->addClass('alert alert-warning')->set("You are not permitted to view this detail");
			return;
		}


		// $this->app->print_r($this->listdata_model->data);
		$this->setModelValue($this->listdata_model);
		// $this->setModel($this->listdata_model);
	}

	function setModelValue($model){
		// parent::setModel($model);

		// public fields
		foreach ($this->listdata_model->public_fields as $normalize_name => $name) {
			if($this->options['show_public_fields'] AND $model[$normalize_name]){
				$this->template->trySetHtml($normalize_name,$model[$normalize_name]);
			}else{
				$this->template->trySetHtml($normalize_name,"");
				$this->template->tryDel($normalize_name."_wrapper");
			}
		}

		// private field
		foreach ($this->listdata_model->public_fields as $normalize_name => $name) {
			if($this->options['show_public_fields'] AND $model[$normalize_name]){
				$this->template->trySetHtml($normalize_name,$model[$normalize_name]);
			}else{
				$this->template->trySetHtml($normalize_name,"");
				$this->template->tryDel($normalize_name."_wrapper");
			}
		}
		
	}

	function requiredOptionMessage(){
		if(!$this->options['listing_id']) return "please select listing ...";
		
		if(!$_GET['list_data_id']) return "listing data not defined ...";
		return false;		
	}

	function defaultTemplate(){

		if($message = $this->requiredOptionMessage()){
			return parent::defaultTemplate();
		}
		

		$layout = trim($this->options['custom_template']);
		if(!$layout){
			return parent::defaultTemplate();
		}

		return ['view/tool/listing/listdetail/'.$layout];
	}
}