<?php


namespace xepan\base;

class Form_Field_CodeEditor extends \Form_Field_Text{
	public $options=array();
	public $lang='html';
	public $theme='tomorrow';
	public $worker='html';

	public $load_js=false;

	function init(){
		parent::init();
		$this->setRows(10);
	}

	function render(){

		if($this->load_js){
			$this->js(true)
					->_load('ace/ace/ace')
					->_load('ace/ace/mode-'.$this->lang)
					->_load('ace/ace/theme-'.$this->theme)
					->_load('ace/ace/worker-'.$this->worker)
					->_load('ace/jquery-ace.min');
		}

		$this->js(true)->ace(['theme' =>$this->theme, 'lang'=>$this->lang, 'width'=>'100%']);
		parent::render();
	}
}