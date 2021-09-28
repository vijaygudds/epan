<?php

namespace xepan\listing;

class Tool_Filter extends \xepan\cms\View_Tool{
	public $options = [
			'listing_id'=>null,
			'list_filter_id'=>null,
			'reload_class'=>null,
			'result_page'=>null,
		];
	public $list_model;
	public $filter_data_list=[];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		if(!$this->options['listing_id']){
			$this->add('View_Warning')->addClass('alert alert-warning')->set("Please Select Listing Id");
			return;
		}

		if(!$this->options['list_filter_id']){
			$this->add('View_Warning')->addClass('alert alert-warning')->set("Please Select Listing Filter");
			return;
		}
		

		$this->setListModel();
		
		$existing_filter_data = $this->app->recall('listing_fiter_data',[]);
		$my_existing_data = $existing_filter_data[$this->list_model->id];

		$this->filter = $filter = $this->add('xepan\listing\Model_Filter');
		$filter->addCondition('id',$this->options['list_filter_id']);
		$filter->tryLoadAny();

		$filter_field_model = $filter->getField();

		$form = $this->add('Form');

		if(trim($filter['layout'])){
			$form->add('xepan\base\Controller_FLC')
				->addContentSpot()
				->layout($filter->getLayoutArray());
		}

		foreach ($filter_field_model as $field) {
			$field_name = $field['name'];
			$f = $form->addField($field['field_type'],$field_name,$field['name']);
			$f->setFieldHint($field['hint']);

			$value_list = [];
			if(in_array($field['field_type'], ['DropDown','radio'])){
				if($values = trim($field['default_value'])){
					$t= explode(",", $values);
					$value_list = array_combine($t, $t);
				}elseif($values = $field['populate_values_from_field_id']){
					$t = $field->getDistinctDataValue($this->list_model);
					$value_list = array_combine($t, $t);
				}
				$f->setValueList($value_list);
				$f->setEmptyText('Please Select');
			}

			if($my_existing_data[$field_name]['filter_id'] == $this->filter->id)
				$f->set($my_existing_data[$field_name]['value']);

			$this->filter_data_list[$field_name] 
			= $this->filter_data_list[$field['filter_effected_field_id']] 
			= [
				'operator'=>$field['operator'],
				'filter_effected_field'=>$this->add('xepan\listing\Model_Fields')->load($field['filter_effected_field_id'])->dbColumnName(),
				'form_field_name'=>$field_name,
				'filter_id'=>$this->filter->id
			];
		}

		$submit_btn = $form->addSubmit('Submit');
		$reset_btn = $form->addSubmit('Clear');
		if($form->isSubmitted()){

			if($form->isClicked($reset_btn)){
				$existing_filter_data = $this->app->recall('listing_fiter_data',[]);
				if(isset($existing_filter_data[$this->list_model->id])){
					unset($existing_filter_data[$this->list_model->id]);
					$this->app->memorize('listing_fiter_data',$existing_filter_data);
				}
				if($this->options['result_page']) $this->app->redirect($this->app->url($this->options['result_page']));
				if($this->options['reload_class']) $this->js(null,$form->js()->reload())->_selector($this->options['reload_class'].'>*')->trigger('reload')->execute();
				$form->js()->reload()->execute();
			}

			foreach ($this->filter_data_list as $key => $value) {
				if(!$form[$value['form_field_name']]){
					unset($this->filter_data_list[$key]);
					continue;
				}

				$this->filter_data_list[$value['form_field_name']]['value'] = $form[$value['form_field_name']];
				$this->filter_data_list[$key]['value'] = $form[$value['form_field_name']];
				unset($this->filter_data_list[$key]['form_field_name']);
			}

			$existing_filter_data[$this->list_model->id] = $this->filter_data_list;
			
			$this->app->memorize('listing_fiter_data',$existing_filter_data);

			if($this->options['result_page']) $this->app->redirect($this->app->url($this->options['result_page']));
			if($this->options['reload_class']) $this->js()->_selector($this->options['reload_class'].'>*')->trigger('reload')->execute();
			$this->app->redirect($this->app->url());
		}
	}

	function setListModel(){
		$this->list_model = $this->add('xepan\listing\Model_List');
		$this->list_model->Load($this->options['listing_id']);
		return $this->list_model;
	}
}