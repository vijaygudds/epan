<?php

/**
* description: Adds Websocket communication in xepan mainly for notifications
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class Controller_WebSocket extends \AbstractController{

	public $server = null;

	function init(){
		parent::init();

		if($this->app->getConfig('websocket-notifications',false)){
			$this->server = $this->app->getConfig('websocket-server',null);
		}

	}

	function sendTo($contact_ids_array, $message){
		
		if(!$this->server) return;
		
		// if(!is_array($contact_ids_array) OR !count($contact_ids_array))
		// 	return false;

		$response = [];

		$uu_ids = [];
		foreach ($contact_ids_array as $id) {
			$uu_ids [] = $this->app->current_website_name.'_'. $id;
		}

		$data = ['clients'=>$uu_ids,'message'=>$message,'cmd'=>'notification'];

		$client = new \Hoa\Websocket\Client(
		    new \Hoa\Socket\Client($this->server)
		);

		$client->on('message', function (\Hoa\Event\Bucket $bucket) use(&$response) {
		    $data = $bucket->getData();
		    $response = $data['message'];
		    return $response;
		});

		$client->connect();
		
		$client->send(json_encode($data));

		$client->receive();

		$client->close();

		return $response;
	}
}