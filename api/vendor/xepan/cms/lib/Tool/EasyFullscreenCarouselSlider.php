<?php

namespace xepan\cms;

/**
* 
*/
class Tool_EasyFullscreenCarouselSlider extends \xepan\cms\View_Tool{
	// public $runatServer = false;
	// public $templateOverridable=false;
	public $options = [
				// 'show_text'=>true,
				'slider_category'=>null
			];
	function init(){
		parent::init();

		$image_m = $this->add('xepan\cms\Model_CarouselImage');
		$image_m->addCondition('carousel_category_id',$this->options['slider_category']);

		$carousel_cl = $this->add('CompleteLister',null,null,['view/tool/easy-fullscreen-carousel-slider1']);
		$carousel_cl->setModel($image_m);
		// $this->carousel_id = '#slider_'.$carousel_cl->name;
		// $this->paginatorPOS = $this->options['paginator-position'];

		$carousel_cl->addHook('formatRow',function($l){
			$l->current_row_html['title'] = $l->model['title'];
			$l->current_row_html['text_to_display'] = $l->model['text_to_display'];
		});

	}
	// function defaultTemplate(){
	// 	return ['view/tool/easy-fullscreen-carousel-slider1'];
	// }
}