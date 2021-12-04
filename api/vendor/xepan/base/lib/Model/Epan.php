<?php

/**
* description: Epan Model is the core of a system
* Epan in xEpan can be considered as a Website Hosted in cloud
* each Epan has its own isolated entities in the xEpan platform.
* for single ERP or Website developement this is a mandotory singlaton entity
* @author : Gowrav Vishwakarma
* 
*/

namespace xepan\base;

class Model_Epan extends \xepan\base\Model_Table{
	public $table='epan';

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan_Category');
		$this->hasOne('xepan\base\Contact','created_by_id')->display(['form'=>'xepan\base\Basic']);
		
		$this->addField('name')->hint('Identification for your epan');
		$this->addField('type')->defaultValue('Epan')->system(true);
		$this->addField('status')->defaultValue('Trial');
		$this->addField('created_at')->type('datetime')->defaultValue(isset($this->app->now)?$this->app->now:null)->system(true);
		$this->addField('is_published')->type('boolean')->defaultValue(false);
		$this->addField('extra_info')->type('text');
		$this->addField('aliases')->type('text');
		$this->addField('xepan_template_id');	
		$this->addField('epan_dbversion');	
		$this->addField('is_template')->type('boolean');
		$this->addField('expiry_date')->type('date');


		$this->hasMany('xepan\base\Epan_InstalledApplication',null,null,'InstalledApplications');
		$this->hasMany('xepan\communication\Communication_EmailSetting',null,null,'EmailSettings');
		
		$this->hasMany('xepan\base\Contact');
		$this->hasMany('xepan\base\User',null,null,'Users');
		$this->hasMany('xepan\base\Epan_Configuration',null,null,'Configurations');

		// $this->addHook('beforeDelete',[$this,'deleteAllEmailSettings']);
		// $this->addHook('beforeDelete',[$this,'deleteInstallApplications']);
		// $this->addHook('beforeDelete',[$this,'deleteContacts']);
		// $this->addHook('beforeDelete',[$this,'deleteUsers']);

		// $this->add('dynamic_model/Controller_AutoCreator');
		$this->is([
				'epan_category_id|required',
				'name|required|to_trim|unique',
			]);
	}

	function installApp($application,$hidden=false){
		$installed  = $this->add('xepan\base\Model_Epan_InstalledApplication')
								->addCondition('epan_id',$this->id)
								->addCondition('application_id',$application->id)
								->tryLoadAny();

		if($installed->loaded())
			throw $this->exception('Application Already Installed','ValidityCheck')
						->setField('application_id')
						->addMoreInfo('App',$application['name']);

		$installed['is_hidden']=$hidden;

		$installed->saveAndUnload();

	}

	function addActivity($contact_id, $activity, $related_contact_id=null, $related_document_id=null, $details=null){
		$activity = $this->add('xepan\base\Model_Activity');
		$activity['contact_id'] = $contact_id;
		$activity['activity'] = $activity;
		$activity['related_contact_id'] = $related_contact_id;
		$activity['related_document_id'] = $related_document_id;
		$activity['details'] = $details;

		$activity->save();
		return $activity;
	}


	function isApplicationInstalled($app){
		
		$installed  = $this->add('xepan\base\Model_Epan_InstalledApplication');
		$installed->join('application','application_id')
			->addField('namespace');

		$installed
					// ->addCondition('epan_id',$this->id)
					->addCondition('namespace',$app)
					->tryLoadAny();
		
		return $installed->loaded();
	}

	function createFolder($m){
		if(file_exists(realpath($this->app->pathfinder->base_location->base_path.'/websites/'.$this['name']))){
			throw $this->exception('Epan cannot be created, folder already exists','ValidityCheck')
						->setField('name')
						->addMoreInfo('epan',$this['name']);
		}

		if(file_exists(realpath($this->app->pathfinder->base_location->base_path.'/websites/default/www'))){
			$fs = \Nette\Utils\FileSystem::createDir('./websites/'.$this['name']);
			$fs = \Nette\Utils\FileSystem::copy('./websites/default/www','./websites/'.$this['name'].'/www',true);
		}else{
			$fs = \Nette\Utils\FileSystem::createDir('./websites/'.$this['name']);
			$fs = \Nette\Utils\FileSystem::copy('./vendor/xepan/cms/templates/defaultlayout','./websites/'.$this['name'],true);
		}
		$fs = \Nette\Utils\FileSystem::createDir('./websites/'.$this['name'].'/assets');
		$fs = \Nette\Utils\FileSystem::createDir('./websites/'.$this['name'].'/upload');
	}

	// function deleteAllEmailSettings(){
		
	// 	$this->ref('EmailSettings')->deleteAll();
	// }

	// function deleteInstallApplications(){
	// 	$this->ref('InstalledApplications')->deleteAll();
	// }

	// function deleteContacts(){
	// 	$this->ref('xepan\base\Contact')->deleteAll();
	// }

	// function deleteUsers(){
	// 	$this->ref('Users')->deleteAll();
	// }
}
