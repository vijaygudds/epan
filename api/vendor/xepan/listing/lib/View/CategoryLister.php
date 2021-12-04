<?php

namespace xepan\listing;

class View_CategoryLister extends \CompleteLister{
		public $options = [];
		
	function init(){
		parent::init();
		
		$model = $this->add('xepan\listing\Model_Category');
		$model->addCondition('status','Active')
				->addCondition('is_website_display',true)
				->addCondition([['parent_category_id',0],['parent_category_id',null]]);

		$model->setOrder('display_sequence','desc');
		$this->setModel($model);

		$this->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$model]);
	}
	
	function formatRow(){

	// 	//calculating url
	// 	if($this->model['custom_link']){
	// 		// if custom link contains http or https then redirect to that website
	// 		$has_https = strpos($this->model['custom_link'], "https");
	// 		$has_http = strpos($this->model['custom_link'], "http");
	// 		if($has_http === false or $has_https === false )
	// 			$url = $this->app->url($this->model['custom_link'],['xsnb_category_id'=>$this->model->id]);
	// 		else
	// 			$url = $this->model['custom_link'];
	// 		$this->current_row_html['url'] = $url;

	// 	}elseif($this->app->enable_sef){
	// 		$url = $this->app->url($this->options['url_page'].'/'.$this->model['sef_url']);
	// 		$url->arguments = [];
	// 		$this->current_row_html['url'] = $url;
	// 	}else{
	// 		$url = $this->app->url($this->options['url_page'],['xsnb_category_id'=>$this->model->id]);
	// 		$this->current_row_html['url'] = $url;
	// 	}

	// 	if($this->options['include_sub_category'] && $this->options['show_only_parent']){
			$sub_cat = $this->add('xepan\listing\Model_Category',['name'=>'model_child_'.$this->model->id]);
			$sub_cat->addCondition('parent_category_id',$this->model->id);
			$sub_cat->addCondition('status',"Active");
			$sub_cat->addCondition('is_website_display',true);
			$sub_cat->setOrder('display_sequence','desc');
			if($sub_cat->count()->getOne() > 0){
				$sub_c = $this->add('xepan\listing\View_CategoryLister',['options'=>$this->options],'nested_category',['view\listing\category\/'.$this->options['template'],'category_list']);
				$sub_c->setModel($sub_cat);
				$this->current_row_html['nested_category']= $sub_c->getHTML();
			}else{
				$this->current_row_html['nested_category'] = "";
			}
	// 	}
		parent::formatRow();
	}

	function defaultTemplate(){
		return ['view/tool/listing/category/'.$this->options['template']];
	}

	// function addToolCondition_row_show_item_count($value,$l){
	// 	if(!$value)
	// 		$l->current_row_html['item_count_wrapper'] = "";
	// 	else
	// 		$l->current_row_html['item_count'] = $l->model['item_count'];
	// }

	function addToolCondition_row_show_image($value,$l){		
		if(!$value)
			$l->current_row_html['image_wrapper'] = "";
		else
			$l->current_row_html['image_url'] = './websites/'.$this->app->current_website_name."/".$l->model['image'];
	}


	// function addToolCondition_row_show_price($value,$l){
	// 	if(!$value)
	// 		$l->current_row_html['price_wrapper'] = "";
	// 	else{
	// 		$l->current_row_html['min_price'] = $l->model['min_price'];	
	// 		$l->current_row_html['max_price'] = $l->model['max_price'];
	// 	}
	// }

}