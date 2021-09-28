<?php

/**
* description: Contact Info stores various info for any contact. 
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Epan_Configuration extends \xepan\base\Model_Table{
	public $table='epan_config';
	public $acl=false;

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan');

		$this->addField('head');		
		$this->addField('value')->type('text')->display(['form'=>'xepan\base\RichText']);

		$this->addField('application');

	}

	function getConfig($head,$app=null){

		// if($cached_value = $this->recall($this->app->epan->id.'_'.$app.'_'.$head,false)) return $cached_value;
		
		$config=$this->add('xepan\base\Model_Epan_Configuration');
		$config->addCondition('head',$head);
		if($app)
			$config->addCondition('application',$app);

		$config->tryLoadAny();
		return $config['value'];
	}

	function setConfig($head,$value,$app){
		$config=$this->add('xepan\base\Model_Epan_Configuration');
		$config->addCondition('head',$head);
		$config->addCondition('application',$app);
		$config->tryLoadAny();

		$config['value'] = $value;
		$config->save();

		// if($this->recall($this->app->epan->id.'_'.$app.'_'.$head,false)){
		// 	$this->memorize($this->app->epan->id.'_'.$app.'_'.$head, $value);
		// }

		return $config;
	}
}
