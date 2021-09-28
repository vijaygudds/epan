<?php


class Form_Field_TimePicker extends Form_Field_Line {
	public $options =[];

	function setOption($options, $value=null){
		if(is_array($options)){
			foreach ($options as $key => $value) {
				$this->setOption($key,$value);
			}
			return;
		}else{
			if($value===null) return $this->options[$options];
		}
		$this->options[$options] = $value;
		return $this;
	}
	function render(){
		$this->js(true)
				->_load('bootstrap-timepicker.min')
				->timepicker($this->options)
				->focus($this->js()->next()->trigger('click'));
		parent::render();
	}
}