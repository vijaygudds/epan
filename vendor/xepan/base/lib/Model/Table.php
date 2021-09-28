<?php

/**
* description: ATK Model_Table is just left to put some global amendments needed in xEpan platform
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Table extends \Model_Table{

	public $acl=true; // true/false/"model_name string";
	public $acl_type=null;
	public $namespace =null;

	public $status=[];
	public $actions = [
		'*'=>['view','edit','delete']
	];

	public $assigable_by_field=false;

	public $skip_epan_condition=false;

	function init(){
		parent::init();

		$this->add('xepan\base\Controller_Validator');

		// if(!$this->skip_epan_condition){
		// 	$this->addHook('afterAdd',function($e){			
		// 		if($e->hasElement('epan_id') && isset($e->app->epan->id)) {
					// if(!isset($e->epan_condition_set)) // comment to mute
		   //              $e->addCondition('epan_id',$e->app->epan->id);
		   //          $e->epan_condition_set = true;  // Comment to mute
	  //           }
			// });
		// }

		$this->addHook('beforeSave',function($m){
			$m->wasDirty=$m->dirty;
		});
	}


}
