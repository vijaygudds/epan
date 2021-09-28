<?php

/*

https://jdewit.github.io/bootstrap-timepicker/

// $from_time_field->set(date("H:i:s",strtotime($in_time)));
// 			$from_time_field
// 				->setOption('showMeridian',false)  // for 24 hours format etc 
// 				->setOption('defaultTime',0)
// 				->setOption('minuteStep',1)
// 				->setOption('showSeconds',true)
// 				;

*/

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