<?php

/**
* description: Contains one or many, CRUD facility on data array 
* 
* @author : RK Sinha
* @email : info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_ConfigJsonModel extends \Model{
	public $fields = [];
	public $config_key;
	public $config_data = []; //STRATEGY_PLANNING_TARGET_AUDIENCE, STRATEGY_PLANNING_TARGET_LOCATION, STRATEGY_PLANNING_BUSINES_DESCRIPTION, STRATEGY_PLANNING_DIGITAL_PRESENCE, STRATEGY_PLANNING_COMETETORS
	public $config_model;
	public $application='base';
	public $namespace='xepan\base';

	public $sort_by=null;

	public $acl_type=null;

	public $acl=true;

	public $status=[
		'All',
	];

	public $actions=[
		'All'=>['view','edit']
	];

	function init(){
		parent::init();
		
		if(!count($this->fields))
			throw new \Exception("must define the fields");
		
		if(!$this->config_key)
			throw new \Exception("must define epan config key");

		if(!$this->acl_type) $this->acl_type = $this->config_key;
		$this->namespace = 'xepan\\'.$this->application;

		foreach ($this->fields as $name => $type) {
			$field = $this->addField($name);
			if($type != "Line")
				$field->display(['form'=>$type]);
		}

		$this->config_model = $this->app->epan->config;
		$this->config_data = json_decode($this->config_model->getConfig($this->config_key,$this->application)?:'{}',true);

		if($this->sort_by){
			uasort($this->config_data, function ($a, $b) { 
			    return ( $a[$this->sort_by] > $b[$this->sort_by] ? 1 : -1 ); 
			});
		}

		$this->setSource("Array",$this->config_data);

		$this->addHook('beforeSave',$this);
		$this->addHook('afterDelete',$this);
	}

	function beforeSave(){
		$this->config_data[$this->id?:uniqid()] = $this->data;
	}

	function save(){
		$this->hook('beforeSave', array($this->id));		
		return $this->config_model->setConfig($this->config_key,json_encode($this->config_data),$this->application);
	}

	function afterDelete(){
		// if(!$this->id)
		// 	throw new \Exception("ConfigJsonModel id not defined", 1);
		
		unset($this->config_data[$this->id]);
		$this->config_model->setConfig($this->config_key,json_encode($this->config_data),$this->application);
	}

}