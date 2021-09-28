<?php

/**
* description: ATK Page
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class page_test extends \Page {
	public $title='Page Title';

	function init(){
		parent::init();

		$v = $this->add('View')->set(rand(100,999));

		$btn = $this->add('Button')->set('PUSH');
		$assigntask_notify_msg = ['title'=>'New task','message'=>" Task Assigned to you : 'ABCD' by 'GVS' ",'type'=>'warning','sticky'=>false,'desktop'=>true, 'js'=>(string) $this->app->js(null, $v->js()->reload())];

		if($btn->isClicked()){
			$this->add('xepan\hr\Model_Activity')
				->pushToWebSocket([$this->app->employee->id],$assigntask_notify_msg);
			$this->js()->execute();
		}


	}
}
