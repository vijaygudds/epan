<?php
namespace xepan\base;

class View_Rating extends \View{
	public $theme = 'bootstrap-stars';
	public $initialRating = 3;
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

	function defaultTemplate(){
		return ['view/rating'];
	}
}