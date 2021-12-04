<?php

/**
* description: ATK Model
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Model_Activity extends Model_Table{
	public $table='activity';

	function init(){
		parent::init();

		// $this->hasOne('xepan\base\Epan');
		$this->hasOne('xepan\base\Contact');
		$this->hasOne('xepan\base\Document','related_document_id');
		$this->hasOne('xepan\base\Contact','related_contact_id');

		$this->addField('activity');
		$this->addField('details');
		$this->addField('notify_to');
		$this->addField('notification');
		$this->addField('document_url');
		$this->addField('score');

		$this->addField('created_at')->type('datetime')->defaultValue($this->api->now);
		$this->setOrder('created_at','desc');		
		$this->add('misc/Field_Callback','callback_date')->set(function($m){
			if(date('Y-m-d',strtotime($m['created_at']))==date('Y-m-d',strtotime($this->app->now))){
				return date('h:i a',strtotime($m['created_at']));	
			}
			return date('M d y',strtotime($m['created_at']));
		});


		$this->addhook('beforeSave',function($m){
			if(!$m['notification']) $m['notification'] = $m['activity'];

			if(!is_array($m['notification']))
				$m['notification'] = ['message'=>$m['notification']];
			$m['notification'] = json_encode($m['notification']);
		});
		
		$this->addhook('afterLoad',function($m){
			if(!$m['notification']) $m['notification'] = $m['activity'];
			if($this->isJson($m['notification']))
				$m['notification'] = json_decode($m['notification'],true);
			else
				$m['notification'] = ['message'=>$m['notification']];
		});


		$this->addHook('beforeSave',$this);
		$this->addHook('afterSave',$this);
	}

	function beforeSave(){
		if(!$this['score']) $this['score'] = 0;
	}

	function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}


	function afterSave(){
		if($this->app->getConfig('websocket-notifications',false) && $this['notify_to']){
			$this->pushToWebSocket(json_decode($this['notify_to'],true),$this['notification']?:$this['activity']);
		}
	}

	function pushToWebSocket($employee_ids, $message){
		if(!is_array($employee_ids) OR !count($employee_ids)){
			return ;
		}
		
		if($this->app->getConfig('websocket-notifications',false)){
			$response = $this->add('xepan\base\Controller_WebSocket')
				->sendTo($employee_ids, is_array($message)?json_encode($message):$message);

			$response = json_decode($response,true);
			$notified_employees= [0];

			foreach ($response as $id) {
				$notified_employees[] = explode("_", $id)[1];
			}

			if($this->id){
				$this->app->db->dsql()->table('employee')
					->set('notified_till',$this->id)
					->where('contact_id','in',$notified_employees)
					->update();

				$this->app->employee->reload();
				$this->app->memorize($this->app->epan->id.'_employee', $this->app->employee);
			}
		}
	}
	
}
