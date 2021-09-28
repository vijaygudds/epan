<?php

namespace xepan\cms;

class Tool_Carousel extends \xepan\cms\View_Tool{
	public $count = 1;
	public $active_indicator_count = 1;
	public $indicator_count = 0;
	public $options = [
				'show_text'=>true,
				'carousel_category'=>null
			];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController) return;

		if(!$this->options['carousel_category']){
			$this->add('View')->addClass('alert alert-info')->set('Please Select Carousel Category');
			return;
		}		
		
		$image_m = $this->add('xepan\cms\Model_CarouselImage');
		$image_m->addCondition([['carousel_category_id',$this->options['carousel_category']],['carousel_category',$this->options['carousel_category']]]);
		$image_m->setOrder('order','asc');

		$carousel_cl = $this->add('CompleteLister',null,null,['view\tool\cmscarousel']);
		$carousel_cl->setModel($image_m);
		
		$temp = $this->add('GiTemplate');
		$temp->loadTemplateFromString('<li data-target="#'.$carousel_cl->name.'" data-slide-to="{$count}" class="{$activeindicator}"></li>');
		$carousel_indicator = $carousel_cl->add('Lister',null,'carousel_indicator',$temp);
		$carousel_indicator->setModel($image_m);

		$carousel_cl->addHook('formatRow',function($l){
			if($this->count == 1)
				$l->current_row_html['active'] = "active";
			else
				$l->current_row_html['active'] = "deactive";
			$this->count++;

			if($this->options['show_text']){
				$l->current_row_html['show_text'] = $l->model['text_to_display'];
			}else{
				$l->current_row_html['show_text_wrapper'] = ' ';
			}
			
			$l->current_row_html['text_to_display'] = $l->model['text_to_display'];
			$l->current_row['file'] = './websites/'.$this->app->current_website_name."/".$l->model['file_id'];
		});

		$carousel_indicator->addHook('formatRow',function($l){
			if($this->active_indicator_count == 1)
				$l->current_row_html['activeindicator'] = "active";
			else
				$l->current_row_html['activeindicator'] = "deactive";
			$this->active_indicator_count++;

			$l->current_row_html['count'] = $this->indicator_count;
			$this->indicator_count ++;
		});
	}
}