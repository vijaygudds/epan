<?php

namespace xepan\base;

class View_Wizard_Step extends \View {
	public $action_btn = null;

	function setAddOn($addon){
		$this->template->set('addon',$addon);
		return $this;
	}
	function setTitle($title){
		$this->template->set('title',$title);
		return $this;
	}

	function setMessage($msg){
		$this->template->set('message',$msg);
		return $this;
	}

	function setHelpMessage($helpmessage){
		$this->template->set('helpmessage',$helpmessage);
		return $this;
	}

	function setHelpURL($url){
		$this->template->set('url',$url);
		return $this;
	}

	function setAction($title,$action, $isDone=false){
		$this->action_btn = $button = $this->add('Button',null,'action_spot')
			->addClass('btn btn-sm');
		if($isDone){
			$button->addClass('btn-success');
			$title = ['Done', 'icon'=>' fa fa-check'];
		}
		else{
			$button->addClass('btn-danger');
		}

		$button->set($title);

		$button->js('click',$action);
		return $this;
	}

	function getActionButton(){
		return $this->action_btn;
	}

	function defaultTemplate(){
		return ['view\wizardsetup'];
	}
}