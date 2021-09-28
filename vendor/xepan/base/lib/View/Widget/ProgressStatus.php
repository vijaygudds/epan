<?php

namespace xepan\base;

class View_Widget_ProgressStatus extends \View{
	
	public $heading = "heading";
	public $heading_class = "pull-left";
	public $icon = "";
	public $value = "";
	public $style = "progress-bar-default";
	public $class = "";
	public $progress_percentage = "70";
	public $footer = "";

	function setHeading($heading){
		$this->heading = $heading;
		return $this;
	}

	function setProgressPercentage($value){
		$this->progress_percentage = $value;
		return $this;	
	}

	function setIcon($icon_class){
		$this->icon = $icon_class;
		return $this;
	}

	function setClass($class){
		$this->class = $class;
		return $this;
	}

	function setStyle($class){
		$this->style = $class;
		return $this;
	}

	function makeInfo(){
		$this->setStyle('progress-bar-default');
		return $this;
	}

	function makeSuccess(){
		$this->setStyle('progress-bar-success');
		return $this;
	}
	
	function makeWarning(){
		$this->setStyle('progress-bar-warning');
		return $this;
	}

	function makeDanger(){
		$this->setStyle('progress-bar-danger');
		return $this;
	}

	function makePurple(){
		$this->setStyle('progress-bar-info');
		return $this;
	}

	function setValue($value){
		$this->value = $value;
		return $this;
	}

	function setFooter($value){
		$this->footer = $value;
		return $this;
	}

	function recursiveRender(){
		$this->addClass($this->class);

		if(!$this->icon)
			$this->template->tryDel('icon_wrapper');
		else
			$this->template->trySet('icon',$this->icon);

		if($this->heading){
			$this->template->trySetHtml('heading',$this->heading);
		}else
			$this->template->tryDel('heading_wrapper');

		// if($this->value){
			$this->template->trySetHtml('value',$this->value);
		// }else
		// 	$this->template->tryDel('value_wrapper');
		if($this->footer){
			$this->template->trySetHtml('footer',$this->footer);
		}

		$this->template->trySet('progress_bar_style',$this->style);
		$this->template->trySet('progress_percentage',$this->progress_percentage);
		$this->template->trySet('heading_class',$this->heading_class);
		
		parent::recursiveRender();
	}

	function defaultTemplate(){
		return ['view/widget/progressstatus'];
	}
}