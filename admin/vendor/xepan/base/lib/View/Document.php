<?php

/**
* description: View_Document is special View that helps to edit 
* any View in its own template by using same template as form layout
* It also helps in managing hasMany relations to be Viewed and Edit on same Level
* 
* @author : Gowrav Vishwakarma
* @email : gowravvishwakarma@gmail.com, info@xavoc.com
* @website : http://xepan.org
* 
*/

namespace xepan\base;

class View_Document extends \View{

	public $view_template = null;
	public $action='view'; // add/edit
	public $id_fields_in_view=[];
	public $deref_fields_in_form=[];
	public $allow_many_on_add=true;

	public $many=[];
	public $view_fields=null;
	public $form_fields=null;

	public $form=null;
	public $id_field_on_reload=null;
	public $submit_button="Save";

	public $effective_template=null;
	public $effective_object=null;

	public $add_on_view=false;

	public $page_reload =false;

	function init(){
		parent::init();


		if($this->action === 'view'){
			$this->form = new \Dummy();
			$this->effective_template=$this->template;
			$this->effective_object= $this;
		}else{
			$ot = clone $this->template;
			$this->template->loadTemplateFromString('{$Content}');
			$this->form = $this->add('Form',null,null,null,true);
			$this->form->setLayout($ot);
			$this->effective_template = $this->form->layout->template;
			$this->effective_object = $this->form;
			if($this->submit_button)
				$this->form->addSubmit($this->submit_button)->addClass('btn btn-primary');
		}
	}

	function add($class,$options=null,$spot=null,$template=null,$isMyform=false){
		if(!$isMyform && $class!='xepan\hr\Controller_ACL' && $this->form instanceof \Form){
			return $this->form->layout->add($class,$options,$spot,$template);
		}

		return parent::add($class,$options,$spot,$template);

	}

	function setIdField($id_field_on_reload){
		$this->id_field_on_reload = $id_field_on_reload;
	}

	function setModel($model,$view_fields=null,$form_fields=null){
		
		$this->view_fields = $view_fields;
		$this->form_fields = $form_fields;
		
		if($this->action=='view'){
			$fields = $view_fields;
		}
		else{
			foreach ($this->form_fields as $fld) {
				if($model->getElement($fld)->system()) $model->getElement($fld)->system(false)->editable(true);
			}
			$m = $this->form->setModel($model,$this->form_fields);

			$fields = $view_fields?:$m->getActualFields();
			/* Still NonEditable fields should show as on view mode */
			$readonly_fields = array_diff($fields, $this->form_fields?:[]);

			// remove derefrenced_fields
			$remove_tag=[];
			foreach ($readonly_fields as $key=>$rf) {
				if( in_array($rf.'_id', $this->form_fields) && !in_array($rf.'_id', $this->deref_fields_in_form)){
					unset($readonly_fields[$key]);
				}
			}

			foreach ($readonly_fields as $fld) {
				@$this->form->layout->template->trySetHTML($fld,$model[$fld]);
			}
		}

		$m = parent::setModel($model,$fields);
		return $m;
	}

	function modelRender()
    {
    	foreach ($this->model->get() as $field => $value) {
    		if(!$this->model->hasElement($field)) continue;
    		if($this->hasMethod('format_'.$field)){
				$value = $this->{'format_'.$field}($value,$this->model);
    		}
    		elseif($this->hasMethod('format_'.$this->model->getElement($field)->type())){
				$value = $this->{'format_'.$this->model->getElement($field)->type()}($field,$value,$this->model);
    		}
			elseif($this->owner->owner->hasMethod('format_'.$field)){
				$value = $this->owner->owner->{'format_'.$field}($value,$this->model);
			}elseif($this->owner->hasMethod('format_'.$this->model->getElement($field)->type())){
				$value = $this->owner->{'format_'.$this->model->getElement($field)->type()}($field,$value,$this->model);
			}elseif($this->hasMethod('format_'.$this->model->getElement($field)->type())){
				$value = $this->{'format_'.$this->model->getElement($field)->type()}($field,$value,$this->model);
			}
			$this->template->trySetHTML($field,$value);
			if(strpos($field,'_id')!==false && !in_array($field, $this->id_fields_in_view)){
				if($this->template->hasTag($field))
					$this->template[$field]='';
			}
		}

    }

	function addMany($entity,$options=null,$spot=null,$template=null,$view_class=null,$form_class=null) {
		$class = $form_class?:'xepan\hr\CRUD';

		if($this->action=='view'){
			$class= $view_class?:'xepan\base\Grid';
			$base = $this;
		}else{
			$base = $this->form->layout;
		}

		$v = $base->add($class,$options,$spot,$template);
		return $this->many[$entity] = $v;
		
	}

	function recursiveRender(){

		if(!$this->id_field_on_reload) throw $this->exception("Please provice 'id_field_on_reload'");
		if($this->action != 'view') {
			$this->form->onSubmit(function($f){	
				$f->save();
				$js = $this->js()->univ()->notify('Saved','Document Saved','success',false);
				if($this->page_reload)
					return $this->js(null,$js)->univ()->location($this->api->url(null,[$this->id_field_on_reload=>$f->model->id,'action'=>($this->action=='add'?'edit':$this->action)]));
				else
					return $this->js(null,$js)->reload(null,null,$this->api->url(null,[$this->id_field_on_reload=>$f->model->id,'action'=>($this->action=='add'?'edit':$this->action),'cut_object'=>$this->name]));
			});	
		}

		return parent::recursiveRender();
	}

	// Formats
	

	function format_boolean($field,$value,$m){
		$icon = $value?'check-circle':'times-circle';
		$color = $value?'green':'red';
		return "<i class='fa fa-$icon status-$color'> $field</i>";
	}

	function format_date($fiels,$value,$m){
		return date('d M Y',strtotime($value));
	}

	function format_datetime($fiels,$value,$m){
		$date = "<div>".date('d M Y',strtotime($value));
		$time = "<br/><small>".date('H:i:s',strtotime($value))."</small></div>";
		return $date.$time;
	}


}
