<?php

namespace xepan\cms;

/**
* display all category and image can be used in portfoli or slide show
* 
* @author : RK Sinha
* @email : info@xavoc.com
* @website : http://xepan.org
* 
*/

class Tool_SlideShow extends \xepan\cms\View_Tool{

	public $options=[
				'slideshow_type'=>'slidepro',
				'carousel_category'=>'',
				'show_thumbnail'=>true
			];

	function init(){
		parent::init();

		if($this->owner instanceof \AbstractController){
			$this->add('View')->addClass('alert alert-info')->set('Tool Slide Show, Double click on it to select it\'s options');
			return;
		}

		if(!$this->options['slideshow_type']){
			$this->add('View')->set('Please Select Slide Show Type')->addClass('alert alert-info');
			return;
		}

		if(!$this->options['carousel_category']){
			$this->add('View')->set('Please Select Slide Show Category')->addClass('alert alert-info');
			return;
		}

		$this->category_model = $this->add('xepan\cms\Model_CarouselCategory');
		$this->category_model->addCondition('name',$this->options['carousel_category']);
		$this->category_model->tryLoadAny();
		if(!$this->category_model->loaded()){
			$this->add('View')->set('selected category not found');
			return;
		}

		switch ($this->options['slideshow_type']){
			case 'slidepro':
				$this->slideproSlideShow();
				break;
		}
	}
	
	function slideproSlideShow(){
		// incude css
		// $this->js(true)->_css('slidepro/examples');
		$this->js(true)->_css('slidepro/slider-pro.min');
		$this->js(true)->_css('fancybox/jquery.fancybox');

		// incude js
		$this->app->jquery->addStaticInclude('slidepro/jquery.sliderPro.min');
		$this->app->jquery->addStaticInclude('fancybox/jquery.fancybox.pack');

		$template = 'view\tool\slideshow\slidepro-'.$this->category_model['layout'];

		$image = $this->add('xepan\cms\Model_CarouselImage');
		$image->addCondition('carousel_category_id',$this->category_model->id);
		$image->addCondition('status','Visible');

		$v = $this->add('View',null,null,[$template]);

		$lister = $v->add('CompleteLister',null,'lister_wrapper',[$template,'lister_wrapper']);
		$lister->setModel($image);
		$lister->addHook('formatRow',function($l){
			$layer_html = $this->getLayerHtml($l->model->id);
			$l->current_row_html['layer_wrapper'] = $layer_html;
			$l->current_row['file'] = './websites/'.$this->app->current_website_name."/".$l->model['file_id'];
			$l->current_row_html['text'] = $l->model['text_to_display'];
		});

		if($this->options['show_thumbnail']){
			$lister = $v->add('CompleteLister',null,'thumbnail_wrapper',[$template,'thumbnail_wrapper']);
			$lister->setModel($image);

			$lister->addHook('formatRow',function($l){
				$l->current_row_html['text'] = $l->model['text_to_display'];
				$l->current_row_html['file'] = './websites/'.$this->app->current_website_name."/".$l->model['file_id'];
			});
		}else
			$v->template->tryDel('thumbnail_wrapper');

		$option_array = $this->getSliderProOptions();
		$v->js(true)->sliderPro($option_array);
	}

