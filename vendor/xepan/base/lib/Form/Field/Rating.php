<?php

namespace xepan\base;

class Form_Field_Rating extends  \xepan\base\Form_Field_DropDownNormal{
	public $theme = 'bootstrap-stars';
	public $initialRating = 5;
	public $readonly = false;

	function init(){
		parent::init();

	}

	function render(){
		$this->js(true)
			->_load('jquery.barrating.min')
			->_css('../js/barthemes/bootstrap-stars')
			->barrating([
					'theme'=>$this->theme,
					'initialRating'=>$this->initialRating,
					'readonly'=>$this->readonly
				]);

		parent::render();
	}

}