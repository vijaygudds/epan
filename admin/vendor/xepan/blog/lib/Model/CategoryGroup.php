<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\blog;

class Model_CategoryGroup extends \xepan\base\Model_Table{
	public $table = "blog_category_group";
	public $status = ['All'];
	public $actions = [
					'All'=>['view','edit','delete']
				];
	public $acl_type = "xepan\blog\CategoryGroup";

	function init(){
		parent::init();
		
		$this->hasOne('xepan\hr\Employee','created_by_id')->defaultValue($this->app->employee->id)->system(true);
		$this->addField('name');
		$this->addField('categories')->type('text')->system(true);
		$this->is(['name|to_trim|required|unique']);

		$this->addhook('beforeSave',function($m){
			$m['categories'] = json_encode($m['categories']);
		});

		$this->addhook('afterLoad',function($m){
			$m['categories'] = json_decode($m['categories'],true);
		});

	}
}
