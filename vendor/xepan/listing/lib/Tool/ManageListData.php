<?php

namespace xepan\listing;

class Tool_ManageListData extends \xepan\cms\View_Tool{
	public $options = [
		'listing_id'=>0,
		'listing_layout_id'=>0,
		'save_button_caption'=>'Save',
		'save_button_class'=>'btn btn-primary',
		'list_data_record_id'=>null,
		'show_data_set_record_only'=>false,
		'list_data_set_id'=>null,
		'list_data_contact_id'=>0,
	];
	
	function init(){
		parent::init();

		if(!$this->options['listing_id']){
			$this->add('View')->addClass('alert alert-warning')->set('Please Select List From Option Panel');
			return;
		}

		if($this->app->stickyGET('list_data_record_id')){
			$this->options['list_data_record_id']=$_GET['list_data_record_id'];
		}

		// try{
			$this->add('xepan\listing\Form_ManageListData',['options'=>$this->options]);
		// }catch(\Exception $e){
		// 	$this->add('View')->addClass('alert alert-warning')->set($e->getMessage());
		// 	return;	
		// }
	}
}