	function getSliderProOptions(){
		
		$option_array = [
			'width'=> (strpos($this->category_model['width'],"%") === false && strpos($this->category_model['width'],"px") === false)?(int)$this->category_model['width']:$this->category_model['width'],
			'height'=> (strpos($this->category_model['height'],"%") === false && strpos($this->category_model['height'],"px") === false)?(int)$this->category_model['height']:$this->category_model['height'],
			'arrows'=> ($this->category_model['show_arrows']?true:false),
			'buttons'=> ($this->category_model['show_buttons']?true:false),
			'loop' => true,
			'waitForLayers'=> true,
			'thumbnailWidth'=> $this->category_model['thumbnail_width'],
			'thumbnailHeight'=> $this->category_model['thumbnail_height'],
			'thumbnailPointer'=> ($this->category_model['thumbnail_pointer']?true:false),
			'thumbnailArrows'=> ($this->category_model['thumbnail_arrows']?true:false),
			'autoplay'=> ($this->category_model['autoplay']?true:false),
			'autoScaleLayers'=> false,

			'visibleSize'=>$this->category_model['visible_size'],
			'forceSize'=>$this->category_model['force_size'],
			'autoSlideSize'=>($this->category_model['auto_slide_size']?true:false),
			'autoHeight'=>($this->category_model['auto_height']?true:false),
			'fullScreen'=>($this->category_model['full_screen']?true:false),
			
			'breakpoints'=> [
				'500'=> [
					'thumbnailWidth' => 120,
					'thumbnailHeight' => 500
				]
			]
			
		];

		if(!$this->category_model['height']) unset($option_array['height']);
		
		$remove_key = [];
		switch ($this->category_model['layout']) {

			case 'highlighted-horizontal-text':
				$remove_key = ['forceSize','visibleSize','autoSlideSize','autoHeight','fullScreen'];
				break;

			case 'multislide':
				$remove_key =['thumbnailWidth','thumbnailHeight','thumbnailPointer','thumbnailArrows','autoScaleLayers','autoHeight','fullScreen','breakpoints','waitForLayers'];
				if(substr($option_array['visibleSize'], -1) != '%')
					$option_array['visibleSize'] = $option_array['visibleSize']."%";
				break;

			case 'highlighted-horizontal-thumbnail':
				$remove_key =['thumbnailWidth','thumbnailHeight','thumbnailPointer','autoScaleLayers','autoHeight','breakpoints','visibleSize','forceSize'];
				$option_array['shuffle'] = true;
				$option_array['fade'] = true;
			break;

			case 'highlighted-vertical-thumbnail':
				
				$remove_key = ['waitForLayers','thumbnailHeight','autoScaleLayers','visibleSize','forceSize','autoSlideSize','autoHeight','fullScreen'];
				$option_array['orientation'] = 'true';
				$option_array['thumbnailsPosition'] = 'right';

				$option_array['breakpoints'] = 
							[
								'800'=> [
									'thumbnailsPosition'=> 'bottom',
									'thumbnailWidth'=> 270,
									'thumbnailHeight'=> 100
								],
								'500'=> [
									'thumbnailsPosition'=> 'bottom',
									'thumbnailWidth'=> 120,
									'thumbnailHeight'=> 50
								]
							];
			break;

		}

		foreach ($remove_key as $key => $value) {
			unset($option_array[$value]);
		}
		return $option_array;
	}

	function getLayerHtml($image_id){

		$lm = $this->add('xepan\cms\Model_CarouselLayer');
		$lm->addCondition('carousel_image_id',$image_id);
		$lm->addCondition('status','Active');
		$html = "";

		foreach ($lm as $m) {
			$width = $height ='';
			if($m['width']) $width = ' width="'. $m['width'].'" ';
			if($m['height']) $height = ' height="'. $m['height'].'" ';

			$html .= '<div class="sp-layer '.str_replace(",", "",$m['layer_class']).'" data-horizontal="'.$m['horizontal_position'].'" data-vertical="'.$m['vertical_position'].'" data-show-transition="'.$m['show_transition'].'" data-hide-transition="'.$m['hide_transition'].'" data-show-delay="'.$m['show_delay'].'" data-hide-delay="'.$m['hide_delay'].'">';
			if($m['layer_type'] == "Image"){
				$html .= '<div class="sp-thumbnail-container"> <img src="'.'./websites/'.$this->app->current_website_name."/".$m['image_id'].'" $width $height /></div>';
			}

			if($m['layer_type'] == "Text"){
				$html .= $m['text'];
			}

			if($m['layer_type'] == "Video")
				$html .= '<a class="sp-video" href="'.$m['video_url'].'"><img src="'.'./websites/'.$this->app->current_website_name."/".$m['image_id'].'" $width $height /></a>';

			$html .= '</div>';
		}

		return $html;
	}
}