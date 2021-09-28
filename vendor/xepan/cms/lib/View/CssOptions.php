<?php

namespace xepan\cms;

class View_CssOptions extends \View{

	function init(){
		parent::init();
	}

	function render(){
		// $this->js('click',$this->js()->univ()->frameURL('Override ToolTemplate',[$this->app->url('xepan_cms_overridetemplate'),'options'=> $this->js(null,'JSON.stringify($(current_selected_component).attr())') ,'xepan-tool-to-clone'=>$this->js()->_selector('.xepan-tools-options div[for-xepan-component]:visible')->attr('for-xepan-component')]))->_selector('#override-xepan-tool-template');
		// $this->api->jquery->addStaticStyleSheet('colorpicker/pick-a-color-1.1.8.min');
		// $this->js()
		// 	->_load('colorpicker/tinycolor-0.9.15.min')
		// 	->_load('colorpicker/pick-a-color-1.1.8.min')
		// 	->_load('colorpicker/colorpicker');
		// 	;
		// $this->js(true)->_selector('.epan-color-picker')->univ()->xEpanColorPicker();
		parent::render();
	}

	function getJSID(){
		return "xepan-basic-css-panel";
	}

	function defaultTemplate(){
		return ['view/cms/toolbar/basic'];
	}
}