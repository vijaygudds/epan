<?php
namespace xepan\cms;

class View_Tool extends \View{

	public $options=[];
	public $_options=[];

	public $virtual_page=null;
	public $add_option_helper = true;

	public $runatServer=true;

	public $templateOverridable = true;
	

	function initializeTemplate($template_spot = null, $template_branch = null){
		foreach ($this->_options as $attr => &$value) {
			if($value==='true') $value=true;
			if($value==='false') $value=false;
			if($value==='1') $value=1;
			if($value==='0') $value=0;
			if($value==='null') $value=null;
		}
		$this->options = $this->_options + $this->options;
		parent::initializeTemplate($template_spot, $template_branch);
	}

	function init(){
		parent::init();
		$this->option_page = $this->option_panel_page = $this->add('VirtualPage');
	}

	function setModel($model,$fields=null){
		$m = parent::setModel($model,$fields);
		if($this->add_option_helper)
			$this->add('xepan\cms\Controller_Tool_Optionhelper');
		return $m;
	}

	function getTemplate(){
		return $this->template;
	}

	function getTemplateFile($options=null){
		return $this->getTemplate()->origin_filename;
	}

	function getTemplatePath(){
		$file = explode("/", $this->getTemplateFile());
		$file_path = array_reverse($file);
		unset($file_path[0]);
		$file_path = implode("/", array_reverse($file_path));
		return $file_path;
	}

	function getCustomtemplateOptionName(){
		return "custom_template";
	}

	function getDefaultTemplate($options=null){
		if(!$this->templateOverridable) return $this->getTemplateFile($options);

		return $this->getTemplate()->template_file;
	}

	function getOptionPanel($parent,$spot,$tool_number=null){
		return $parent->add('View',['name'=>'options_'.$tool_number],$spot,[strtolower(get_class($this)).'_options']);
	}
}