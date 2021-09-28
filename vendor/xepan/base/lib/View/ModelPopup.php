<?php
namespace xepan\base;

// $popup->js()->modal() to show modal
class View_ModelPopup extends \View{
	public $options = [
					'addCloseButton'=>true,
					'close_button_label' => "Close",
					'addSaveButton'=>true,
					'save_button_label' => "Save Changes",
					'template'=>"modelpopup"
				];

	function init(){
		parent::init();
	}


	function setModelSize(){

	}

	function addCloseButton(){
		$this->add('Button',null,'close_button')
				->set($this->options['close_button_label'])
				->setAttr(array("data-dismiss"=>'modal',"type"=>"button"))
				->addClass('btn btn-default');
	}

	function saveButtonLable($label){
		$this->options['save_button_label']=$label;
		return $this;
	}

	function addSaveButton(){
		$save_btn = $this->add('Button',null,'save_button');
		$save_btn->set($this->options['save_button_label'])
			->setAttr(array("type"=>"button"))
			->addClass('btn btn-primary');

		$save_btn->js('click',$this->js()->find('.atk-form')->atk4_form("submitForm"));

	}

	function setTitle($title){
		$this->template->trySet('title',$title);
	}

	function setSaveButtonLabel($label){
		$this->template->trySet('save_button_label',$label);
	}

	function setCloseButtonLabel($label){
		$this->template->trySet('close_button_label',$label);
	}

	function defaultTemplate(){
		return ['view/'.($this->options['template']?:"modelpopup")];
	}
	
	function recursiveRender(){
		if($this->options['addCloseButton'])
			$this->addCloseButton();

		if($this->options['addSaveButton'])
			$this->addSaveButton();

		parent::recursiveRender();
	}

}