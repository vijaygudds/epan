<?php

namespace xepan\listing;

class Model_Category extends \xepan\base\Model_Table{

	public $table = 'list_category';
	public $status = ['Active','Inactive'];
	public $actions = [
					'Active'=>['view','list_association','edit','delete','deactivate'],
					'Inactive'=>['view','list_association','edit','delete','activate']
					];

	public $acl_type = "Listing\Category";
	function init(){
		parent::init();

		$this->hasOne('xepan\listing\ParentCategory','parent_category_id')->sortable(true);
		$this->hasOne('xepan\listing\List','list_id');

		$this->addField('name');
		$this->addField('display_sequence')->type('int')->defaultValue(0);
		$this->addField('status')->enum(['Active','InActive'])->defaultValue('Active');
		$this->addField('description')->type('text')->display(array('form'=>'xepan\base\RichText'));
		$this->addField('custom_link');
		$this->addField('is_website_display')->type('boolean')->defaultValue(true);
		$this->addField('meta_title');
		$this->addField('meta_description')->type('text');
		$this->addField('slug_url')->hint('auto generated leave empty');
		$this->addField('image')->display(['form'=>'xepan\base\ElImage']);
		
		// $this->addField('type')->defaultValue('');
		
		$this->is(['name|to_trim|required']);
		// $this->add('dynamic_model\Controller_AutoCreator');

	}

	function page_list_association($page){
		$page->add('View')->set('hello');
	}

	function list_association(){

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