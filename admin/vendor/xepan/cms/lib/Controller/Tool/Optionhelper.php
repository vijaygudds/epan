<?php

namespace xepan\cms;

class Controller_Tool_Optionhelper extends \AbstractController {

	public $model=null;
	public $options =null ;

	function init(){
		parent::init();

		if(!$this->options)
			$this->options = $this->owner->options;
		
		if(is_string($this->model))
			throw $this->exception("Please specify model object not model string")
						->addMoreInfo('model_provided',$this->model);

		if(!$this->model)
			$this->model = $this->owner->model;


		if($this->model === null) throw $this->exception("Please specify model");

		// Manage model condition
		foreach ($this->options as $opt => $value) {
			if($value=='true') $value=true;
			if($value=='false') $value=false;
			if($value=='null') $value=null;
			
			$method = "addToolCondition_".$opt;
			
			if($this->owner->hasMethod($method)){
				$this->owner->{'addToolCondition_'.$opt}($value, $this->model);
			}elseif($this->owner->owner->hasMethod($method)){
				$this->owner->owner->{'addToolCondition_'.$opt}($value, $this->model);
			}elseif($this->model->hasMethod($method)){
				$this->model->{'addToolCondition_'.$opt}($value, $this->model);
			}else{
				$elm = $this->model->hasElement($opt);
				if($elm && $opt!='id' && $elm instanceof \Field && $value !=='%'){
					$this->model->addCondition($opt,$value);
				}
			}
		}

		$cl_remove_list = [];

		// Manage show options
		foreach ($this->options as $opt=>$value) {
			$opt = strtolower($opt);
			if(strpos($opt, "show_")!==false){
				$opt = str_replace('show_', '', $opt);				
				if($value === false || $value ==='0' || $value === 'false' || $value ===null || $value ==='null' || $value==='undefined'){
					if($this->owner instanceof \Lister){
						$cl_remove_list[] = $opt.'_wrapper';
					}else{
						$this->owner->template->tryDel($opt.'_wrapper');
					}
				} 
			}
		}

		if($this->owner instanceof \Lister){
			$this->owner->addHook('formatRow',function($l)use($cl_remove_list){
				foreach ($cl_remove_list as $rm) {
					$l->current_row_html[$rm]="";
				}
				foreach ($this->options as $opt=>$value) {
					if($value==='true') $value=true;
					if($value==='false') $value=false;
					if($value==='null') $value=null;
					
					$method = "addToolCondition_row_".$opt;

					if($this->owner->hasMethod($method)){		
						$this->owner->$method($value, $l);
					}elseif($this->owner->owner->hasMethod($method)){
						$this->owner->owner->$method($value, $l);
					}elseif($this->model->hasMethod($method)){
						$this->model->$method($value, $l);
					}
				}

			});
		}

	}
}