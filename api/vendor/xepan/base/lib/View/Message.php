<?php
namespace xepan\base;
class View_Message extends \CompleteLister{
	function init(){
		parent::init();

        $comm_read_model = $this->add('xepan\base\Model_Contact_CommunicationReadEmail');
		$comm_read_model->addCondition('contact_id',$this->app->employee->id);
        // $unread_msg_m->addCondition('is_read_contact',false);
        // $unread_msg_m->addCondition('extra_info','not like','%'.$this->app->employee->id.'%');
		
		$unread_msg_m = $this->add('xepan\communication\Model_Communication_AbstractMessage');
		$unread_msg_m->addCondition('is_read',false);
        $unread_msg_m->setLimit(3);
        $this->setModel($unread_msg_m);

	}
	function setModel($m){
		$m = parent::setModel($m);
		if($this->model->count()->getOne() == 0){
			$v =  $this->add('H3',null,'no_record_found')->addClass('xepan-push text-center')->set('No New Message Found');
			// $this->template->trySet('no_record_found',$v->getHTML());
		}
	}

	function formatRow(){
		
		$this->add('xepan\base\Controller_Avatar',['options'=>['size'=>30,'border'=>['width'=>0]],'name_field'=>'contact']);		
		$this->current_row_html['name'] = $this->model['from_raw']['name'];
		// $this->current_row_html['message']  = $this->model['description'];
		$this->current_row_html['title']  = $this->model['title'];
		
		parent::formatRow();
	}

	function getJSID(){
		return "messageid";
	}

	function defaultTemplate(){
		return ['view/message'];
	}
}