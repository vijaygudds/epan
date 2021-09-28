<?php

/**
* description: User Model used for Authentication basically. Not much is logged based on User Id
* Insted xEpan Platform throws an event and Application can hook this staff with any other Model like Staff
* and will log based on that model.
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_User extends \xepan\base\Model_Table{

	public $table="user";
	public $acl=true;
	public $title_field = "username";
	public $status=[
	'Active',
	'InActive'
	];
	public $actions=[
		'Active'=>['view','edit','delete','deactivate'],
		'InActive'=>['view','edit','delete','activate']
	];

	function init(){
		parent::init();
		
		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\base\Contact','created_by_id')->display(array('form'=>'xepan\base\Basic'));

		$this->addField('username')->sortable(true);
		$this->addField('password')->type('password');
		$this->addField('type');
		$this->addField('scope')->enum(['WebsiteUser','AdminUser','SuperUser'])->defaultValue('WebsiteUser');
		$this->addField('hash');
		$this->addField('access_token')->system(true);
		$this->addField('access_token_expiry')->type('datetime')->system(true);
		$this->addField('last_login_date')->type('datetime');
		$this->addField('status')->enum(['Active','Inactive'])->defaultValue('Active');
		$this->addCondition('type','User');
		$this->hasMany('xepan\base\Contact','user_id',null,'Contacts');
		$this->is([
				'username|unique|to_trim|required',
				'status|required'
			]);

		if($this->app->getConfig('username_is_email',true)){
			$this->is([
					'username|email'
				]);
		}
		// $this->app->auth->addEncryptionHook($this);


		
		$this->addExpression('related_contact')->set(function($m,$q){
			return $m->refSQL('Contacts')->setLimit(1)->fieldQuery('name');
		})->sortable(true);

		$this->addExpression('related_contact_type')->set(function($m,$q){
			return $m->refSQL('Contacts')->setLimit(1)->fieldQuery('type');
		})->sortable(true);
		$this->addHook('beforeDelete',[$this,'checkContactExistance']);
		$this->addHook('beforeSave',[$this,'checkLimits']);

	}

	function checkLimits(){
		$extra_info = $this->app->recall('epan_extra_info_array',false);
		// 0 means unlimited backend user add

        if((isset($extra_info ['specification']['Backend User Limit'])) AND ($extra_info ['specification']['Backend User Limit'] > 0) AND in_array($this['scope'], ['AdminUser','SuperUser'])){
        	$user_count = $this->add('xepan\base\Model_User')
        				->addCondition('scope',['AdminUser','SuperUser']);
        	if($this->loaded()){
        		$user_count->addCondition('id','<>',$this->id);
        	}
        	$user_count = $user_count->count()->getOne();
			
        	if($user_count >= $extra_info ['specification']['Backend User Limit']){
        		throw $this->exception("Sorry ! You cannot add more Backend User. Your Limit is over")
        				->addMoreInfo('Back end Count',$user_count)
        				->addMoreInfo('Back end Limit',$extra_info ['specification']['Backend User Limit'])
        			;
        	}
        }

	}

	function isSuperUser(){
		return $this['scope']=='SuperUser';
	}

	function isAdminUser(){
		return $this['scope'] == 'AdminUser';
	}

	function updatePassword($new_password){
		if(!$this->loaded()) return false;

		$this->add('BasicAuth')
			->usePasswordEncryption('md5')
			->addEncryptionHook($this);
		
		$this['password'] = $new_password;
		$this->save();
		return $this;
	}

	function checkContactExistance(){
		if(!$this->loaded()) return;
		
		if($this->add('xepan\base\Model_Contact')->addCondition('user_id',$this->id)->tryLoadAny()->loaded()){
			if($this['related_contact_type'] == "Employee")
				throw new \Exception("It is associated with an employee", 1);
			else
				throw new \Exception("It is associated with a '".$this['related_contact_type']."'", 1);
		}
			
	}

	function deactivate(){
		$this['status']='InActive';
		$this->app->employee
            ->addActivity("User : '". $this['username'] ."' has been deactivated", null/* Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('activate','InActive',$this);
		$this->save();
	}

	function activate(){
		$this['status']='Active';
		$this->app->employee
            ->addActivity("User : '".$this['username']."' now active", null /*Related Document ID*/, $this->id /*Related Contact ID*/)
            ->notifyWhoCan('deactivate','Active',$this);
		$this->save();
	}
}
